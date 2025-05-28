<?php
/**
 * Группа полей типа материалов
 */
namespace RAAS\CMS;

/**
 * Класс группы полей типа материалов
 * @property-read Material_Type $parent Родительский объект
 */
class MaterialFieldGroup extends FieldGroup
{
    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Material_Type::class,
            'cascade' => true
        ],
    ];

    /**
     * Возвращает собственные поля группы
     * @param Material_Type $parent Родительский объект
     * @return Field[]
     */
    public function getSelfFields(Material_Type $parent): array
    {
        return array_filter($parent->selfFields, function ($field) {
            return (int)$field->gid == (int)$this->id;
        });
    }


    /**
     * Возвращает видимые собственные поля группы
     * @param Material_Type $parent Родительский объект
     * @return Field[]
     */
    public function getVisSelfFields(Material_Type $parent): array
    {
        return array_filter($parent->visSelfFields, function ($field) {
            return (int)$field->gid == (int)$this->id;
        });
    }


    /**
     * Возвращает поля группы для отображения в форме
     * @param Material_Type $parent Родительский объект
     * @return Field[]
     */
    public function getFormFields(Material_Type $parent): array
    {
        return array_filter($parent->formFields, function ($field) {
            return (int)$field->gid == (int)$this->id;
        });
    }


    /**
     * Возвращает ID# полей группы для отображения в форме
     * @param Material_Type $parent Родительский объект
     * @return int[]
     */
    public function getFormFieldsIds(Material_Type $parent): array
    {
        return array_map(function ($field) {
            return (int)$field->id;
        }, $this->getFormFields($parent));
    }
}
