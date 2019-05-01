<?php
/**
 * Форма
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс формы
 * @property-read array<Form_Field> $fields Поля формы
 *                                          с установленным свойством $Owner
 * @property-read int $unreadFeedbacks Количество непрочитанных сообщений
 * @property-read Material_Type $Material_Type Тип создаваемых материалов
 * @property-read Snippet $Interface Интерфейс уведомления формы
 */
class Form extends SOME
{
    use ImportByURNTrait;

    protected static $tablename = 'cms_forms';

    protected static $defaultOrderBy = "name";

    protected static $objectCascadeDelete = true;

    protected static $cognizableVars = [
        'fields',
        'unreadFeedbacks'
    ];

    protected static $references = [
        'Material_Type' => [
            'FK' => 'material_type',
            'classname' => Material_Type::class,
            'cascade' => false
        ],
        'Interface' => [
            'FK' => 'interface_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
    ];

    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);
        parent::commit();
    }


    public static function delete(self $object)
    {
        foreach ($object->fields as $row) {
            Form_Field::delete($row);
        }
        parent::delete($object);
    }


    /**
     * Поля формы с установленным свойством $Owner
     * @return array<Form_Field>
     */
    protected function _fields()
    {
        $sqlQuery = "SELECT *
                       FROM " . Form_Field::_tablename()
                  . " WHERE classname = ?
                        AND pid = ?
                   ORDER BY priority";
        $sqlBind = [get_class($this), (int)$this->id];
        $temp = Form_Field::getSQLSet([$sqlQuery, $sqlBind]);
        $arr = [];
        foreach ($temp as $row) {
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    /**
     * Количество непрочитанных сообщений
     * @return int
     */
    protected function _unreadFeedbacks()
    {
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . Feedback::_tablename()
                  . " WHERE pid = ?
                        AND NOT vis";
        return self::$SQL->getvalue([$sqlQuery, (int)$this->id]);
    }
}
