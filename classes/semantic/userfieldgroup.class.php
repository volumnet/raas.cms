<?php
/**
 * Группа полей пользователей
 */
namespace RAAS\CMS;

/**
 * Класс группы полей пользователей
 * @property-read Material_Type $parent Родительский объект
 */
class UserFieldGroup extends FieldGroup
{
    const DEFAULT_CLASSNAME = User::class;

    protected static $references = [];

    public static function getSet(): array
    {
        $args = func_get_args();
        $args[0]['where'] = (array)($args[0]['where'] ?? []);
        $args[0]['where'][] = "classname = '" . static::$SQL->real_escape_string(User::class) . "'";
        $args[0]['where'][] = "NOT pid";
        $sqlResult = parent::getSet(...$args);
        $result = [];
        foreach ($sqlResult as $row) {
            $result[$row->urn] = $row;
        }
        $result = array_merge(
            [
                '' => new static([
                    'classname' => User::class,
                    'pid' => 0
                ])
            ],
            $result
        );
        return $result;
    }
}
