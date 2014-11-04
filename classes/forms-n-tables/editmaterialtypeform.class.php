<?php
namespace RAAS\CMS;
use \RAAS\Field as RAASField;
use \RAAS\Option;
use \RAAS\Application;

class EditMaterialTypeForm extends \RAAS\Form
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
        
        $defaultParams = array(
            'caption' => $Item->id ? $Item->name : $this->view->_('CREATING_MATERIAL_TYPE'),
            'parentUrl' => Sub_Dev::i()->url . '&action=material_types',
            'export' => function($Form) use ($Parent) {
                $Form->exportDefault();
                if (!$Form->Item->id) {
                    $Form->Item->pid = (int)$Parent->id;
                }
            },
            'children' => array(
                array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'),
                array('name' => 'urn', 'caption' => $this->view->_('URN')),
                array('type' => 'checkbox', 'name' => 'global_type', 'caption' => $this->view->_('GLOBAL_MATERIALS'))
            )
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}