<?php
namespace RAAS\CMS;
use \RAAS\FormTab;
use \RAAS\FieldSet;
use \RAAS\Field as RAASField;
use \ArrayObject;

class EditTemplateForm extends \RAAS\Form
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
        $NameField = new RAASField(array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'));
        $UrnField = new RAASField(array('name' => 'urn', 'caption' => $view->_('URN')));
        $DescriptionField = new RAASField(array('type' => 'codearea', 'name' => 'description', 'caption' => $this->view->_('TEMPLATE_CODE'), 'required' => 'required'));
        $BackgroundField = new RAASField(array(
            'type' => 'image', 
            'name' => 'background', 
            'caption' => $this->view->_('BACKGROUND'), 
            'meta' => array('attachmentVar' => 'Background', 'deleteAttachmentPath' => $this->view->url . '&action=delete_template_image&id=' . (int)$Item->id)
        ));
        $defaultParams = array(
            'Item' => $Item, 'caption' => $this->view->_('EDIT_TEMPLATE'), 'parentUrl' => $this->view->url . '&action=templates'
        );
        if ($Item->id) {
            $defaultParams['children'] = array(
                new FormTab(array('name' => 'edit', 'caption' => $this->view->_('EDITING'), 'children' => array($NameField, $UrnField, $DescriptionField))),
                new FormTab(array(
                    'name' => 'layout',
                    'caption' => $this->view->_('LAYOUT'),
                    'children' => array(
                        new FieldSet(array(
                            'template' => 'dev_edit_template',
                            'export' => function($FormTab) {
                                $Item = $FormTab->Form->Item;
                                foreach (array('width', 'height') as $key) {
                                    if (isset($_POST[$key]) && (int)$_POST[$key]) {
                                        $Item->$key = (int)$_POST[$key];
                                    }
                                }
                                if (isset($_POST['location'])) {
                                    $Item->locs = new ArrayObject();
                                    foreach ($_POST['location'] as $key => $val) {
                                        $Item->locs[] = array(
                                            'urn' => isset($_POST['location'][$key]) ? (string)$_POST['location'][$key] : 'Location',
                                            'x' => isset($_POST['location-left'][$key]) ? (string)$_POST['location-left'][$key] : 0,
                                            'y' => isset($_POST['location-top'][$key]) ? (string)$_POST['location-top'][$key] : 0,
                                            'width' => isset($_POST['location-width'][$key]) ? (string)$_POST['location-width'][$key] : $Item->width,
                                            'height' => isset($_POST['location-height'][$key]) ? (string)$_POST['location-height'][$key] : Location::min_height,
                                        );
                                    }
                                }
                            }
                        )),
                        $BackgroundField
                    )
                ))
            );
        } else {
            $defaultParams['children'] = array($NameField, $DescriptionField, $BackgroundField);
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}