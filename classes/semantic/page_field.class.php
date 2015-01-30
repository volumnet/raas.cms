<?php
namespace RAAS\CMS;

class Page_Field extends Field
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
                if ($val instanceof Page) {
                    $this->Owner = $val;
                }
                break;
            default:
                return parent::__set($var, $val);
                break;
        }
    }

    public static function getSet()
    {
        $args = func_get_args();
        if (!isset($args[0]['where'])) {
            $args[0]['where'] = array();
        } else {
            $args[0]['where'] = (array)$args[0]['where'];
        }
        $args[0]['where'][] = "NOT pid";
        return call_user_func_array('parent::getSet', $args);
    }
}