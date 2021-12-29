<?php
/**
 * Поле материала
 */
namespace RAAS\CMS;

/**
 * Класс поля материала
 * @property-read Material_Type $parent Родительский тип материалов
 * @property-read Snippet $Preprocessor Препроцессор поля
 * @property-read Snippet $Postprocessor Постпроцессор поля
 * @property Material $Owner Владелец поля
 */
class Material_Field extends Field
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
                if ($val instanceof Material) {
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
        if ($pid = $this->pid) {
            unset(
                Material_Type::$selfFieldsCache[$pid],
                Material_Type::$visSelfFieldsCache[$pid],
                Material_Type::$fieldsCache[$pid],
                Material_Type::$visFieldsCache[$pid]
            );
        }
        parent::commit();
    }
}
