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

        $CONTENT = array();
        $CONTENT['templates'] = array('Set' => array_merge(array(new Template(array('id' => 0, 'name' => $this->view->_('NOT_SELECTED')))), Template::getSet()));
        $CONTENT['languages'] = array();
        foreach ($this->view->availableLanguages as $key => $val) {
            $CONTENT['languages'][] = array('value' => $key, 'caption' => $val);
        }
        if ($Parent->id) {
            $title = $Item->id ? $this->view->_('EDITING_PAGE') : $this->view->_('CREATING_PAGE');
        } else {
            $title = $Item->id ? $this->view->_('EDITING_SITE') : $this->view->_('CREATING_SITE');
        }
        
        $commonTab = new FormTab(array('name' => 'common', 'caption' => $this->view->_('GENERAL'), 'children' => array(array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'))));
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


        if ($Parent->id) {
            $commonTab->children[] = array('name' => 'urn', 'caption' => $this->view->_('URN'));
        } else {
            $commonTab->children[] = array('name' => 'urn', 'caption' => $this->view->_('DOMAIN_NAMES'), 'required' => 'required');
        }
        foreach (array('meta_title', 'meta_description', 'meta_keywords') as $key) {
            $commonTab->children[] = new FieldSet(array(
                'template' => 'edit_page.inherit.php',
                'children' => array(
                    array('name' => $key, 'caption' => $this->view->_(strtoupper($key)), 'default' => ($Parent->id ? $Parent->$key : '')), 
                    array('type' => 'checkbox', 'name' => 'inherit_' . $key, 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->{'inherit_' . $key} : 1))
                )
            ));
        }
        if ($Item->id) {
            $serviceTab->children[] = array('name' => 'post_date', 'caption' => $this->view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'modify_date', 'caption' => $this->view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
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
        $defaultParams = array(
            'parentUrl' => $this->view->url . '&id=%s#subsections', 
            'caption' => $title,
            'children' => array($commonTab, $serviceTab),
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
}