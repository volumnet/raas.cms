<?php
namespace RAAS\CMS;

class EditMenuForm extends \RAAS\Form
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Dev::i();
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
        $CONTENT['pages'] = array(new Page(array('id' => 0, 'name' => '--')));
        $defaultParams = array(
            'caption' => $Item->id ? $Item->name : ($Parent->id ? $this->view->_('CREATING_NOTE') : $this->view->_('CREATING_MENU')),
            'parentUrl' => $this->view->url . '&action=menus&id=%s',
            'export' => function($Form) use ($Parent) {
                $Form->exportDefault();
                if (!$Form->Item->id) {
                    $Form->Item->pid = (int)$Parent->id;
                }
            },
            'children' => array(
                array(
                    'type' => 'hidden', 
                    'name' => 'pid', 
                    'export' => 'is_null', 
                    'import' => function() use ($Parent) { return (int)$Parent->id; }, 
                    'default' => (int)$Parent->id
                ),
                array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_('VISIBLE'), 'default' => 1),
                array('type' => 'select', 'name' => 'page_id', 'caption' => $this->view->_('PAGE'), 'children' => array('Set' => $CONTENT['pages'])),
                array(
                    'type' => 'number', 
                    'name' => 'inherit', 
                    'caption' => $this->view->_('INHERIT_LEVEL'), 
                    'check' => function($Field) use ($Parent) {  
                        if (!$Parent->id && (int)$_POST['page_id'] && !(isset($_POST['inherit']) && (int)$_POST['inherit'])) {
                            return array('name' => 'MISSED', 'value' => $Field->name, 'description' => 'ERR_NO_MENU_INHERIT');
                        }
                    },
                ),
                array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'),
                array(
                    'name' => 'url', 
                    'caption' => $this->view->_('URL'), 
                    'check' => function($Field) use ($Parent) {
                        if ($Parent->id && !(int)$_POST['page_id'] && !trim($_POST['url'])) {
                            return array('name' => 'MISSED', 'value' => $Field->name, 'description' => 'ERR_NO_URL');
                        }
                    }
                ),
                
            )
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}