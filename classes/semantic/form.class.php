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
        $id = (int)$object->id;
        foreach ($object->fields as $row) {
            Form_Field::delete($row);
        }
        parent::delete($object);
        // 2020-05-07, AVS: Удаление блоков делаем после основного,
        // иначе в методе SOME:ondelete класс Block_Form подхватывается
        // в качестве ссылки, а поскольку там ссылка на Form идет из вторичной
        // таблицы, возникает ошибка MySQL
        $sqlQuery = "SELECT id
                      FROM " . Block::_dbprefix() . "cms_blocks_form
                     WHERE form = ?";
        $blocksIds = Block_Form::_SQL()->getcol([$sqlQuery, (int)$id]);
        foreach ($blocksIds as $blockId) {
            $block = new Block_Form($blockId);
            Block_Form::delete($block);
        }
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


    /**
     * Получает подпись формы
     * @param Block $block Блок, для которого получается подпись
     * @return string
     */
    public function getSignature(Block $block)
    {
        return md5('form' . (int)$this->id . (int)$block->id);
    }
}
