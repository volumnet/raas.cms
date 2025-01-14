<?php
/**
 * Поле страницы
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс поля страницы
 * @property-read Material_Type $parent Тип материала, к которому принадлежит поле (пустой)
 * @property-read Snippet $Preprocessor Препроцессор поля
 * @property-read Snippet $Postprocessor Постпроцессор поля
 * @property-read Page|null $Owner Страница-владелец поля (если назначена)
 */
class Page_Field extends Field
{
    // 2024-04-19, AVS: Убрал ссылку на Material_Type по аналогии с User_Field
    //     действительно, ссылка тут не при чем
    // 2024-05-02, AVS: вместо этого добавил правки в commit() и getSet()

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


    public function commit()
    {
        $this->classname = Material_Type::class;
        $this->pid = 0;
        parent::commit();
    }


    public static function getSet(): array
    {
        $args = func_get_args();
        $args[0]['where'] = (array)($args[0]['where'] ?? []);
        $args[0]['where'][] = "classname = '" . static::$SQL->real_escape_string(Material_Type::class) . "'";
        $args[0]['where'][] = "NOT pid";
        // return call_user_func_array('parent::getSet', $args);
        $result = parent::getSet(...$args);
        return $result;
    }


    /**
     * Поиск поля по URN
     * @param string $urn URN для поиска
     */
    public static function importByURN($urn = '')
    {
        $sqlQuery = "SELECT * FROM " . self::_tablename() . " WHERE urn = ? AND classname = ? AND NOT pid";
        $sqlResult = self::$SQL->getline([$sqlQuery, $urn, Material_Type::class]);
        if ($sqlResult) {
            return new self($sqlResult);
        }
        return null;
    }
}
