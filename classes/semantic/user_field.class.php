<?php
namespace RAAS\CMS;
use \RAAS\CMS\Field;

class User_Field extends Field
{
    protected static $references = array('parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\User', 'cascade' => false));
    
    public function __set($var, $val)
    {
        switch ($var) {
            case 'Owner':
                if ($val instanceof User) {
                    $this->Owner = $val;
                }
                break;
            default:
                return parent::__set($var, $val);
                break;
        }
    }


    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = \SOME\Text::beautify($this->name);
        }
        while (in_array($this->urn, array('login', 'password', 'social', 'email'))) {
            $this->urn = '_' . $this->urn . '_';
        }
        parent::commit();
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