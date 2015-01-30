<?php
namespace RAAS\CMS;

class Material_Field extends Field
{
    protected static $references = array(
        'parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Material_Type', 'cascade' => false),
        'Preprocessor' => array('FK' => 'preprocessor_id', 'classname' => 'RAAS\\CMS\\Snippet', 'cascade' => false),
        'Postprocessor' => array('FK' => 'postprocessor_id', 'classname' => 'RAAS\\CMS\\Snippet', 'cascade' => false),
    );
    
    public function __set($var, $val)
    {
        switch ($var) {
            case 'Owner':
                if ($val instanceof Material) {
                    $this->Owner = $val;
                }
                break;
            default:
                return parent::__set($var, $val);
                break;
        }
    }
}