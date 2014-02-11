<?php
namespace RAAS\CMS;
use \RAAS\Redirector as Redirector;
use \RAAS\Attachment as Attachment;
use \ArrayObject as ArrayObject;
use \RAAS\Field as Field;
use \RAAS\FieldSet as FieldSet;
use \RAAS\FieldContainer as FieldContainer;
use \RAAS\FormTab as FormTab;
use \RAAS\CMS\Form as CMSForm;
use \RAAS\OptGroup as OptGroup;
use \RAAS\Option as Option;
use \RAAS\StdSub as StdSub;

class Sub_Feedback extends \RAAS\Abstract_Sub_Controller
{
    protected static $instance;
    
    public function run()
    {
        switch ($this->action) {
            case 'view':
                $this->{$this->action}();
                break;
            case 'chvis': 
                $Item = new Feedback($this->id);
                StdSub::chvis($Item, $this->url);
                break;
            case 'delete':
                $Item = new Feedback($this->id);
                StdSub::delete($Item, $this->url . '&id=' . (int)$Item->pid);
                break;
            default:
                $this->feedback();
                break;
        }
    }
    
    
    protected function feedback()
    {
        $IN = $this->model->feedback();
        $Set = $IN['Set'];
        $Pages = $IN['Pages'];
        $Item = $IN['Parent'];
        $Forms = $this->model->forms();
        
        $OUT['Item'] = $Item;
        $OUT['columns'] = $IN['columns'];
        $OUT['Set'] = $Set;
        $OUT['Pages'] = $Pages;
        $OUT['Forms'] = $Forms;
        $OUT['search_string'] = isset($_GET['search_string']) ? (string)$_GET['search_string'] : '';
        $this->view->feedback($OUT);
    }
    
    
    protected function view()
    {
        $Item = new Feedback($this->id);
        $Forms = $this->model->forms();
        if (!$Item->id) {
            new Redirector(\SOME\HTTP::queryString('id=&action='));
        }
        $Item->vis = (int)$this->application->user->id;
        $Item->commit();
        $OUT['Item'] = $Item;
        $OUT['Forms'] = $Forms;
        $this->view->view($OUT);
    }
}