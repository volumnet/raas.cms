<?php
namespace RAAS\CMS;
use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\FieldSet;

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


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $Type = isset($params['Type']) ? $params['Type'] : null;
        $Parent = isset($params['Parent']) ? $params['Parent'] : null;

        $CONTENT = array();
        $temp = new Page();
        $CONTENT['cats'] = array('Set' => $temp->children, 'additional' => function($row) { return array('data-group' => $row->template); });
        if ($Parent->id) {
            $title = $Item->id ? $this->view->_('EDITING_PAGE') : $this->view->_('CREATING_PAGE');
        } else {
            $title = $Item->id ? $this->view->_('EDITING_SITE') : $this->view->_('CREATING_SITE');
        }
        $commonTab = new FormTab(array(
            'name' => 'common', 
            'caption' => $this->view->_('GENERAL'), 
            'children' => array(
                array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'),
                array('type' => 'htmlarea', 'name' => 'description', 'caption' => $this->view->_('DESCRIPTION'))
            )
        ));
        $seoTab = new FormTab(array(
            'name' => 'seo', 
            'caption' => $this->view->_('SEO'), 
            'children' => array(
                array('name' => 'urn', 'caption' => $this->view->_('URN')),
                array('name' => 'meta_title', 'caption' => $this->view->_('META_TITLE')),
                array('name' => 'meta_description', 'caption' => $this->view->_('META_DESCRIPTION')),
                array('name' => 'meta_keywords', 'caption' => $this->view->_('META_KEYWORDS'))
            )
        ));
        $serviceTab = new FormTab(array(
            'name' => 'service', 
            'caption' => $this->view->_('SERVICE'), 
            'children' => array(array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_($Parent->id ? 'VISIBLE' : 'IS_ACTIVE'), 'default' => 1))
        ));
        if ($Item->id) {
            $serviceTab->children[] = array('name' => 'post_date', 'caption' => $this->view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'modify_date', 'caption' => $this->view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
        }
        foreach ($Item->fields as $row) {
            $commonTab->children[] = $row->Field;
        }

        $defaultParams = array(
            'Item' => $Item, 
            'parentUrl' => $this->view->url . '&id=' . $Parent->id . '#_' . $Type->urn, 
            'caption' => $Item->id ? $Item->name : $this->view->_('CREATING_MATERIAL'),
            'children' => array($commonTab, $seoTab, $serviceTab),
            'export' => function($Form) use ($Parent) {
                $Form->exportDefault();
                $Form->Item->editor_id = Application::i()->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            }
        );
        if (!$Type->global_type){
            $defaultParams['children'][] = new FormTab(array(
                'name' => 'pages',
                'caption' => $this->view->_('PAGES'),
                'children' => array(
                    array(
                        'type' => 'checkbox', 
                        'multiple' => true, 
                        'name' => 'cats', 
                        'caption' => $this->view->_('PAGES'),
                        'required' => 'required', 
                        'children' => $CONTENT['cats'], 
                        'default' => array((int)$Parent->id),
                        'import' => function($Field) { return $Field->Form->Item->pages_ids; },
                    )
                )
            ));
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}