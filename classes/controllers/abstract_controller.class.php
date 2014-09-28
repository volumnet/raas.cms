<?php
namespace RAAS\CMS;
use \RAAS\Application as Application;
use \RAAS\Redirector as Redirector;
use \RAAS\Attachment as Attachment;
use \ArrayObject as ArrayObject;
use \RAAS\Field as Field;
use \RAAS\FieldSet as FieldSet;
use \RAAS\FieldContainer as FieldContainer;
use \RAAS\Form as Form;
use \RAAS\FormTab as FormTab;
use \RAAS\CMS\Form as CMSForm;
use \RAAS\OptGroup as OptGroup;
use \RAAS\Option as Option;
use \RAAS\StdSub as StdSub;

abstract class Abstract_Controller extends \RAAS\Abstract_Package_Controller
{
    protected static $instance;
    
    protected function execute()
    {
        switch ($this->sub) {
            case 'dev': case 'feedback':
                parent::execute();
                break;
            default:
                Sub_Main::i()->run();
                break;
        }
        $this->model->cleanCache();
    }
    
    
    public function config()
    {
        return array(
            array('type' => 'number', 'name' => 'tnsize', 'caption' => $this->view->_('THUMBNAIL_SIZE')),
            array('type' => 'number', 'name' => 'maxsize', 'caption' => $this->view->_('MAX_IMAGE_SIZE'))
        );
    }
}