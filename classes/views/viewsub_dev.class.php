<?php
/**
 * Представление для подмодуля "Разработка"
 */
namespace RAAS\CMS;

use SOME\Text;
use RAAS\Abstract_Sub_View as RAASAbstractSubView;

/**
 * Класс представления для подмодуля "Разработка"
 */
class ViewSub_Dev extends RAASAbstractSubView
{
    protected static $instance;

    /**
     * Список справочников
     * @param [
     *            'Item' => Dictionary Родительский справочник
     *            'Set' => array<Dictionary> набор подразделов,
     *            'Pages' => Pages Постраничная разбивка,
     *            'sort' => string Поля для сортировки,
     *            'order' => 'asc'|'desc' Порядок сортировки,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *        ] $in Входные данные
     */
    public function dictionaries(array $in = [])
    {
        $in['Table'] = new DictionariesTable($in);
        $this->assignVars($in);
        $this->title = $in['Item']->id
                     ? $in['Item']->name
                     : $this->_('DICTIONARIES');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=dictionaries',
            'name' => $this->_('DICTIONARIES')
        ];
        if ($in['Item']->parents) {
            foreach ($in['Item']->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&action=dictionaries&id='
                           .  (int)$row->id,
                    'name' => $row->name
                ];
            }
        }
        $this->contextmenu = $this->getDictionaryContextMenu($in['Item']);
        $this->template = $in['Table']->template;
        $this->subtitle = $this->getDictionarySubtitle($in['Item']);
    }


    /**
     * Редактирование справочника
     * @param [
     *            'Parent' => Dictionary Родительский справочник,
     *            'Item' => Dictionary Текущий справочник,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditDictionaryForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_dictionary(array $in = [])
    {
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=dictionaries',
            'name' => $this->_('DICTIONARIES')
        ];
        if ($in['Parent']->id) {
            if ($in['Parent']->parents) {
                foreach ($in['Parent']->parents as $row) {
                    $this->path[] = [
                        'href' => $this->url . '&action=dictionaries&id='
                               .  (int)$row->id,
                        'name' => $row->name
                    ];
                }
            }
            $this->path[] = [
                'href' => $this->url . '&action=dictionaries&id='
                       .  (int)$in['Parent']->id,
                'name' => $in['Parent']->name
            ];
        }
        $this->stdView->stdEdit($in, 'getDictionaryContextMenu');
        $this->subtitle = $this->getDictionarySubtitle($in['Item']);
    }


    /**
     * Перемещение справочников
     * @param [
     *            'Item' => Dictionary Текущий справочник,
     *            'items' =>? array<Dictionary> Список текущих справочников,
     *        ] $in Входные данные
     */
    public function move_dictionary(array $in = [])
    {
        $ids = array_map(function ($x) {
            return (int)$x->id;
        }, $in['items']);
        $ids = array_unique($ids);
        $ids = array_values($ids);
        $pids = array_map(function ($x) {
            return (int)$x->pid;
        }, $in['items']);
        $pids = array_unique($pids);
        $pids = array_values($pids);
        $actives = [];
        foreach ($in['items'] as $row) {
            $actives = array_merge($actives, $row->selfAndParentsIds);
        }
        $actives = array_unique($actives);
        $actives = array_values($actives);
        $in['ids'] = $ids;
        $in['pids'] = $pids;
        $in['actives'] = $actives;

        $this->assignVars($in);
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=dictionaries',
            'name' => $this->_('DICTIONARIES')
        ];
        if ($in['Item']->parents) {
            foreach ($in['Item']->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&action=dictionaries' . '&id=' . (int)$row->id,
                    'name' => $row->name
                ];
            }
        }
        $this->path[] = [
            'href' => $this->url . '&action=dictionaries' . '&id=' . (int)$in['Item']->id,
            'name' => $in['Item']->name
        ];
        if (count($in['items']) == 1) {
            $this->contextmenu = $this->getDictionaryContextMenu($in['Item']);
        }
        $this->title = $this->_('MOVING_NOTE');
        $this->template = 'dev_move_dictionary';
        $this->subtitle = $this->getDictionarySubtitle($in['Item']);
    }


    /**
     * Задает "хлебные крошки" для меню
     * @param Menu $current Текущее меню
     * @return array<[
     *             'name' => string Наименование пункта,
     *             'href' => string Ссылка пункта
     *         ]>
     */
    public function getMenuBreadcrumbs(Menu $current)
    {
        $pageCache = PageRecursiveCache::i();
        $menuCache = MenuRecursiveCache::i();
        $domainsIds = $pageCache->getChildrenIds(0);

        $this->path[] = [
            'href' => $this->url . '&action=menus',
            'name' => $this->_('MENUS')
        ];
        if (count($domainsIds) > 1) {
            $domainName = $current->domain_id
                        ? $pageCache->cache[(int)$current->domain_id]['name']
                        : $this->_('WITHOUT_DOMAIN');
            $this->path[] = [
                'href' => $this->url . '&action=menus&domain_id=' . (int)$current->domain_id,
                'name' => $domainName
            ];
        }
        foreach ($menuCache->getParentsIds($current->id) as $parentId) {
            $parentData = $menuCache->cache[$parentId];
            $this->path[] = [
                'href' => $this->url . '&action=menus' . '&id=' . (int)$parentData['id'],
                'name' => $parentData['name']
            ];
        }
    }


    /**
     * Отображает страницу списка меню
     * @param [
     *            'Item' => Menu Текущий пункт меню
     *            'Parent' => Menu Родительский пункт меню,
     *            'Set' => array<Menu> Список пунктов,
     *            'DATA' => array<
     *                string[] => mixed
     *            > Представление текущего пункта меню в виде массива
     *        ] $in Входные данные
     */
    public function menus(array $in = [])
    {
        $item = $in['Item'];

        $in['Table'] = new MenusTable($in);
        if ($item->id && !$item->pid) {
            $in['Form'] = new ViewMenuForm($in);
        }
        $this->assignVars($in);
        $this->title = $item->id ? $item->name : $this->_('MENUS');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        if ($item->id) {
            $this->getMenuBreadcrumbs($item);
        }
        $this->contextmenu = $this->getMenuContextMenu($in['Item']);
        $this->template = ($item->id && !$item->pid) ? $in['Form']->template : 'dev_menus';
        $this->subtitle = $this->getMenuSubtitle($in['Item']);
    }


    /**
     * Редактирование меню
     * @param [
     *            'Parent' => Menu Родительский пункт меню,
     *            'Item' => Menu Текущий пункт меню,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditMenuForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_menu(array $in = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_menu.js';
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=menus',
            'name' => $this->_('MENUS')
        ];
        if ($in['Parent']->id) {
            foreach ((array)$in['Parent']->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&action=menus&id=' . (int)$row->id,
                    'name' => $row->name
                ];
            }
            $this->path[] = [
                'href' => $this->url . '&action=menus&id=' . (int)$in['Parent']->id,
                'name' => $in['Parent']->name
            ];
        }
        $this->stdView->stdEdit($in, 'getMenuContextMenu');
        $this->subtitle = $this->getMenuSubtitle($in['Item']);
    }


    /**
     * Перемещение меню
     * @param [
     *            'Item' =>? Menu Текущий пункт меню,
     *            'items' =>? array<Menu> Текущие пункты меню
     *        ] $in Входные данные
     */
    public function move_menu(array $in = [])
    {
        $ids = array_map(function ($x) {
            return (int)$x->id;
        }, $in['items']);
        $ids = array_unique($ids);
        $ids = array_values($ids);
        $pids = array_map(function ($x) {
            return (int)$x->pid;
        }, $in['items']);
        $pids = array_unique($pids);
        $pids = array_values($pids);
        $actives = [];
        foreach ($in['items'] as $row) {
            $actives = array_merge($actives, $row->selfAndParentsIds);
        }
        $actives = array_unique($actives);
        $actives = array_values($actives);
        $in['ids'] = $ids;
        $in['pids'] = $pids;
        $in['actives'] = $actives;

        $this->assignVars($in);
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=menus',
            'name' => $this->_('MENUS')
        ];
        if ($in['Item']->parents) {
            foreach ($in['Item']->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&action=menus&id=' .  (int)$row->id,
                    'name' => $row->name
                ];
            }
        }
        $this->path[] = [
            'href' => $this->url . '&action=menus&id=' .  (int)$in['Item']->id,
            'name' => $in['Item']->name
        ];
        if (count($in['items']) == 1) {
            $this->contextmenu = $this->getMenuContextMenu($in['Item']);
        }
        $this->title = $this->_('MOVING_NOTE');
        $this->template = 'dev_move_menu';
        $this->subtitle = $this->getMenuSubtitle($in['Item']);
    }


    /**
     * Отображает страницу редиректов
     * @param [
     *            'Form' => RedirectsForm Форма редиректов,
     *        ] $in Входные данные
     */
    public function redirects(array $in = [])
    {
        $this->assignVars($in);
        $form = $in['Form'];
        $this->title = $form->caption;
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->template = $form->template;
        $this->js[] = $this->publicURL . '/redirects.js';
    }


    /**
     * Корень раздела "Разработка"
     * @param [] $in Входные данные
     */
    public function dev(array $in = [])
    {
        $this->title = $this->_('DEVELOPMENT');
        $this->template = 'dev';
    }


    /**
     * Список шаблонов
     * @param [
     *            'Set' => array<Template> Список шаблонов
     *        ] $in Входные данные
     */
    public function templates(array $in = [])
    {
        $in['Table'] = new TemplatesTable($in);
        $this->assignVars($in);
        $this->title = $this->_('TEMPLATES');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->contextmenu = [
            [
                'href' => $this->url . '&action=edit_template',
                'name' => $this->_('ADD_TEMPLATE'),
                'icon' => 'plus'
            ]
        ];
        $this->template = $in['Table']->template;
    }


    /**
     * Редактирование шаблона
     * @param [
     *            'Item' => Template Шаблон для редактирования
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditTemplateForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_template(array $in = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_template.js';
        $this->css[] = $this->publicURL . '/dev_edit_template.css';
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('TEMPLATES'),
            'href' => $this->url . '&action=templates'
        ];
        $this->stdView->stdEdit($in, 'getTemplateContextMenu');
        $this->subtitle = $this->getTemplateSubtitle($in['Item']);
    }


    /**
     * Список сниппетов
     * @param array $in Входные данные
     */
    public function snippets(array $in = [])
    {
        $view = $this;
        $in['Table'] = new SnippetsTable();
        $in['Set'] = (array)$in['Table']->Set;
        $this->assignVars($in);
        $this->title = $this->_('SNIPPETS');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->contextmenu = [
            [
                'name' => $this->_('CREATE_SNIPPET'),
                'href' => $this->url . '&action=edit_snippet',
                'icon' => 'plus'
            ],
            [
                'name' => $this->_('CREATE_SNIPPET_FOLDER'),
                'href' => $this->url . '&action=edit_snippet_folder',
                'icon' => 'plus'
            ],
        ];
        $this->template = $in['Table']->template;
    }


    /**
     * Редактирование папки сниппетов
     * @param [
     *            'Item' => Snippet_Folder Папка для редактирования,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditSnippetFolderForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_snippet_folder(array $in = [])
    {
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('SNIPPETS'),
            'href' => $this->url . '&action=snippets'
        ];
        $this->stdView->stdEdit($in, 'getSnippetFolderContextMenu');
        $this->subtitle = $this->getSnippetFolderSubtitle($in['Item']);
    }


    /**
     * Редактирование сниппета
     * @param [
     *            'Item' => Snippet Сниппет для редактирования,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditSnippetForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_snippet(array $in = [])
    {
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('SNIPPETS'),
            'href' => $this->url . '&action=snippets'
        ];
        $this->stdView->stdEdit($in, 'getSnippetContextMenu');
        $this->subtitle = $this->getSnippetSubtitle($in['Item']);
    }


    /**
     * Список типов материалов
     * @param [] $in Входные данные
     */
    public function material_types(array $in = [])
    {
        $view = $this;
        $in['Table'] = new MaterialTypesTable($in);
        $this->assignVars($in);
        $this->title = $this->_('MATERIAL_TYPES');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->contextmenu = [
            [
                'name' => $this->_('CREATE_MATERIAL_TYPE'),
                'href' => $this->url . '&action=edit_material_type',
                'icon' => 'plus'
            ]
        ];
        $this->template = $in['Table']->template;
    }


    /**
     * Редактирование типа материалов
     * @param [
     *            'Item' => Material_Type Текущий тип материалов,
     *            'Parent' =>? Material_Type Родительский тип материалов,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditMaterialTypeForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_material_type(array $in = [])
    {
        $view = $this;
        $Set = [];
        $fieldGroups = $in['Item']->fieldGroups;
        $systemFields = [
            new Material_Field([
                'name' => $this->_('NAME'),
                'urn' => 'name',
                'datatype' => 'text'
            ]),
            new Material_Field([
                'name' => $this->_('DESCRIPTION'),
                'urn' => 'description',
                'datatype' => 'htmlarea'
            ])
        ];
        $grouped = (count($fieldGroups) > 1);
        if ($grouped) {
            foreach ($fieldGroups as $fieldGroup) {
                $groupFields = $fieldGroup->getFields($in['Item']);
                if (!$fieldGroup->id) {
                    $fieldGroup->name = $this->_('GENERAL');
                }
                $Set[] = $fieldGroup;
                if (!$fieldGroup->id) {
                    $Set = array_merge($Set, $systemFields);
                }
                foreach ($groupFields as $row) {
                    $Set[] = $row;
                }
            }
        } else {
            $Set = array_merge($Set, $systemFields);
            foreach ($in['Item']->fields as $row) {
                $Set[] = $row;
            }
        }
        $in['Table'] = new MaterialFieldsTable(array_merge($in, ['Set' => $Set, 'grouped' => $grouped]));
        $in['childrenTable'] = new MaterialTypesTable(['Item' => $in['Item']]);
        $this->assignVars($in);
        $this->title = $in['Form']->caption;
        $this->template = 'edit_material_type';
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('MATERIAL_TYPES'),
            'href' => $this->url . '&action=material_types'
        ];
        if ($in['Parent']->id) {
            foreach ((array)$in['Parent']->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&action=edit_material_type&id=' . (int)$row->id,
                    'name' => $row->name
                ];
            }
            $this->path[] = [
                'href' => $this->url . '&action=edit_material_type&id=' . (int)$in['Parent']->id,
                'name' => $in['Parent']->name
            ];
        }
        $this->contextmenu = $this->getMaterialTypeContextMenu($in['Item']);
        $this->subtitle = $this->getMaterialTypeSubtitle($in['Item']);
    }


    /**
     * Редактирование поля материалов
     * @param [
     *            'Item' => Material_Field Поле для редактирования,
     *            'Parent' =>? Material_Type Родительский тип материала
     *            'meta' => [
     *                'Parent' =>? Material_Type Родительский тип материала,
     *                'parentUrl' => string URL родительской страницы
     *            ],
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditFieldForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_material_field(array $in = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_field.js';
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('MATERIAL_TYPES'),
            'href' => $this->url . '&action=material_types'
        ];
        foreach ((array)$in['Parent']->parents as $row) {
            $this->path[] = [
                'href' => $this->url . '&action=edit_material_type&id=' . (int)$row->id,
                'name' => $row->name
            ];
        }
        $this->path[] = [
            'name' => $in['Parent']->name,
            'href' => $this->url . '&action=edit_material_type&id=' . (int)$in['Parent']->id
        ];
        $this->stdView->stdEdit($in, 'getMaterialFieldContextMenu');
        $this->subtitle = $this->getFieldSubtitle($in['Item']);
    }


    /**
     * Редактирование группы полей материалов
     * @param [
     *            'Item' => Material_Field Поле для редактирования,
     *            'Parent' =>? Material_Type Родительский тип материала
     *            'meta' => [
     *                'Parent' =>? Material_Type Родительский тип материала,
     *                'parentUrl' => string URL родительской страницы
     *            ],
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditFieldForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function editMaterialFieldGroup(array $in = [])
    {
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('MATERIAL_TYPES'),
            'href' => $this->url . '&action=material_types'
        ];
        foreach ((array)$in['Parent']->parents as $row) {
            $this->path[] = [
                'href' => $this->url . '&action=edit_material_type&id=' . (int)$row->id,
                'name' => $row->name
            ];
        }
        $this->path[] = [
            'name' => $in['Parent']->name,
            'href' => $this->url . '&action=edit_material_type&id=' . (int)$in['Parent']->id
        ];
        $this->stdView->stdEdit($in, 'getMaterialFieldGroupContextMenu');
        $this->subtitle = $this->getFieldGroupSubtitle($in['Item']);
    }


    /**
     * Перемещение поля материалов
     * @param [
     *            'Item' =>? Material_Field Текущее поле,
     *            'items' =>? array<Material_Field> Список текущих полей
     *        ] $in Входные данные
     */
    public function move_material_field(array $in = [])
    {
        $ids = array_map(function ($x) {
            return (int)$x->id;
        }, $in['items']);
        $ids = array_unique($ids);
        $ids = array_values($ids);
        $pids = array_map(function ($x) {
            return (int)$x->pid;
        }, $in['items']);
        $pids = array_unique($pids);
        $pids = array_values($pids);
        $actives = [];
        foreach ($in['items'] as $row) {
            $actives[] = (int)$row->pid;
        }
        $actives = array_unique($actives);
        $actives = array_values($actives);
        $in['ids'] = $ids;
        $in['pids'] = $pids;
        $in['actives'] = $actives;

        $this->assignVars($in);
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=material_types',
            'name' => $this->_('MATERIAL_TYPES')
        ];
        if ($in['Item']->parent->id) {
            foreach ((array)$in['Item']->parent->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&action=edit_material_type&id=' . (int)$row->id,
                    'name' => $row->name
                ];
            }
            $this->path[] = [
                'href' => $this->url . '&action=edit_material_type&id=' . (int)$in['Item']->parent->id,
                'name' => $in['Item']->parent->name
            ];
        }
        $this->path[] = [
            'href' => $this->url . '&action=edit_material_field&id=' . (int)$in['Item']->id,
            'name' => $in['Item']->name
        ];
        if (count($in['items']) == 1) {
            $this->contextmenu = $this->getMaterialFieldContextMenu($in['Item']);
            $this->subtitle = $this->getFieldSubtitle($in['Item']);
        }
        $this->title = $this->_('MOVING_NOTE');
        $this->template = 'dev_move_material_field';
    }


    /**
     * Перемещение поля материалов в группу
     * @param [
     *            'Item' =>? Material_Field Текущее поле,
     *            'items' =>? array<Material_Field> Список текущих полей
     *        ] $in Входные данные
     */
    public function moveMaterialFieldToGroup(array $in = [])
    {
        $ids = array_map(function ($x) {
            return (int)$x->id;
        }, $in['items']);
        $ids = array_unique($ids);
        $ids = array_values($ids);
        $pids = array_map(function ($x) {
            return (int)$x->pid;
        }, $in['items']);
        $pids = array_unique($pids);
        $pids = array_values($pids);
        $actives = [];
        foreach ($in['items'] as $row) {
            $actives[] = (int)$row->pid;
        }
        $actives = array_unique($actives);
        $actives = array_values($actives);
        $in['ids'] = $ids;
        $in['pids'] = $pids;
        $in['actives'] = $actives;

        $this->assignVars($in);
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=material_types',
            'name' => $this->_('MATERIAL_TYPES')
        ];
        if ($in['Item']->parent->id) {
            foreach ((array)$in['Item']->parent->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&action=edit_material_type&id=' . (int)$row->id,
                    'name' => $row->name
                ];
            }
            $this->path[] = [
                'href' => $this->url . '&action=edit_material_type&id=' . (int)$in['Item']->parent->id,
                'name' => $in['Item']->parent->name
            ];
        }
        if (count($in['items']) == 1) {
            $this->contextmenu = $this->getMaterialFieldContextMenu($in['Item']);
            $this->subtitle = $this->getFieldSubtitle($in['Item']);
        }
        $this->title = $this->_('MOVING_FIELDS_TO_GROUP');
        $this->template = 'dev_move_material_field_to_group';
    }

    /**
     * Перемещение значений поля материалов
     * @param [
     *            'Item' =>? Material_Field Текущее поле,
     *            'Set' =>? array<Material_Field> Список полей-акцепторов
     *        ] $in Входные данные
     */
    public function moveMaterialFieldValues(array $in = [])
    {
        $this->assignVars($in);
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=material_types',
            'name' => $this->_('MATERIAL_TYPES')
        ];
        if ($in['Item']->parent->id) {
            foreach ((array)$in['Item']->parent->parents as $row) {
                $this->path[] = [
                    'href' => $this->url . '&action=edit_material_type&id=' . (int)$row->id,
                    'name' => $row->name
                ];
            }
            $this->path[] = [
                'href' => $this->url . '&action=edit_material_type&id=' . (int)$in['Item']->parent->id,
                'name' => $in['Item']->parent->name
            ];
        }
        $this->contextmenu = $this->getMaterialFieldContextMenu($in['Item']);
        $this->subtitle = $this->getFieldSubtitle($in['Item']);
        $this->title = $this->_('MOVING_FIELD_VALUES');
        $this->template = 'dev_move_material_field_values';
    }


    /**
     * Перемещение типа материалов
     * @param [
     *            'Item' =>? Material_Type Текущее поле,
     *            'items' =>? array<Material_Type> Список текущих полей
     *        ] $in Входные данные
     */
    public function move_material_type(array $in = [])
    {
        $ids = array_map(function ($x) {
            return (int)$x->id;
        }, $in['items']);
        $ids = array_unique($ids);
        $ids = array_values($ids);
        $in['ids'] = $ids;

        $this->assignVars($in);
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'href' => $this->url . '&action=material_types',
            'name' => $this->_('MATERIAL_TYPES')
        ];
        if ($in['Item']->parent->id) {
            $this->path[] = [
                'href' => $this->url . '&action=edit_material_type&id=' . (int)$in['Item']->parent->id,
                'name' => $in['Item']->parent->name
            ];
        }
        $this->path[] = [
            'href' => $this->url . '&action=edit_material_field&id=' . (int)$in['Item']->id,
            'name' => $in['Item']->name
        ];
        if (count($in['items']) == 1) {
            $this->contextmenu = $this->getMaterialTypeContextMenu($in['Item']);
        }
        $this->title = $this->_('MOVING_NOTE');
        $this->template = 'dev_move_material_type';
        $this->subtitle = $this->getMaterialTypeSubtitle($in['Item']);
    }


    /**
     * Список форм
     * @param [
     *            'Set' => array<Form> Список форм
     *        ] $in Входные данные
     */
    public function forms(array $in = [])
    {
        $in['Table'] = new FormsTable($in);
        $this->assignVars($in);
        $this->title = $this->_('FORMS');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->contextmenu = [
            [
                'href' => $this->url . '&action=edit_form',
                'name' => $this->_('CREATE_FORM'),
                'icon' => 'plus'
            ]
        ];
        $this->template = $in['Table']->template;
    }


    /**
     * Редактирование формы
     * @param [
     *            'Item' => Form Форма для редактирования,
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditFormForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_form(array $in = [])
    {
        $view = $this;
        $Set = [];
        foreach ($in['Item']->fields as $row) {
            $Set[] = $row;
        }
        $in['Table'] = new FieldsTable(array_merge($in, [
            'editAction' => 'edit_form_field',
            'ctxMenu' => 'getFormFieldContextMenu',
            'Set' => $Set
        ]));
        $this->assignVars($in);
        $this->title = $in['Form']->caption;
        $this->template = 'dev_edit_form';
        $this->js[] = $this->publicURL . '/dev_edit_form.js';
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('FORMS'),
            'href' => $this->url . '&action=forms'
        ];
        $this->contextmenu = $this->getFormContextMenu($in['Item']);
        $this->subtitle = $this->getFormSubtitle($in['Item']);
    }


    /**
     * Редактирование поля формы
     * @param [
     *            'Item' => Form_Field Поле для редактирования,
     *            'Parent' =>? Form Родительская форма,
     *            'meta' => [
     *                'Parent' =>? Form Родительская форма,
     *                'parentUrl' => string URL родительской страницы
     *            ],
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditFieldForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_form_field(array $in = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_field.js';
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('FORMS'),
            'href' => $this->url . '&action=forms'
        ];
        $this->path[] = [
            'name' => $in['Parent']->name,
            'href' => $this->url . '&action=edit_form&id=' . (int)$in['Parent']->id
        ];
        $this->stdView->stdEdit($in, 'getFormFieldContextMenu');
        $this->subtitle = $this->getFieldSubtitle($in['Item']);
    }


    /**
     * Список полей страниц
     * @param [
     *            'Set' => array<Page_Field> Список полей
     *        ] $in Входные данные
     */
    public function pages_fields(array $in = [])
    {
        $in['Table'] = new FieldsTable(array_merge($in, [
            'editAction' => 'edit_page_field',
            'ctxMenu' => 'getPageFieldContextMenu'
        ]));
        $this->assignVars($in);
        $this->title = $this->_('PAGES_FIELDS');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->contextmenu = [
            [
                'name' => $this->_('CREATE_FIELD'),
                'href' => $this->url . '&action=edit_page_field',
                'icon' => 'plus'
            ]
        ];
        $this->template = $in['Table']->template;
    }


    /**
     * Редактирование поля страниц
     * @param [
     *            'Item' => Page_Field Поле для редактирования,
     *            'meta' => [
     *                'parentUrl' => string URL родительской страницы
     *            ],
     *            'localError' =>? array<[
     *                'name' => string Тип ошибки,
     *                'value' => string URN поля, к которому относится ошибка,
     *                'description' => string Описание ошибки,
     *            ]> Ошибки,
     *            'Form' => EditFieldForm Форма редактирования,
     *        ] $in Входные данные
     */
    public function edit_page_field(array $in = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_field.js';
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $this->_('PAGES_FIELDS'),
            'href' => $this->url . '&action=pages_fields'
        ];
        $this->stdView->stdEdit($in, 'getPageFieldContextMenu');
        $this->subtitle = $this->getFieldSubtitle($in['Item']);
    }


    /**
     * Диагностика
     * @param [
     *            'Item' => Diag Объект диагностики,
     *            'from' =>? string Дата, от (формат ГГГГ-ММ-ДД),
     *            'to' => string Дата, до (формат ГГГГ-ММ-ДД),
     *        ] $in Входные данные
     */
    public function diag(array $in = [])
    {
        $in['Form'] = new DiagForm([
            'Item' => $in['Item'],
            'from' => $in['from'],
            'to' => $in['to']
        ]);
        $this->assignVars($in);
        $this->title = $in['Form']->caption;
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->contextmenu = [
            [
                'name' => $this->_('CLEAR_DIAGNOSTICS_PERIOD'),
                'href' => $this->url . '&action=delete_diag&from=' . $in['from'] . '&to=' . $in['to'],
                'icon' => 'remove',
                'onclick' => 'return confirm("' . addslashes($this->_('CLEAR_DIAGNOSTICS_CONFIRM')) . '")'
            ],
            [
                'name' => $this->_('CLEAR_DIAGNOSTICS_ALL'),
                'href' => $this->url . '&action=delete_diag',
                'icon' => 'remove',
                'onclick' => 'return confirm("' . addslashes($this->_('CLEAR_DIAGNOSTICS_CONFIRM')) . '")'
            ],
        ];
        $this->template = $in['Form']->template;
    }


    /**
     * Управление кэшированием
     * @param [] $in Входные данные
     */
    public function cache(array $in = [])
    {
        $this->js[] = $this->publicURL . '/dev_cache.js';
        $this->assignVars($in);
        $this->title = $this->_('CACHE_CONTROL');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        $this->template = 'cache';
    }


    /**
     * Возвращает левое меню подмодуля "Разработка"
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'active' ?=> bool Пункт меню активен,
     *             'class' ?=> string Класс пункта меню,
     *             'submenu' => *рекурсивно*,
     *         ]>
     */
    public function devMenu()
    {
        $submenu = [];
        $submenu[] = [
            'href' => $this->url . '&action=templates',
            'name' => $this->_('TEMPLATES'),
            'active' => (
                in_array($this->action, ['templates', 'edit_template']) &&
                !$this->moduleName
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=dictionaries',
            'name' => $this->_('DICTIONARIES'),
            'active' => (
                in_array($this->action, ['dictionaries', 'edit_dictionary', 'move_dictionary']) &&
                !$this->moduleName
            ),
            'submenu' => (
                in_array($this->action, ['dictionaries', 'edit_dictionary', 'move_dictionary']) ?
                $this->dictionariesMenu(new Dictionary(
                    $this->id ?: (isset($this->nav['pid']) ? $this->nav['pid'] : 0)
                )) :
                null
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=snippets',
            'name' => $this->_('SNIPPETS'),
            'active' => (
                in_array($this->action, ['snippets', 'edit_snippet', 'edit_snippet_folder', 'copy_snippet']) &&
                !$this->moduleName
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=material_types',
            'name' => $this->_('MATERIAL_TYPES'),
            'active' => (
                in_array($this->action, ['material_types', 'edit_material_type', 'edit_material_field']) &&
                !$this->moduleName
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=pages_fields',
            'name' => $this->_('PAGES_FIELDS'),
            'active' => (in_array($this->action, ['pages_fields', 'edit_page_field']) && !$this->moduleName)
        ];
        $submenu[] = [
            'href' => $this->url . '&action=forms',
            'name' => $this->_('FORMS'),
            'active' => (in_array($this->action, ['forms', 'edit_form', 'edit_form_field']) && !$this->moduleName)
        ];
        $submenu[] = [
            'href' => $this->url . '&action=menus',
            'name' => $this->_('MENUS'),
            'active' => (in_array($this->action, ['menus', 'edit_menu', 'move_menu']) && !$this->moduleName),
            'submenu' => (
                in_array($this->action, ['menus', 'edit_menu', 'move_menu']) ?
                $this->menusMenu(new Menu($this->id ?: (isset($this->nav['pid']) ? $this->nav['pid'] : 0))) :
                null
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=redirects',
            'name' => $this->_('REDIRECTS'),
        ];
        if (Package::i()->registryGet('diag')) {
            $submenu[] = [
                'href' => $this->url . '&action=diag',
                'name' => $this->_('DIAGNOSTICS')
            ];
        }
        $submenu[] = [
            'href' => $this->url . '&action=cache',
            'name' => $this->_('CACHE_CONTROL')
        ];
        foreach ($this->model->modules as $module) {
            $NS = \SOME\Namespaces::getNS($module);
            $sub_classname = $NS . '\\Sub_Dev';
            $view_classname = $NS . '\\ViewSub_Dev';
            if (method_exists($view_classname, 'devMenu')) {
                $row = $sub_classname::i();
                $temp = (array)$row->view->devMenu();
                $submenu = array_merge($submenu, $temp);
            }
        }
        return $submenu;
    }


    /**
     * Возвращает меню для списка меню
     * @param Menu $current Текущий выбранный пункт меню
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'active' ?=> bool Пункт меню активен,
     *             'class' ?=> string Класс пункта меню,
     *             'submenu' => *рекурсивно*,
     *         ]>
     */
    public function menusMenu(Menu $current)
    {
        $pageCache = PageRecursiveCache::i();
        $domainsIds = $pageCache->getChildrenIds(0);
        if (count($domainsIds) > 1) {
            $menu = [];
            array_unshift($domainsIds, 0);
            foreach ($domainsIds as $domainId) {
                if ($domainId) {
                    $domainData = $pageCache->cache[$domainId];
                } else {
                    $domainData = ['name' => $this->_('WITHOUT_DOMAIN')];
                }
                $subMenu = $this->menusMenuByDomainId($current, $domainId);
                if ($subMenu) {
                    $active =  in_array($this->action, ['menus', 'edit_menu', 'move_menu']) &&
                        ((string)$_GET['domain_id'] === (string)$domainId);
                    $semiactive = (bool)array_filter($subMenu, function ($x) {
                        return (bool)$x['active'];
                    });
                    $menu[] = [
                        'name' => Text::cuttext($domainData['name'], 64, '...'),
                        'href' => $this->url . '&sub=dev&action=menus&domain_id=' . (int)$domainId,
                        'active' => $active || $semiactive,
                        'submenu' => $subMenu,
                    ];
                }
            }
        } else {
            $menu = $this->menusMenuByDomainId($current);
        }
        return $menu;
    }


    /**
     * Возвращает меню для списка справочников
     * @param Dictionary $current Текущий выбранный справочник
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'active' ?=> bool Пункт справочника активен,
     *             'class' ?=> string Класс пункта справочника,
     *             'submenu' => *рекурсивно*,
     *         ]>
     */
    public function dictionariesMenu(Dictionary $current)
    {
        $menu = [];
        $node = new Dictionary();
        foreach ($node->children as $row) {
            $temp = [
                'name' => Text::cuttext($row->name, 64, '...'),
                'href' => $this->url . '&sub=dev&action=dictionaries&id=' . (int)$row->id,
                'class' => '',
                'active' => false
            ];

            if (($row->id == $current->id) || in_array($current->id, $row->all_children_ids)) {
                $temp['active'] = true;
            }

            if (!$row->vis) {
                $temp['class'] .= ' muted';
            }

            $menu[] = $temp;
        }
        return $menu;
    }


    /**
     * Возвращает меню для списка меню домена
     * @param Menu $current Текущий выбранный пункт меню
     * @param int|null $domainId ID# домена, либо null для всех
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'active' ?=> bool Пункт меню активен,
     *             'class' ?=> string Класс пункта меню,
     *             'submenu' => *рекурсивно*,
     *         ]>
     */
    public function menusMenuByDomainId(Menu $current, $domainId = null)
    {
        $menu = [];
        $cache = MenuRecursiveCache::i();
        $menusIds = $cache->getChildrenIds(0);
        foreach ($menusIds as $menuId) {
            $row = $cache->cache[$menuId];
            if (($domainId !== null) && ($row['domain_id'] != $domainId)) {
                continue;
            }
            $temp = [
                'name' => Text::cuttext($row['name'], 64, '...'),
                'href' => $this->url . '&sub=dev&action=menus&id=' . (int)$row['id'],
                'class' => '',
                'active' => (
                    ($row['id'] == $current->id) ||
                    in_array($row['id'], MenuRecursiveCache::i()->getParentsIds($current->id))
                )
            ];

            if (!$row['vis']) {
                $temp['class'] .= ' muted';
            }
            if (!$row['pvis']) {
                $temp['class'] .= ' cms-inpvis';
            }
            $menu[] = $temp;
        }
        return $menu;
    }


    /**
     * Возвращает контекстое меню для шаблона
     * @param Template $template Шаблон для получения контекстого меню
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getTemplateContextMenu(Template $template)
    {
        return $this->stdView->stdContextMenu($template, 0, 0, 'edit_template', 'templates', 'delete_template');
    }


    /**
     * Возвращает контекстное меню для списка шаблонов
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllTemplatesContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_template&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\''
                      .  $this->_('DELETE_MULTIPLE_TEXT')
                      .  '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для справочника
     * @param Dictionary $dictionary Справочник для получения меню
     * @param int $i Порядок справочника в списке
     * @param int $c Количество справочников в списке
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getDictionaryContextMenu(Dictionary $dictionary, $i = 0, $c = 0)
    {
        $arr = [];
        $edit = false;
        if ($dictionary->id) {
            $edit = ($this->action == 'edit_dictionary');
            $showlist = ($this->action == 'dictionaries');
            if ($this->id == $dictionary->id) {
                $arr[] = [
                    'href' => $this->url . '&action=edit_dictionary&pid=' . (int)$dictionary->id,
                    'name' => $this->_('CREATE_SUBNOTE'),
                    'icon' => 'plus'
                ];
            }
            if ($edit) {
                $arr[] = [
                    'href' => $this->url . '&action=dictionaries&id=' . (int)$dictionary->id,
                    'name' => htmlspecialchars($dictionary->name),
                    'icon' => 'th-list'
                ];
            }
            $arr[] = [
                'name' => $dictionary->vis
                       ?  $this->_('VISIBLE')
                       :  '<span class="muted">' . $this->_('INVISIBLE') . '</span>',
                'href' => $this->url . '&action=chvis_dictionary&id=' . (int)$dictionary->id . '&back=1',
                'icon' => $dictionary->vis ? 'ok' : '',
                'title' => $this->_($dictionary->vis ? 'HIDE' : 'SHOW')
            ];
            if ($this->action != 'move_dictionary') {
                $arr[] = [
                    'href' => $this->url . '&action=move_dictionary&id=' . (int)$dictionary->id,
                    'name' => $this->_('MOVE'),
                    'icon' => 'share-alt'
                ];
            }
            $arr = array_merge($arr, $this->stdView->stdContextMenu(
                $dictionary,
                0,
                0,
                'edit_dictionary',
                'dictionaries',
                'delete_dictionary'
            ));
        } elseif (!$edit) {
            $arr[] = [
                'href' => $this->url . '&action=edit_dictionary',
                'name' => $this->_('CREATE_NOTE'),
                'icon' => 'plus'
            ];
        }
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка справочников
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllDictionariesContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis_dictionary&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        ];
        $arr[] = [
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis_dictionary&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        ];
        $arr[] = [
            'name' => $this->_('MOVE'),
            'href' => $this->url . '&action=move_dictionary',
            'icon' => 'share-alt'
        ];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_dictionary&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для папки сниппетов
     * @param Snippet_Folder $snippetFolder Папка сниппетов для получения меню
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getSnippetFolderContextMenu(Snippet_Folder $snippetFolder)
    {
        $arr = [];
        if (!$snippetFolder->locked) {
            $arr = $this->stdView->stdContextMenu(
                $snippetFolder,
                0,
                0,
                'edit_snippet_folder',
                'snippets',
                'delete_snippet_folder'
            );
        }
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка папок сниппетов
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllSnippetFoldersContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_snippet_folder&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для сниппета
     * @param Snippet $snippet Сниппет для получения контекстного меню
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getSnippetContextMenu(Snippet $snippet)
    {
        if (!$snippet->locked) {
            $arr = $this->stdView->stdContextMenu($snippet, 0, 0, 'edit_snippet', 'snippets', 'delete_snippet');
        }
        if ($snippet->id) {
            $arr[] = [
                'href' => $this->url . '&action=copy_snippet&id=' .  (int)$snippet->id,
                'name' => $this->_('COPY'),
                'icon' => 'tags'
            ];
        }
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка сниппетов
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllSnippetsContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_snippet&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для типа материалов
     * @param Material_Type $materialType Тип материалов для получения меню
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getMaterialTypeContextMenu(Material_Type $materialType)
    {
        $arr = [];
        if ($materialType->id) {
            if ($this->action == 'edit_material_type') {
                $arr[] = [
                    'href' => $this->url . '&action=edit_material_field&pid=' . (int)$materialType->id,
                    'name' => $this->_('CREATE_FIELD'),
                    'icon' => 'plus'
                ];
                $arr[] = [
                    'href' => $this->url . '&action=edit_material_fieldgroup&pid=' . (int)$materialType->id,
                    'name' => $this->_('CREATE_FIELDGROUP'),
                    'icon' => 'plus'
                ];
            }
            if (Package::i()->registryGet('allowChangeMaterialType')) {
                $arr[] = [
                    'href' => $this->url . '&action=move_material_type&id=' . (int)$materialType->id,
                    'name' => $this->_('MOVE'),
                    'icon' => 'share-alt'
                ];
            }
            $arr[] = [
                'href' => $this->url . '&action=edit_material_type&pid=' . (int)$materialType->id,
                'name' => $this->_('CREATE_CHILD_TYPE'),
                'icon' => 'plus'
            ];
        }
        $arr = array_merge($arr, $this->stdView->stdContextMenu(
            $materialType,
            0,
            0,
            'edit_material_type',
            'material_types',
            'delete_material_type'
        ));
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка типов материалов
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllMaterialTypesContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_material_type&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для поля материалов
     * @param Material_Field $field Поле для получения контекстного меню
     * @param int $i Порядок поля в списке
     * @param int $c Количество полей в списке
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getMaterialFieldContextMenu(
        Material_Field $field,
        $i = 0,
        $c = 0
    ) {
        $arr = [];
        if ($field->id) {
            $arr[] = [
                'name' => $field->vis
                       ?  $this->_('VISIBLE')
                       :  '<span class="muted">' . $this->_('INVISIBLE') . '</span>',
                'href' => $this->url . '&action=chvis_material_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->vis ? 'ok' : '',
                'title' => $this->_($field->vis ? 'HIDE' : 'SHOW')
            ];
            $arr[] = [
                'name' => $this->_('SHOW_IN_TABLE'),
                'href' => $this->url .  '&action=show_in_table_material_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->show_in_table ? 'ok' : '',
            ];
            $arr[] = [
                'name' => $this->_('REQUIRED'),
                'href' => $this->url . '&action=required_material_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->required ? 'ok' : '',
            ];
            $arr[] = [
                'name' => $this->_('MOVE'),
                'href' => $this->url . '&action=move_material_field&id=' . (int)$field->id,
                'icon' => 'share-alt'
            ];
            $arr[] = [
                'name' => $this->_('MOVE_VALUES'),
                'href' => $this->url . '&action=move_material_field_values&id=' . (int)$field->id,
                'icon' => 'code-merge'
            ];
        }
        $arr = array_merge($arr, $this->stdView->stdContextMenu(
            $field,
            $i,
            $c,
            'edit_material_field',
            'edit_material_type',
            'delete_material_field'
        ));
        return $arr;
    }


    /**
     * Возвращает контекстное меню для группы полей материалов
     * @param MaterialFieldGroup $fieldGroup Группа полей для получения контекстного меню
     * @param int $i Порядок поля в списке
     * @param int $c Количество полей в списке
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getMaterialFieldGroupContextMenu(MaterialFieldGroup $fieldGroup, $i = 0, $c = 0)
    {
        $arr = [];
        $arr = array_merge($arr, $this->stdView->stdContextMenu(
            $fieldGroup,
            $i,
            $c,
            'edit_material_fieldgroup',
            'edit_material_type',
            'delete_material_fieldgroup'
        ));
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка полей материалов
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllMaterialFieldsContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis_material_field&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        ];
        $arr[] = [
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis_material_field&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        ];
        $arr[] = [
            'name' => $this->_('SHOW_IN_TABLE'),
            'href' => $this->url . '&action=show_in_table_material_field&back=1',
            'icon' => 'align-justify',
        ];
        $arr[] = [
            'name' => $this->_('REQUIRED'),
            'href' => $this->url . '&action=required_material_field&back=1',
            'icon' => 'asterisk',
        ];
        $arr[] = [
            'name' => $this->_('MOVE'),
            'href' => $this->url . '&action=move_material_field',
            'icon' => 'share-alt'
        ];
        $arr[] = [
            'name' => $this->_('MOVE_TO_FIELDGROUP'),
            'href' => $this->url . '&action=move_material_field_to_group&pid=' . ($_GET['id'] ?? ''),
            'icon' => 'share-alt'
        ];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_material_field&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для поля страниц
     * @param Page_Field $field Поле для получения контекстного меню
     * @param int $i Порядок поля в списке
     * @param int $c Количество полей в списке
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getPageFieldContextMenu(Page_Field $field, $i = 0, $c = 0)
    {
        $arr = [];
        if ($field->id) {
            $arr[] = [
                'name' => $field->vis
                       ?  $this->_('VISIBLE')
                       :  '<span class="muted">' . $this->_('INVISIBLE') . '</span>',
                'href' => $this->url . '&action=chvis_page_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->vis ? 'ok' : '',
                'title' => $this->_($field->vis ? 'HIDE' : 'SHOW')
            ];
            $arr[] = [
                'name' => $this->_('SHOW_IN_TABLE'),
                'href' => $this->url . '&action=show_in_table_page_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->show_in_table ? 'ok' : '',
            ];
            $arr[] = [
                'name' => $this->_('REQUIRED'),
                'href' => $this->url . '&action=required_page_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->required ? 'ok' : '',
            ];
        }
        $arr = array_merge($arr, $this->stdView->stdContextMenu(
            $field,
            $i,
            $c,
            'edit_page_field',
            'pages_fields',
            'delete_page_field'
        ));
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка полей страниц
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllPageFieldsContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis_page_field&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        ];
        $arr[] = [
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis_page_field&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        ];
        $arr[] = [
            'name' => $this->_('SHOW_IN_TABLE'),
            'href' => $this->url . '&action=show_in_table_page_field&back=1',
            'icon' => 'align-justify',
        ];
        $arr[] = [
            'name' => $this->_('REQUIRED'),
            'href' => $this->url . '&action=required_page_field&back=1',
            'icon' => 'asterisk',
        ];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_page_field&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для формы
     * @param Form $form Форма для получения контекстного меню
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getFormContextMenu(Form $form)
    {
        $arr = [];
        if ($form->id &&$this->action == 'edit_form') {
            $arr[] = [
                'href' => $this->url . '&action=edit_form_field&pid=' . (int)$form->id,
                'name' => $this->_('CREATE_FIELD'),
                'icon' => 'plus'
            ];
        }
        if ($form->id) {
            $arr[] = [
                'href' => $this->url . '&action=copy_form&id=' . (int)$form->id,
                'name' => $this->_('COPY'),
                'icon' => 'tags'
            ];
        }
        $arr = array_merge($arr, $this->stdView->stdContextMenu(
            $form,
            $i ?? 0,
            $c ?? 0,
            'edit_form',
            'forms',
            'delete_form'
        ));
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка форм
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllFormsContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_form&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для поля формы
     * @param Form_Field $field Поле для получения контекстного меню
     * @param int $i Порядок поля в списке
     * @param int $c Количество полей в списке
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getFormFieldContextMenu(Form_Field $field, $i = 0, $c = 0)
    {
        $arr = [];
        if ($field->id) {
            $arr[] = [
                'name' => $field->vis
                       ?  $this->_('VISIBLE')
                       :  '<span class="muted">' . $this->_('INVISIBLE') . '</span>',
                'href' => $this->url . '&action=chvis_form_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->vis ? 'ok' : '',
                'title' => $this->_($field->vis ? 'HIDE' : 'SHOW')
            ];
            $arr[] = [
                'name' => $this->_('SHOW_IN_TABLE'),
                'href' => $this->url . '&action=show_in_table_form_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->show_in_table ? 'ok' : '',
            ];
            $arr[] = [
                'name' => $this->_('REQUIRED'),
                'href' => $this->url . '&action=required_form_field&id=' . (int)$field->id . '&back=1',
                'icon' => $field->required ? 'ok' : '',
            ];
        }
        $arr = array_merge($arr, $this->stdView->stdContextMenu(
            $field,
            $i,
            $c,
            'edit_form_field',
            'pages_fields',
            'delete_form_field'
        ));
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка полей формы
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllFormFieldsContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis_form_field&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        ];
        $arr[] = [
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis_form_field&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        ];$arr[] = [
            'name' => $this->_('SHOW_IN_TABLE'),
            'href' => $this->url . '&action=show_in_table_form_field&back=1',
            'icon' => 'align-justify',
        ];
        $arr[] = [
            'name' => $this->_('REQUIRED'),
            'href' => $this->url . '&action=required_form_field&back=1',
            'icon' => 'asterisk',
        ];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_form_field&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        ];
        return $arr;
    }


    /**
     * Возвращает контекстное меню для меню
     * @param Menu $menu Меню для получения контекстного меню
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getMenuContextMenu(Menu $menu)
    {
        $arr = [];
        $edit = false;
        if ($menu->id) {
            $edit = ($this->action == 'edit_menu');
            $showlist = ($this->action == 'menus');
            if (!$showlist) {
                $arr[] = [
                    'href' => $this->url . '&action=menus&id=' . (int)$menu->id,
                    'name' => $this->_('VIEW'),
                    'icon' => 'search'
                ];
            }
            if ($this->id == $menu->id) {
                $arr[] = [
                    'href' => $this->url . '&action=edit_menu&pid=' . (int)$menu->id,
                    'name' => $this->_('CREATE_SUBNOTE'),
                    'icon' => 'plus'
                ];
            }
            if ($menu->vis) {
                $arr[] = [
                    'name' => $this->_('VISIBLE'),
                    'href' => $this->url . '&action=chvis_menu&id=' . (int)$menu->id . '&back=1',
                    'icon' => 'ok',
                    'title' => $this->_('HIDE')
                ];
            } else {
                $arr[] = [
                    'name' => '<span class="muted">' . $this->_('INVISIBLE') . '</span>',
                    'href' => $this->url . '&action=chvis_menu&id=' . (int)$menu->id . '&back=1',
                    'icon' => '',
                    'title' => $this->_('SHOW')
                ];
            }
            if ($this->action != 'move_menu') {
                $arr[] = [
                    'href' => $this->url . '&action=move_menu&id=' . (int)$menu->id,
                    'name' => $this->_('MOVE'),
                    'icon' => 'share-alt'
                ];
            }
            if (($this->id == $menu->id) && ($menu->inherit > 0)) {
                $arr[] = [
                    'href' => $this->url . '&action=realize_menu&id=' . (int)$menu->id
                           .  ($edit || $showlist ? '' : '&back=1'),
                    'name' => $this->_('REALIZE'),
                    'icon' => 'asterisk',
                    'onclick' => 'return confirm(\'' . $this->_('REALIZE_MENU_TEXT') . '\')'
                ];
            }
            $arr = array_merge($arr, $this->stdView->stdContextMenu(
                $menu,
                0,
                0,
                'edit_menu',
                'menus',
                'delete_menu'
            ));
        } elseif (!$edit) {
            $arr[] = [
                'href' => $this->url . '&action=edit_menu',
                'name' => $this->_('CREATE_NOTE'),
                'icon' => 'plus'
            ];
        }
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка меню
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllMenusContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis_menu&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        ];
        $arr[] = [
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis_menu&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        ];
        $arr[] = [
            'name' => $this->_('MOVE'),
            'href' => $this->url . '&action=move_menu',
            'icon' => 'share-alt'
        ];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_menu&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')',
        ];
        return $arr;
    }


    /**
     * Получает подзаголовок шаблона
     * @param Template $template Шаблон для получения
     * @return string HTML-код подзаголовка
     */
    public function getTemplateSubtitle(Template $template)
    {
        $subtitleArr = [];
        if ($template->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$template->id;
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок справочника
     * @param Dictionary $dictionary Справочник для получения
     * @return string HTML-код подзаголовка
     */
    public function getDictionarySubtitle(Dictionary $dictionary)
    {
        $subtitleArr = [];
        if ($dictionary->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$dictionary->id;
            $subtitleArr[] = $this->_('URN') . ': ' . htmlspecialchars($dictionary->urn);
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок папки сниппетов
     * @param Snippet_Folder $snippetFolder Папка для получения
     * @return string HTML-код подзаголовка
     */
    public function getSnippetFolderSubtitle(Snippet_Folder $snippetFolder)
    {
        $subtitleArr = [];
        if ($snippetFolder->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$snippetFolder->id;
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок сниппета
     * @param Snippet $snippet Сниппет для получения
     * @return string HTML-код подзаголовка
     */
    public function getSnippetSubtitle(Snippet $snippet)
    {
        $subtitleArr = [];
        if ($snippet->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$snippet->id;
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок типа материалов
     * @param Material_Type $materialType Тип материалов для получения
     * @return string HTML-код подзаголовка
     */
    public function getMaterialTypeSubtitle(Material_Type $materialType)
    {
        $subtitleArr = [];
        if ($materialType->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$materialType->id;
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок поля
     * @param Field $field Поле для получения
     * @return string HTML-код подзаголовка
     */
    public function getFieldSubtitle(Field $field)
    {
        $subtitleArr = [];
        if ($field->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$field->id;
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок группы полей
     * @param FieldПкщгз $fieldGroup Группа полей для получения
     * @return string HTML-код подзаголовка
     */
    public function getFieldGroupSubtitle(FieldGroup $fieldGroup)
    {
        $subtitleArr = [];
        if ($fieldGroup->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$fieldGroup->id;
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок формы
     * @param Form $form Форма для получения
     * @return string HTML-код подзаголовка
     */
    public function getFormSubtitle(Form $form)
    {
        $subtitleArr = [];
        if ($form->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$form->id;
            return implode('; ', $subtitleArr);
        }
        return '';
    }


    /**
     * Получает подзаголовок меню
     * @param Form $menu Меню для получения
     * @return string HTML-код подзаголовка
     */
    public function getMenuSubtitle(Menu $menu)
    {
        $subtitleArr = [];
        if ($menu->id) {
            $subtitleArr[] = $this->_('ID') . ': ' . (int)$menu->id;
            if ($menu->urn) {
                $subtitleArr[] = $this->_('URN') . ': ' . htmlspecialchars($menu->urn);
            }
            if ($menu->url) {
                $subtitleArr[] = $this->_('URL') . ': '
                               . '<a href="' . htmlspecialchars($menu->url) . '" target="_blank">'
                               .    htmlspecialchars($menu->url)
                               . '</a>';
            }
            return implode('; ', $subtitleArr);
        }
        return '';
    }
}
