<?php
/**
 * Тип блока
 */
namespace RAAS\CMS;

use RAAS\Exception;

/**
 * Класс типа блока
 * @property-read Block Новый блок соответствующего типа
 * @property-read ViewBlock Представление блока
 */
class Block_Type
{
    /**
     * Тип блока по умолчанию
     */
    const defaultBlockType = Block_HTML::class;

    /**
     * Класс блока
     * @var string
     */
    protected $className;

    /**
     * Класс представления
     * @var string
     */
    protected $viewerClassName;

    /**
     * Класс формы редактирования блока
     * @var string
     */
    protected $editFormClassName;

    /**
     * Зарегистрированные типы блоков
     * @var array<static>
     */
    protected static $_types = [];

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


    /**
     *
     * @param string $className Класс блока
     * @param string $viewerClassName Класс представления
     * @param string $editFormClassName Класс формы редактирования
     * @throws Exception Выбрасывает исключение, когда класс блока,
     *                   представления или формы редактирования не найден
     */
    public static function registerType(
            $className,
            $viewerClassName,
            $editFormClassName
    ) {
        $Item = new static();
        if (!class_exists($className)) {
            throw new Exception($className . ' doesn\'t exist');
        }
        if (!class_exists($viewerClassName)) {
            throw new Exception($viewerClassName . ' doesn\'t exist');
        }
        if (!class_exists($editFormClassName)) {
            throw new Exception($editFormClassName . ' doesn\'t exist');
        }
        $Item->className = $className;
        $Item->viewerClassName = $viewerClassName;
        $Item->editFormClassName = $editFormClassName;
        self::$_types[$className] = $Item;
    }


    /**
     * Отменить регистрацию блока
     * @param string $className Класс блока
     */
    public static function unregisterType($className)
    {
        unset(self::$_types[$className]);
    }


    /**
     * Получить список типов блоков
     * @return array<static>
     */
    public static function getTypes()
    {
        return self::$_types;
    }


    /**
     * Получает тип для класса блока
     * @param string $className Класс блока
     * @return static
     */
    public static function getType($className)
    {
        if (isset(self::$_types[$className])) {
            return self::$_types[$className];
        } elseif (isset(self::$_types[$className = self::defaultBlockType])) {
            return self::$_types[$className];
        }
        return null;
    }


    /**
     * Получает форму редактирования блока
     * @param array $arr Параметры создания формы
     * @return Form
     */
    public function getForm(array $arr = [])
    {
        $classname = $this->editFormClassName;
        return new $classname($arr);
    }
}
