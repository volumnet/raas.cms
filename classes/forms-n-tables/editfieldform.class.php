<?php
namespace RAAS\CMS;
use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\Field as RAASField;
use \RAAS\Option;
use \RAAS\FieldSet;

class EditFieldForm extends \RAAS\Form
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
        $Parent = isset($params['meta']['Parent']) ? $params['meta']['Parent'] : null;
        $parentUrl = $params['meta']['parentUrl'];
        $CONTENT = array();
        foreach (\RAAS\CMS\Field::$fieldTypes as $key) {
            $CONTENT['datatypes'][] = array('value' => $key, 'caption' => $this->view->_('DATATYPE_' . str_replace('-', '_', strtoupper($key))));
        }
        $CONTENT['datatypes'][] = array('value' => 'material', 'caption' => $this->view->_('DATATYPE_MATERIAL'));
        foreach (\RAAS\CMS\Field::$sourceTypes as $key) {
            $CONTENT['sourcetypes'][] = array(
                'value' => $key, 'caption' => $this->view->_('SOURCETYPE_' . strtoupper($key)), 'data-hint' => $this->view->_('SOURCETYPE_' . strtoupper($key) . '_HINT')
            );
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

        $temp = new Dictionary();
        $CONTENT['dictionaries'] = array('Set' => array_merge(array(new Dictionary(array('id' => 0, 'name' => $this->view->_('SELECT_DICTIONARY')))), $temp->children), 'level' => 0);
        $defaultParams = array(
            'caption' => $Item->id ? $Item->name : $this->view->_('CREATING_FIELD'),
            'parentUrl' => $parentUrl,
            'export' => function($Form) use ($Item, $Parent) {
                $Form->exportDefault();
                if (!$Form->Item->id && isset($Parent) && $Parent && $Parent->id) {
                    $Form->Item->pid = (int)$Parent->id;
                }
            },
            'children' => array(
                array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'),
                array('name' => 'urn', 'caption' => $this->view->_('URN')),
                array('type' => 'checkbox', 'name' => 'required', 'caption' => $this->view->_('REQUIRED')),
                array('type' => 'checkbox', 'name' => 'multiple', 'caption' => $this->view->_('MULTIPLE')),
                array('type' => 'number', 'name' => 'maxlength', 'caption' => $this->view->_('MAXLENGTH')),
                array('type' => 'select', 'name' => 'datatype', 'caption' => $this->view->_('DATATYPE'), 'children' => $CONTENT['datatypes'], 'default' => 'text'),
                array('type' => 'select', 'name' => 'source_type', 'caption' => $this->view->_('SOURCETYPE'), 'children' => $CONTENT['sourcetypes'], 'data-hint' => ''),
                array(
                    'name' => 'source',
                    'caption' => $this->view->_('SOURCE'),
                    'template' => 'cms/dev_edit_field.source.tmp.php',
                    'check' => function ($Field) {
                        if (in_array($_POST['datatype'], array('select', 'radio')) || (($_POST['datatype'] == 'checkbox') && isset($_POST['multiple']))) {
                            if ((!isset($_POST['source_type']) || !trim($_POST['source_type'])) || (!isset($_POST['source']) || !trim($_POST['source']))) {
                                return array('name' => 'MISSED', 'value' => 'source', 'description' => 'ERR_NO_DATA_SOURCE');
                            }
                        }
                    },
                    'children' => $CONTENT['dictionaries']
                ),
                new FieldSet(array(
                    'template' => 'cms/dev_edit_field.range.tmp.php',
                    'caption' => $this->view->_('RANGE'),
                    'children' => array(
                        array('type' => 'number', 'name' => 'min_val', 'class' => 'span1'),
                        array('type' => 'number', 'name' => 'max_val', 'class' => 'span1'),
                        array('type' => 'text', 'name' => 'step', 'class' => 'span1', 'default' => 1),
                    )
                )),
                array('name' => 'defval', 'caption' => $this->view->_('DEFAULT_VALUE')),
                array('name' => 'placeholder', 'caption' => $this->view->_('PLACEHOLDER')),
                array('name' => 'pattern', 'caption' => $this->view->_('PATTERN')),
                array(
                    'type' => 'select',
                    'class' => 'input-xxlarge',
                    'name' => 'preprocessor_id',
                    'caption' => $this->view->_('PREPROCESSOR'),
                    'placeholder' => $this->view->_('_NONE'),
                    'children' => $wf(new Snippet_Folder())
                ),
                array(
                    'type' => 'select',
                    'class' => 'input-xxlarge',
                    'name' => 'postprocessor_id',
                    'caption' => $this->view->_('POSTPROCESSOR'),
                    'placeholder' => $this->view->_('_NONE'),
                    'children' => $wf(new Snippet_Folder())
                ),
                array('type' => 'checkbox', 'name' => 'show_in_table', 'caption' => $this->view->_('SHOW_IN_TABLE'))
            )
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
