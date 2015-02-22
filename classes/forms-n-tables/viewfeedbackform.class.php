<?php
namespace RAAS\CMS;
use \RAAS\Form as RAASForm;
use \RAAS\FormTab;
use \RAAS\Field as RAASField;
use \RAAS\FieldSet;

class viewFeedbackForm extends RAASForm
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Feedback::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $defaultParams = $this->getParams();
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
        $this->__set('children', $this->getChildren());
    }


    protected function getParams()
    {
        $arr = array();
        $arr['caption'] = $this->view->_('FEEDBACK');
        $arr['template'] = 'cms/feedback_view';
        return $arr;
    }


    protected function getChildren()
    {
        return $this->getDetails();
    }


    protected function getDetails()
    {
        $arr = array();
        $arr['post_date'] = $this->getFeedbackField(array('name' => 'post_date', 'caption' => $this->view->_('POST_DATE')));
        $arr = array_merge($arr, $this->getDetailsFields());
        $arr['pid'] = array('name' => 'pid', 'caption' => $this->view->_('FORM'), 'template' => 'cms/feedback_view.form_field.inc.php');
        $arr = array_merge($arr, $this->getStat());
        return $arr;
    }


    protected function getDetailsFields()
    {
        $arr = array();
        foreach ($this->Item->fields as $field) {
            $arr['field.' . $field->urn] = $this->getFeedbackField(array(
                'name' => 'field.' . $field->urn,
                'caption' => $field->name,
                'meta' => array('Field' => $field),
            ));
        }
        return $arr;
    }


    protected function getStat()
    {
        $arr = array();
        if ($this->Item->uid) {
            $arr['uid'] = array(
                'name' => 'uid',
                'caption' => $this->view->_('USER'),
                'template' => 'cms/feedback_view.field.inc.php',
            );
        }
        $arr['page_id'] = array(
            'name' => 'page_id',
            'caption' => $this->view->_('PAGE'),
            'template' => 'cms/feedback_view.field.inc.php',
        );
        if ($this->Item->viewer->id) {
            $arr['vis'] = array(
                'name' => 'vis',
                'caption' => $this->view->_('VIEWED_BY'),
                'template' => 'cms/feedback_view.field.inc.php',
            );
        }
        $arr['ip'] = array(
            'name' => 'ip',
            'caption' => $this->view->_('IP_ADDRESS'),
            'template' => 'cms/feedback_view.field.inc.php',
        );
        $arr['user_agent'] = array(
            'name' => 'user_agent',
            'caption' => $this->view->_('USER_AGENT'),
            'template' => 'cms/feedback_view.field.inc.php',
        );
        return $arr;
    }


    protected function getFeedbackField(array $params = array())
    {
        $defaultParams = array('template' => 'cms/feedback_view.field.inc.php');
        $arr = array_merge($defaultParams, $params);
        return new RAASField($arr);
    }


}