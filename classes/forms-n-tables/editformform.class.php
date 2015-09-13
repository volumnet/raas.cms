<?php
namespace RAAS\CMS;
use \RAAS\Field as RAASField;
use \RAAS\Option;
use \RAAS\Application;

class EditFormForm extends \RAAS\Form
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
        $CONTENT = array();
        $CONTENT['material_types'] = (array)Material_Type::getSet();
        foreach (array('' => '_OFF', 'captcha' => 'CAPTCHA', 'hidden' => 'HIDDEN_FIELD') as $key => $val) {
            $CONTENT['antispam'][] = array('value' => $key, 'caption' => $view->_($val));
        }
        $wf = function(Snippet_Folder $x) use (&$wf) {
            $temp = array();
            foreach ($x->children as $row) {
                if (strtolower($row->urn) != '__raas_views') {
                    $o = new Option(array('value' => '', 'caption' => $row->name, 'disabled' => 'disabled'));
                    $o->__set('children', $wf($row));
                    $temp[] = $o;
                }
            }
            foreach ($x->snippets as $row) {
                $temp[] = new Option(array('value' => $row->id, 'caption' => $row->name));
            }
            return $temp;
        };
        $field = new RAASField();

        $defaultParams = array(
            'caption' => $Item->id ? $Item->name : $view->_('CREATING_FORM'),
            'parentUrl' => Sub_Dev::i()->url . '&action=forms',
            'children' => array(
                array('name' => 'name', 'caption' => $view->_('NAME'), 'required' => 'required'),
                array(
                    'type' => 'select', 
                    'name' => 'material_type', 
                    'caption' => $view->_('MATERIAL_TYPE'), 
                    'children' => array('Set' => $CONTENT['material_types']), 
                    'placeholder' => $view->_('_NONE')
                ),
                array('type' => 'checkbox', 'name' => 'create_feedback', 'caption' => $view->_('CREATE_FEEDBACK'), 'default' => 1),
                array('type' => 'checkbox', 'name' => 'signature', 'caption' => $view->_('REQUIRE_UNIQUE'), 'default' => 1),
                array(
                    'type' => 'select', 'name' => 'antispam', 'caption' => $view->_('ANTISPAM_FIELD'), 'children' => $CONTENT['antispam'], 'default' => 'captcha'
                ),
                array('name' => 'antispam_field_name', 'caption' => $view->_('ANTISPAM_VARIABLE'), 'default' => 'captcha'),
                array(
                    'name' => 'email', 
                    'caption' => $view->_('EMAIL_TO_SEND_NOTIFY'), 
                    'data-hint' => $view->_('SPACE_COMMA_SEMICOLON_SEPARATED'), 
                    'default' => Application::i()->user->email
                ),
                array(
                    'type' => 'select',
                    'class' => 'input-xxlarge',
                    'name' => 'interface_id', 
                    'required' => true,
                    'caption' => $view->_('INTERFACE'), 
                    'placeholder' => $view->_('_NONE'), 
                    'children' => $wf(new Snippet_Folder()),
                    'default' => Snippet::importByURN('__raas_form_notify')->id,
                ),
            )
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}