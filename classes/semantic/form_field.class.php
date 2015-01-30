<?php
namespace RAAS\CMS;

class Form_Field extends Field
{
    protected static $references = array(
        'parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Form', 'cascade' => false),
        'Preprocessor' => array('FK' => 'preprocessor_id', 'classname' => 'RAAS\\CMS\\Snippet', 'cascade' => false),
        'Postprocessor' => array('FK' => 'postprocessor_id', 'classname' => 'RAAS\\CMS\\Snippet', 'cascade' => false),
    );
    
    public function __set($var, $val)
    {
        switch ($var) {
            case 'Owner':
                if ($val instanceof Feedback) {
                    $this->Owner = $val;
                }
                break;
            default:
                return parent::__set($var, $val);
                break;
        }
    }
}