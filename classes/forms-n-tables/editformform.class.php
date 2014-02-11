<?php
namespace RAAS\CMS;
use \RAAS\Field as RAASField;
use \RAAS\Option;

class EditFormForm extends \RAAS\Form
{
    public function __construct(array $params = array())
    {
        $view = isset($params['view']) ? $params['view'] : null;
        unset($params['view']);
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $CONTENT = array();
        $CONTENT['material_types'] = array_merge(array(new Material_Type(array('id' => 0, 'name' => $view->_('_NONE')))), (array)Material_Type::getSet());
        foreach (array('' => '_OFF', 'captcha' => 'CAPTCHA', 'hidden' => 'HIDDEN_FIELD') as $key => $val) {
            $CONTENT['antispam'][] = array('value' => $key, 'caption' => $view->_($val));
        }
        $wf = function(Snippet_Folder $x) use (&$wf) {
            $temp = array();
            foreach ($x->children as $row) {
                if ($row->urn != '__RAAS_views') {
                    $o = new Option(array('value' => '', 'caption' => $row->name, 'disabled' => 'disabled'));
                    $o->children = $wf($row);
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
            'parentUrl' => $this->url . '&action=forms',
            'children' => array(
                array('name' => 'name', 'caption' => $view->_('NAME'), 'required' => 'required'),
                array(
                    'type' => 'select', 'name' => 'material_type', 'caption' => $view->_('MATERIAL_TYPE'), 'children' => array('Set' => $CONTENT['material_types'])
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
                    'default' => $this->application->user->email
                ),
                array(
                    'type' => 'select',
                    'class' => 'input-xxlarge',
                    'name' => 'interface_id', 
                    'caption' => $view->_('INTERFACE'), 
                    'placeholder' => $view->_('_NONE'), 
                    'children' => $wf(new Snippet_Folder()),
                    'default' => Snippet::importByURN('__RAAS_form_notify')->id,
                ),
                array(
                    'type' => 'codearea', 
                    'name' => 'description', 
                    'caption' => $view->_('TEMPLATE_CODE'), 
                    'default' => $this->model->stdFormTemplate,
                    'import' => function($Field) { 
                        return $Field->Form->Item->Interface->id ? $Field->Form->Item->Interface->description : $Field->importDefault(); 
                    },
                    'export' => function($Field) {
                        $Field->Form->Item->description = '';
                        if (!(isset($_POST['interface_id']) && (int)$_POST['interface_id']) && isset($_POST['description'])) {
                            $Field->Form->Item->description = (string)$_POST['description'];
                        }
                    }, 
                )
            )
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}