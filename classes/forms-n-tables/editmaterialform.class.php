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
        $item = isset($params['Item']) ? $params['Item'] : null;
        $type = isset($params['Type']) ? $params['Type'] : null;
        $related = isset($params['related']) ? $params['related'] : [];
        $parent = isset($params['Parent']) ? $params['Parent'] : null;

        $title = $this->view->_(
            ($item && $item->id ? 'EDITING' : 'CREATING') . '_' .
            ($parent && $parent->id ? 'PAGE' : 'SITE')
        );

        $tabs = [];
        foreach (($type->fieldGroups ?? []) as $fieldGroupURN => $fieldGroup) {
            if ($fieldGroupURN == '') {
                $tabs['common'] = $this->getCommonTab($item, $type);
            } else {
                if ($tab = $this->getGroupTab($fieldGroup, $item, $type)) {
                    $tabs[$tab->name] = $tab;
                }
            }
        }
        $tabs['seo'] = $this->getSeoTab($item);
        if (isset(Application::i()->packages['cms']->modules['users'])) {
            $tabs['access'] = new CMSAccessFormTab($params);
        }
        $tabs['service'] = $this->getServiceTab($item, $parent);
        $tabs['pages'] = $this->getPagesTab($item, $parent, $type);
        if ($item && $item->id) {
            foreach ($item->relatedMaterialTypes as $mtype) {
                if ($params['MSet'][$mtype->urn]) {
                    $tabs['_' . $mtype->urn] = $this->getMTab(
                        $item,
                        $mtype,
                        $params
                    );
                }
            }
        }

        $defaultParams = [
            'Item' => $item,
            'action' => '#',
            'parentUrl' => $this->view->url . '&id=' . ($parent->id ?? '') .  '#_' . ($type->urn ?? ''),
            'caption' => ($item->id ?? null) ? $item->name : $this->view->_('CREATING_MATERIAL'),
            'children' => $tabs,
            'export' => function ($Form) use ($parent) {
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
     * Получает поле смены типа материалов
     * @param Material $item Материал для редактирования
     * @param Material_Type $type Тип материалов для редактирования
     * @return RAASField
     */
    protected function getChangeTypeField(Material $item, Material_Type $type): RAASField
    {
        $allowChangeMaterialType = Package::i()->registryGet('allowChangeMaterialType');
        if ($allowChangeMaterialType) {
            $tempMType = new Material_Type();
            $children = $tempMType->children;
        } else {
            $children = [$type];
        }
        $field = new RAASField([
            'type' => 'select',
            'name' => 'pid',
            'required' => true,
            'caption' => $this->view->_('MATERIAL_TYPE'),
            'children' => [
                'Set' => $children,
            ],
            'default' => $type->id,
        ]);
        if ($item->id) {
            $field->onchange = 'if (confirm(\'' . addslashes($this->view->_('CHANGE_MATERIAL_TYPE_EXISTING_CONFIRM')) . '\')) { '
                                                  .    ' this.form.submit(); '
                                                  . '}';
        } else {
            $field->onchange = 'if (confirm(\'' . addslashes($this->view->_('CHANGE_MATERIAL_TYPE_NEW_CONFIRM')) . '\')) { '
                                                  .    ' var url = document.location.href; '
                                                  .    ' url = url.replace(/(&|\\?)mtype=\\d+/, \'\'); '
                                                  .    ' url += (/\\?/.test(url) ? \'&\' : \'?\') + \'mtype=\' + this.value; '
                                                  .    ' document.location.href = url; '
                                                  . '}';
        }
        return $field;
    }


    /**
     * Получает вкладку "Общие"
     * @param Material $item Материал для редактирования
     * @param Material_Type $type Тип материалов для редактирования
     * @return FormTab
     */
    protected function getCommonTab(Material $item, Material_Type $type): FormTab
    {
        $groupTab = $this->getGroupTab($type->fieldGroups[''], $item, $type);
        $tab = new FormTab([
            'name' => 'common',
            'caption' => $this->view->_('GENERAL'),
        ]);
        if ($type->children || Package::i()->registryGet('allowChangeMaterialType')) {
            $tab->children['pid'] = $this->getChangeTypeField($item, $type);
        }
        $tab->children['name'] = new RAASField([
            'name' => 'name',
            'class' => 'span8',
            'caption' => $this->view->_('NAME'),
            'required' => 'required'
        ]);
        $tab->children['description'] = new RAASField([
            'type' => 'htmlarea',
            'name' => 'description',
            'caption' => $this->view->_('DESCRIPTION')
        ]);
        foreach ($groupTab->children as $fieldURN => $field) {
            $tab->children[$fieldURN] = $field;
        }
        return $tab;
    }


    /**
     * Получает вкладку по группе полей
     * @param MaterialFieldGroup $fieldGroup Группа полей
     * @param Material $item Материал для редактирования
     * @param Material_Type $type Тип материалов
     * @return FormTab|null null, если нет полей и группа не общая
     */
    protected function getGroupTab(FieldGroup $fieldGroup, Material $item, Material_Type $type): ?FormTab
    {
        $tab = new FormTab([
            'name' => 'group_' . $fieldGroup->urn,
            'caption' => $fieldGroup->name
        ]);
        $formFields = $fieldGroup->getFormFields($type);
        if (!$formFields && $fieldGroup->id) {
            return null;
        }
        foreach ($formFields as $field) {
            $field = $field->deepClone();
            $field->Owner = $item;
            $tab->children[$field->urn] = $field->Field;
        }
        return $tab;
    }


    /**
     * Получает вкладку "Продвижение"
     * @param ?Material $item Текущий материал
     * @return FormTab
     */
    protected function getSeoTab(?Material $item = null): FormTab
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
                    'placeholder' => ($item && $item->name) ? $item->name : $this->view->_('FROM_NAME'),
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
                    'placeholder' => ($item && $item->name) ? $item->name : $this->view->_('FROM_NAME'),
                    'class' => 'span5'
                ],
                [
                    'name' => 'menu_name',
                    'caption' => $this->view->_('MENU_NAME'),
                    'placeholder' => ($item && $item->name) ? $item->name : $this->view->_('FROM_NAME'),
                    'class' => 'span5'
                ],
                [
                    'name' => 'breadcrumbs_name',
                    'caption' => $this->view->_('BREADCRUMBS_NAME'),
                    'placeholder' => ($item && $item->name) ? $item->name : $this->view->_('FROM_NAME'),
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
     * @param Material $item Текущий материал
     * @param ?Page $parent Родительская страница
     * @return FormTab
     */
    protected function getServiceTab(Material $item, ?Page $parent = null): FormTab
    {
        $serviceTab = new FormTab([
            'name' => 'service',
            'caption' => $this->view->_('SERVICE'),
            'children' => [
                [
                    'type' => 'checkbox',
                    'name' => 'vis',
                    'caption' => $this->view->_(($parent && $parent->id) ? 'VISIBLE' : 'IS_ACTIVE'),
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
        if ($item && $item->id) {
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
     * @param Material $item Текущий материал
     * @param ?Page $parent Родительская страница
     * @param Material_Type $type Тип материалов
     * @return FormTab
     */
    protected function getPagesTab(Material $item, ?Page $parent = null, ?Material_Type $type = null): FormTab
    {
        $temp = new Page();
        $affectedPagesIds = [];
        foreach (($item->affectedPages ?? []) as $affectedPage) {
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
        if (!($type->global_type ?? false)) {
            $pagesTab->children['cats'] = [
                'type' => 'checkbox',
                'multiple' => true,
                'name' => 'cats',
                'caption' => $this->view->_('PAGES'),
                'required' => 'required',
                'children' => $this->getMetaCats(),
                'default' => [(int)($parent->id ?? 0)],
                'import' => function ($Field) {
                    return $Field->Form->Item->pages_ids;
                },
            ];
        }
        return $pagesTab;
    }


    /**
     * Получает вкладку связанных материалов
     * @param Material $item Материал, для которого получается вкладка
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
    ): FormTab {
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
     * @param ?int[] $relatedPagesIds Фильтр по связанным категориям,
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
    public function getMetaCats(int $pid = 0, ?array $relatedPagesIds = null): array
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
            if (($relatedPagesIds !== null) && !isset($relatedPagesIds[$pageId])) { // Есть фильтр, но текущей страницы в нем нет
                if (array_intersect($relatedPagesIds, $pageCache->getAllChildrenIds($pageId))) { // В фильтре есть дочерние страницы, делаем disabled
                    $pageArr['disabled'] = true;
                } else { // Дочерних тоже нет, пропускаем
                    continue;
                }
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
