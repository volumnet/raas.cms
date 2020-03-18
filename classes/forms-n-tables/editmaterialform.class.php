<?php
/**
 * Форма редактирования материала
 */
namespace RAAS\CMS;

use SOME\Pages;
use SOME\Text;
use RAAS\Application;
use RAAS\FormTab;
use RAAS\FieldSet;
use RAAS\Field as RAASField;
use RAAS\Column;

/**
 * Класс формы редактирования материала
 * @property-read ViewSub_Main $view Представление
 */
class EditMaterialForm extends \RAAS\Form
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
        $Type = isset($params['Type']) ? $params['Type'] : null;
        $related = isset($params['related']) ? $params['related'] : [];
        $Parent = isset($params['Parent']) ? $params['Parent'] : null;

        $title = $this->view->_(
            ($Item->id ? 'EDITING' : 'CREATING') . '_' .
            ($Parent->id ? 'PAGE' : 'SITE')
        );

        $tabs = [];
        $tabs['common'] = $this->getCommonTab($Item, $Type);
        $tabs['seo'] = $this->getSeoTab($Item);
        if (isset(Application::i()->packages['cms']->modules['users'])) {
            $tabs['access'] = new CMSAccessFormTab($params);
        }
        $tabs['service'] = $this->getServiceTab($Item, $Parent);
        $tabs['pages'] = $this->getPagesTab($Item, $Parent, $Type);
        if ($Item->id) {
            foreach ($Item->relatedMaterialTypes as $mtype) {
                if ($params['MSet'][$mtype->urn]) {
                    $tabs['_' . $mtype->urn] = $this->getMTab(
                        $Item,
                        $mtype,
                        $params
                    );
                }
            }
        }

        $defaultParams = [
            'Item' => $Item,
            'action' => '#',
            'parentUrl' => $this->view->url . '&id=' . $Parent->id
                        .  '#_' . $Type->urn,
            'caption' => $Item->id
                      ?  $Item->name
                      :  $this->view->_('CREATING_MATERIAL'),
            'children' => $tabs,
            'export' => function ($Form) use ($Parent) {
                $Form->exportDefault();
                $Form->Item->editor_id = Application::i()->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
                if (!$Form->Item->urn &&
                    isset($_POST['article']) &&
                    Text::beautify($_POST['article'])
                ) {
                    $Form->Item->urn = Text::beautify($_POST['article']);
                }
            },
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает вкладку "Общие"
     * @return FormTab
     */
    protected function getCommonTab($Item, $Type)
    {
        $commonTab = new FormTab([
            'name' => 'common',
            'caption' => $this->view->_('GENERAL')
        ]);
        if ($Type->children) {
            $commonTab->children['pid'] = new RAASField([
                'type' => 'select',
                'name' => 'pid',
                'caption' => $this->view->_('MATERIAL_TYPE'),
                'children' => ['Set' => [$Type]]
            ]);
            if ($Item->id) {
                $commonTab->children['pid']->onchange = 'if (confirm(\'' . addslashes($this->view->_('CHANGE_MATERIAL_TYPE_EXISTING_CONFIRM')) . '\')) { '
                                                      .    ' this.form.submit(); '
                                                      . '}';
            } else {
                $commonTab->children['pid']->onchange = 'if (confirm(\'' . addslashes($this->view->_('CHANGE_MATERIAL_TYPE_NEW_CONFIRM')) . '\')) { '
                                                      .    ' var url = document.location.href; '
                                                      .    ' url = url.replace(/(&|\\?)mtype=\\d+/, \'\'); '
                                                      .    ' url += (/\\?/.test(url) ? \'&\' : \'?\') + \'mtype=\' + this.value; '
                                                      .    ' document.location.href = url; '
                                                      . '}';
            }
        }
        $commonTab->children['name'] = new RAASField([
            'name' => 'name',
            'class' => 'span5',
            'caption' => $this->view->_('NAME'),
            'required' => 'required'
        ]);
        $commonTab->children['description'] = new RAASField([
            'type' => 'htmlarea',
            'name' => 'description',
            'caption' => $this->view->_('DESCRIPTION')
        ]);
        foreach ($Item->fields as $row) {
            $commonTab->children[] = $row->Field;
        }
        return $commonTab;
    }


    /**
     * Получает вкладку "Продвижение"
     * @param Material $item Текущий материал
     * @return FormTab
     */
    protected function getSeoTab(Material $item = null)
    {
        $seoTab = new FormTab([
            'name' => 'seo',
            'caption' => $this->view->_('SEO'),
            'children' => [
                [
                    'name' => 'urn',
                    'class' => 'span5',
                    'caption' => $this->view->_('URN')
                ],
                [
                    'name' => 'meta_title',
                    'class' => 'span5',
                    'caption' => $this->view->_('META_TITLE'),
                    'placeholder' => $item->name ?: $this->view->_('FROM_NAME'),
                    'data-hint' => sprintf(
                        $this->view->_('META_TITLE_RECOMMENDED_LIMIT'),
                        SeoOptimizer::META_TITLE_RECOMMENDED_LIMIT,
                        SeoOptimizer::META_TITLE_WORDS_LIMIT
                    ),
                    'data-recommended-limit' => SeoOptimizer::META_TITLE_RECOMMENDED_LIMIT,
                    'data-strict-limit' => SeoOptimizer::META_TITLE_STRICT_LIMIT,
                    'data-words-limit' => SeoOptimizer::META_TITLE_WORDS_LIMIT,
                ],
                [
                    'type' => 'textarea',
                    'name' => 'meta_description',
                    'class' => 'span5',
                    'rows' => 5,
                    'caption' => $this->view->_('META_DESCRIPTION'),
                    'data-hint' => sprintf(
                        $this->view->_('META_DESCRIPTION_RECOMMENDED_LIMIT'),
                        SeoOptimizer::META_DESCRIPTION_RECOMMENDED_LIMIT
                    ),
                    'data-recommended-limit' => SeoOptimizer::META_DESCRIPTION_RECOMMENDED_LIMIT,
                    'data-strict-limit' => SeoOptimizer::META_DESCRIPTION_STRICT_LIMIT,
                ],
                [
                    'type' => 'textarea',
                    'name' => 'meta_keywords',
                    'class' => 'span5',
                    'rows' => 5,
                    'caption' => $this->view->_('META_KEYWORDS')
                ],
                [
                    'name' => 'h1',
                    'caption' => $this->view->_('H1'),
                    'placeholder' => $item->name ?: $this->view->_('FROM_NAME'),
                    'class' => 'span5'
                ],
                [
                    'name' => 'menu_name',
                    'caption' => $this->view->_('MENU_NAME'),
                    'placeholder' => $item->name ?: $this->view->_('FROM_NAME'),
                    'class' => 'span5'
                ],
                [
                    'name' => 'breadcrumbs_name',
                    'caption' => $this->view->_('BREADCRUMBS_NAME'),
                    'placeholder' => $item->name ?: $this->view->_('FROM_NAME'),
                    'class' => 'span5'
                ],
                [
                    'type' => 'select',
                    'name' => 'changefreq',
                    'caption' => $this->view->_('CHANGEFREQ'),
                    'placeholder' => $this->view->_('AUTOMATICALLY'),
                    'children' => [
                        [
                            'value' => 'always',
                            'caption' => $this->view->_('CHANGEFREQ_ALWAYS')
                        ],
                        [
                            'value' => 'hourly',
                            'caption' => $this->view->_('CHANGEFREQ_HOURLY')
                        ],
                        [
                            'value' => 'daily',
                            'caption' => $this->view->_('CHANGEFREQ_DAILY')
                        ],
                        [
                            'value' => 'weekly',
                            'caption' => $this->view->_('CHANGEFREQ_WEEKLY')
                        ],
                        [
                            'value' => 'monthly',
                            'caption' => $this->view->_('CHANGEFREQ_MONTHLY')
                        ],
                        [
                            'value' => 'yearly',
                            'caption' => $this->view->_('CHANGEFREQ_YEARLY')
                        ],
                        [
                            'value' => 'never',
                            'caption' => $this->view->_('CHANGEFREQ_NEVER')
                        ],
                    ]
                ],
                [
                    'type' => 'number',
                    'class' => 'span5',
                    'min' => 0,
                    'step' => 0.1,
                    'max' => 1,
                    'name' => 'sitemaps_priority',
                    'caption' => $this->view->_('SITEMAPS_PRIORITY'),
                    'default' => 0.5
                ],
            ]
        ]);
        return $seoTab;
    }


    /**
     * Получает вкладку "Служебные"
     * @return FormTab
     */
    protected function getServiceTab($Item, $Parent)
    {
        $serviceTab = new FormTab([
            'name' => 'service',
            'caption' => $this->view->_('SERVICE'),
            'children' => [
                [
                    'type' => 'checkbox',
                    'name' => 'vis',
                    'caption' => $this->view->_(
                        $Parent->id ?
                        'VISIBLE' :
                        'IS_ACTIVE'
                    ),
                    'default' => 1
                ],
                [
                    'type' => 'datetime',
                    'name' => 'show_from',
                    'caption' => $this->view->_('SHOW_FROM')
                ],
                [
                    'type' => 'datetime',
                    'name' => 'show_to',
                    'caption' => $this->view->_('SHOW_TO')
                ],
            ]
        ]);
        if ($Item->id) {
            $serviceTab->children[] = [
                'name' => 'post_date',
                'caption' => $this->view->_('CREATED_BY'),
                'export' => 'is_null',
                'import' => 'is_null',
                'template' => 'stat.inc.php'
            ];
            $serviceTab->children[] = [
                'name' => 'modify_date',
                'caption' => $this->view->_('EDITED_BY'),
                'export' => 'is_null',
                'import' => 'is_null',
                'template' => 'stat.inc.php'
            ];
            $serviceTab->children[] = [
                'name' => 'last_modified',
                'caption' => $this->view->_('LAST_AFFECTED_MODIFICATION'),
                'export' => 'is_null',
                'import' => 'is_null',
                'template' => 'stat.inc.php'
            ];
        }
        return $serviceTab;
    }


    /**
     * Получает вкладку "Страницы"
     * @return FormTab
     */
    protected function getPagesTab($Item, $Parent, $Type)
    {
        $temp = new Page();
        $affectedPagesIds = [];
        foreach ($Item->affectedPages as $affectedPage) {
            $affectedPageId = (int)$affectedPage->id;
            $affectedPagesIds[(string)$affectedPageId] = $affectedPageId;
        }
        $pagesTab = new FormTab([
            'name' => 'pages',
            'caption' => $this->view->_('PAGES'),
            'children' => [
                'page_id' => [
                    'type' => 'select',
                    'name' => 'page_id',
                    'caption' => $this->view->_('MAIN_PARENT_PAGE'),
                    'children' => $this->getMetaCats(0, $affectedPagesIds),
                    'placeholder' => $this->view->_('DEFAULT'),
                ]
            ]
        ]);
        if (!$Type->global_type) {
            $pagesTab->children['cats'] = [
                'type' => 'checkbox',
                'multiple' => true,
                'name' => 'cats',
                'caption' => $this->view->_('PAGES'),
                'required' => 'required',
                'children' => $this->getMetaCats(),
                'default' => [(int)$Parent->id],
                'import' => function ($Field) {
                    return $Field->Form->Item->pages_ids;
                },
            ];
        }
        return $pagesTab;
    }


    /**
     * Получает вкладку связанных материалов
     * @param Material $Item Материал, для которого получается вкладка
     * @param Material_Type $mtype Связанный тип материалов
     * @param [
     *            'MSet' => array<Material> Список связанных материалов,
     *            'MPages' => Pages Постраничная разбивка,
     *            'Msort' => string Поле сортировки,
     *            'Morder' => 'asc'|'desc' Порядок сортировки,
     *        ] $params Дополнительные параметры
     * @return FormTab
     */
    protected function getMTab(
        Material $item,
        Material_Type $mtype,
        array $params = []
    ) {
        $temp = new MaterialsRelatedTable([
            'Item' => $item,
            'mtype' => $mtype,
            'hashTag' => $mtype->urn,
            'Set' => $params['MSet'][$mtype->urn],
            'Pages' => $params['MPages'][$mtype->urn],
            'sortVar' => 'm' . $mtype->id . 'sort',
            'orderVar' => 'm' . $mtype->id . 'order',
            'pagesVar' => 'm' . $mtype->id . 'page',
            'sort' => $params['Msort'][$mtype->urn],
            'order' => (strtolower($params['Morder'][$mtype->urn]) == 'desc')
                    ?  Column::SORT_DESC
                    :  Column::SORT_ASC
        ]);
        $tab = new FormTab([
            'name' => '_' . $mtype->urn,
            'meta' => ['Table' => $temp, 'mtype' => $mtype],
            'caption' => $this->view->_($mtype->name),
            'template' => 'material_related.inc.php'
        ]);
        return $tab;
    }


    /**
     * Получает список категорий для отображения в поле страниц
     * @param int $pid ID# родительской страницы
     * @param array<int>|null $relatedPagesIds Фильтр по связанным категориям,
     *                                         либо null, если не нужно
     * @return array<[
     *             'value' => int ID# страницы,
     *             'caption' => string Наименование страницы,
     *             'data-group' => int ID# шаблона страницы
     *                             (группировочный параметр),
     *             'style' => string Стиль пункта,
     *             'children' => *рекурсивно*
     *         ]>
     */
    public function getMetaCats($pid = 0, array $relatedPagesIds = null)
    {
        $pageCache = PageRecursiveCache::i();
        $result = [];
        $pagesIds = $pageCache->getChildrenIds($pid);
        $pagesData = [];
        foreach ($pagesIds as $pageId) {
            $pageData = $pageCache->cache[$pageId];
            $pageArr = [
                'value' => (int)$pageData['id'],
                'caption' => $pageData['name'],
                'data-group' => $pageData['template'],
            ];
            if ($pageId &&
                ($relatedPagesIds !== null) &&
                !isset($relatedPagesIds[$pageId])
            ) {
                $pageArr['style'] = 'display: none';
            }
            $pagesData[] = $pageArr;
        }
        foreach ($pagesData as $pageData) {
            if ($children = $this->getMetaCats(
                (int)$pageData['value'],
                $relatedPagesIds
            )) {
                $pageData['children'] = $children;
            }
            $result[] = $pageData;
        }
        return $result;
    }
}
