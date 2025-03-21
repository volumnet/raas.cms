<?php
/**
 * Форма редактирования страницы
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FieldSet;
use RAAS\Form as RAASForm;
use RAAS\FormTab;

/**
 * Класс формы редактирования страницы
 */
class EditPageForm extends RAASForm
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
        $item = isset($params['Item']) ? $params['Item'] : new Page();
        $parent = isset($params['Parent']) ? $params['Parent'] : $item->parent;

        if ($parent && $parent->id) {
            if ($item->id) {
                $title =  $this->view->_('EDITING_PAGE');
            } else {
                $title =  $this->view->_('CREATING_PAGE');
            }
        } else {
            if ($item && $item->id) {
                $title = $this->view->_('EDITING_SITE');
            } else {
                $title = $this->view->_('CREATING_SITE');
            }
        }

        $tabs = [];
        $tabs['common'] = $this->getCommonTab($item, $parent);
        $tabs['seo'] = $this->getSeoTab($parent, $item);
        if (isset(Application::i()->packages['cms']->modules['users'])) {
            $tabs['access'] = new CMSAccessFormTab($params);
        }
        $tabs['service'] = $this->getServiceTab($item, $parent);

        $defaultParams = [
            'parentUrl' => $this->view->url . '&id=%s#subsections',
            'caption' => $title,
            'children' => $tabs,
            'export' => function ($Form) use ($parent) {
                $Form->exportDefault();
                $Form->Item->editor_id = Application::i()->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->pid = (int)$parent->id;
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            }
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает вкладку "Общие"
     * @param Page $item Текущая страница
     * @param Page $parent Родительская страница
     * @return FormTab
     */
    protected function getCommonTab(Page $item, Page $parent)
    {
        $commonTab = new FormTab([
            'name' => 'common',
            'caption' => $this->view->_('GENERAL'),
            'children' => [
                [
                    'name' => 'name',
                    'class' => 'span5',
                    'caption' => $this->view->_('NAME'),
                    'required' => 'required'
                ]
            ]
        ]);
        if ($parent->id) {
            $commonTab->children['urn'] = [
                'name' => 'urn',
                'class' => 'span5',
                'caption' => $this->view->_('URN')
            ];
        } else {
            $commonTab->children['urn'] = [
                'name' => 'urn',
                'class' => 'span5',
                'multiple' => true,
                'caption' => $this->view->_('DOMAIN_NAMES'),
                'required' => true,
                'import' => function () use ($item) {
                    $domains = explode(' ', $item->urn);
                    $domains = array_map('trim', $domains);
                    $domains = array_filter($domains);
                    return $domains;
                },
                'export' => function ($field) {
                    $domains = array_map('trim', $_POST['urn']);
                    $domains = array_filter($domains);
                    $domains = implode(' ', $domains);
                    $field->Form->Item->urn = $domains;
                },
            ];
        }
        foreach ($item->fields as $row) {
            $commonTab->children[$row->urn] = $row->Field;
        }
        return $commonTab;
    }


    /**
     * Получает вкладку "Продвижение"
     * @param Page $parent Родительская страница
     * @param ?Page $item Текущая страница
     * @return FormTab
     */
    protected function getSeoTab(Page $parent, ?Page $item = null)
    {
        $seoTab = new FormTab(
            [
                'name' => 'seo',
                'caption' => $this->view->_('SEO'),
                'children' => [
                    'meta_title' => [
                        'name' => 'meta_title',
                        'class' => 'span5',
                        'caption' => $this->view->_(strtoupper('meta_title')),
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
                    'meta_description' => [
                        'type' => 'textarea',
                        'name' => 'meta_description',
                        'class' => 'span5',
                        'rows' => 5,
                        'caption' => $this->view->_(strtoupper('meta_description')),
                        'data-hint' => sprintf(
                            $this->view->_('META_DESCRIPTION_RECOMMENDED_LIMIT'),
                            SeoOptimizer::META_DESCRIPTION_RECOMMENDED_LIMIT
                        ),
                        'data-recommended-limit' => SeoOptimizer::META_DESCRIPTION_RECOMMENDED_LIMIT,
                        'data-strict-limit' => SeoOptimizer::META_DESCRIPTION_STRICT_LIMIT,
                    ],
                    'meta_keywords' => [
                        'type' => 'textarea',
                        'name' => 'meta_keywords',
                        'class' => 'span5',
                        'rows' => 5,
                        'caption' => $this->view->_(strtoupper('meta_keywords')),
                    ],
                    'h1' => [
                        'name' => 'h1',
                        'caption' => $this->view->_('H1'),
                        'placeholder' => $item->name ?: $this->view->_('FROM_NAME'),
                        'class' => 'span5'
                    ],
                    'menu_name' => [
                        'name' => 'menu_name',
                        'caption' => $this->view->_('MENU_NAME'),
                        'placeholder' => $item->name ?: $this->view->_('FROM_NAME'),
                        'class' => 'span5'
                    ],
                    'breadcrumbs_name' => [
                        'name' => 'breadcrumbs_name',
                        'caption' => $this->view->_('BREADCRUMBS_NAME'),
                        'placeholder' => $item->name ?: $this->view->_('FROM_NAME'),
                        'class' => 'span5'
                    ],
                    'changefreq' => [
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
                    'sitemaps_priority' => [
                        'type' => 'number',
                        'class' => 'span1',
                        'min' => 0,
                        'step' => 0.1,
                        'max' => 1,
                        'name' => 'sitemaps_priority',
                        'caption' => $this->view->_('SITEMAPS_PRIORITY'),
                        'default' => 0.5
                    ],
                ],
            ],
        );
        return $seoTab;
    }


    /**
     * Получает вкладку "Служебные"
     * @param Page $item Текущая страница
     * @param Page $parent Родительская страница
     * @return FormTab
     */
    protected function getServiceTab(Page $item, Page $parent)
    {
        $CONTENT = [];
        $templates = Template::getSet();
        $CONTENT['templates'] = [];
        foreach ($templates as $template) {
            $CONTENT['templates'][] = [
                'value' => $template->id,
                'caption' => trim('#' . $template->id . ' ' . $template->name),
            ];
        }
        $CONTENT['languages'] = [];
        foreach ($this->view->availableLanguages as $key => $val) {
            $CONTENT['languages'][] = ['value' => $key, 'caption' => $val];
        }
        $serviceTab = new FormTab([
            'name' => 'service',
            'caption' => $this->view->_('SERVICE'),
            'children' => [
                'vis' => [
                    'type' => 'checkbox',
                    'name' => 'vis',
                    'caption' => $this->view->_(
                        $parent->id ?
                        'VISIBLE' :
                        'IS_ACTIVE'
                    ),
                    'default' => 1
                ],
                'response_code' => [
                    'name' => 'response_code',
                    'class' => 'span1',
                    'maxlength' => 3,
                    'caption' => $this->view->_('SERVICE_RESPONSE_CODE'),
                    'data-hint' => $this->view->_('SERVICE_PAGE_DESCRIPTION'),
                    'import' => function () use ($item) {
                        return (int)$item->response_code ?: '';
                    }
                ],
                'mime' => [
                    'name' => 'mime',
                    'caption' => $this->view->_('PAGE_MIME'),
                    'children' => array_map(fn($x) => ['value' => $x, 'caption' => $x], Page::$mimeTypes),
                ],
                'nat' => [
                    'type' => 'checkbox',
                    'name' => 'nat',
                    'caption' => $this->view->_('TRANSLATE_ADDRESS')
                ],
                'cacheFieldSet' => new FieldSet([
                    'template' => 'edit_page.inherit.inc.php',
                    'children' => [
                        'cache' => [
                            'type' => 'checkbox',
                            'name' => 'cache',
                            'caption' => $this->view->_('CACHE_PAGE'),
                            'default' => ($parent->id ? $parent->cache : 0)
                        ],
                        'inherit_cache' => [
                            'type' => 'checkbox',
                            'name' => 'inherit_cache',
                            'caption' => $this->view->_('INHERIT'),
                            'default' => (
                                $parent->id ?
                                $parent->inherit_cache :
                                1
                            )
                        ]
                    ]
                ]),
                'templateFieldSet' => new FieldSet([
                    'template' => 'edit_page.inherit.inc.php',
                    'children' => [
                        'template' => [
                            'type' => 'select',
                            'name' => 'template',
                            'caption' => $this->view->_('TEMPLATE'),
                            'children' => $CONTENT['templates'],
                            'placeholder' => $this->view->_('NOT_SELECTED'),
                            'default' => ($parent->id ? $parent->template : 0)
                        ],
                        'inherit_template' => [
                            'type' => 'checkbox',
                            'name' => 'inherit_template',
                            'caption' => $this->view->_('INHERIT'),
                            'default' => (
                                $parent->id ?
                                $parent->inherit_template :
                                1
                            )
                        ]
                    ]
                ]),
                'languageFieldSet' => new FieldSet([
                    'template' => 'edit_page.inherit.inc.php',
                    'children' => [
                        'lang' => [
                            'type' => 'select',
                            'name' => 'lang',
                            'caption' => $this->view->_('LANGUAGE'),
                            'children' => $CONTENT['languages'],
                            'default' => (
                                $parent->id ?
                                $parent->lang :
                                $this->view->language
                            )
                        ],
                        'inherit_lang' => [
                            'type' => 'checkbox',
                            'name' => 'inherit_lang',
                            'caption' => $this->view->_('INHERIT'),
                            'default' => (
                                $parent->id ?
                                $parent->inherit_lang :
                                1
                            )
                        ]
                    ]
                ]),
            ]
        ]);

        if ($item->id) {
            $serviceTab->children['post_date'] = [
                'name' => 'post_date',
                'caption' => $this->view->_('CREATED_BY'),
                'export' => 'is_null',
                'import' => 'is_null',
                'template' => 'stat.inc.php'
            ];
            $serviceTab->children['modify_date'] = [
                'name' => 'modify_date',
                'caption' => $this->view->_('EDITED_BY'),
                'export' => 'is_null',
                'import' => 'is_null',
                'template' => 'stat.inc.php'
            ];
            $serviceTab->children['last_modified'] = [
                'name' => 'last_modified',
                'caption' => $this->view->_('LAST_AFFECTED_MODIFICATION'),
                'export' => 'is_null',
                'import' => 'is_null',
                'template' => 'stat.inc.php'
            ];
        }
        return $serviceTab;
    }
}
