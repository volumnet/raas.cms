<?php
/**
 * Форма редактирования блока
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FormTab;
use RAAS\Field as RAASField;
use RAAS\Form as RAASForm;
use RAAS\Option;
use RAAS\FieldSet;

/**
 * Класс формы редактирования блока
 * @property-read ViewSub_Main $view Представление
 */
class EditBlockForm extends RAASForm
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Main::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $Parent = $params['meta']['Parent'];
        $loc = $Item->location ?: (isset($_GET['loc']) ? $_GET['loc'] : '');
        $defaultParams = [
            'caption' => $this->view->_(
                $Item->id ?
                'EDITING_BLOCK' :
                'CREATING_BLOCK'
            ),
            'data-block-type' => str_replace(
                'RAAS\\CMS\\',
                '',
                $Item->block_type
            ),
            'parentUrl' => Package::i()->url . '&id=' . (int)$Parent->id,
            'newUrl' => Package::i()->url
                     . '&pid=%s&action=edit_block&pid=' . (int)$Parent->id
                     . '&type='
                     . str_replace(
                         '\\',
                         '.',
                         str_replace('RAAS\\CMS\\', '', $Item->block_type)
                     ) . '&loc=' . $loc,
            'export' => function ($Form) use ($t) {
                $Form->exportDefault();
                $Form->Item->editor_id = Application::i()->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            }
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
        $this->meta['CONTENT'] = [];
        $temp = new Page();
        $this->meta['CONTENT']['cats'] = $this->getMetaCats();
        foreach ($this->meta['Parent']->Template->locations as $key => $val) {
            $this->meta['CONTENT']['locations'][] = [
                'value' => $key,
                'caption' => $key
            ];
        }
        $this->children['commonTab'] = $this->getCommonTab($Parent);
        if (isset(Application::i()->packages['cms']->modules['users'])) {
            $this->children['accessTab'] = new CMSAccessFormTab($params);
        }
        $this->children['serviceTab'] = $this->getServiceTab();
        $this->children['pagesTab'] = $this->getPagesTab();
        if ($this->Item->id) {
            $this->children['serviceTab']->children['post_date'] = new RAASField([
                'name' => 'post_date',
                'caption' => $this->view->_('CREATED_BY'),
                'export' => 'is_null',
                'import' => 'is_null',
                'template' => 'stat.inc.php'
            ]);
            $this->children['serviceTab']->children['modify_date'] = new RAASField([
                'name' => 'modify_date',
                'caption' => $this->view->_('EDITED_BY'),
                'export' => 'is_null',
                'import' => 'is_null',
                'template' => 'stat.inc.php'
            ]);
        }


        $interfaceField = $this->getInterfaceField();
        $interfaceField->name = 'cache_interface_id';
        $interfaceField->caption = $this->view->_('CACHE_INTERFACE');
        $interfaceField->placeholder = null;
        $s = Snippet::importByURN('__raas_cache_interface');
        $interfaceField->default = $s->id;

        $this->children['serviceTab']->children['cache_type'] = [
            'type' => 'select',
            'name' => 'cache_type',
            'caption' => $this->view->_('CACHE_TYPE'),
            'children' => [
                [
                    'value' => Block::CACHE_NONE,
                    'caption' => $this->view->_('_NONE')
                ],
                [
                    'value' => Block::CACHE_DATA,
                    'caption' => $this->view->_('CACHE_DATA')
                ],
                [
                    'value' => Block::CACHE_HTML,
                    'caption' => $this->view->_('CACHE_HTML')
                ],
            ],
            'default' => Block::CACHE_NONE
        ];
        $this->children['serviceTab']->children['cache_single_page'] = [
            'type' => 'checkbox',
            'name' => 'cache_single_page',
            'caption' => $this->view->_('CACHE_BY_SINGLE_PAGES')
        ];
        $this->children['serviceTab']->children['cache_interface_id'] = $interfaceField;
    }


    /**
     * Получает поле "Интерфейс"
     * @return RAASField
     */
    protected function getInterfaceField()
    {
        $wf = function (Snippet_Folder $x) use (&$wf) {
            $temp = [];
            foreach ($x->children as $row) {
                if (strtolower($row->urn) != '__raas_views') {
                    $o = new Option([
                        'value' => '',
                        'caption' => $row->name,
                        'disabled' => 'disabled'
                    ]);
                    $o->__set('children', $wf($row));
                    $temp[] = $o;
                }
            }
            foreach ($x->snippets as $row) {
                $temp[] = new Option([
                    'value' => $row->id,
                    'caption' => $row->name
                ]);
            }
            return $temp;
        };
        $field = new RAASField([
            'type' => 'select',
            'class' => 'input-xxlarge',
            'name' => 'interface_id',
            'caption' => $this->view->_('INTERFACE'),
            'placeholder' => $this->view->_('_NONE'),
            'children' => $wf(new Snippet_Folder())
        ]);
        return $field;
    }


    /**
     * Получает поле "Представление"
     * @return RAASField
     */
    protected function getWidgetField()
    {
        $wf = function (Snippet_Folder $x) use (&$wf) {
            $temp = [];
            foreach ($x->children as $row) {
                if (strtolower($row->urn) != '__raas_interfaces') {
                    $o = new Option([
                        'value' => '',
                        'caption' => $row->name,
                        'disabled' => 'disabled'
                    ]);
                    $o->__set('children', $wf($row));
                    $temp[] = $o;
                }
            }
            foreach ($x->snippets as $row) {
                $temp[] = new Option([
                    'value' => $row->id,
                    'caption' => $row->name
                ]);
            }
            return $temp;
        };
        $field = new RAASField([
            'type' => 'select',
            'class' => 'input-xxlarge',
            'name' => 'widget_id',
            'caption' => $this->view->_('WIDGET'),
            'placeholder' => $this->view->_('_NONE'),
            'children' => $wf(new Snippet_Folder())
        ]);
        return $field;
    }


    /**
     * Получает поле "Переменная $_GET постраничной разбивки"
     * @return RAASField
     */
    protected function getPagesVarField()
    {
        $field = new RAASField([
            'name' => 'pages_var_name',
            'caption' => $this->view->_('PAGES_VAR_NAME'),
            'default' => 'page'
        ]);
        return $field;
    }


    /**
     * Получает поле "Количество записей на странице (0 — все)"
     * @return RAASField
     */
    protected function getRowsPerPageField()
    {
        $field = new RAASField([
            'name' => 'rows_per_page',
            'caption' => $this->view->_('ITEMS_PER_PAGE'),
            'default' => Application::i()->registryGet('rowsPerPage')
        ]);
        return $field;
    }


    /**
     * Получает вкладку "Общие"
     * @return FormTab
     */
    protected function getCommonTab()
    {
        $tab = new FormTab([
            'name' => 'common',
            'caption' => $this->view->_('GENERAL'),
            'children' => [
                ['name' => 'name', 'caption' => $this->view->_('NAME')]
            ]
        ]);
        return $tab;
    }


    /**
     * Получает вкладку "Служебные"
     * @return FormTab
     */
    protected function getServiceTab()
    {
        $item = $this->Item;
        $tab = new FormTab([
            'name' => 'service',
            'caption' => $this->view->_('SERVICE'),
            'children' => [
                'vis' => [
                    'type' => 'checkbox',
                    'name' => 'vis',
                    'caption' => $this->view->_('VISIBLE'),
                    'default' => 1
                ],
                'vis_material' => [
                    'type' => 'select',
                    'name' => 'vis_material',
                    'caption' => $this->view->_('VISIBILITY_WITH_ACTIVE_MATERIAL'),
                    'children' => [
                        [
                            'value' => Block::BYMATERIAL_BOTH,
                            'caption' => $this->view->_('BYMATERIAL_BOTH')
                        ],
                        [
                            'value' => Block::BYMATERIAL_WITH,
                            'caption' => $this->view->_('BYMATERIAL_WITH')
                        ],
                        [
                            'value' => Block::BYMATERIAL_WITHOUT,
                            'caption' => $this->view->_('BYMATERIAL_WITHOUT')
                        ],
                    ],
                ],
                'params' => new FieldSet([
                    'name' => 'params',
                    'caption' => $this->view->_('ADDITIONAL_PARAMS'),
                    'children' => [
                        'params_name' => [
                            'name' => 'params_name',
                            'multiple' => true,
                        ],
                        'params_value' => [
                            'name' => 'params_name',
                            'multiple' => true,
                        ],
                    ],
                    'template' => 'edit_block.params.php',
                    'import' => function () use ($item) {
                        $params = explode('&', $item['params']);
                        $result = [];
                        foreach ($params as $row) {
                            if (trim($row)) {
                                $row = explode('=', trim($row));
                                $result['params_name'][] = $row[0];
                                $result['params_value'][] = $row[1];
                            }
                        }
                        return $result;
                    },
                    'export' => function () use ($item) {
                        $result = [];
                        foreach ($_POST['params_name'] as $i => $val) {
                            $result[] = $_POST['params_name'][$i] . '='
                                      . $_POST['params_value'][$i];
                        }
                        $result = implode('&', $result);
                        $item->params = $result;
                    },
                ]),
            ]
        ]);
        return $tab;
    }


    /**
     * Получает вкладку "Страницы"
     * @return FormTab
     */
    protected function getPagesTab()
    {
        $tab = new FormTab([
            'name' => 'pages',
            'caption' => $this->view->_('PAGES')
        ]);
        $loc = $Item->location ?: (isset($_GET['loc']) ? $_GET['loc'] : '');
        $tab->children[] = new RAASField([
            'type' => 'checkbox',
            'name' => 'inherit',
            'caption' => $this->view->_('INHERIT')
        ]);
        $tab->children[] = new RAASField([
            'type' => 'select',
            'name' => 'location',
            'caption' => $this->view->_('LOCATION'),
            'default' => $loc,
            'placeholder' => '--',
            'children' => $this->meta['CONTENT']['locations']
        ]);
        $tab->children[] = new RAASField([
            'type' => 'checkbox',
            'name' => 'cats',
            'caption' => $this->view->_('PAGES'),
            'multiple' => 'multiple',
            'children' => $this->meta['CONTENT']['cats'],
            'check' => function ($Field) {
                if (!isset($_POST['cats']) || !$_POST['cats']) {
                    return [
                        'name' => 'MISSED',
                        'value' => $Field->name,
                        'description' => 'ERR_NO_PAGES'
                    ];
                }
            },
            'import' => function ($Field) {
                return $Field->Form->Item->pages_ids;
            },
            'default' => [(int)$this->meta['Parent']->id],
        ]);
        return $tab;
    }


    /**
     * Получает список категорий для отображения в поле страниц
     * @param int $pid ID# родительской страницы
     * @return array<[
     *             'value' => int ID# страницы,
     *             'caption' => string Наименование страницы,
     *             'data-group' => int ID# шаблона страницы
     *                             (группировочный параметр),
     *             'children' => *рекурсивно*
     *         ]>
     */
    public function getMetaCats($pid = 0)
    {
        $pageCache = PageRecursiveCache::i();
        $result = [];
        $pagesIds = $pageCache->getChildrenIds($pid);
        $pagesData = [];
        foreach ($pagesIds as $pageId) {
            $pageData = $pageCache->cache[$pageId];
            $pagesData[] = [
                'value' => (int)$pageData['id'],
                'caption' => $pageData['name'],
                'data-group' => $pageData['template'],
            ];
        }
        foreach ($pagesData as $pageData) {
            if ($children = $this->getMetaCats((int)$pageData['value'])) {
                $pageData['children'] = $children;
            }
            $result[] = $pageData;
        }
        return $result;
    }
}
