<?php
namespace RAAS\CMS;
use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\FieldSet;

class EditPageForm extends \RAAS\Form
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


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $Parent = isset($params['Parent']) ? $params['Parent'] : null;

        if ($Parent->id) {
            $title = $Item->id ? $this->view->_('EDITING_PAGE') : $this->view->_('CREATING_PAGE');
        } else {
            $title = $Item->id ? $this->view->_('EDITING_SITE') : $this->view->_('CREATING_SITE');
        }

        $tabs = array();
        $tabs['common'] = $this->getCommonTab($Item, $Parent);
        $tabs['seo'] = $this->getSeoTab($Parent);
        if (isset(Application::i()->packages['cms']->modules['users'])) {
            $tabs['access'] = new CMSAccessFormTab($params);
        }
        $tabs['service'] = $this->getServiceTab($Item, $Parent);

        $defaultParams = array(
            'parentUrl' => $this->view->url . '&id=%s#subsections',
            'caption' => $title,
            'children' => $tabs,
            'export' => function($Form) use ($Parent) {
                $Form->exportDefault();
                $Form->Item->editor_id = Application::i()->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->pid = $Parent->id;
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            }
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    private function getCommonTab($Item, $Parent)
    {
        $commonTab = new FormTab(array(
            'name' => 'common',
            'caption' => $this->view->_('GENERAL'),
            'children' => array(array('name' => 'name', 'class' => 'span5', 'caption' => $this->view->_('NAME'), 'required' => 'required'))
        ));
        if ($Parent->id) {
            $commonTab->children[] = array('name' => 'urn', 'class' => 'span5', 'caption' => $this->view->_('URN'));
        } else {
            $commonTab->children[] = array('name' => 'urn', 'class' => 'span5', 'caption' => $this->view->_('DOMAIN_NAMES'), 'required' => 'required');
        }
        foreach ($Item->fields as $row) {
            $f = $row->Field;
            $commonTab->children[] = new FieldSet(array(
                'template' => 'edit_page.inherit.php',
                'children' => array(
                    $f,
                    array(
                        'type' => 'checkbox',
                        'name' => 'inherit_' . $row->Field->name,
                        'caption' => $this->view->_('INHERIT'),
                        'default' => ($Parent->id ? $Parent->{'inherit_' . $row->Field->name} : 1),
                        'oncommit' => function() use ($row) {
                            if ($_POST['inherit_' . $row->Field->name]) {
                                $row->inheritValues();
                            }
                        },
                        'import' => function() use ($row) { return $row->inherited; }
                    )
                ),
            ));
        }
        return $commonTab;
    }


    private function getSeoTab($Parent)
    {
        $seoTab = new FormTab(
            array(
                'name' => 'seo',
                'caption' => $this->view->_('SEO'),
                'children' => array()
            )
        );
        $seoTab->children[] = new FieldSet(array(
            'template' => 'edit_page.inherit.php',
            'children' => array(
                array(
                    'name' => 'meta_title',
                    'class' => 'span5',
                    'caption' => $this->view->_(strtoupper('meta_title')),
                    'data-hint' => sprintf($this->view->_('META_TITLE_RECOMMENDED_LIMIT'), SeoOptimizer::META_TITLE_RECOMMENDED_LIMIT, SeoOptimizer::META_TITLE_WORDS_LIMIT),
                    'data-recommended-limit' => SeoOptimizer::META_TITLE_RECOMMENDED_LIMIT,
                    'data-strict-limit' => SeoOptimizer::META_TITLE_STRICT_LIMIT,
                    'data-words-limit' => SeoOptimizer::META_TITLE_WORDS_LIMIT,
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'inherit_meta_title',
                    'caption' => $this->view->_('INHERIT'),
                    'default' => ($Parent->id ? $Parent->{'inherit_meta_title'} : 1)
                )
            )
        ));
        $seoTab->children[] = new FieldSet(array(
            'template' => 'edit_page.inherit.php',
            'children' => array(
                array(
                    'type' => 'textarea',
                    'name' => 'meta_description',
                    'class' => 'span5',
                    'rows' => 5,
                    'caption' => $this->view->_(strtoupper('meta_description')),
                    'data-hint' => sprintf($this->view->_('META_DESCRIPTION_RECOMMENDED_LIMIT'), SeoOptimizer::META_DESCRIPTION_RECOMMENDED_LIMIT),
                    'data-recommended-limit' => SeoOptimizer::META_DESCRIPTION_RECOMMENDED_LIMIT,
                    'data-strict-limit' => SeoOptimizer::META_DESCRIPTION_STRICT_LIMIT,
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'inherit_meta_description',
                    'caption' => $this->view->_('INHERIT'),
                    'default' => ($Parent->id ? $Parent->{'inherit_meta_description'} : 1)
                )
            )
        ));
        $seoTab->children[] = new FieldSet(array(
            'template' => 'edit_page.inherit.php',
            'children' => array(
                array(
                    'type' => 'textarea',
                    'name' => 'meta_keywords',
                    'class' => 'span5',
                    'rows' => 5,
                    'caption' => $this->view->_(strtoupper('meta_keywords')),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'inherit_meta_keywords',
                    'caption' => $this->view->_('INHERIT'),
                    'default' => ($Parent->id ? $Parent->{'inherit_meta_keywords'} : 1)
                )
            )
        ));
        $seoTab->children[] = array('name' => 'h1', 'caption' => $this->view->_('H1'), 'placeholder' => $this->view->_('FROM_NAME'), 'class' => 'span5');
        $seoTab->children[] = array('name' => 'menu_name', 'caption' => $this->view->_('MENU_NAME'), 'placeholder' => $this->view->_('FROM_NAME'), 'class' => 'span5');
        $seoTab->children[] = array('name' => 'breadcrumbs_name', 'caption' => $this->view->_('BREADCRUMBS_NAME'), 'placeholder' => $this->view->_('FROM_NAME'), 'class' => 'span5');

        $seoTab->children[] = new FieldSet(array(
            'template' => 'edit_page.inherit.php',
            'children' => array(
                array(
                    'type' => 'select',
                    'name' => 'changefreq',
                    'caption' => $this->view->_('CHANGEFREQ'),
                    'placeholder' => $this->view->_('AUTOMATICALLY'),
                    'children' => array(
                        array('value' => 'always', 'caption' => $this->view->_('CHANGEFREQ_ALWAYS')),
                        array('value' => 'hourly', 'caption' => $this->view->_('CHANGEFREQ_HOURLY')),
                        array('value' => 'daily', 'caption' => $this->view->_('CHANGEFREQ_DAILY')),
                        array('value' => 'weekly', 'caption' => $this->view->_('CHANGEFREQ_WEEKLY')),
                        array('value' => 'monthly', 'caption' => $this->view->_('CHANGEFREQ_MONTHLY')),
                        array('value' => 'yearly', 'caption' => $this->view->_('CHANGEFREQ_YEARLY')),
                        array('value' => 'never', 'caption' => $this->view->_('CHANGEFREQ_NEVER'))
                    )
                ),
                array('type' => 'checkbox', 'name' => 'inherit_changefreq', 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->inherit_changefreq : 1))
            )
        ));
        $seoTab->children[] = new FieldSet(array(
            'template' => 'edit_page.inherit.php',
            'children' => array(
                array(
                    'type' => 'number',
                    'class' => 'span1',
                    'min' => 0,
                    'step' => 0.1,
                    'max' => 1,
                    'name' => 'sitemaps_priority',
                    'caption' => $this->view->_('SITEMAPS_PRIORITY'),
                    'default' => 0.5
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'inherit_sitemaps_priority',
                    'caption' => $this->view->_('INHERIT'),
                    'default' => ($Parent->id ? $Parent->inherit_sitemaps_priority : 1)
                )
            )
        ));
        return $seoTab;
    }


    private function getServiceTab($Item, $Parent)
    {
        $CONTENT = array();
        $CONTENT['templates'] = array('Set' => array_merge(array(new Template(array('id' => 0, 'name' => $this->view->_('NOT_SELECTED')))), Template::getSet()));
        $CONTENT['languages'] = array();
        foreach ($this->view->availableLanguages as $key => $val) {
            $CONTENT['languages'][] = array('value' => $key, 'caption' => $val);
        }
        $serviceTab = new FormTab(array(
            'name' => 'service',
            'caption' => $this->view->_('SERVICE'),
            'children' => array(
                array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_($Parent->id ? 'VISIBLE' : 'IS_ACTIVE'), 'default' => 1),
                array(
                    'name' => 'response_code',
                    'class' => 'span1',
                    'maxlength' => 3,
                    'caption' => $this->view->_('SERVICE_RESPONSE_CODE'),
                    'data-hint' => $this->view->_('SERVICE_PAGE_DESCRIPTION'),
                    'import' => function() use ($Item) { return (int)$Item->response_code ? (int)$Item->response_code : ''; }
                ),
                array('type' => 'checkbox', 'name' => 'nat', 'caption' => $this->view->_('TRANSLATE_ADDRESS')),
                new FieldSet(array(
                    'template' => 'edit_page.inherit.php',
                    'children' => array(
                        array('type' => 'checkbox', 'name' => 'cache', 'caption' => $this->view->_('CACHE_PAGE'), 'default' => ($Parent->id ? $Parent->cache : 0)),
                        array('type' => 'checkbox', 'name' => 'inherit_cache', 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->inherit_cache : 1))
                    )
                )),
                new FieldSet(array(
                    'template' => 'edit_page.inherit.php',
                    'children' => array(
                        array('type' => 'select', 'name' => 'template', 'caption' => $this->view->_('TEMPLATE'), 'children' => $CONTENT['templates'], 'default' => ($Parent->id ? $Parent->template : 0)),
                        array('type' => 'checkbox', 'name' => 'inherit_template', 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->inherit_template : 1))
                    )
                )),
                new FieldSet(array(
                    'template' => 'edit_page.inherit.php',
                    'children' => array(
                        array('type' => 'select', 'name' => 'lang', 'caption' => $this->view->_('LANGUAGE'), 'children' => $CONTENT['languages'], 'default' => ($Parent->id ? $Parent->lang : $this->view->language)),
                        array('type' => 'checkbox', 'name' => 'inherit_lang', 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->inherit_lang : 1))
                    )
                )),
            )
        ));

        if ($Item->id) {
            $serviceTab->children[] = array('name' => 'post_date', 'caption' => $this->view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'modify_date', 'caption' => $this->view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'last_modified', 'caption' => $this->view->_('LAST_AFFECTED_MODIFICATION'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
        }
        return $serviceTab;
    }
}
