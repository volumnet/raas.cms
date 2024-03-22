<?php
/**
 * Поле пользователей
 */
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
    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => User::class,
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


    public static function getSet()
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
}
