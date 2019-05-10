<?php
/**
 * Таблица связанных материалов
 */
namespace RAAS\CMS;

/**
 * Класс таблицы связанных материалов
 */
class MaterialsRelatedTable extends MaterialsTable
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        // 2016-03-11, AVS: убрал удаление контекстного меню —
        // непонятно, зачем удалял, клиентам неудобно админить отзывы
        unset($this->columns['priority']/*, $this->columns[' ']*/);
    }
}
