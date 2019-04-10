<?php
/**
 * Представление для подмодуля "Разработка"
 */
namespace RAAS\CMS;

use SOME\Text;
use RAAS\Application;

/**
 * Класс представления для подмодуля "Разработка"
 */
class ViewSub_Dev extends \RAAS\Abstract_Sub_View
{
    protected static $instance;

    public function dictionaries(array $IN = [])
    {
        $IN['Table'] = new DictionariesTable($IN);
        $this->assignVars($IN);
        $this->title = $IN['Item']->id ? $IN['Item']->name : $this->_('DICTIONARIES');
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('href' => $this->url . '&action=dictionaries', 'name' => $this->_('DICTIONARIES'));
        if ($IN['Item']->parents) {
            foreach ($IN['Item']->parents as $row) {
                $this->path[] = array('href' => $this->url . '&action=dictionaries&id=' . (int)$row->id, 'name' => $row->name);
            }
        }
        $this->contextmenu = $this->getDictionaryContextMenu($IN['Item']);
        $this->template = $IN['Table']->template;
    }


    public function edit_dictionary(array $IN = [])
    {
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('href' => $this->url . '&action=dictionaries', 'name' => $this->_('DICTIONARIES'));
        if ($IN['Parent']->id) {
            if ($IN['Parent']->parents) {
                foreach ($IN['Parent']->parents as $row) {
                    $this->path[] = array('href' => $this->url . '&action=dictionaries&id=' . (int)$row->id, 'name' => $row->name);
                }
            }
            $this->path[] = array('href' => $this->url . '&action=dictionaries&id=' . (int)$IN['Parent']->id, 'name' => $IN['Parent']->name);
        }
        $this->stdView->stdEdit($IN, 'getDictionaryContextMenu');
    }


    public function move_dictionary(array $IN = [])
    {
        $ids = array_map(function ($x) {
            return (int)$x->id;
        }, $IN['items']);
        $ids = array_unique($ids);
        $ids = array_values($ids);
        $pids = array_map(function ($x) {
            return (int)$x->pid;
        }, $IN['items']);
        $pids = array_unique($pids);
        $pids = array_values($pids);
        $actives = [];
        foreach ($IN['items'] as $row) {
            $actives = array_merge($actives, array($row->id), (array)$row->parents_ids);
        }
        $actives = array_unique($actives);
        $actives = array_values($actives);
        $IN['ids'] = $ids;
        $IN['pids'] = $pids;
        $IN['actives'] = $actives;

        $this->assignVars($IN);
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('href' => $this->url . '&action=dictionaries', 'name' => $this->_('DICTIONARIES'));
        if ($IN['Item']->parents) {
            foreach ($IN['Item']->parents as $row) {
                $this->path[] = array('href' => $this->url . '&action=dictionaries' . '&id=' . (int)$row->id, 'name' => $row->name);
            }
        }
        $this->path[] = array('href' => $this->url . '&action=dictionaries' . '&id=' . (int)$IN['Item']->id, 'name' => $IN['Item']->name);
        if (count($IN['items']) == 1) {
            $this->contextmenu = $this->getDictionaryContextMenu($IN['Item']);
        }
        $this->title = $this->_('MOVING_NOTE');
        $this->template = 'dev_move_dictionary';
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
                'href' => $this->url . '&action=menus&domain_id='
                       .  (int)$current->domain_id,
                'name' => $domainName
            ];
        }
        foreach ($menuCache->getParentsIds($current->id) as $parentId) {
            $parentData = $menuCache->cache[$parentId];
            $this->path[] = [
                'href' => $this->url . '&action=menus'
                       .  '&id=' . (int)$parentData['id'],
                'name' => $parentData['name']
            ];
        }
    }


    /**
     * Отображает страницу списка меню
     * @param ['Item' => Menu текущее меню] $IN Входные данные
     */
    public function menus(array $IN = [])
    {
        $item = $IN['Item'];

        $IN['Table'] = new MenusTable($IN);
        $this->assignVars($IN);
        $this->title = $item->id ? $item->name : $this->_('MENUS');
        $this->path[] = [
            'name' => $this->_('DEVELOPMENT'),
            'href' => $this->url
        ];
        if ($item->id) {
            $this->getMenuBreadcrumbs($item);
        }
        $this->contextmenu = $this->getMenuContextMenu($IN['Item']);
        $this->template = 'dev_menus';
    }


    public function edit_menu(array $IN = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_menu.js';
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('href' => $this->url . '&action=menus', 'name' => $this->_('MENUS'));
        if ($IN['Parent']->id) {
            if ($IN['Parent']->parents) {
                foreach ($IN['Parent']->parents as $row) {
                    $this->path[] = array('href' => $this->url . '&action=menus' . '&id=' . (int)$row->id, 'name' => $row->name);
                }
            }
            $this->path[] = array('href' => $this->url . '&action=menus&id=' . (int)$IN['Parent']->id, 'name' => $IN['Parent']->name);
        }
        $this->stdView->stdEdit($IN, 'getMenuContextMenu');
    }


    public function move_menu(array $IN = [])
    {
        $ids = array_map(function ($x) {
            return (int)$x->id;
        }, $IN['items']);
        $ids = array_unique($ids);
        $ids = array_values($ids);
        $pids = array_map(function ($x) {
            return (int)$x->pid;
        }, $IN['items']);
        $pids = array_unique($pids);
        $pids = array_values($pids);
        $actives = [];
        foreach ($IN['items'] as $row) {
            $actives = array_merge($actives, array($row->id), (array)$row->parents_ids);
        }
        $actives = array_unique($actives);
        $actives = array_values($actives);
        $IN['ids'] = $ids;
        $IN['pids'] = $pids;
        $IN['actives'] = $actives;

        $this->assignVars($IN);
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('href' => $this->url . '&action=menus', 'name' => $this->_('MENUS'));
        if ($IN['Item']->parents) {
            foreach ($IN['Item']->parents as $row) {
                $this->path[] = array('href' => $this->url . '&action=menus' . '&id=' . (int)$row->id, 'name' => $row->name);
            }
        }
        $this->path[] = array('href' => $this->url . '&action=menus' . '&id=' . (int)$IN['Item']->id, 'name' => $IN['Item']->name);
        if (count($IN['items']) == 1) {
            $this->contextmenu = $this->getMenuContextMenu($IN['Item']);
        }
        $this->title = $this->_('MOVING_NOTE');
        $this->template = 'dev_move_menu';
    }


    public function dev(array $IN = [])
    {
        $this->title = $this->_('DEVELOPMENT');
        $this->template = 'dev';
    }


    public function templates(array $IN = [])
    {
        $IN['Table'] = new TemplatesTable($IN);
        $this->assignVars($IN);
        $this->title = $this->_('TEMPLATES');
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->contextmenu = array(array('href' => $this->url . '&action=edit_template', 'name' => $this->_('ADD_TEMPLATE'), 'icon' => 'plus'));
        $this->template = $IN['Table']->template;
    }


    public function edit_template(array $IN = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_template.js';
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('name' => $this->_('TEMPLATES'), 'href' => $this->url . '&action=templates');
        $this->stdView->stdEdit($IN, 'getTemplateContextMenu');
    }


    public function snippets(array $IN = [])
    {
        $view = $this;
        $IN['Table'] = new SnippetsTable();
        $IN['Set'] = (array)$IN['Table']->Set;
        $this->assignVars($IN);
        $this->title = $this->_('SNIPPETS');
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->contextmenu = array(
            array('name' => $this->_('CREATE_SNIPPET'), 'href' => $this->url . '&action=edit_snippet', 'icon' => 'plus'),
            array('name' => $this->_('CREATE_SNIPPET_FOLDER'), 'href' => $this->url . '&action=edit_snippet_folder', 'icon' => 'plus'),
        );
        $this->template = $IN['Table']->template;
    }


    public function edit_snippet_folder(array $IN = [])
    {
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('name' => $this->_('SNIPPETS'), 'href' => $this->url . '&action=snippets');
        $this->stdView->stdEdit($IN, 'getSnippetFolderContextMenu');
    }


    public function edit_snippet(array $IN = [])
    {
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('name' => $this->_('SNIPPETS'), 'href' => $this->url . '&action=snippets');
        $this->stdView->stdEdit($IN, 'getSnippetContextMenu');
    }


    public function material_types(array $IN = [])
    {
        $view = $this;
        $IN['Table'] = new MaterialTypesTable($IN);
        $this->assignVars($IN);
        $this->title = $this->_('MATERIAL_TYPES');
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->contextmenu = array(array('name' => $this->_('CREATE_MATERIAL_TYPE'), 'href' => $this->url . '&action=edit_material_type', 'icon' => 'plus'));
        $this->template = $IN['Table']->template;
    }


    public function edit_material_type(array $IN = [])
    {
        $view = $this;
        $Set = [];
        $Set[] = new Material_Field(array('name' => $this->_('NAME'), 'urn' => 'name', 'datatype' => 'text'));
        $Set[] = new Material_Field(array('name' => $this->_('DESCRIPTION'), 'urn' => 'description', 'datatype' => 'htmlarea'));
        foreach ($IN['Item']->fields as $row) {
            $Set[] = $row;
        }
        $IN['Table'] = new MaterialFieldsTable(array_merge($IN, array('Set' => $Set)));
        $this->assignVars($IN);
        $this->title = $IN['Form']->caption;
        $this->template = 'form_table';
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('name' => $this->_('MATERIAL_TYPES'), 'href' => $this->url . '&action=material_types');
        if ($IN['Parent']->id) {
            if ($IN['Parent']->parents) {
                foreach ($IN['Parent']->parents as $row) {
                    $this->path[] = array('href' => $this->url . '&action=edit_material_type' . '&id=' . (int)$row->id, 'name' => $row->name);
                }
            }
            $this->path[] = array('href' => $this->url . '&action=edit_material_type&id=' . (int)$IN['Parent']->id, 'name' => $IN['Parent']->name);
        }
        $this->contextmenu = $this->getMaterialTypeContextMenu($IN['Item']);
    }


    public function edit_material_field(array $IN = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_field.js';
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('name' => $this->_('MATERIAL_TYPES'), 'href' => $this->url . '&action=material_types');
        if ($IN['Parent']->parents) {
            foreach ($IN['Parent']->parents as $row) {
                $this->path[] = array('href' => $this->url . '&action=edit_material_type' . '&id=' . (int)$row->id, 'name' => $row->name);
            }
        }
        $this->path[] = array('name' => $IN['Parent']->name, 'href' => $this->url . '&action=edit_material_type&id=' . (int)$IN['Parent']->id);
        $this->stdView->stdEdit($IN, 'getMaterialFieldContextMenu');
    }


    public function move_material_field(array $IN = [])
    {
        $ids = array_map(function ($x) {
            return (int)$x->id;
        }, $IN['items']);
        $ids = array_unique($ids);
        $ids = array_values($ids);
        $pids = array_map(function ($x) {
            return (int)$x->pid;
        }, $IN['items']);
        $pids = array_unique($pids);
        $pids = array_values($pids);
        $actives = [];
        foreach ($IN['items'] as $row) {
            $actives[] = (int)$row->pid;
        }
        $actives = array_unique($actives);
        $actives = array_values($actives);
        $IN['ids'] = $ids;
        $IN['pids'] = $pids;
        $IN['actives'] = $actives;

        $this->assignVars($IN);
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('href' => $this->url . '&action=material_types', 'name' => $this->_('MATERIAL_TYPES'));
        if ($IN['Item']->Owner->id) {
            $this->path[] = array('href' => $this->url . '&action=edit_material_type' . '&id=' . (int)$IN['Item']->Owner->id, 'name' => $IN['Item']->Owner->name);
        }
        $this->path[] = array('href' => $this->url . '&action=edit_material_field' . '&id=' . (int)$IN['Item']->id, 'name' => $IN['Item']->name);
        if (count($IN['items']) == 1) {
            $this->contextmenu = $this->getMaterialFieldContextMenu($IN['Item']);
        }
        $this->title = $this->_('MOVING_NOTE');
        $this->template = 'dev_move_material_field';
    }


    public function forms(array $IN = [])
    {
        $IN['Table'] = new FormsTable($IN);
        $this->assignVars($IN);
        $this->title = $this->_('FORMS');
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->contextmenu = array(array('href' => $this->url . '&action=edit_form', 'name' => $this->_('CREATE_FORM'), 'icon' => 'plus'));
        $this->template = $IN['Table']->template;
    }


    public function edit_form(array $IN = [])
    {
        $view = $this;
        $Set = [];
        foreach ($IN['Item']->fields as $row) {
            $Set[] = $row;
        }
        $IN['Table'] = new FieldsTable(array_merge($IN, array('editAction' => 'edit_form_field', 'ctxMenu' => 'getFormFieldContextMenu', 'Set' => $Set)));
        $this->assignVars($IN);
        $this->title = $IN['Form']->caption;
        $this->template = 'form_table';
        $this->js[] = $this->publicURL . '/dev_edit_form.js';
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('name' => $this->_('FORMS'), 'href' => $this->url . '&action=forms');
        $this->contextmenu = $this->getFormContextMenu($IN['Item']);
    }


    public function edit_form_field(array $IN = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_field.js';
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('name' => $this->_('FORMS'), 'href' => $this->url . '&action=forms');
        $this->path[] = array('name' => $IN['Parent']->name, 'href' => $this->url . '&action=edit_form&id=' . (int)$IN['Parent']->id);
        $this->stdView->stdEdit($IN, 'getFormFieldContextMenu');
    }


    public function pages_fields(array $IN = [])
    {
        $IN['Table'] = new FieldsTable(array_merge($IN, array('editAction' => 'edit_page_field', 'ctxMenu' => 'getPageFieldContextMenu')));
        $this->assignVars($IN);
        $this->title = $this->_('PAGES_FIELDS');
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->contextmenu = array(array('name' => $this->_('CREATE_FIELD'), 'href' => $this->url . '&action=edit_page_field', 'icon' => 'plus'));
        $this->template = $IN['Table']->template;
    }


    public function edit_page_field(array $IN = [])
    {
        $this->js[] = $this->publicURL . '/dev_edit_field.js';
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->path[] = array('name' => $this->_('PAGES_FIELDS'), 'href' => $this->url . '&action=pages_fields');
        $this->stdView->stdEdit($IN, 'getPageFieldContextMenu');
    }


    public function diag(array $IN = [])
    {
        $IN['Form'] = new DiagForm(array('Item' => $IN['Item'], 'from' => $IN['from'], 'to' => $IN['to']));
        $this->assignVars($IN);
        $this->title = $IN['Form']->caption;
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
        $this->contextmenu = array(
            array(
                'name' => $this->_('CLEAR_DIAGNOSTICS_PERIOD'),
                'href' => $this->url . '&action=delete_diag&from=' . $IN['from'] . '&to=' . $IN['to'],
                'icon' => 'remove',
                'onclick' => 'return confirm("' . addslashes($this->_('CLEAR_DIAGNOSTICS_CONFIRM')) . '")'
            ),
            array(
                'name' => $this->_('CLEAR_DIAGNOSTICS_ALL'),
                'href' => $this->url . '&action=delete_diag',
                'icon' => 'remove',
                'onclick' => 'return confirm("' . addslashes($this->_('CLEAR_DIAGNOSTICS_CONFIRM')) . '")'
            ),
        );
        $this->template = $IN['Form']->template;
    }


    public function cache(array $IN = [])
    {
        $this->js[] = $this->publicURL . '/dev_cache.js';
        $this->assignVars($IN);
        $this->title = $this->_('CACHE_CONTROL');
        $this->path[] = array('name' => $this->_('DEVELOPMENT'), 'href' => $this->url);
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
                in_array(
                    $this->action,
                    ['dictionaries', 'edit_dictionary', 'move_dictionary']
                ) &&
                !$this->moduleName
            ),
            'submenu' => (
                in_array(
                    $this->action,
                    ['dictionaries', 'edit_dictionary', 'move_dictionary']
                ) ?
                $this->dictionariesMenu(new Dictionary(
                    $this->id ?:
                    (isset($this->nav['pid']) ? $this->nav['pid'] : 0)
                )) :
                null
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=snippets',
            'name' => $this->_('SNIPPETS'),
            'active' => (
                in_array(
                    $this->action,
                    [
                        'snippets',
                        'edit_snippet',
                        'edit_snippet_folder',
                        'copy_snippet'
                    ]
                ) &&
                !$this->moduleName
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=material_types',
            'name' => $this->_('MATERIAL_TYPES'),
            'active' => (
                in_array(
                    $this->action,
                    [
                        'material_types',
                        'edit_material_type',
                        'edit_material_field'
                    ]
                ) &&
                !$this->moduleName
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=pages_fields',
            'name' => $this->_('PAGES_FIELDS'),
            'active' => (
                in_array(
                    $this->action,
                    ['pages_fields', 'edit_page_field']
                ) &&
                !$this->moduleName
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=forms',
            'name' => $this->_('FORMS'),
            'active' => (
                in_array(
                    $this->action,
                    ['forms', 'edit_form', 'edit_form_field']
                ) &&
                !$this->moduleName
            )
        ];
        $submenu[] = [
            'href' => $this->url . '&action=menus',
            'name' => $this->_('MENUS'),
            'active' => (
                in_array(
                    $this->action,
                    ['menus', 'edit_menu', 'move_menu']
                ) &&
                !$this->moduleName
            ),
            'submenu' => (
                in_array($this->action, ['menus', 'edit_menu', 'move_menu']) ?
                $this->menusMenu(new Menu(
                    $this->id ?:
                    (isset($this->nav['pid']) ? $this->nav['pid'] : 0)
                )) :
                null
            )
        ];
        if (Package::i()->registryGet('diag')) {
            $submenu[] = [
                'href' => $this->url . '&action=diag',
                'name' => $this->_('DIAGNOSTICS')
            ];
        }
        if (Package::i()->registryGet('clear_cache_manually')) {
            $submenu[] = [
                'href' => $this->url . '&action=cache',
                'name' => $this->_('CACHE_CONTROL')
            ];
        }
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
                    $active =  in_array($this->action, ['menus', 'edit_menu', 'move_menu'])
                            && ((string)$_GET['domain_id'] === (string)$domainId);
                    $semiactive = (bool)array_filter($subMenu, function ($x) {
                        return (bool)$x['active'];
                    });
                    $menu[] = [
                        'name' => Text::cuttext($domainData['name'], 64, '...'),
                        'href' => $this->url
                               .  '&sub=dev&action=menus&domain_id='
                               .  (int)$domainId,
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
                'href' => $this->url . '&sub=dev&action=dictionaries&id='
                       .  (int)$row->id,
                'class' => '',
                'active' => false
            ];

            if (($row->id == $current->id) ||
                in_array($current->id, $node->all_children_ids)
            ) {
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
                'href' => $this->url
                       .  '&sub=dev&action=menus&id=' . (int)$row['id'],
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


    public function getTemplateContextMenu(Template $Item)
    {
        return $this->stdView->stdContextMenu($Item, 0, 0, 'edit_template', 'templates', 'delete_template');
    }


    public function getAllTemplatesContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_template&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getDictionaryContextMenu(Dictionary $Item, $i = 0, $c = 0)
    {
        $arr = [];
        if ($Item->id) {
            $edit = ($this->action == 'edit_dictionary');
            $showlist = ($this->action == 'dictionaries');
            if ($this->id == $Item->id) {
                $arr[] = array('href' => $this->url . '&action=edit_dictionary&pid=' . (int)$Item->id, 'name' => $this->_('CREATE_SUBNOTE'), 'icon' => 'plus');
            }
            if ($edit) {
                $arr[] = array('href' => $this->url . '&action=dictionaries&id=' . (int)$Item->id, 'name' => htmlspecialchars($Item->name), 'icon' => 'th-list');
            }
            $arr[] = array(
                'name' => $Item->vis ? $this->_('VISIBLE') : '<span class="muted">' . $this->_('INVISIBLE') . '</span>',
                'href' => $this->url . '&action=chvis_dictionary&id=' . (int)$Item->id . '&back=1',
                'icon' => $Item->vis ? 'ok' : '',
                'title' => $this->_($Item->vis ? 'HIDE' : 'SHOW')
            );
            if ($this->action != 'move_dictionary') {
                $arr[] = array('href' => $this->url . '&action=move_dictionary&id=' . (int)$Item->id, 'name' => $this->_('MOVE'), 'icon' => 'share-alt');
            }
            $arr = array_merge($arr, $this->stdView->stdContextMenu($Item, 0, 0, 'edit_dictionary', 'dictionaries', 'delete_dictionary'));
        } elseif (!$edit) {
            $arr[] = array('href' => $this->url . '&action=edit_dictionary', 'name' => $this->_('CREATE_NOTE'), 'icon' => 'plus');
        }
        return $arr;
    }


    public function getAllDictionariesContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis_dictionary&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        );
        $arr[] = array(
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis_dictionary&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        );
        $arr[] = array(
            'name' => $this->_('MOVE'),
            'href' => $this->url . '&action=move_dictionary',
            'icon' => 'share-alt'
        );
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_dictionary&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getSnippetFolderContextMenu(Snippet_Folder $Item)
    {
        if (!$Item->locked) {
            $arr = $this->stdView->stdContextMenu($Item, 0, 0, 'edit_snippet_folder', 'snippets', 'delete_snippet_folder');
        }
        return $arr;
    }


    public function getAllSnippetFoldersContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_snippet_folder&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getSnippetContextMenu(Snippet $Item)
    {
        if (!$Item->locked) {
            $arr = $this->stdView->stdContextMenu($Item, 0, 0, 'edit_snippet', 'snippets', 'delete_snippet');
        }
        if ($Item->id) {
            $arr[] = array('href' => $this->url . '&action=copy_snippet&id=' . (int)$Item->id, 'name' => $this->_('COPY'), 'icon' => 'tags');
        }
        return $arr;
    }


    public function getAllSnippetsContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_snippet&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getMaterialTypeContextMenu(Material_Type $Item)
    {
        $arr = [];
        if ($Item->id) {
            if ($this->action == 'edit_material_type') {
                $arr[] = array('href' => $this->url . '&action=edit_material_field&pid=' . (int)$Item->id, 'name' => $this->_('CREATE_FIELD'), 'icon' => 'plus');
            }
            $arr[] = array('href' => $this->url . '&action=edit_material_type&pid=' . (int)$Item->id, 'name' => $this->_('CREATE_CHILD_TYPE'), 'icon' => 'plus');
        }
        $arr = array_merge($arr, $this->stdView->stdContextMenu($Item, 0, 0, 'edit_material_type', 'material_types', 'delete_material_type'));
        return $arr;
    }


    public function getAllMaterialTypesContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_material_type&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getMaterialFieldContextMenu(Material_Field $Item, $i = 0, $c = 0)
    {
        $arr = [];
        if ($Item->id) {
            $arr[] = array(
                'name' => $this->_('SHOW_IN_TABLE'),
                'href' => $this->url . '&action=show_in_table_material_field&id=' . (int)$Item->id . '&back=1',
                'icon' => $Item->show_in_table ? 'ok' : '',
            );
            $arr[] = array(
                'name' => $this->_('REQUIRED'),
                'href' => $this->url . '&action=required_material_field&id=' . (int)$Item->id . '&back=1',
                'icon' => $Item->required ? 'ok' : '',
            );
            $arr[] = array(
                'name' => $this->_('MOVE'),
                'href' => $this->url . '&action=move_material_field&id=' . (int)$Item->id,
                'icon' => 'share-alt'
            );
        }
        $arr = array_merge(
            $arr,
            $this->stdView->stdContextMenu($Item, $i, $c, 'edit_material_field', 'material_types', 'delete_material_field')
        );
        return $arr;
    }


    public function getAllMaterialFieldsContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('SHOW_IN_TABLE'),
            'href' => $this->url . '&action=show_in_table_material_field&back=1',
            'icon' => 'align-justify',
        );
        $arr[] = array(
            'name' => $this->_('REQUIRED'),
            'href' => $this->url . '&action=required_material_field&back=1',
            'icon' => 'asterisk',
        );
        $arr[] = array(
            'name' => $this->_('MOVE'),
            'href' => $this->url . '&action=move_material_field',
            'icon' => 'share-alt'
        );
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_material_field&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getPageFieldContextMenu(Page_Field $Item, $i = 0, $c = 0)
    {
        $arr = [];
        if ($Item->id) {
            $arr[] = array(
                'name' => $this->_('SHOW_IN_TABLE'),
                'href' => $this->url . '&action=show_in_table_page_field&id=' . (int)$Item->id . '&back=1',
                'icon' => $Item->show_in_table ? 'ok' : '',
            );
            $arr[] = array(
                'name' => $this->_('REQUIRED'),
                'href' => $this->url . '&action=required_page_field&id=' . (int)$Item->id . '&back=1',
                'icon' => $Item->required ? 'ok' : '',
            );
        }
        $arr = array_merge(
            $arr,
            $this->stdView->stdContextMenu($Item, $i, $c, 'edit_page_field', 'pages_fields', 'delete_page_field')
        );
        return $arr;
    }


    public function getAllPageFieldsContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('SHOW_IN_TABLE'),
            'href' => $this->url . '&action=show_in_table_page_field&back=1',
            'icon' => 'align-justify',
        );
        $arr[] = array(
            'name' => $this->_('REQUIRED'),
            'href' => $this->url . '&action=required_page_field&back=1',
            'icon' => 'asterisk',
        );
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_page_field&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getFormContextMenu(Form $Item)
    {
        $arr = [];
        if ($Item->id &&$this->action == 'edit_form') {
            $arr[] = array('href' => $this->url . '&action=edit_form_field&pid=' . (int)$Item->id, 'name' => $this->_('CREATE_FIELD'), 'icon' => 'plus');
        }
        $arr = array_merge($arr, $this->stdView->stdContextMenu($Item, $i, $c, 'edit_form', 'forms', 'delete_form'));
        return $arr;
    }


    public function getAllFormsContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_form&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getFormFieldContextMenu(Form_Field $Item, $i = 0, $c = 0)
    {
        $arr = [];
        if ($Item->id) {
            $arr[] = array(
                'name' => $this->_('SHOW_IN_TABLE'),
                'href' => $this->url . '&action=show_in_table_form_field&id=' . (int)$Item->id . '&back=1',
                'icon' => $Item->show_in_table ? 'ok' : '',
            );
            $arr[] = array(
                'name' => $this->_('REQUIRED'),
                'href' => $this->url . '&action=required_form_field&id=' . (int)$Item->id . '&back=1',
                'icon' => $Item->required ? 'ok' : '',
            );
        }
        $arr = array_merge(
            $arr,
            $this->stdView->stdContextMenu($Item, $i, $c, 'edit_form_field', 'pages_fields', 'delete_form_field')
        );
        return $arr;
    }


    public function getAllFormFieldsContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('SHOW_IN_TABLE'),
            'href' => $this->url . '&action=show_in_table_form_field&back=1',
            'icon' => 'align-justify',
        );
        $arr[] = array(
            'name' => $this->_('REQUIRED'),
            'href' => $this->url . '&action=required_form_field&back=1',
            'icon' => 'asterisk',
        );
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_form_field&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function getMenuContextMenu(Menu $Item)
    {
        $arr = [];
        if ($Item->id) {
            $edit = ($this->action == 'edit_menu');
            $showlist = ($this->action == 'menus');
            if ($this->id == $Item->id) {
                $arr[] = array('href' => $this->url . '&action=edit_menu&pid=' . (int)$Item->id, 'name' => $this->_('CREATE_SUBNOTE'), 'icon' => 'plus');
            }
            if ($Item->vis) {
                $arr[] = array(
                    'name' => $this->_('VISIBLE'),
                    'href' => $this->url . '&action=chvis_menu&id=' . (int)$Item->id . '&back=1',
                    'icon' => 'ok',
                    'title' => $this->_('HIDE')
                );
            } else {
                $arr[] = array(
                    'name' => '<span class="muted">' . $this->_('INVISIBLE') . '</span>',
                    'href' => $this->url . '&action=chvis_menu&id=' . (int)$Item->id . '&back=1',
                    'icon' => '',
                    'title' => $this->_('SHOW')
                );
            }
            if ($this->action != 'move_menu') {
                $arr[] = array('href' => $this->url . '&action=move_menu&id=' . (int)$Item->id, 'name' => $this->_('MOVE'), 'icon' => 'share-alt');
            }
            if (($this->id == $Item->id) && ($Item->inherit > 0)) {
                $arr[] = array(
                    'href' => $this->url . '&action=realize_menu&id=' . (int)$Item->id . ($edit || $showlist ? '' : '&back=1'),
                    'name' => $this->_('REALIZE'),
                    'icon' => 'asterisk',
                    'onclick' => 'return confirm(\'' . $this->_('REALIZE_MENU_TEXT') . '\')'
                );
            }
            $arr = array_merge($arr, $this->stdView->stdContextMenu($Item, 0, 0, 'edit_menu', 'menus', 'delete_menu'));
        } elseif (!$edit) {
            $arr[] = array('href' => $this->url . '&action=edit_menu', 'name' => $this->_('CREATE_NOTE'), 'icon' => 'plus');
        }
        return $arr;
    }


    public function getAllMenusContextMenu()
    {
        $arr = [];
        $arr[] = array(
            'name' => $this->_('SHOW'),
            'href' => $this->url . '&action=vis_menu&back=1',
            'icon' => 'eye-open',
            'title' => $this->_('SHOW')
        );
        $arr[] = array(
            'name' => $this->_('HIDE'),
            'href' => $this->url . '&action=invis_menu&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('HIDE')
        );
        $arr[] = array(
            'name' => $this->_('MOVE'),
            'href' => $this->url . '&action=move_menu',
            'icon' => 'share-alt'
        );
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete_menu&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }
}
