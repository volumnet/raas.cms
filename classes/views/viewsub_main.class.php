<?php
/**
 * Представление главного подмодуля
 */
namespace RAAS\CMS;

use SOME\HTTP;
use SOME\Pages;
use SOME\SOME;
use SOME\Text;
use RAAS\Abstract_Sub_View as RAASAbstractSubView;
use RAAS\Column as Column;

/**
 * Класс представления главного подмодуля
 */
class ViewSub_Main extends RAASAbstractSubView
{
    /**
     * Экземпляр класса
     * @var static
     */
    protected static $instance;

    /**
     * Отобразить страницу
     * @param [
     *            'Item' => $Page Страница для отображения
     *            'MSet' => array<
     *                string[] URN типа материала => array<Material>
     *            > Связанные типы материалов,
     *            'MPages' => array<
     *                string[] URN типа материала => array<Pages>
     *            > Постраничная разбивка по связанным типам материалов,
     *            'Msort' => array<
     *                string[] URN типа материала => array<string>
     *            > Параметры сортировки по связанным типам материалов,
     *            'Morder' => array<
     *                string[] URN типа материала => array<'asc'|'desc'>
     *            > Параметры упорядочения по связанным типам материалов,
     *        ] $in Входные данные
     */
    public function show_page(array $in = [])
    {
        $view = $this;
        $in['Table'] = new SubsectionsTable($in);

        if ($in['Item']->id) {
            $in['MTable'] = [];
            foreach ($in['Item']->affectedMaterialTypes as $mtype) {
                $in['MTable'][$mtype->urn] = new MaterialsTable([
                    'Item' => $in['Item'],
                    'mtype' => $mtype,
                    'hashTag' => $mtype->urn,
                    'Set' => $in['MSet'][$mtype->urn],
                    'Pages' => $in['MPages'][$mtype->urn],
                    'sortVar' => 'm' . $mtype->id . 'sort',
                    'orderVar' => 'm' . $mtype->id . 'order',
                    'pagesVar' => 'm' . $mtype->id . 'page',
                    'sort' => $in['Msort'][$mtype->urn],
                    'order' => (strtolower($in['Morder'][$mtype->urn]) == 'desc')
                            ?  Column::SORT_DESC
                            : Column::SORT_ASC
                ]);
            }
        }

        $this->assignVars($in);
        $this->title = $in['Item']->id ? $in['Item']->name : $this->_('SITES');
        $this->subtitle = $this->getPageSubtitle($in['Item']);
        if ($in['Item']->id) {
            $this->path[] = ['href' => $this->url, 'name' => $this->_('PAGES')];
            if ($in['Item']->parents) {
                foreach ($in['Item']->parents as $row) {
                    $this->path[] = [
                        'href' => $this->url . '&id=' . (int)$row->id
                               .  '#subsections',
                        'name' => $row->name
                    ];
                }
            }
        }
        $this->submenu = $this->pagesMenu(new Page(), $in['Item']);
        if ($in['Item']->id) {
            $this->contextmenu = $this->getPageContextMenu($in['Item']);
        } else {
            $this->contextmenu = [[
                'href' => $this->url . '&action=edit',
                'name' => $this->_('CREATE_SITE')
            ]];
        }
        $this->template = ($in['Item']->id ?? null) ? 'pages' : $in['Table']->template;
    }


    /**
     * Редактирование страницы
     * @param [
     *            'Item' => Page Страница для редактирования
     *            'Parent' => Page Родительская страница
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditPageForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_page(array $in = [])
    {
        $this->path[] = ['href' => $this->url, 'name' => $this->_('PAGES')];
        if ($in['Parent']->id) {
            if ($in['Parent']->parents) {
                foreach ($in['Parent']->parents as $row) {
                    $this->path[] = [
                        'href' => $this->url . '&id=' . (int)$row->id
                               .  '#subsections',
                        'name' => $row->name
                    ];
                }
            }
            $this->path[] = [
                'href' => $this->url . '&id=' . (int)$in['Parent']->id
                       .  '#subsections',
                'name' => $in['Parent']->name
            ];
        }
        if ($in['Item']->id) {
            $this->path[] = [
                'href' => $this->url . '&id=' . (int)$in['Item']->id,
                'name' => $in['Item']->name
            ];
        }
        $this->submenu = $this->pagesMenu(
            new Page(),
            $in['Item']->id ? $in['Item'] : $in['Parent']
        );
        $this->js[] = $this->publicURL . '/edit_meta.inc.js';
        $this->stdView->stdEdit($in, 'getPageContextMenu');
        $this->subtitle = $this->getPageSubtitle($in['Item']);
    }


    /**
     * Перемещение страницы
     * @param [
     *            'items' =>? array<Page> Страницы для переноса
     *            'Item' =>? Page Одиночная страница для переноса
     *        ] $in Входные данные
     */
    public function move_page(array $in = [])
    {
        $in['menu'] = $this->movePagesMenu(new Page(), $in['items']);

        $this->assignVars($in);
        $this->path[] = ['href' => $this->url, 'name' => $this->_('PAGES')];
        if ($in['Item']->parents) {
            foreach ($in['Item']->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&id=' . (int)$row->id
                           .  '#subsections',
                    'name' => $row->name
                ];
            }
        }
        $this->path[] = [
            'href' => $this->url . '&id=' . (int)$in['Item']->id,
            'name' => $in['Item']->name
        ];
        if (count($in['items']) == 1) {
            $this->contextmenu = $this->getPageContextMenu($in['Item']);
            $this->submenu = $this->pagesMenu(new Page(), $in['Item']);
        } else {
            $this->submenu = $this->pagesMenu(new Page(), null);
        }
        $this->title = $this->_('MOVING_PAGE');
        $this->template = '/move';
        $this->subtitle = $this->getPageSubtitle($in['Item']);
    }


    /**
     * Редактирование блока
     * @param [
     *            'Item' => Block Блок для редактирования,
     *            'Parent' => Page Родительская страница для блока,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditBlockForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_block(array $in = [])
    {
        $this->js[] = $this->publicURL . '/edit_block.js';
        $this->path[] = ['href' => $this->url, 'name' => $this->_('PAGES')];
        if ($in['Parent']->id) {
            if ($in['Parent']->parents) {
                foreach ($in['Parent']->parents as $row) {
                    $this->path[] = [
                        'href' => $this->url . '&id=' . (int)$row->id,
                        'name' => $row->name
                    ];
                }
            }
            $this->path[] = [
                'href' => $this->url . '&id=' . (int)$in['Parent']->id,
                'name' => $in['Parent']->name
            ];
        }
        $this->submenu = $this->pagesMenu(new Page(), $in['Parent']);
        $this->contextmenu = $this->getBlockContextMenu(
            $in['Item'],
            $in['Parent']
        );
        $this->stdView->stdEdit($in);
        $this->subtitle = $this->getBlockSubtitle($in['Item']);
    }


    /**
     * Редактирование материала
     * @param [
     *            'Item' => Material Материал для редактирования,
     *            'Parent' => Page Родительская страница,
     *            'Type' => Material_Type Тип материалов,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditMaterialForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_material(array $in = [])
    {
        $this->path[] = ['href' => $this->url, 'name' => $this->_('PAGES')];
        if ($in['Parent']->id) {
            if ($in['Parent']->parents) {
                foreach ($in['Parent']->parents as $row) {
                    $this->path[] = [
                        'href' => $this->url . '&id=' . (int)$row->id
                               .  '#_' . $in['Type']->urn,
                        'name' => $row->name/* . ': ' . $in['Type']->name*/
                    ];
                }
            }
            $this->path[] = [
                'href' => $this->url . '&id=' . (int)$in['Parent']->id
                       .  '#_' . $in['Type']->urn,
                'name' => $in['Parent']->name/* . ': ' . $in['Type']->name*/
            ];
        }
        $this->submenu = $this->pagesMenu(new Page(), $in['Parent']);
        $this->js[] = $this->publicURL . '/edit_meta.inc.js';
        $this->stdView->stdEdit($in, 'getMaterialContextMenu');
        $this->subtitle = $this->getMaterialSubtitle($in['Item']);
    }


    /**
     * Перемещение материала
     * @param [
     *            'items' => array<Material> Список материалов для перемещения,
     *            'page' => Page Страница, куда перемещаем
     *        ] $in Входные данные
     */
    public function move_material(array $in = [])
    {
        $in['menu'] = $this->moveMaterialsMenu(new Page(), [$in['page']->id], $in['items']);

        $this->assignVars($in);
        $this->path[] = ['href' => $this->url, 'name' => $this->_('PAGES')];
        if ($in['page']->parents) {
            foreach ($in['page']->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&id=' . (int)$row->id
                           .  '#subsections',
                    'name' => $row->name
                ];
            }
        }
        $this->path[] = [
            'href' => $this->url . '&id=' . (int)$in['page']->id
                   .  '#_' . $in['mtype']->id,
            'name' => $in['page']->name
        ];
        if (count($in['items']) == 1) {
            $this->contextmenu = $this->getMaterialContextMenu($in['Item']);
            $this->submenu = $this->pagesMenu(new Page(), $in['page']);
            $this->subtitle = $this->getMaterialSubtitle($in['Item']);
        } else {
            $this->submenu = $this->pagesMenu(new Page(), null);
        }
        $this->title = $this->_('MOVING_MATERIAL');
        $this->template = '/move';
    }


    /**
     * Смена типа материала
     * @param [
     *            'items' => array<Material> Список материалов для перемещения,
     *            'page' => Page Страница, куда перемещаем
     *        ] $in Входные данные
     */
    public function chtype_material(array $in = [])
    {
        $in['menu'] = $this->changeMaterialTypeMenu(new Material_Type(), [$in['mtype']->id], $in['items']);

        $ids = array_map(
            function ($x) {
                return (int)$x->id;
            },
            $in['items']
        );
        $ids = [$in['page']->id];
        $in['ids'] = $ids;

        $this->assignVars($in);
        $this->path[] = ['href' => $this->url, 'name' => $this->_('PAGES')];
        if ($in['page']->parents) {
            foreach ($in['page']->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&id=' . (int)$row->id
                           .  '#subsections',
                    'name' => $row->name
                ];
            }
        }
        $this->path[] = [
            'href' => $this->url . '&id=' . (int)$in['page']->id
                   .  '#_' . $in['mtype']->id,
            'name' => $in['page']->name
        ];
        if (count($in['items']) == 1) {
            $this->contextmenu = $this->getMaterialContextMenu($in['Item']);
            $this->submenu = $this->pagesMenu(new Page(), $in['page']);
            $this->subtitle = $this->getMaterialSubtitle($in['Item']);
        } else {
            $this->submenu = $this->pagesMenu(new Page(), null);
        }
        $this->title = $this->_('CHANGE_MATERIAL_TYPE');
        $this->template = '/move';
    }


    /**
     * Возвращает контекстное меню для страницы
     * @param Page $page Страница
     * @param int $i Порядок в списке
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getPageContextMenu(Page $page)
    {
        $arr = [];
        if ($page->id) {
            $edit = ($this->action == 'edit');
            $arr[] = [
                'name' => $this->_('BROWSE'),
                'href' => $page->conditionalDomainURL,
                'icon' => 'globe',
                'target' => '_blank',
                'active' => false,
            ];
            $arr[] = [
                'name' => $page->vis
                       ?  $this->_('VISIBLE')
                       : '<span class="muted">' .
                            $this->_('INVISIBLE') .
                         '</span>',
                'href' => $this->url . '&action=chvis&id=' . (int)$page->id
                       .  '&back=1',
                'icon' => $page->vis ? 'ok' : '',
                'title' => $this->_($page->vis ? 'HIDE' : 'SHOW')
            ];
            $arr[] = [
                'href' => $this->url . '&action=copy&id=' . (int)$page->id,
                'name' => $this->_('COPY'),
                'icon' => 'tags'
            ];
            if ($this->action != 'move') {
                $arr[] = [
                    'href' => $this->url . '&action=move&id=' . (int)$page->id,
                    'name' => $this->_('MOVE'),
                    'icon' => 'share-alt'
                ];
            }


            $edit = ($this->action == 'edit');
            $showlist = (($this->action == '') && ($this->id != $page->id));
            if (!$edit) {
                $arr[] = [
                    'href' => $this->url . '&action=edit&id=' . (int)$page->id,
                    'name' => $this->_('EDIT'),
                    'icon' => 'edit'
                ];
            }
            if ($page->cache &&
                Package::i()->registryGet('clear_cache_manually')
            ) {
                $arr[] = [
                    'href' => $this->url . '&action=clear_cache&id='
                           .  (int)$page->id
                           .  ($showlist ? '&back=1' : ''),
                    'name' => $this->_('CLEAR_CACHE'),
                    'icon' => 'refresh',
                ];
            }
            $arr[] = [
                'href' => $this->url . '&action=delete&id=' . (int)$page->id
                       .  ($showlist ? '&back=1' : ''),
                'name' => $this->_('DELETE'),
                'icon' => 'remove',
                'onclick' => 'return confirm(\'' .
                             $this->_('DELETE_TEXT') .
                             '\')'
            ];
        }
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка страниц
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllPagesContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        ];
        $arr[] = [
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        ];
        $arr[] = [
            'name' => $this->_('MOVE'),
            'href' => $this->url . '&action=move',
            'icon' => 'share-alt'
        ];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' .
                         $this->_('DELETE_MULTIPLE_TEXT') .
                         '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для материала
     * @param Material $Item Материал
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getMaterialContextMenu(Material $item)
    {
        $arr = [];
        if ($item->id) {
            $mType = $item->material_type;
            $globalType = (bool)$mType->global_type;
            $urlParent = $item->urlParent;
            if ($urlParent->id) {
                $arr[] = [
                    'name' => $this->_('BROWSE'),
                    'href' => $item->conditionalDomainURL,
                    'icon' => 'globe',
                    'target' => '_blank',
                    'active' => false,
                ];
            }

            if ($this->action == 'edit_material') {
                $edit = ($this->id == $item->id);
                $pidText = '';
            } else {
                $edit = false;
                $pidText = '&pid=' . (int)$this->id;
            }
            if (!$edit) {
                $arr[] = [
                    'href' => $this->url . '&action=edit_material&id='
                           .  (int)$item->id . $pidText,
                    'name' => $this->_('EDIT'),
                    'icon' => 'edit'
                ];
            }
            if ($item->vis) {
                $arr[] = [
                    'name' => $this->_('VISIBLE'),
                    'href' => $this->url . '&action=chvis_material&id='
                           .  (int)$item->id . '&back=1',
                    'icon' => 'ok',
                    'title' => $this->_('HIDE')
                ];
            } else {
                $arr[] = [
                    'name' => '<span class="muted">'
                           .     $this->_('INVISIBLE')
                           .  '</span>',
                    'href' => $this->url . '&action=chvis_material&id='
                           .  (int)$item->id . '&back=1',
                    'icon' => '',
                    'title' => $this->_('SHOW')
                ];
            }
            $arr[] = [
                'href' => $this->url . '&action=copy_material&id='
                       .  (int)$item->id,
                'name' => $this->_('COPY'),
                'icon' => 'tags'
            ];
            if (Package::i()->registryGet('allowChangeMaterialType') &&
                ($this->action != 'chtype_material')
            ) {
                $arr[] = [
                    'href' => $this->url . '&action=chtype_material&id='
                           .  (int)$item->id
                           . $pidText,
                    'name' => $this->_('CHANGE_MATERIAL_TYPE'),
                    'icon' => 'random'
                ];
            }
            if (!$edit && ($this->action != 'move_material') && !$globalType) {
                $arr[] = [
                    'href' => $this->url . '&action=move_material&id='
                           .  (int)$item->id
                           . '&mtype=' . (int)$mType->id
                           . $pidText,
                    'name' => $this->_('PLACE_ON_PAGE'),
                    'icon' => 'share-alt'
                ];
                $arr[] = [
                    'href' => $this->url . '&action=move_material&id='
                           .  (int)$item->id
                           . '&mtype=' . (int)$mType->id
                           . $pidText . '&move=1',
                    'name' => $this->_('MOVE_TO_PAGE'),
                    'icon' => 'share-alt'
                ];
                $pagesCounter = (int)$item->pages_counter ?: count($item->pages);
                if ($pagesCounter > 1) {
                    $arr[] = [
                        'href' => $this->url . '&action=deassoc_material&id='
                               .  (int)$item->id
                               . '&mtype=' . (int)$mType->id . $pidText,
                        'name' => $this->_('DEASSOCIATE_MATERIAL'),
                        'icon' => 'times-circle',
                        'onclick' => 'return confirm(\'' .  $this->_('DEASSOCIATE_MATERIAL_TEXT') . '\')'
                    ];
                }
            }
            if ($urlParent->cache &&
                Package::i()->registryGet('clear_cache_manually')
            ) {
                $arr[] = [
                    'href' => $this->url . '&action=clear_material_cache&id=' . (int)$item->id,
                    'name' => $this->_('CLEAR_CACHE'),
                    'icon' => 'refresh',
                ];
            }
            $arr[] = [
                'href' => $this->url . '&action=delete_material&id='
                       .  (int)$item->id
                       . (
                            !$edit ?
                            '&back=1' :
                            (
                                isset($_GET['pid']) ?
                                '&pid=' . (int)$_GET['pid'] :
                                ''
                            )
                        ),
                'name' => $this->_('DELETE'),
                'icon' => 'remove',
                'onclick' => 'return confirm(\''
                          .  $this->_('DELETE_TEXT')
                          . '\')'
            ];
        }
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка материалов
     * @param Material_Type $materialType Тип материалов
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllMaterialsContextMenu(Material_Type $materialType)
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis_material&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        ];
        $arr[] = [
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis_material&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        ];
        if (Package::i()->registryGet('allowChangeMaterialType')) {
            $arr[] = [
                'href' => $this->url . '&action=chtype_material',
                'name' => $this->_('CHANGE_MATERIAL_TYPE'),
                'icon' => 'random'
            ];
        }
        if (!$materialType->global_type) {
            $arr[] = [
                'href' => $this->url . '&action=move_material&pid=' . $this->id,
                'name' => $this->_('PLACE_ON_PAGE'),
                'icon' => 'share-alt'
            ];
            $arr[] = [
                'href' => $this->url . '&action=move_material&pid=' . $this->id
                       . '&move=1',
                'name' => $this->_('MOVE_TO_PAGE'),
                'icon' => 'share-alt'
            ];
            $arr[] = [
                'href' => $this->url . '&action=deassoc_material&pid=' . $this->id
                    . '&mtype=' . (int)$materialType->id,
                'name' => $this->_('DEASSOCIATE_MATERIALS'),
                'icon' => 'times-circle',
                'onclick' => 'return confirm(\'' .  $this->_('DEASSOCIATE_MATERIALS_TEXT') . '\')'
            ];
        }

        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_material&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\''
                      .  $this->_('DELETE_MULTIPLE_TEXT')
                      . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для размещения
     * @param Location $item Размещение
     * @param Page $page Страница
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getLocationContextMenu(Location $item, Page $page)
    {
        $arr = [];
        foreach (Block_Type::getTypes() as $key => $row) {
            $arr2 = $row->viewer->locationContextMenu($page, $item);
            $arr = array_merge($arr, $arr2);
        }
        return $arr;
    }


    /**
     * Возвращает контекстное меню для блока
     * @param Block $block Блок
     * @param ?Page $page Страница
     * @param int $i Порядок блока в списке
     * @param int $c Количество блоков в списке
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getBlockContextMenu(
        Block $block,
        ?Page $page = null,
        $i = 0,
        $c = 0
    ) {
        $arr = [];
        if ($block->id) {
            $edit = ($this->action == 'edit_block');
            if ($block->cache_type) {
                $cacheItem = [
                    'href' => $this->url . '&action=clear_block_cache&id=' . (int)$block->id,
                    'name' => $this->_('CLEAR_CACHE'),
                    'icon' => 'refresh',
                ];
            }
            if (!$edit) {
                $arr[] = [
                    'href' => $this->url . '&action=edit_block&id='
                           .  (int)$block->id
                           .  ($page->id ? '&pid=' . (int)$page->id : ''),
                    'name' => $this->_('EDIT'),
                    'icon' => 'edit'
                ];
                $arr[] = [
                    'name' => $block->vis
                           ?  $this->_('VISIBLE')
                           : '<span class="muted">' .
                                $this->_('INVISIBLE') .
                             '</span>',
                    'href' => $this->url . '&action=chvis_block&id='
                           .  (int)$block->id
                           .  ($page->id ? '&pid=' . (int)$page->id : '')
                           .  '&back=1',
                    'icon' => $block->vis ? 'ok' : '',
                    'title' => $this->_($block->vis ? 'HIDE' : 'SHOW')
                ];
                if ($i) {
                    $arr[] = [
                        'href' => $this->url . '&action=move_up_block&id='
                               .  (int)$block->id
                               .  ($page->id ? '&pid=' . (int)$page->id : '')
                               .  ($edit ? '' : '&back=1'),
                        'name' => $this->_('MOVE_UP'),
                        'icon' => 'arrow-up'
                    ];
                }
                if ($i < $c - 1) {
                    $arr[] = [
                        'href' => $this->url . '&action=move_down_block&id='
                               .  (int)$block->id
                               .  ($page->id ? '&pid=' . (int)$page->id : '')
                               .  ($edit ? '' : '&back=1'),
                        'name' => $this->_('MOVE_DOWN'),
                        'icon' => 'arrow-down'
                    ];
                }
                if ($block->cache_type) {
                    $arr[] = $cacheItem;
                }
                $arr[] = [
                    'href' => $this->url . '&action=delete_block&id='
                           .  (int)$block->id . ($edit ? '' : '&back=1'),
                    'name' => $this->_('DELETE'),
                    'icon' => 'remove',
                    'onclick' => 'return confirm(\''
                              .  $this->_('DELETE_TEXT')
                              .  '\')'
                ];
            } else {
                if ($block->cache_type) {
                    $arr[] = $cacheItem;
                }
                $arr[] = [
                    'href' => $this->url . '&action=delete_block&id='
                           .  (int)$block->id
                           .  ($page->id ? '&pid=' . (int)$page->id : '')
                           .  ($edit ? '' : '&back=1'),
                    'name' => $this->_('DELETE'),
                    'icon' => 'remove',
                    'onclick' => 'return confirm(\''
                              .  $this->_('DELETE_TEXT')
                              .  '\')'
                ];
            }
        }
        return $arr;
    }


    /**
     * Возвращает меню для списка страниц
     * @param Page|int $node Страница или ID# страницы, для которой строим меню
     * @param Page|int $current Текущая страница или ID# текущей страницы
     * @param int $level Уровень вложенности общий
     * @param int $activeLevel Уровень вложенности относительно активного элемента
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'active' ?=> bool Пункт меню активен,
     *             'class' ?=> string Класс пункта меню,
     *             'submenu' => *рекурсивно*,
     *         ]>
     */
    public function pagesMenu($node, $current = 0, $level = 0, $activeLevel = 0)
    {
        // Статическая переменная введена для оптимизации при большом количестве страниц
        static $packageUrl;
        if (!$packageUrl) {
            $packageUrl = $this->url;
        }
        $st = microtime(true);
        $pageCache = PageRecursiveCache::i();
        $menu = [];
        $nodeId = (int)(($node instanceof SOME) ? $node->id : $node);
        $currentId = (int)(($current instanceof SOME) ? $current->id : $current);
        $childrenIds = $pageCache->getChildrenIds($nodeId);
        foreach ($childrenIds as $childId) {
            $allGrandchildrenIds = $pageCache->getSelfAndChildrenIds($childId, PageRecursiveCache::ASSOC_INNER);
            $childData = $pageCache->cache[$childId];
            $row = [
                'name' => Text::cuttext($childData['name'], 64, '...'),
                'href' => $packageUrl . '&id=' . (int)$childId,
                'class' => '',
                'active' => false,
                // 'data-active-level' => $activeLevel,
            ];

            $active = $row['active'] = isset($allGrandchildrenIds[$currentId]);
            if ($active || !$activeLevel || ($level <= 1)) {
                $submenu = $this->pagesMenu($childId, $currentId, $level + 1, $active ? 0 : $activeLevel + 1);
                $row['submenu'] = $submenu;
                if (!$active && $submenu) {
                    $row['data-ajax-submenu-url'] = '?p=cms&action=pages_menu&id=' . $childId;
                }
            }

            if (!$childData['vis']) {
                $row['class'] .= ' muted';
            } elseif ($childData['response_code']) {
                $row['class'] .= ' text-error';
            }
            if (!$childData['pvis']) {
                $row['class'] .= ' cms-inpvis';
            }

            $menu[] = $row;
        }
        return $menu;
    }


    /**
     * Возвращает меню для перемещения страниц
     * @param Page|int $node Страница или ID# страницы, для которой строим меню
     * @param array $current <pre><code>(Page|int)[]</code></pre> Текущие страницы или ID# текущих страниц
     * @param int $level Уровень вложенности общий
     * @param int $activeLevel Уровень вложенности относительно активного элемента
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'active' ?=> bool Пункт меню активен,
     *             'unfolded' ?=> bool Пункт меню развернут
     *             'class' ?=> string Класс пункта меню,
     *             'submenu' => *рекурсивно*,
     *             'isCurrent' => bool Является текущим элементом для переноса,
     *             'isParent' => bool Является непосредственным родителем элемента для переноса
     *         ]>
     */
    public function movePagesMenu($node, array $current = [], $level = 0, $activeLevel = 0)
    {
        // Статическая переменная введена для оптимизации при большом количестве страниц
        static $packageUrl;
        if (!$packageUrl) {
            $packageUrl = $this->url;
        }
        $currentIds = array_map(
            function ($x) {
                if ($x instanceof SOME) {
                    return $x->id;
                }
                return $x;
            },
            $current
        );
        $st = microtime(true);
        $pageCache = PageRecursiveCache::i();
        $menu = [];
        $nodeId = (int)(($node instanceof SOME) ? $node->id : $node);
        $childrenIds = $pageCache->getChildrenIds($nodeId);
        foreach ($childrenIds as $childId) {
            $isCurrent = in_array($childId, $currentIds);
            $grandchildrenIds = $pageCache->getChildrenIds($childId);
            $isParent = (bool)array_intersect($grandchildrenIds, $currentIds);
            $allGrandchildrenIds = $pageCache->getSelfAndChildrenIds($childId);
            $childData = $pageCache->cache[$childId];
            $row = [
                'name' => $childData['name'],
                'class' => '',
                'active' => false,
                'unfolded' => false,
                'isCurrent' => $isCurrent,
                'isParent' => $isParent,
            ];

            $unfolded = $row['unfolded'] = $row['active'] = (bool)array_intersect($allGrandchildrenIds, $currentIds);
            if (!$isCurrent && !$isParent) {
                $row['href'] = HTTP::queryString('new_pid=' . (int)$childId);
            }
            if (!$isCurrent && ($unfolded || !$activeLevel || ($level <= 1))) {
                $submenu = $this->movePagesMenu($childId, $currentIds, $level + 1, $unfolded ? 0 : $activeLevel + 1);
                $row['submenu'] = $submenu;
                if (!$unfolded && $submenu) {
                    $row['data-ajax-submenu-url'] = '?p=cms&action=pages_menu&id=' . $childId;
                }
            }

            if (!$childData['vis']) {
                $row['class'] .= ' muted';
            } elseif ($childData['response_code']) {
                $row['class'] .= ' text-error';
            }
            if (!$childData['pvis']) {
                $row['class'] .= ' cms-inpvis';
            }

            $menu[] = $row;
        }

        if (!$level) {
            $grandchildrenIds = $pageCache->getChildrenIds(0);
            $isParent = (bool)array_intersect($grandchildrenIds, $currentIds);
            $rootItem = [
                'name' => $this->_('ROOT_SECTION'),
                'class' => '',
                'active' => true,
                'unfolded' => true,
                'submenu' => $menu,
                'isCurrent' => false,
                'isParent' => $isParent,
            ];
            if (!$isParent) {
                $rootItem['href'] = HTTP::queryString('new_pid=0');
            }
            $menu = [$rootItem];
        }
        return $menu;
    }


    /**
     * Возвращает меню для перемещения материалов
     * @param Page|int $node Страница или ID# страницы, для которой строим меню
     * @param array $current <pre><code>(Page|int)[]</code></pre> Текущие страницы или ID# текущих страниц
     * @param array $currentMaterials <pre><code>(Material|int)[]</code></pre> Список ID# материалов для перемещения
     * @param int $level Уровень вложенности общий
     * @param int $activeLevel Уровень вложенности относительно активного элемента
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'active' ?=> bool Пункт меню активен,
     *             'unfolded' ?=> bool Пункт меню развернут
     *             'class' ?=> string Класс пункта меню,
     *             'submenu' => *рекурсивно*,
     *             'isCurrent' => bool Является текущим элементом для переноса,
     *             'isParent' => bool Является непосредственным родителем элемента для переноса
     *         ]>
     */
    public function moveMaterialsMenu($node, array $current = [], $currentMaterials = [], $level = 0, $activeLevel = 0)
    {
        // Статическая переменная введена для оптимизации при большом количестве страниц
        static $packageUrl;
        if (!$packageUrl) {
            $packageUrl = $this->url;
        }
        $currentIds = array_map(
            function ($x) {
                if ($x instanceof SOME) {
                    return $x->id;
                }
                return $x;
            },
            $current
        );
        $currentMaterialsIds = array_map(
            function ($x) {
                if ($x instanceof SOME) {
                    return $x->id;
                }
                return $x;
            },
            $currentMaterials
        );
        $st = microtime(true);
        $pageCache = PageRecursiveCache::i();
        $menu = [];
        $nodeId = (int)(($node instanceof SOME) ? $node->id : $node);
        $childrenIds = $pageCache->getChildrenIds($nodeId);
        foreach ($childrenIds as $childId) {
            $isCurrent = in_array($childId, $currentIds);
            $allGrandchildrenIds = $pageCache->getSelfAndChildrenIds($childId);
            $childData = $pageCache->cache[$childId];
            $row = [
                'name' => $childData['name'],
                'class' => '',
                'active' => false,
                'unfolded' => false,
                'isCurrent' => $isCurrent,
            ];

            $unfolded = $row['unfolded'] = (bool)array_intersect($allGrandchildrenIds, $currentIds);
            $row['active'] = $isCurrent;
            if (!$isCurrent) {
                $row['href'] = HTTP::queryString('new_pid=' . (int)$childId);
            }
            if ($unfolded || !$activeLevel || ($level <= 1)) {
                $submenu = $this->moveMaterialsMenu(
                    $childId,
                    $currentIds,
                    $currentMaterialsIds,
                    $level + 1,
                    $unfolded ? 0 : $activeLevel + 1
                );
                $row['submenu'] = $submenu;
                if (!$unfolded && $submenu) {
                    $row['data-ajax-submenu-url'] = '?p=cms&action=pages_menu&id=' . $childId;
                }
            }

            if (!$childData['vis']) {
                $row['class'] .= ' muted';
            } elseif ($childData['response_code']) {
                $row['class'] .= ' text-error';
            }
            if (!$childData['pvis']) {
                $row['class'] .= ' cms-inpvis';
            }

            $menu[] = $row;
        }

        return $menu;
    }


    /**
     * Возвращает меню для перемещения материалов
     * @param Material_Type|int $node Тип материалов или ID# типа материалов, для которой строим меню
     * @param array $current <pre><code>(Material_Type|int)[]</code></pre> Текущие типы материалов или ID# текущих типов материалов
     * @param array $currentMaterials <pre><code>(Material|int)[]</code></pre> Список ID# материалов для смены типа
     * @param int $level Уровень вложенности общий
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'active' ?=> bool Пункт меню активен,
     *             'unfolded' ?=> bool Пункт меню развернут
     *             'class' ?=> string Класс пункта меню,
     *             'submenu' => *рекурсивно*,
     *             'isCurrent' => bool Является текущим элементом для переноса,
     *             'isParent' => bool Является непосредственным родителем элемента для переноса
     *         ]>
     */
    public function changeMaterialTypeMenu($node, array $current = [], $currentMaterials = [], $level = 0)
    {
        // Статическая переменная введена для оптимизации при большом количестве страниц
        static $packageUrl;
        if (!$packageUrl) {
            $packageUrl = $this->url;
        }
        $currentIds = array_map(
            function ($x) {
                if ($x instanceof SOME) {
                    return $x->id;
                }
                return $x;
            },
            $current
        );
        $currentMaterialsIds = array_map(
            function ($x) {
                if ($x instanceof SOME) {
                    return $x->id;
                }
                return $x;
            },
            $currentMaterials
        );
        $st = microtime(true);
        $materialTypeCache = MaterialTypeRecursiveCache::i();
        $menu = [];
        $nodeId = (int)(($node instanceof SOME) ? $node->id : $node);
        $childrenIds = $materialTypeCache->getChildrenIds($nodeId);
        foreach ($childrenIds as $childId) {
            $isCurrent = in_array($childId, $currentIds);
            $allGrandchildrenIds = $materialTypeCache->getSelfAndChildrenIds($childId);
            $childData = $materialTypeCache->cache[$childId];
            $row = [
                'name' => $childData['name'],
                'class' => '',
                'active' => false,
                'unfolded' => false,
                'isCurrent' => $isCurrent,
            ];

            $unfolded = $row['unfolded'] = (bool)array_intersect($allGrandchildrenIds, $currentIds);
            $row['active'] = $isCurrent;
            if (!$isCurrent) {
                $row['href'] = HTTP::queryString('new_pid=' . (int)$childId);
            }
            if ($unfolded || ($level <= 1)) {
                $submenu = $this->changeMaterialTypeMenu(
                    $childId,
                    $currentIds,
                    $currentMaterialsIds,
                    $level + 1
                );
                $row['submenu'] = $submenu;
            }

            $menu[] = $row;
        }

        return $menu;
    }


    /**
     * Получает подзаголовок страницы
     * @param Page $page Страница для получения
     * @return string HTML-код подзаголовка
     */
    public function getPageSubtitle(Page $page)
    {
        $subtitleArr = [];
        if ($page->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$page->id;
            $subtitleArr[] = $this->_('URL') . ': '
                           . '<a href="' . htmlspecialchars($page->conditionalDomainURL) . '" target="_blank">'
                           .    htmlspecialchars($page->conditionalDomainURL)
                           . '</a>';
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок материала
     * @param Material $item Материал для получения
     * @return string HTML-код подзаголовка
     */
    public function getMaterialSubtitle(Material $item)
    {
        $subtitleArr = [];
        if ($item->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$item->id;
            $subtitleArr[] = $this->_('MATERIAL_TYPE') . ': '
                           . '<a href="' . htmlspecialchars(Sub_Dev::i()->url) . '&action=edit_material_type&id=' . (int)$item->pid . '" target="_blank">'
                           .    htmlspecialchars($item->material_type->name)
                           . '</a>';
            if ($item->url) {
                $subtitleArr[] = $this->_('URL') . ': '
                               . '<a href="' . htmlspecialchars($item->conditionalDomainURL) . '" target="_blank">'
                               .    htmlspecialchars($item->conditionalDomainURL)
                               . '</a>';
            }
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок блока
     * @param Block $block Блок для получения
     * @return string HTML-код подзаголовка
     */
    public function getBlockSubtitle(Block $block)
    {
        $subtitleArr = [];
        if ($block->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$block->id;
            if ($blockType = Block_Type::getType($block->block_type)) {
                if ($blockRenderer = $blockType->viewer) {
                    $subtitleArr[] = $this->_('BLOCK_TYPE') .  ': ' . htmlspecialchars($blockRenderer->renderBlockTypeName());
                }
            }
            return implode('; ', $subtitleArr);
        }
        return '';
    }
}
