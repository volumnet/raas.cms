<?php
/**
 * Группа полей страниц
 */
namespace RAAS\CMS;

/**
 * Класс группы полей страниц
 * @property-read Material_Type $parent Родительский объект
 */
class PageFieldGroup extends FieldGroup
{
    protected static $references = [];

    public static function getSet(): array
    {
        $args = func_get_args();
        $args[0]['where'] = (array)($args[0]['where'] ?? []);
        $args[0]['where'][] = "classname = '" . static::$SQL->real_escape_string(Material_Type::class) . "'";
        $args[0]['where'][] = "NOT pid";
        $sqlResult = parent::getSet(...$args);
        $result = [];
        foreach ($sqlResult as $row) {
            $result[$row->urn] = $row;
        }
        $result = array_merge(
            [
                '' => new static([
                    'classname' => Material_Type::class,
                    'pid' => 0
                ])
            ],
            $result
        );
        return $result;
    }
}
