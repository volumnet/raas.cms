<?php
/**
 * Группа полей
 */
namespace RAAS\CMS;

use SOME\SOME;
use SOME\Text;
use RAAS\Application;

/**
 * Класс группы полей
 * @property-read SOME $parent Родительский объект
 */
class FieldGroup extends SOME
{
    protected static $objectCascadeDelete = false;

    protected static $defaultOrderBy = "priority";

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Material_Type::class,
            'cascade' => true
        ],
    ];

    protected static $tablename = 'cms_fieldgroups';

    public function commit()
    {
        if (!$this->id || !$this->priority) {
            $sqlQuery = "SELECT MAX(priority) FROM " . static::_tablename();
            $this->priority = static::$SQL->getvalue($sqlQuery) + 1;
        }
        if (!$this->urn && $this->name) {
            $this->urn = Text::beautify($this->name);
        }
        if (!$this->classname) {
            $this->classname = static::$references['parent']['classname'];
        }
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . static::_tablename() . "
                      WHERE urn = ?
                        AND classname = ?
                        AND pid = ?
                        AND id != ?";
        while ((int)static::$SQL->getvalue([
                $sqlQuery,
                $this->urn,
                $this->classname,
                (int)$this->pid,
                (int)$this->id
            ])
        ) {
            $this->urn = '_' . $this->urn . '_';
        }
        parent::commit();
    }


    /**
     * Возвращает поля группы
     * @param SOME $parent Родительский объект
     * @return Field[]
     */
    public function getFields(SOME $parent)
    {
        return array_filter($parent->fields, function ($field) {
            return (int)$field->gid == (int)$this->id;
        });
    }


    /**
     * Возвращает видимые поля группы
     * @param SOME $parent Родительский объект
     * @return Field[]
     */
    public function getVisFields(SOME $parent)
    {
        return array_filter($parent->visFields, function ($field) {
            return (int)$field->gid == (int)$this->id;
        });
    }


    public static function delete(SOME $object)
    {
        static::$SQL->update(
            Field::_tablename(),
            "gid = " . (int)$object->id,
            ['gid' => 0]
        );
        parent::delete($object);
    }
}
