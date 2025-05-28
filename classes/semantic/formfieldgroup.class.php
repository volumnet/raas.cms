<?php
/**
 * Группа полей формы
 */
namespace RAAS\CMS;

/**
 * Класс группы полей формы
 * @property-read Material_Type $parent Родительский объект
 */
class FormFieldGroup extends FieldGroup
{
    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Form::class,
            'cascade' => true
        ],
    ];
}
