<?php
namespace RAAS\CMS;
use \RAAS\Field as RAASField;
use \RAAS\Option;
use \RAAS\Application;

class EditMaterialTypeForm extends \RAAS\Form
{
    const CREATE_MATERIAL_TYPE_NONE = 0;
    const CREATE_MATERIAL_TYPE_SIMPLE = 1;
    const CREATE_MATERIAL_TYPE_EXTENDED = 2;
    const CREATE_MATERIAL_TYPE_BOTH = 3;

    const MATERIAL_TYPE_TEMPLATE_NONE = '';
    const MATERIAL_TYPE_TEMPLATE_NEWS = 'news';
    const MATERIAL_TYPE_TEMPLATE_BANNERS = 'banners';
    
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
            'oncommit' => function() use ($view, $Item) {
                if ($_POST['template']) {
                    switch ($_POST['template']) {
                        case self::MATERIAL_TYPE_TEMPLATE_NEWS:
                            $dateField = new Material_Field(array('pid' => $Item->id, 'name' => $this->view->_('DATE'), 'urn' => 'date', 'datatype' => 'date', 'show_in_table' => 1,));
                            $dateField->commit();

                            $F = new Material_Field(array('pid' => $Item->id, 'name' => $this->view->_('IMAGE'), 'multiple' => 1, 'urn' => 'images', 'datatype' => 'image',));
                            $F->commit();

                            $F = new Material_Field(array('pid' => $Item->id, 'name' => $this->view->_('BRIEF_TEXT'), 'multiple' => 0, 'urn' => 'brief', 'datatype' => 'textarea',));
                            $F->commit();
                            break;
                        case self::MATERIAL_TYPE_TEMPLATE_BANNERS:
                            $F = new Material_Field(array('pid' => $Item->id, 'name' => $this->view->_('URL'), 'urn' => 'url', 'datatype' => 'text', 'show_in_table' => 1,));
                            $F->commit();

                            $F = new Material_Field(array('pid' => $Item->id, 'name' => $this->view->_('IMAGE'), 'urn' => 'image', 'datatype' => 'image',));
                            $F->commit();
                            break;
                    }
                }
                if ($_POST['add_snippet']) {
                    $add = (int)$_POST['add_snippet'];
                    $urn = $Item->urn;
                    $pid = Snippet_Folder::importByURN('__raas_views')->id;
                    $name = $Item->name;
                    if ($add | self::CREATE_MATERIAL_TYPE_SIMPLE) {
                        $f = Package::i()->resourcesDir . '/material_main.tmp.php';
                        $text = file_get_contents($f);
                        $text = str_ireplace('{BLOCK_NAME}', $urn . '_main', $text);
                        $text = str_ireplace('{MATERIAL_NAME}', $name, $text);

                        $s = new Snippet();
                        $s->pid = $pid;
                        $s->urn = $urn . '_main';
                        $s->name = $name . ' ' . $view->_('FOR_MAIN');
                        $s->description = $text;
                        $s->commit();
                    }
                    if ($add | self::CREATE_MATERIAL_TYPE_EXTENDED) {
                        $f = Package::i()->resourcesDir . '/material.tmp.php';
                        $text = file_get_contents($f);
                        $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
                        $text = str_ireplace('{MATERIAL_NAME}', $name, $text);

                        $s = new Snippet();
                        $s->pid = $pid;
                        $s->urn = $urn;
                        $s->name = $name;
                        $s->description = $text;
                        $s->commit();
                    }
                }
            },
            'children' => array(
                array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'),
                array('name' => 'urn', 'caption' => $this->view->_('URN')),
                array('type' => 'checkbox', 'name' => 'global_type', 'caption' => $this->view->_('GLOBAL_MATERIALS'))
            )
        );
        if (!$Item->id) {
            $defaultParams['children']['template'] = array(
                'type' => 'select', 
                'name' => 'template', 
                'caption' => $this->view->_('MATERIAL_TYPE_TEMPLATE'),
                'children' => array(
                    array('value' => self::MATERIAL_TYPE_TEMPLATE_NONE, 'caption' => $this->view->_('_NONE')),
                    array('value' => self::MATERIAL_TYPE_TEMPLATE_NEWS, 'caption' => $this->view->_('NEWS')),
                    array('value' => self::MATERIAL_TYPE_TEMPLATE_BANNERS, 'caption' => $this->view->_('BANNERS')),
                )
            );
            $defaultParams['children']['add_snippet'] = array(
                'type' => 'select', 
                'name' => 'add_snippet', 
                'caption' => $this->view->_('ADD_SNIPPET_FOR_THIS_TYPE'),
                'children' => array(
                    array('value' => self::CREATE_MATERIAL_TYPE_NONE, 'caption' => $this->view->_('_NONE')),
                    array('value' => self::CREATE_MATERIAL_TYPE_SIMPLE, 'caption' => $this->view->_('ADD_SNIPPET_FOR_THIS_TYPE_SIMPLE_FOR_MAIN')),
                    array('value' => self::CREATE_MATERIAL_TYPE_EXTENDED, 'caption' => $this->view->_('ADD_SNIPPET_FOR_THIS_TYPE_EXTENDED')),
                    array('value' => self::CREATE_MATERIAL_TYPE_BOTH, 'caption' => $this->view->_('ADD_SNIPPET_FOR_THIS_TYPE_BOTH'))
                )
            );
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}