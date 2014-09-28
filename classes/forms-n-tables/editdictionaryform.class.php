<?php
namespace RAAS\CMS;

class EditDictionaryForm extends \RAAS\Form
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
        foreach (\RAAS\CMS\Dictionary::$ordersBy as $key => $val) {
            $CONTENT['orderBy'][] = array('value' => $key, 'caption' => $this->view->_($val));
        }
        $defaultParams = array(
            'caption' => $Item->id ? $Item->name : ($Parent->id ? $this->view->_('CREATING_NOTE') : $this->view->_('CREATING_DICTIONARY')),
            'export' => function($Form) use ($Parent) { $Form->exportDefault(); $Form->Item->pid = (int)$Parent->id; },
            'parentUrl' => $this->view->url . '&action=dictionaries&id=' . (int)$Parent->id,
            'newUrl' => $this->view->url . '&action=edit_dictionary&pid=%s'
        );
        $defaultParams['children'][] = array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_('VISIBLE'), 'default' => 1);
        $defaultParams['children'][] = array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required');
        if ($Parent->id) {
            $defaultParams['children'][] = array('name' => 'urn', 'caption' => $this->view->_('VALUE'));
        }
        $defaultParams['children'][] = array('type' => 'radio', 'name' => 'orderby', 'children' => $CONTENT['orderBy'], 'default' => 'priority');
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}