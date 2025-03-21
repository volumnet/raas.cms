<?php
/**
 * Форма редактирования блока
 */
declare(strict_types=1);

namespace RAAS\CMS;

use ReflectionClass;
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
    /**
     * Класс блока по умолчанию
     */
    const DEFAULT_BLOCK_CLASSNAME = Block_PHP::class;

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
        $item = isset($params['Item']) ? $params['Item'] : null;
        $parent = $params['meta']['Parent'] ?? new Page();
        $loc = ($item && $item->location) ? $item->location : (isset($_GET['loc']) ? $_GET['loc'] : '');
        $blockType = str_replace('RAAS\\CMS\\', '', (string)($item ? $item->block_type : static::DEFAULT_BLOCK_CLASSNAME));
        $newUrl = Package::i()->url . '&pid=%s&action=edit_block&pid=' . (int)$parent->id . '&type='
            . str_replace('\\', '.', $blockType) . '&loc=' . $loc;
        $defaultParams = [
            'caption' => $this->view->_(($item && $item->id) ? 'EDITING_BLOCK' : 'CREATING_BLOCK'),
            'data-block-type' => $blockType,
            'parentUrl' => Package::i()->url . '&id=' . (int)$parent->id,
            'newUrl' => $newUrl,
            'export' => function ($form) {
                $form->exportDefault();
                if ($form->Item && Application::i()->user) {
                    $form->Item->editor_id = Application::i()->user->id;
                    if (!$form->Item->id) {
                        $form->Item->author_id = $form->Item->editor_id;
                    }
                }
            }
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
        $this->meta['CONTENT'] = ['cats' => [], 'locations' => []];
        $temp = new Page();
        $this->meta['CONTENT']['cats'] = $this->getMetaCats();
        foreach ($parent->Template->locations as $key => $val) {
            $this->meta['CONTENT']['locations'][] = [
                'value' => $key,
                'caption' => $key
            ];
        }
        $this->children['commonTab'] = $this->getCommonTab($parent);
        if (isset(Application::i()->packages['cms']->modules['users'])) {
            $this->children['accessTab'] = new CMSAccessFormTab($params);
        }
        $this->children['serviceTab'] = $this->getServiceTab();
        $this->children['pagesTab'] = $this->getPagesTab();
        if ($this->Item && $this->Item->id) {
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

        $this->children['serviceTab']->children['cache_type'] = [
            'type' => 'select',
            'name' => 'cache_type',
            'caption' => $this->view->_('CACHE_TYPE'),
            'children' => [
                ['value' => Block::CACHE_NONE, 'caption' => $this->view->_('_NONE')],
                ['value' => Block::CACHE_DATA, 'caption' => $this->view->_('CACHE_DATA')],
                ['value' => Block::CACHE_HTML, 'caption' => $this->view->_('CACHE_HTML')],
            ],
            'default' => Block::CACHE_NONE
        ];
        $this->children['serviceTab']->children['cache_single_page'] = [
            'type' => 'checkbox',
            'name' => 'cache_single_page',
            'caption' => $this->view->_('CACHE_BY_SINGLE_PAGES')
        ];
        $this->children['serviceTab']->children['cache_interface_id'] = new InterfaceField([
            'name' => 'cache_interface_id',
            'meta' => [
                'interfaceClassnameFieldName' => 'cache_interface_classname',
                'rootInterfaceClass' => CacheInterface::class
            ],
            'caption' => $this->view->_('CACHE_INTERFACE'),
            'default' => CacheInterface::class,
        ]);
    }


    /**
     * Получает поле "Интерфейс"
     * @return InterfaceField
     */
    protected function getInterfaceField(): InterfaceField
    {
        $defaultBlockClassname = static::DEFAULT_BLOCK_CLASSNAME;
        $field = new InterfaceField([
            'name' => 'interface_id',
            'meta' => [
                'interfaceClassnameFieldName' => 'interface_classname',
                'rootInterfaceClass' => $defaultBlockClassname::ALLOWED_INTERFACE_CLASSNAME
            ],
            'caption' => $this->view->_('INTERFACE'),
        ]);
        $reflectionClass = new ReflectionClass($defaultBlockClassname::ALLOWED_INTERFACE_CLASSNAME);
        if (!$reflectionClass->isAbstract()) {
            $field->default = $defaultBlockClassname::ALLOWED_INTERFACE_CLASSNAME;
        }
        return $field;
    }


    /**
     * Получает поле "Представление"
     * @return WidgetField
     */
    protected function getWidgetField(): WidgetField
    {
        $field = new WidgetField([
            'name' => 'widget_id',
            'caption' => $this->view->_('WIDGET'),
        ]);
        return $field;
    }


    /**
     * Получает поле "Переменная $_GET постраничной разбивки"
     * @return RAASField
     */
    protected function getPagesVarField(): RAASField
    {
        $field = new RAASField([
            'name' => 'pages_var_name',
            'caption' => $this->view->_('PAGES_VAR_NAME'),
        ]);
        return $field;
    }


    /**
     * Получает поле "Количество записей на странице (0 — все)"
     * @return RAASField
     */
    protected function getRowsPerPageField(): RAASField
    {
        $field = new RAASField([
            'type' => 'number',
            'name' => 'rows_per_page',
            'caption' => $this->view->_('ITEMS_PER_PAGE'),
        ]);
        return $field;
    }


    /**
     * Получает вкладку "Общие"
     * @return FormTab
     */
    protected function getCommonTab(): FormTab
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
    protected function getServiceTab(): FormTab
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
                    'template' => 'edit_block.params.inc.php',
                    'import' => function () use ($item) {
                        $params = explode('&', $item['params'] ?? '');
                        $result = [];
                        foreach ($params as $row) {
                            if (trim($row)) {
                                $row = explode('=', trim($row));
                                $result['params_name'][] = $row[0];
                                $result['params_value'][] = urldecode((string)($row[1] ?? ''));
                            }
                        }
                        return $result;
                    },
                    'export' => function () use ($item) {
                        $result = [];
                        foreach (($_POST['params_name'] ?? []) as $i => $val) {
                            $result[] = $_POST['params_name'][$i] . '='
                                      . urlencode($_POST['params_value'][$i]);
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
    protected function getPagesTab(): FormTab
    {
        $item = $this->Form->Item;
        $parent = $this->meta['Parent'] ?? new Page();
        $tab = new FormTab([
            'name' => 'pages',
            'caption' => $this->view->_('PAGES')
        ]);
        $loc = ($item && $item->location) ? $item->location : (isset($_GET['loc']) ? $_GET['loc'] : '');
        $tab->children['inherit'] = new RAASField([
            'type' => 'checkbox',
            'name' => 'inherit',
            'caption' => $this->view->_('INHERIT')
        ]);
        $tab->children['location'] = new RAASField([
            'type' => 'select',
            'name' => 'location',
            'caption' => $this->view->_('LOCATION'),
            'default' => $loc,
            'placeholder' => '--',
            'children' => isset($this->meta['CONTENT']['locations']) ? $this->meta['CONTENT']['locations'] : []
        ]);
        $tab->children['cats'] = new RAASField([
            'type' => 'checkbox',
            'name' => 'cats',
            'caption' => $this->view->_('PAGES'),
            'multiple' => 'multiple',
            'children' => isset($this->meta['CONTENT']['cats']) ? $this->meta['CONTENT']['cats'] : [],
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
            'default' => [(int)$parent->id],
        ]);
        return $tab;
    }


    /**
     * Получает список категорий для отображения в поле страниц
     * @param int $pid ID# родительской страницы
     * @return array <pre><code>array<[
     *     'value' => int ID# страницы,
     *     'caption' => string Наименование страницы,
     *     'data-group' => int ID# шаблона страницы
     *                     (группировочный параметр),
     *     'children' => *рекурсивно*
     * ]></code></pre>
     */
    public function getMetaCats($pid = 0): array
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
