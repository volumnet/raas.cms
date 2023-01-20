<?php
/**
 * Основной подмодуль
 */
namespace RAAS\CMS;

use RAAS\StdSub;
use RAAS\Abstract_Sub_Controller as RAASAbstractSubController;
use RAAS\Application;
use RAAS\Redirector;

/**
 * Класс основного подмодуля
 */
class Sub_Main extends RAASAbstractSubController
{
    /**
     * Экземпляр класса
     * @var Sub_Main
     */
    protected static $instance;

    /**
     * Точка запуска
     */
    public function run()
    {
        switch ($this->action) {
            case 'edit':
            case 'copy':
            case 'move':
                $this->{$this->action . '_page'}();
                break;
            case 'edit_block':
            case 'edit_material':
            case 'copy_material':
            case 'move_material':
            case 'chtype_material':
                $this->{$this->action}();
                break;
            case 'deassoc_material':
                $this->deassocMaterial();
                break;
            case 'chvis':
            case 'delete':
            case 'vis':
            case 'invis':
                $items = [];
                $ids = (array)$_GET['id'];
                if (in_array('all', $ids, true)) {
                    $pids = (array)$_GET['pid'];
                    $pids = array_filter($pids, 'trim');
                    $pids = array_map('intval', $pids);
                    if ($pids) {
                        $items = Page::getSet([
                            'where' => "pid IN (" . implode(", ", $pids) . ")"
                        ]);
                    }
                } else {
                    $items = array_map(
                        function ($x) {
                            return new Page((int)$x);
                        },
                        $ids
                    );
                }
                $items = array_values($items);
                $Item = isset($items[0]) ? $items[0] : new Page();
                $f = $this->action;
                StdSub::$f(
                    $items,
                    (
                        isset($_GET['back']) ?
                        'history:back' :
                        $this->url . '&id=' . (int)$Item->pid
                    ) . '#subsections',
                    false
                );
                break;
            case 'chvis_block':
            case 'delete_block':
                $Item = Block::spawn((int)$this->id);
                $Page = new Page((int)(isset($_GET['pid']) ? $_GET['pid'] : 0));
                $f = str_replace('_block', '', $this->action);
                StdSub::$f(
                    $Item,
                    $this->url . '&id=' . (int)$Item->pid,
                    true,
                    true,
                    $Page
                );
                break;
            case 'move_up_block':
            case 'move_down_block':
                $Item = Block::spawn((int)$this->id);
                $Page = new Page((int)(isset($_GET['pid']) ? $_GET['pid'] : 0));
                $step = (isset($_GET['step']) && (int)$_GET['step'])
                      ? abs((int)$_GET['step'])
                      : 1;
                if ($this->action == 'move_up_block') {
                    $step *= -1;
                }
                StdSub::swap(
                    $Item,
                    $this->url . '&id=' . (int)$Item->pid,
                    true,
                    true,
                    $step,
                    $Page
                );
                break;
            case 'unassoc_block':
                $Item = Block::spawn((int)$this->id);
                $pid = (isset($_GET['pid']) ? $_GET['pid'] : $Item->pid);
                $Page = new Page(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
                StdSub::unassoc(
                    $Item,
                    $this->url . '&id=' . (int)$pid,
                    true,
                    isset($_GET['pid']) && $Page->id,
                    $Page
                );
                break;
            case 'delete_material':
            case 'chvis_material':
            case 'vis_material':
            case 'invis_material':
                $items = [];
                $ids = (array)$_GET['id'];
                $pids = (array)$_GET['pid'];
                $pids = array_filter($pids, 'trim');
                if (in_array('all', $ids, true)) {
                    $mtype = new Material_Type((int)$_GET['mtype']);
                    $mtypes = (array)$mtype->selfAndChildrenIds;
                    $where = [
                        "Material.pid IN (" . implode(", ", $mtypes) . ")"
                    ];
                    if (!$mtype->global_type) {
                        if (in_array('all', $pids, true)) {
                        } elseif ($pids) {
                            $pids = array_map('intval', $pids);
                            $where[] = "pages___LINK.pid IN (" . implode(", ", $pids) . ")";
                        } else {
                            $where[] = "pages___LINK.pid IN (0)";
                        }
                    }
                    $items = Material::getSet([
                        'where' => $where,
                        'orderBy' => "id"
                    ]);
                } else {
                    $items = array_map(
                        function ($x) {
                            return new Material((int)$x);
                        },
                        $ids
                    );
                }
                $items = array_values($items);
                $Item = isset($items[0]) ? $items[0] : new Material();
                $mtype = $Item->material_type;
                $f = str_replace('_material', '', $this->action);
                $pid = ($mtype->global_type || $pids)
                     ? array_shift($pids)
                     : (int)$Item->pages_ids[0];
                StdSub::$f(
                    $items,
                    (
                        isset($_GET['back']) ?
                        'history:back' :
                        $this->url . '&id=' . (int)$pid
                    ) . '#_' . $mtype->urn,
                    false
                );
                break;
            case 'clear_cache':
                $this->clearPageCache();
                break;
            case 'clear_material_cache':
                $this->clearMaterialCache();
                break;
            case 'clear_block_cache':
                $this->clearBlockCache();
                break;
            default:
                $this->show_page();
                break;
        }
    }


    /**
     * Отображение страницы
     */
    protected function show_page()
    {
        if (isset($_POST['priority']) && is_array($_POST['priority'])) {
            $this->model->setEntitiesPriority(
                Material::class,
                (array)$_POST['priority']
            );
        }
        if (isset($_POST['page_priority']) &&
            is_array($_POST['page_priority'])
        ) {
            $this->model->setEntitiesPriority(
                Page::class,
                (array)$_POST['page_priority']
            );
        }
        $Page = new Page($this->id);
        $OUT = [];
        $OUT = array_merge($OUT, $this->model->show_page());
        $OUT['Item'] = $Page;
        $MSet = [];
        $MPages = [];
        foreach ($Page->affectedMaterialTypes as $mtype) {
            foreach (['sort', 'order'] as $v) {
                $var = 'm' . $mtype->id . $v;
                if (isset($_GET[$var])) {
                    Application::i()->setcookie($var, $_GET[$var]);
                }
            }

            $temp = $this->model->getPageMaterials(
                $Page,
                $mtype,
                (
                    isset($_GET['m' . $mtype->id . 'search_string']) ?
                    $_GET['m' . $mtype->id . 'search_string'] :
                    ''
                ),
                (
                    isset($_COOKIE['m' . $mtype->id . 'sort']) ?
                    $_COOKIE['m' . $mtype->id . 'sort'] :
                    'post_date'
                ),
                (
                    isset($_COOKIE['m' . $mtype->id . 'order']) ?
                    $_COOKIE['m' . $mtype->id . 'order'] :
                    'asc'
                ),
                (
                    isset($_GET['m' . $mtype->id . 'page']) ?
                    (int)$_GET['m' . $mtype->id . 'page'] :
                    1
                )
            );
            $MSet[$mtype->urn] = $temp['Set'];
            $MPages[$mtype->urn] = $temp['Pages'];
            $OUT['Morder'][$mtype->urn] = $temp['order'];
            $OUT['Msort'][$mtype->urn] = $temp['sort'];
        }
        $OUT['MSet'] = $MSet;
        $OUT['MPages'] = $MPages;
        $this->view->show_page($OUT);
    }


    /**
     * Редактирование страницы
     */
    protected function edit_page()
    {
        $Item = new Page((int)$this->id);
        $Parent = $Item->pid
                ? $Item->parent
                : new Page(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $Form = new EditPageForm([
            'Item' => $Item,
            'Parent' => $Parent
        ]);
        $this->view->edit_page(
            array_merge($Form->process(), ['Parent' => $Parent])
        );
    }


    /**
     * Копирование страницы
     */
    protected function copy_page()
    {
        $Original = $Item = new Page((int)$this->id);
        if (!$Item->id) {
            new Redirector($this->url);
        }
        $Parent = $Item->pid ? $Item->parent : new Page();
        $OUT = [];
        $OUT['Parent'] = $Parent;
        $OUT['Original'] = $Original;
        $OUT['Type'] = $Type;
        $Item = $this->model->copyItem($Item);
        $Form = new CopyPageForm([
            'Item' => $Item,
            'Parent' => $Parent,
            'Type' => $Type,
            'Original' => $Original,
        ]);
        $OUT = array_merge($OUT, (array)$Form->process());
        $this->view->edit_page($OUT);
    }


    /**
     * Редактирование блока
     */
    protected function edit_block()
    {
        if ($this->id) {
            $Item = Block::spawn($this->id);
            $classname = $Item->block_type;
        } else {
            $classname = 'RAAS\\CMS\\' . str_replace('.', '\\', ($_GET['type'] ?? ''));
        }
        if (!($blockType = Block_Type::getType($classname)) ||
            !class_exists($classname)
        ) {
            $classname = Block_HTML::class;
            $blockType = Block_Type::getType($classname);
        }
        if (!$this->id) {
            $Item = new $classname();
        }
        $Parent = isset($_GET['pid'])
                ? new Page((int)$_GET['pid'])
                : ($Item->pid ? $Item->parent : new Page());
        $t = $this;
        if (!$Parent->id) {
            new Redirector($this->url);
        }
        $arr = ['Item' => $Item, 'meta' => ['Parent' => $Parent]];

        $Form = $blockType->getForm($arr);
        $this->view->edit_block(
            array_merge($Form->process(), ['Parent' => $Parent])
        );
    }


    /**
     * Перемещение страницы
     */
    protected function move_page()
    {
        $items = [];
        $ids = (array)$_GET['id'];
        if (in_array('all', $ids, true)) {
            $pids = (array)$_GET['pid'];
            $pids = array_filter($pids, 'trim');
            $pids = array_map('intval', $pids);
            if ($pids) {
                $items = Page::getSet([
                    'where' => "pid IN (" . implode(", ", $pids) . ")"
                ]);
            }
        } else {
            $items = array_map(
                function ($x) {
                    return new Page((int)$x);
                },
                $ids
            );
        }
        $items = array_values($items);
        $Item = isset($items[0]) ? $items[0] : new Page();
        if ($items) {
            if (isset($_GET['new_pid'])) {
                $Parent = new Page((int)$_GET['new_pid']);
                StdSub::move($items, $Parent, $this->url . '&id=%s#subsections');
            } else {
                $this->view->move_page(['Item' => $Item, 'items' => $items]);
                return;
            }
        }
        new Redirector('history:back#subsections');
    }


    /**
     * Редактирование материала
     */
    protected function edit_material()
    {
        $Item = new Material($this->id);
        $Type = (isset($Item->pid) && $Item->pid)
              ? $Item->material_type
              : new Material_Type(
                  isset($_GET['mtype']) ?
                  (int)$_GET['mtype'] :
                  0
              );
        if (!$Type->id) {
            new Redirector($this->url . '&id=' . (int)$Parent->id);
        }
        if (!$Item->id) {
            $Item->pid = (int)$Type->id;
        }
        $Parent = new Page();
        $OUT = [];
        $MSet = $MPages = $Morder = $Msort = [];
        if ($Item->id) {
            if (isset($_GET['pid']) &&
                in_array((int)$_GET['pid'], $Item->pages_ids)
            ) {
                $Parent = new Page((int)($_GET['pid'] ?? 0));
            } elseif ($Item->parents) {
                $Parent = new Page($Item->parents_ids[0]);
            } else {
                $Parent = new Page((int)($_GET['pid'] ?? 0));
            }
        } elseif (isset($_GET['pid'])) {
            $Parent = new Page((int)($_GET['pid'] ?? 0));
        }
        foreach ($Item->relatedMaterialTypes as $mtype) {
            foreach (['sort', 'order'] as $v) {
                $var = 'm' . $mtype->id . $v;
                if (isset($_GET[$var])) {
                    Application::i()->setcookie($var, $_GET[$var]);
                }
            }
            $temp = $this->model->getRelatedMaterials(
                $Item,
                $mtype,
                (
                    isset($_GET['m' . $mtype->id . 'search_string']) ?
                    $_GET['m' . $mtype->id . 'search_string'] :
                    ''
                ),
                (
                    isset($_COOKIE['m' . $mtype->id . 'sort']) ?
                    $_COOKIE['m' . $mtype->id . 'sort'] :
                    'post_date'
                ),
                (
                    isset($_COOKIE['m' . $mtype->id . 'order']) ?
                    $_COOKIE['m' . $mtype->id . 'order'] :
                    'asc'
                ),
                (
                    isset($_GET['m' . $mtype->id . 'page']) ?
                    (int)$_GET['m' . $mtype->id . 'page'] :
                    1
                )
            );
            $MSet[$mtype->urn] = $temp['Set'];
            $MPages[$mtype->urn] = $temp['Pages'];
            $Morder[$mtype->urn] = $temp['order'];
            $Msort[$mtype->urn] = $temp['sort'];
        }
        $OUT['Morder'] = $Morder;
        $OUT['Msort'] = $Msort;
        $OUT['MSet'] = $MSet;
        $OUT['MPages'] = $MPages;
        $OUT['Parent'] = $Parent;
        $OUT['Type'] = $Type;
        $Form = new EditMaterialForm([
            'Item' => $Item,
            'Parent' => $Parent,
            'Type' => $Type,
            'MSet' => $MSet,
            'MPages' => $MPages,
            'Msort' => $Msort,
            'Morder' => $Morder
        ]);
        $OUT = array_merge($OUT, (array)$Form->process());
        $this->view->edit_material($OUT);
    }


    /**
     * Копирование материала
     */
    protected function copy_material()
    {
        $Original = $Item = new Material((int)$this->id);
        // 2018-04-04, AVS: добавил возможность дублирования с другим
        // типом материала
        $Type = isset($_GET['mtype'])
              ? new Material_Type((int)$_GET['mtype'])
              : $Original->material_type;
        // $Type = $Item->material_type;
        if (!$Item->id) {
            new Redirector($this->url);
        }
        if (isset($_GET['pid']) &&
            in_array((int)$_GET['pid'], $Item->pages_ids)
        ) {
            $Parent = new Page((int)($_GET['pid'] ?? 0));
        } elseif ($Item->pages) {
            $Parent = new Page($Item->pages_ids[0]);
        } else {
            $Parent = new Page((int)($_GET['pid'] ?? 0));
        }
        $OUT = [];
        $OUT['Parent'] = $Parent;
        $OUT['Original'] = $Original;
        $OUT['Type'] = $Type;
        $Item = $this->model->copyItem($Item);
        // 2018-04-04, AVS: добавил возможность дублирования с другим
        // типом материала
        $Item->pid = $Type->id;
        $Form = new CopyMaterialForm([
            'Item' => $Item,
            'Parent' => $Parent,
            'Type' => $Type,
            'Original' => $Original,
        ]);
        $OUT = array_merge($OUT, (array)$Form->process());
        $this->view->edit_material($OUT);
    }


    /**
     * Перемещение/размещение материала
     */
    protected function move_material()
    {
        $items = [];
        $ids = (array)$_GET['id'];
        $pids = (array)$_GET['pid'];
        $pids = array_filter($pids, 'trim');
        $mtype = new Material_Type((int)$_GET['mtype']);
        if ($mtype->global_type) {
            new Redirector('history:back#_' . $mtype->urn);
        }

        if (in_array('all', $ids, true)) {
            $mtypes = (array)$mtype->selfAndChildrenIds;
            $where = ["Material.pid IN (" . implode(", ", $mtypes) . ")"];
            if (!$mtype->global_type) {
                if (in_array('all', $pids, true)) {
                } elseif ($pids) {
                    $pids = array_map('intval', $pids);
                    $where[] = "pages___LINK.pid IN (" . implode(", ", $pids) . ")";
                } else {
                    $where[] = "pages___LINK.pid IN (0)";
                }
            }
            $items = Material::getSet(['where' => $where, 'orderBy' => "id"]);
        } else {
            $items = array_map(
                function ($x) {
                    return new Material((int)$x);
                },
                $ids
            );
        }
        $items = array_values($items);
        $Item = isset($items[0]) ? $items[0] : new Material();
        $mtype = $Item->material_type;
        $pid = ($mtype->global_type || $pids)
             ? array_shift($pids)
             : (int)$Item->pages_ids[0];
        $oldPage = new Page($pid);
        if ($items) {
            if (isset($_GET['new_pid'])) {
                $Parent = new Page((int)$_GET['new_pid']);
                foreach ($items as $row) {
                    $row->dontUpdateAffectedPages = true;
                    if ($_GET['move']) {
                        $row->deassoc($oldPage);
                    }
                    $row->assoc($Parent);
                }
                Material::updateAffectedPages();
                Material_Type::updateAffectedPagesForSelf($mtype);
                new Redirector(
                    $this->url . '&id=' . (int)$Parent->id . '#_' . $mtype->urn
                );
            } else {
                $this->view->move_material([
                    'Item' => $Item,
                    'items' => $items,
                    'mtype' => $mtype,
                    'page' => $oldPage
                ]);
                return;
            }
        }
        new Redirector('history:back#_' . $mtype->urn);
    }


    /**
     * Удаление материала из раздела
     */
    protected function deassocMaterial()
    {
        $st = microtime(1);
        $items = [];
        $ids = (array)$_GET['id'];
        $pids = (array)$_GET['pid'];
        $pids = array_filter($pids, 'trim');
        $mtype = new Material_Type((int)$_GET['mtype']);
        if ($mtype->global_type) {
            new Redirector('history:back#_' . $mtype->urn);
        }

        if (in_array('all', $ids, true)) {
            $mtypes = (array)$mtype->selfAndChildrenIds;
            $where = ["Material.pid IN (" . implode(", ", $mtypes) . ")"];
            if (in_array('all', $pids, true)) {
            } elseif ($pids) {
                $pids = array_map('intval', $pids);
                $where[] = "pages___LINK.pid IN (" . implode(", ", $pids) . ")";
            } else {
                $where[] = "pages___LINK.pid IN (0)";
            }
            $where[] = "(
                            SELECT COUNT(tMPA2.pid)
                              FROM " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA2
                             WHERE id = Material.id
                        ) > 1";
            $items = Material::getSet(['where' => $where, 'orderBy' => "id"]);
        } else {
            $items = array_map(function ($x) {
                return new Material((int)$x);
            }, $ids);
        }
        $items = array_values($items);
        $Item = isset($items[0]) ? $items[0] : new Material();
        $mtype = $Item->material_type;
        $Parent = new Page((int)$_GET['pid']);
        if ($items) {
            if (count($items) > 1) {
                $itemsIds = array_map(function ($x) {
                    return $x->id;
                }, $items);
                $sqlQuery = "DELETE FROM " . Material::_dbprefix() . "cms_materials_pages_assoc
                              WHERE id IN (" . implode(", ", $itemsIds) . ")
                                AND pid = " . (int)$Parent->id;
                Material::_SQL()->query($sqlQuery);
            } else {
                foreach ($items as $row) {
                    $row->dontUpdateAffectedPages = true;
                    $row->deassoc($Parent);
                }
            }
            Material::updateAffectedPages();
            Material_Type::updateAffectedPagesForSelf($mtype);
        }
        new Redirector(
            $this->url . '&id=' . (int)$Parent->id . '#_' . $mtype->urn
        );
    }


    /**
     * Смена типа материала
     */
    protected function chtype_material()
    {
        $items = [];
        $ids = (array)$_GET['id'];
        $pids = (array)$_GET['pid'];
        $pids = array_filter($pids, 'trim');
        $mtype = new Material_Type((int)$_GET['mtype']);

        if (in_array('all', $ids, true)) {
            $mtypes = (array)$mtype->selfAndChildrenIds;
            $where = ["Material.pid IN (" . implode(", ", $mtypes) . ")"];
            if (!$mtype->global_type) {
                if (in_array('all', $pids, true)) {
                } elseif ($pids) {
                    $pids = array_map('intval', $pids);
                    $where[] = "pages___LINK.pid IN (" . implode(", ", $pids) . ")";
                } else {
                    $where[] = "pages___LINK.pid IN (0)";
                }
            }
            $items = Material::getSet(['where' => $where, 'orderBy' => "id"]);
        } else {
            $items = array_map(
                function ($x) {
                    return new Material((int)$x);
                },
                $ids
            );
        }
        $items = array_values($items);
        $item = isset($items[0]) ? $items[0] : new Material();
        $mtype = $item->material_type;
        $pid = ($mtype->global_type || $pids)
             ? array_shift($pids)
             : (int)$item->pages_ids[0];
        $oldPage = new Page($pid);
        if ($items) {
            if (isset($_GET['new_pid'])) {
                $mtype = new Material_Type((int)$_GET['new_pid']);
                foreach ($items as $row) {
                    $row->pid = $mtype->id;
                    $row->commit();
                }
                new Redirector(
                    $this->url . '&id=' . (int)$pid . '#_' . $mtype->urn
                );
            } else {
                $this->view->chtype_material([
                    'Item' => $item,
                    'items' => $items,
                    'mtype' => $mtype,
                    'page' => $oldPage
                ]);
                return;
            }
        }
        new Redirector('history:back#_' . $mtype->urn);
    }


    /**
     * Очистка кэша страницы
     */
    public function clearPageCache()
    {
        $page = new Page((int)$this->id);
        $page->clearCache();
        new Redirector(
            'history:back' .
            (
                (
                    stristr($_SERVER['HTTP_REFERER'], 'action=edit') ||
                    stristr($_SERVER['HTTP_REFERER'], 'id=' . (int)$page->id)
                ) ?
                '' :
                '#subsections'
            )
        );
    }


    /**
     * Очистка кэша материала
     */
    public function clearMaterialCache()
    {
        $material = new Material((int)$this->id);
        $material->clearCache();
        new Redirector(
            'history:back' .
            (
                (
                    stristr($_SERVER['HTTP_REFERER'], 'action=edit_material') ||
                    stristr($_SERVER['HTTP_REFERER'], 'id=' . (int)$material->id)
                ) ?
                '' :
                '#_' . $material->material_type->urn
            )
        );
    }


    /**
     * Очистка кэша блока
     */
    public function clearBlockCache()
    {
        $block = Block::spawn((int)$this->id);
        $block->clearCache();
        new Redirector('history:back');
    }
}
