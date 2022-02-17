<?php
/**
 * Подмодуль "Разработка"
 */
namespace RAAS\CMS;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SOME\HTTP;
use RAAS\Redirector;
use RAAS\Application;
use RAAS\StdSub;
use RAAS\Abstract_Sub_Controller as RAASAbstractSubController;

/**
 * Класс подмодуля "Разработка"
 */
class Sub_Dev extends RAASAbstractSubController
{
    protected static $instance;

    public function run()
    {
        $this->view->submenu = $this->view->devMenu();
        switch ($this->action) {
            case 'edit_template':
            case 'edit_snippet_folder':
            case 'edit_snippet':
            case 'edit_material_type':
            case 'edit_form':
            case 'menus':
            case 'edit_menu':
            case 'move_menu':
            case 'dictionaries':
            case 'edit_dictionary':
            case 'move_dictionary':
            case 'copy_snippet':
            case 'diag':
            case 'pages_fields':
            case 'forms':
            case 'material_types':
            case 'move_material_field':
            case 'move_material_type':
                $this->{$this->action}();
                break;
            case 'move_material_field_to_group':
                $this->moveMaterialFieldToGroup();
                break;
            case 'edit_material_field':
            case 'edit_form_field':
            case 'edit_page_field':
                $f = str_replace('_form', '', $this->action);
                $f = str_replace('_page', '', $f);
                $f = str_replace('_material', '', $f);
                $this->$f();
                break;
            case 'edit_material_fieldgroup':
                $this->editMaterialFieldGroup();
                break;
            case 'templates':
                $this->view->templates(['Set' => $this->model->dev_templates()]);
                break;
            case 'snippets':
                $this->view->snippets();
                break;
            case 'chvis_dictionary':
            case 'vis_dictionary':
            case 'invis_dictionary':
            case 'delete_dictionary':
                $items = [];
                $ids = (array)$_GET['id'];
                if (in_array('all', $ids, true)) {
                    $pids = (array)$_GET['pid'];
                    $pids = array_filter($pids, 'trim');
                    $pids = array_map('intval', $pids);
                    if ($pids) {
                        $items = Dictionary::getSet([
                            'where' => "pid IN (" . implode(", ", $pids) . ")"
                        ]);
                    }
                } else {
                    $items = array_map(function ($x) {
                        return new Dictionary((int)$x);
                    }, $ids);
                }
                $items = array_values($items);
                $Item = isset($items[0]) ? $items[0] : new Dictionary();
                $f = str_replace('_dictionary', '', $this->action);
                StdSub::$f(
                    $items,
                    $this->url . '&action=dictionaries&id=' . (int)$Item->pid
                );
                break;
            case 'chvis_menu':
            case 'vis_menu':
            case 'invis_menu':
            case 'delete_menu':
            case 'realize_menu':
                $items = [];
                $ids = (array)$_GET['id'];
                if (in_array('all', $ids, true)) {
                    $pids = (array)$_GET['pid'];
                    $pids = array_filter($pids, 'trim');
                    $pids = array_map('intval', $pids);
                    if ($pids) {
                        $items = Menu::getSet([
                            'where' => "pid IN (" . implode(", ", $pids) . ")"
                        ]);
                    }
                } else {
                    $items = array_map(function ($x) {
                        return new Menu((int)$x);
                    }, $ids);
                }
                $items = array_values($items);
                $Item = isset($items[0]) ? $items[0] : new Menu();
                $f = str_replace('_menu', '', $this->action);
                StdSub::$f(
                    $items,
                    $this->url . '&action=menus&id=' . (int)$Item->id
                );
                break;
            case 'delete_template_image':
                $Item = new Template((int)$this->id);
                StdSub::deleteBackground(
                    $Item,
                    (
                        $_GET['back'] ?
                        'history:back' :
                        (
                            $this->url .
                            '&action=edit_template&id=' .
                            (int)$Item->id
                        )
                    ) . '#layout',
                    false
                );
                break;
            case 'delete_template':
                $ids = (array)$_GET['id'];
                $items = array_map(function ($x) {
                    return new Template((int)$x);
                }, $ids);
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=templates');
                break;
            case 'delete_snippet_folder':
                $ids = (array)$_GET['id'];
                $items = array_map(function ($x) {
                    return new Snippet_Folder((int)$x);
                }, $ids);
                $items = array_filter($items, function ($x) {
                    return !$x->locked;
                });
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=snippets');
                break;
            case 'delete_snippet':
                $ids = (array)$_GET['id'];
                $items = array_map(function ($x) {
                    return new Snippet((int)$x);
                }, $ids);
                $items = array_filter($items, function ($x) {
                    return !$x->locked;
                });
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=snippets');
                break;
            case 'delete_form':
                $ids = (array)$_GET['id'];
                $items = array_map(function ($x) {
                    return new Form((int)$x);
                }, $ids);
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=forms');
                break;
            case 'delete_diag':
                $from = (strtotime($_GET['from']) > 0)
                      ? date('Y-m-d', strtotime($_GET['from']))
                      : null;
                $to = (strtotime($_GET['to']) > 0)
                    ? date('Y-m-d', strtotime($_GET['to']))
                    : null;
                Diag::deleteStat($from, $to);
                new Redirector(
                    isset($_GET['back']) ?
                    'history:back' :
                    $this->url . '&action=diag'
                );
                break;
            case 'chvis_material_field':
            case 'vis_material_field':
            case 'invis_material_field':
            case 'delete_material_field':
            case 'show_in_table_material_field':
            case 'required_material_field':
            case 'chvis_form_field':
            case 'vis_form_field':
            case 'invis_form_field':
            case 'delete_form_field':
            case 'show_in_table_form_field':
            case 'required_form_field':
            case 'chvis_page_field':
            case 'vis_page_field':
            case 'invis_page_field':
            case 'delete_page_field':
            case 'show_in_table_page_field':
            case 'required_page_field':
                if (strstr($this->action, 'form')) {
                    $classname = Form_Field::class;
                    $parentClassname = Form::class;
                } elseif (strstr($this->action, 'material')) {
                    $classname = Material_Field::class;
                    $parentClassname = Material_Type::class;
                } else {
                    $classname = Page_Field::class;
                    $parentClassname = Material_Type::class;
                }
                $items = $where = [];
                $ids = (array)$_GET['id'];
                if (in_array('all', $ids, true)) {
                    $where[] = "classname = '" . Application::i()->SQL->real_escape_string($parentClassname) . "'";
                    $pids = (array)$_GET['pid'];
                    $pids = array_filter($pids, 'trim');
                    $pids = array_map('intval', $pids);
                    if ($pids) {
                        $where[] = "pid IN (" . implode(", ", $pids) . ")";
                        $items = $classname::getSet(['where' => $where]);
                    } elseif ($classname == Page_Field::class) {
                        $items = $classname::getSet(['where' => $where]);
                    }
                } else {
                    $items = array_map(function ($x) use ($classname) {
                        return new $classname((int)$x);
                    }, $ids);
                }
                $items = array_values($items);
                $Item = isset($items[0]) ? $items[0] : new $classname();
                $f = str_replace('_form', '', $this->action);
                $f = str_replace('_page', '', $f);
                $f = str_replace('_material', '', $f);
                $f = str_replace('_field', '', $f);
                if (strstr($this->action, 'form')) {
                    $url2 .= '&action=edit_form&id=' . (int)$Item->parent->id;
                } elseif (strstr($this->action, 'material')) {
                    $url2 .= '&action=edit_material_type&id='
                          .  (int)$Item->parent->id;
                } else {
                    $url2 .= '&action=pages_fields';
                }
                StdSub::$f($items, $this->url . $url2);
                break;
            case 'delete_material_type':
                $ids = (array)$_GET['id'];
                $items = array_map(function ($x) {
                    return new Material_Type((int)$x);
                }, $ids);
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=material_types');
                break;
            case 'delete_material_fieldgroup':
                $ids = (array)$_GET['id'];
                $items = array_map(function ($x) {
                    return new MaterialFieldGroup((int)$x);
                }, $ids);
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=material_types');
                break;
                break;
            case 'webmaster_faq':
                $w = new Webmaster();
                $w->createFAQ(
                    $this->view->_('FAQ'),
                    'faq',
                    $this->view->_('FAQ_MAIN')
                );
                new Redirector(HTTP::queryString('action='));
                break;
            case 'webmaster_reviews':
                $w = new Webmaster();
                $w->createFAQ(
                    $this->view->_('REVIEWS'),
                    'reviews',
                    $this->view->_('REVIEWS_MAIN')
                );
                new Redirector(HTTP::queryString('action='));
                break;
            case 'webmaster_photos':
                $w = new Webmaster();
                $w->createPhotos($this->view->_('PHOTOS'), 'photos');
                new Redirector(HTTP::queryString('action='));
                break;
            case 'webmaster_search':
                $w = new Webmaster();
                $w->createSearch();
                new Redirector(HTTP::queryString('action='));
                break;
            case 'clear_cache':
                $this->model->clearCache(true, true);
                Material_Type::updateAffectedPagesForSelf();
                Material_Type::updateAffectedPagesForMaterials();
                new Redirector(HTTP::queryString('action=cache'));
                break;
            case 'update_affected_pages':
                Material_Type::updateAffectedPagesForMaterials();
                Material_Type::updateAffectedPagesForSelf();
                new Redirector(HTTP::queryString('action='));
                break;
            case 'cache':
                $diskFreeSpace = disk_free_space(Application::i()->baseDir);
                $usedByCache = 0;
                $directory = new RecursiveDirectoryIterator(Package::i()->cacheDir);
                $iterator = new RecursiveIteratorIterator($directory);
                $filesCounter = 0;
                foreach ($iterator as $fileEntry) {
                    if ($fileEntry->isFile()) {
                        $usedByCache += $fileEntry->getSize();
                        $filesCounter++;
                    }
                }
                $diskFreeSpace /= (1024 * 1024);
                $usedByCache /= (1024 * 1024);
                $cacheLeaveFreeSpace = (int)Package::i()->registryGet('cache_leave_free_space');
                $availableForCache = $diskFreeSpace - $cacheLeaveFreeSpace;
                $result = [
                    'diskFreeSpace' => $diskFreeSpace,
                    'usedByCache' => $usedByCache,
                    'cacheLeaveFreeSpace' => $cacheLeaveFreeSpace,
                    'availableForCache' => $availableForCache,
                    'filesCounter' => $filesCounter,
                ];
                $this->view->cache($result);
                break;
            case 'copy_form':
                $this->copyForm();
                break;
            case 'redirects':
                $this->redirects();
                break;
            default:
                $this->view->dev();
                break;
        }
    }


    /**
     * Справочники
     */
    protected function dictionaries()
    {
        $Item = new Dictionary((int)$this->id);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $localError = [];
            if ($Item->id) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    if (!in_array($ext, Dictionary::$availableExtensions)) {
                        $localError[] = [
                            'name' => 'INVALID',
                            'value' => 'file',
                            'description' => sprintf(
                                $this->view->_('AVAILABLE_DICTIONARIES_FORMATS'),
                                strtoupper(implode(
                                    ', ',
                                    Dictionary::$availableExtensions
                                ))
                            )
                        ];
                    }
                    if (!$localError) {
                        $this->model->dev_dictionaries_loadFile(
                            $Item,
                            $_FILES['file']
                        );
                    }
                }
            }
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                $this->model->setEntitiesPriority(
                    Dictionary::class,
                    (array)$_POST['priority']
                );
            }
            $OUT['localError'] = $localError;
        }
        $OUT['Item'] = $Item;
        $OUT = array_merge($OUT, $this->model->dev_dictionaries());
        $this->view->dictionaries($OUT);
    }


    /**
     * Редактирование справочника
     */
    protected function edit_dictionary()
    {
        $Item = new Dictionary((int)$this->id);
        $Parent = $Item->pid
                ? $Item->parent
                : new Dictionary(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $Form = new EditDictionaryForm(['Item' => $Item, 'Parent' => $Parent]);
        $this->view->edit_dictionary(
            array_merge($Form->process(), ['Parent' => $Parent])
        );
    }


    /**
     * Перемещение справочника
     */
    protected function move_dictionary()
    {
        $items = [];
        $ids = (array)$_GET['id'];
        if (in_array('all', $ids, true)) {
            $pids = (array)$_GET['pid'];
            $pids = array_filter($pids, 'trim');
            $pids = array_map('intval', $pids);
            if ($pids) {
                $items = Dictionary::getSet([
                    'where' => "pid IN (" . implode(", ", $pids) . ")"
                ]);
            }
        } else {
            $items = array_map(function ($x) {
                return new Dictionary((int)$x);
            }, $ids);
        }
        $items = array_values($items);
        $Item = isset($items[0]) ? $items[0] : new Dictionary();

        if ($items) {
            if (isset($_GET['new_pid'])) {
                StdSub::move(
                    $items,
                    new Dictionary((int)$_GET['new_pid']),
                    $this->url . '&action=dictionaries&id=%s'
                );
            } else {
                $this->view->move_dictionary([
                    'Item' => $Item,
                    'items' => $items
                ]);
                return;
            }
        }
        new Redirector(
            isset($_GET['back']) ?
            'history:back' :
            $this->url . '&action=dictionaries&id=' . (int)$Item->pid
        );
    }


    /**
     * Меню
     */
    protected function menus()
    {
        $menuCache = MenuRecursiveCache::i();
        $Item = new Menu((int)$this->id);
        $Parent = $Item->pid
                ? $Item->parent
                : new Menu(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $OUT = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                foreach ($_POST['priority'] as $key => $val) {
                    $row = new Menu($key);
                    if ($row->id) {
                        $row->priority = (int)$val;
                        $row->commit();
                        $menuCache->refresh(); // 2019-10-07, AVS: сбрасываем, иначе порядок остается
                    }
                }
            }
        }
        $OUT['DATA'] = $Item->getArrayCopy();
        if (!$Item->id) {
            $OUT['DATA']['vis'] = 1;
        }
        $OUT['Item'] = $Item;
        $OUT['Parent'] = $Parent;
        if ($Item->id || ($this->action != 'edit_menu')) {
            if ($Item->id) {
                $OUT['Set'] = $Item->subMenu;
            } else {
                $menusIds = $menuCache->getChildrenIds(0);
                $set = [];
                foreach ($menusIds as $menuId) {
                    $menuData = $menuCache->cache[$menuId];
                    if (!isset($_GET['domain_id']) ||
                        ((string)$menuData['domain_id'] == (string)$_GET['domain_id'])
                    ) {
                        $set[] = new Menu($menuData);
                    }
                }
                $OUT['Set'] = $set;
            }
        }
        $this->view->menus($OUT);
    }


    /**
     * Редактирование меню
     */
    protected function edit_menu()
    {
        $Item = new Menu((int)$this->id);
        $Parent = $Item->pid
                ? $Item->parent
                : new Menu(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $Form = new EditMenuForm(['Item' => $Item, 'Parent' => $Parent]);
        $this->view->edit_menu(
            array_merge($Form->process(), ['Parent' => $Parent])
        );
    }


    /**
     * Перемещение меню
     */
    protected function move_menu()
    {
        $items = [];
        $ids = (array)$_GET['id'];
        if (in_array('all', $ids, true)) {
            $pids = (array)$_GET['pid'];
            $pids = array_filter($pids, 'trim');
            $pids = array_map('intval', $pids);
            if ($pids) {
                $items = Menu::getSet([
                    'where' => "pid IN (" . implode(", ", $pids) . ")"
                ]);
            }
        } else {
            $items = array_map(function ($x) {
                return new Menu((int)$x);
            }, $ids);
        }
        $items = array_values($items);
        $Item = isset($items[0]) ? $items[0] : new Menu();

        if ($items) {
            if (isset($_GET['new_pid'])) {
                StdSub::move(
                    $items,
                    new Menu((int)$_GET['new_pid']),
                    $this->url . '&action=menus&id=%s'
                );
            } else {
                $this->view->move_menu(['Item' => $Item, 'items' => $items]);
                return;
            }
        }
        new Redirector('history:back');
    }


    /**
     * Редактирование шаблона
     */
    protected function edit_template()
    {
        $Item = new Template((int)$this->id);
        $Form = new EditTemplateForm(['Item' => $Item]);
        $this->view->edit_template($Form->process());
    }


    /**
     * Редактирование папки сниппетов
     */
    protected function edit_snippet_folder()
    {
        $Item = new Snippet_Folder((int)$this->id);
        if ($Item->locked) {
            exit;
        }
        $Form = new EditSnippetFolderForm(['Item' => $Item]);
        $this->view->edit_snippet_folder($Form->process());
    }


    /**
     * Редактирование сниппета
     */
    protected function edit_snippet()
    {
        $Item = new Snippet((int)$this->id);
        if ($Item->locked) {
            exit;
        }
        $Form = new EditSnippetForm(['Item' => $Item]);
        $this->view->edit_snippet($Form->process());
    }


    /**
     * Копирование снипппета
     */
    protected function copy_snippet()
    {
        $Item = new Snippet((int)$this->id);
        $Item = $this->model->copyItem($Item);
        $Item->locked = 0;
        $Form = new CopySnippetForm(['Item' => $Item]);
        $this->view->edit_snippet($Form->process());
    }


    /**
     * Типы материалов
     */
    protected function material_types()
    {
        $this->view->material_types();
    }


    /**
     * Редактирование типа материалов
     */
    protected function edit_material_type()
    {
        $Item = new Material_Type((int)$this->id);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                $this->model->setEntitiesPriority(
                    Material_Field::class,
                    (array)$_POST['priority']
                );
            }
            if (isset($_POST['fieldgrouppriority']) && is_array($_POST['fieldgrouppriority'])) {
                $this->model->setEntitiesPriority(
                    FieldGroup::class,
                    (array)$_POST['fieldgrouppriority']
                );
            }
            if (isset($_POST['show_in_form']) && is_array($_POST['show_in_form'])) {
                $fields = $Item->fields;
                $fieldsIds = array_map(function ($field) {
                    return (int)$field->id;
                }, $fields);
                $formVisArr = [];
                foreach ($fieldsIds as $fieldId) {
                    $formVisArr[trim($fieldId)] = [
                        'vis' => isset($_POST['show_in_form'][$fieldId]),
                        'inherit' => isset($_POST['inherit_show_in_form'][$fieldId])
                    ];
                }
                $Item->setFormFieldsIds($formVisArr);
            }
        }
        if ($Item->pid) {
            $Parent = $Item->parent;
        } else {
            $Parent = new Material_Type(
                isset($_GET['pid']) ?
                (int)$_GET['pid'] :
                0
            );
        }
        $Form = new EditMaterialTypeForm([
            'Item' => $Item,
            'Parent' => $Parent
        ]);
        $this->view->edit_material_type(
            array_merge($Form->process(), ['Parent' => $Parent])
        );
    }


    /**
     * Формы
     */
    protected function forms()
    {
        $this->view->forms(['Set' => $this->model->forms()]);
    }


    /**
     * Редактирование формы
     */
    protected function edit_form()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                $this->model->setEntitiesPriority(
                    Form_Field::class,
                    (array)$_POST['priority']
                );
            }
        }
        $Item = new Form((int)$this->id);
        $Form = new EditFormForm(['Item' => $Item]);
        $this->view->edit_form($Form->process());
    }


    /**
     * Дублирование формы
     */
    protected function copyForm()
    {
        $original = $item = new Form((int)$this->id);
        if (!$item->id) {
            new Redirector($this->url);
        }
        $OUT = [];
        $OUT['Original'] = $original;
        $item = $this->model->copyItem($item);
        $Form = new CopyFormForm([
            'Item' => $item,
            'Original' => $original,
        ]);
        $OUT = array_merge($OUT, (array)$Form->process());
        $this->view->edit_form($OUT);
    }


    /**
     * Поля страниц
     */
    protected function pages_fields()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                $this->model->setEntitiesPriority(
                    Page_Field::class,
                    (array)$_POST['priority']
                );
            }
        }
        $this->view->pages_fields(['Set' => $this->model->dev_pages_fields()]);
    }


    /**
     * Редактирование поля
     */
    protected function edit_field()
    {
        if ($this->sub == 'dev' && $this->action == 'edit_form_field') {
            $item = new Form_Field((int)$this->id);
            if ($item->pid) {
                $parent =$item->parent;
            } else {
                $parent = new Form(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
            }
            $parentUrl = $this->url . '&action=edit_form';
            if (!$parent->id) {
                new Redirector($parentUrl);
            }
            $parentUrl .= '&id=' . (int)$parent->id;
        } elseif (strstr($this->action, 'material')) {
            $item = new Material_Field((int)$this->id);
            if ($item->pid) {
                $parent = $item->parent;
            } else {
                $parent = new Material_Type(
                    isset($_GET['pid']) ?
                    (int)$_GET['pid'] :
                    0
                );
            }
            $parentUrl = $this->url . '&action=edit_material_type';
            if (!$parent->id) {
                new Redirector($parentUrl);
            }
            $parentUrl .= '&id=' . (int)$parent->id;
        } else {
            $item = new Page_Field((int)$this->id);
            $parent = null;
            $parentUrl = $this->url . '&action=pages_fields';
        }
        if ($item instanceof Material_Field) {
            $formClassname = EditMaterialFieldForm::class;
        } else {
            $formClassname = EditFieldForm::class;
        }
        $form = new $formClassname([
            'Item' => $item,
            'meta' => [
                'Parent' => $parent,
                'parentUrl' => $parentUrl
            ]
        ]);
        $out = $form->process();
        if ($item instanceof Material_Field) {
            $out['Parent'] = $parent;
            $this->view->edit_material_field($out);
        } elseif ($item instanceof Form_Field) {
            $out['Parent'] = $parent;
            $this->view->edit_form_field($out);
        } else {
            $this->view->edit_page_field($out);
        }
    }


    /**
     * Редактирование группы полей типа материалов
     */
    protected function editMaterialFieldGroup()
    {
        $item = new MaterialFieldGroup((int)$this->id);
        if ($item->pid) {
            $parent = $item->parent;
        } else {
            $parent = new Material_Type(
                isset($_GET['pid']) ?
                (int)$_GET['pid'] :
                0
            );
        }
        if (!$parent->id) {
            new Redirector($parentUrl);
        }
        $parentUrl = $this->url . '&action=edit_material_type&id='
            . (int)$parent->id;
        $form = new EditFieldGroupForm([
            'Item' => $item,
            'meta' => [
                'Parent' => $parent,
                'parentUrl' => $parentUrl
            ]
        ]);
        $out = $form->process();
        $out['Parent'] = $parent;
        $this->view->editMaterialFieldGroup($out);
    }


    /**
     * Перемещение поля материалов
     */
    protected function move_material_field()
    {
        $items = [];
        $ids = (array)$_GET['id'];
        if (in_array('all', $ids, true)) {
            $pids = (array)$_GET['pid'];
            $pids = array_filter($pids, 'trim');
            $pids = array_map('intval', $pids);
            if ($pids) {
                $items = Material_Field::getSet([
                    'where' => "classname = 'RAAS\\\\CMS\\\\Material_Type'
                            AND pid IN (" . implode(", ", $pids) . ")"
                ]);
            }
        } else {
            $items = array_map(function ($x) {
                return new Material_Field((int)$x);
            }, $ids);
        }
        $items = array_values($items);
        $item = isset($items[0]) ? $items[0] : new Material_Field();

        if ($items) {
            if (isset($_GET['new_pid'])) {
                StdSub::move(
                    $items,
                    new Material_Type((int)$_GET['new_pid']),
                    $this->url . '&action=edit_material_type&id=%s'
                );
            } else {
                $this->view->move_material_field([
                    'Item' => $item,
                    'items' => $items
                ]);
                return;
            }
        }
        new Redirector(
            isset($_GET['back']) ?
            'history:back' :
            $this->url . '&action=edit_material_type&id=' . (int)$item->pid
        );
    }


    /**
     * Размещение полей материалов в группе
     */
    protected function moveMaterialFieldToGroup()
    {
        $items = [];
        $ids = (array)$_GET['id'];
        if (in_array('all', $ids, true)) {
            $items = Material_Field::getSet([
                'where' => "classname = 'RAAS\\\\CMS\\\\Material_Type' AND pid = " . (int)$_GET['pid']
            ]);
        } else {
            $items = array_map(function ($x) {
                return new Material_Field((int)$x);
            }, $ids);
        }
        $items = array_values($items);
        $parent = new Material_Type($_GET['pid']);
        $item = isset($items[0]) ? $items[0] : new Material_Field();

        if ($items) {
            if (isset($_GET['gid'])) {
                foreach ($items as $row) {
                    $row->gid = $_GET['gid'];
                    $row->commit();
                }
                new Redirector(
                    $_GET['back'] ?
                    'history:back' :
                    $this->url . '&action=edit_material_type&id=' . (int)$item->pid
                );
            } else {
                $this->view->moveMaterialFieldToGroup([
                    'Item' => $item,
                    'items' => $items,
                    'Parent' => $parent,
                ]);
                return;
            }
        }
    }


    /**
     * Перемещение типа материалов
     */
    protected function move_material_type()
    {
        $items = [];
        $ids = (array)$_GET['id'];

        $items = array_map(function ($x) {
            return new Material_Type((int)$x);
        }, $ids);

        $items = array_values($items);
        $item = isset($items[0]) ? $items[0] : new Material_Type();

        if ($items) {
            if (isset($_GET['new_pid'])) {
                StdSub::move(
                    $items,
                    new Material_Type((int)$_GET['new_pid']),
                    $this->url . '&action=material_types'
                );
            } else {
                $this->view->move_material_type([
                    'Item' => $item,
                    'items' => $items
                ]);
                return;
            }
        }
        new Redirector(
            isset($_GET['back']) ?
            'history:back' :
            $this->url . '&action=material_types&id=' . (int)$item->pid
        );
    }


    /**
     * Диагностика
     */
    protected function diag()
    {
        $tFrom = strtotime($_GET['from']);
        $tTo = strtotime($_GET['to']);
        $from = date('Y-m-d', ($tFrom > 0) ? $tFrom : time());
        $to = date('Y-m-d', ($tTo > 0) ? $tTo : time());
        $Item = Diag::getMerged($from, $to);
        $this->view->diag(['Item' => $Item, 'from' => $from, 'to' => $to]);
    }


    /**
     * Редиректы
     */
    protected function redirects()
    {
        $set = Redirect::getSet();
        $form = new RedirectsForm(['Set' => $set]);
        $this->view->redirects($form->process());
    }
}
