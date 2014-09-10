<?php
namespace RAAS\CMS;
use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\Field as RAASField;
use \RAAS\Option;
use \RAAS\FieldSet;

class EditFieldForm extends \RAAS\Form
{
    protected $_view;

    public function __construct(array $params = array())
    {
        $this->_view = isset($params['view']) ? $params['view'] : null;
        unset($params['view']);
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $Parent = isset($params['meta']['Parent']) ? $params['meta']['Parent'] : null;
        $parentUrl = $params['meta']['parentUrl'];
        $CONTENT = array();
        foreach (\RAAS\CMS\Field::$fieldTypes as $key) {
            $CONTENT['datatypes'][] = array('value' => $key, 'caption' => $this->_view->_('DATATYPE_' . str_replace('-', '_', strtoupper($key))));
        }
        foreach (\RAAS\CMS\Field::$sourceTypes as $key) {
            $CONTENT['sourcetypes'][] = array(
                'value' => $key, 'caption' => $this->_view->_('SOURCETYPE_' . strtoupper($key)), 'data-hint' => $this->_view->_('SOURCETYPE_' . strtoupper($key) . '_HINT')
            );
        }
        $temp = new Dictionary();
        $CONTENT['dictionaries'] = array('Set' => array_merge(array(new Dictionary(array('id' => 0, 'name' => $this->_view->_('SELECT_DICTIONARY')))), $temp->children), 'level' => 0);
        $defaultParams = array(
            'caption' => $Item->id ? $Item->name : $this->_view->_('CREATING_FIELD'),
            'parentUrl' => $parentUrl,
            'export' => function($Form) use ($Item, $Parent) {
                $Form->exportDefault();
                if (!$Form->Item->id && isset($Parent) && $Parent && $Parent->id) {
                    $Form->Item->pid = (int)$Parent->id;
                }
            },
            'children' => array(
                array('name' => 'name', 'caption' => $this->_view->_('NAME'), 'required' => 'required'),
                array('name' => 'urn', 'caption' => $this->_view->_('URN')),
                array('type' => 'checkbox', 'name' => 'required', 'caption' => $this->_view->_('REQUIRED')),
                array('type' => 'checkbox', 'name' => 'multiple', 'caption' => $this->_view->_('MULTIPLE')),
                array('type' => 'number', 'name' => 'maxlength', 'caption' => $this->_view->_('MAXLENGTH')),
                array('type' => 'select', 'name' => 'datatype', 'caption' => $this->_view->_('DATATYPE'), 'children' => $CONTENT['datatypes'], 'default' => 'text'),
                array('type' => 'select', 'name' => 'source_type', 'caption' => $this->_view->_('SOURCETYPE'), 'children' => $CONTENT['sourcetypes'], 'data-hint' => ''),
                array(
                    'name' => 'source', 
                    'caption' => $this->_view->_('SOURCE'), 
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
                    'caption' => $this->_view->_('RANGE'),
                    'children' => array(
                        array('type' => 'number', 'name' => 'min_val', 'class' => 'span1'), array('type' => 'number', 'name' => 'max_val', 'class' => 'span1')
                    )
                )),
                array('name' => 'placeholder', 'caption' => $this->_view->_('PLACEHOLDER')),
                array('type' => 'checkbox', 'name' => 'show_in_table', 'caption' => $this->_view->_('SHOW_IN_TABLE'))
            )
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}