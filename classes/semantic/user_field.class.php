<?php
/**
 * Поле пользователей
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\Text;

/**
 * Класс поля пользователей
 * @property-read User $parent Пользователь (с нулевым ID#)
 * @property-read Snippet $Preprocessor Препроцессор поля
 * @property-read Snippet $Postprocessor Постпроцессор поля
 * @property User $Owner Владелец поля
 */
class User_Field extends Field
{
    // 2024-04-19, AVS: Убрал ссылку на User, т.к. в SOME проверяется связка только по pid без учета classname
    //     И при удалении 3-го пользователя обнулялись все поля 3-й формы (даже без каскадирования)
    // 2024-05-02, AVS: вместо этого добавил правки в commit() и getSet()

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
        $this->classname = User::class;
        $this->pid = 0;
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        if ($this->updates['urn'] ?? null) {
            $this->urn = Text::beautify($this->urn);
        }
        while (in_array($this->urn, ['login', 'password', 'social', 'email'])) {
            $this->urn = '_' . $this->urn . '_';
        }
        parent::commit();
    }


    public static function getSet(): array
    {
        $args = func_get_args();
        $args[0]['where'] = (array)($args[0]['where'] ?? []);
        $args[0]['where'][] = "classname = '" . static::$SQL->real_escape_string(User::class) . "'";
        $args[0]['where'][] = "NOT pid";
        return call_user_func_array('parent::getSet', $args);
    }
}
