<?php
namespace RAAS\CMS;

class Block_Type
{
    const defaultBlockType = 'RAAS\\CMS\\HTML';

    protected $className;
    protected $viewerClassName;
    protected $editFormClassName;

    protected static $_types = array();

    protected function __construct()
    {}


    public function __get($var)
    {
        switch ($var) {
            case 'block':
                $classname = $this->className;
                if (class_exists($classname)) {
                    return new $classname();
                }
                break;
            case 'viewer':
                $classname = $this->viewerClassName;
                if (class_exists($classname)) {
                    return new $classname();
                }
                break;
        }
    }


    public static function registerType($className, $viewerClassName, $editFormClassName)
    {
        $Item = new static();
        if (!class_exists($className)) {
            throw new \Exception($className . ' doesn\'t exist');
        }
        if (!class_exists($viewerClassName)) {
            throw new \Exception($viewerClassName . ' doesn\'t exist');
        }
        if (!class_exists($editFormClassName)) {
            throw new \Exception($editFormClassName . ' doesn\'t exist');
        }
        $Item->className = $className;
        $Item->viewerClassName = $viewerClassName;
        $Item->editFormClassName = $editFormClassName;
        self::$_types[$className] = $Item;
    }


    public static function unregisterType($className)
    {
        unset(self::$_types[$className]);
    }


    public static function getTypes()
    {
        return self::$_types;
    }


    public static function getType($className)
    {
        if (isset(self::$_types[$className])) {
            return self::$_types[$className];
        } elseif (isset(self::$_types[$className = self::defaultBlockType])) {
            return self::$_types[$className];
        }
        return null;
    }


    public function getForm(array $arr = array())
    {
        $classname = $this->editFormClassName;
        return new $classname($arr);
    }
}
