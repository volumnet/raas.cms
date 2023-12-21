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
        $new = !$this->id;

        // Определим старого и нового родителя в случае переноса поля
        $oldParentType = $newParentType = null;
        if (!$new &&
            ($this->updates['pid'] ?? null) &&
            ($this->properties['pid'] ?? null) &&
            ($this->updates['pid'] != $this->properties['pid'])
        ) {
            $oldParentType = new Material_Type($this->properties['pid']);
            $newParentType = new Material_Type($this->updates['pid']);
            $oldParentFormFields = $oldParentType->formFields;
            $oldParentFormFieldsIds = array_map(function ($x) {
                return (int)$x->id;
            }, $oldParentFormFields);
            $isFormField = in_array($this->id, $oldParentFormFieldsIds); // Была ли видимой в форме
        }

        if ($pid = $this->pid) {
            unset(
                Material_Type::$selfFieldsCache[$pid],
                Material_Type::$visSelfFieldsCache[$pid],
                Material_Type::$fieldsCache[$pid],
                Material_Type::$visFieldsCache[$pid]
            );
        }
        parent::commit();
        if ($new) {
            $parentType = new Material_Type($this->pid);
            $formFieldsToSet = [];
            $formFieldsToSet[trim($this->id)] = [
                'vis' => true,
                'inherit' => true,
            ];
            $parentType->setFormFieldsIds($formFieldsToSet);
        } elseif ($oldParentType && $newParentType) {
            $oldFormFieldsToSet = $newFormFieldsToSet = [];
            $oldFormFieldsToSet[trim($this->id)] = [
                'vis' => false,
                'inherit' => true,
            ];
            $oldParentType->setFormFieldsIds($oldFormFieldsToSet);
            if ($isFormField) {
                $newFormFieldsToSet[trim($this->id)] = [
                    'vis' => true,
                    'inherit' => true,
                ];
                $newParentType->setFormFieldsIds($newFormFieldsToSet);
            }
        }
    }
}
