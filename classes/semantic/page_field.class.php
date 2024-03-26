<?php
/**
 * Поле страницы
 */
namespace RAAS\CMS;

/**
 * Класс поля страницы
 * @property-read Material_Type $parent Тип материала, к которому принадлежит
 *                                      поле (пустой)
 * @property-read Snippet $Preprocessor Препроцессор поля
 * @property-read Snippet $Postprocessor Постпроцессор поля
 * @property-read Page|null $Owner Страница-владелец поля (если назначена)
 */
class Page_Field extends Field
{
    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Material_Type::class,
            'cascade' => false
        ],
        'Preprocessor' => [
            'FK' => 'preprocessor_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
        'Postprocessor' => [
            'FK' => 'postprocessor_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
    ];

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


    public static function getSet(): array
    {
        $args = func_get_args();
        if (!isset($args[0]['where'])) {
            $args[0]['where'] = [];
        } else {
            $args[0]['where'] = (array)$args[0]['where'];
        }
        $args[0]['where'][] = "NOT pid";
        return call_user_func_array('parent::getSet', $args);
    }


    /**
     * Поиск поля по URN
     * @param string $urn URN для поиска
     */
    public static function importByURN($urn = '')
    {
        $sqlQuery = "SELECT *
                       FROM " . self::_tablename()
                  . " WHERE urn = ?
                        AND classname = ?
                        AND NOT pid";
        if ($sqlResult = self::$SQL->getline([
            $sqlQuery,
            $urn,
            Material_Type::class
        ])) {
            return new self($sqlResult);
        }
        return null;
    }
}
