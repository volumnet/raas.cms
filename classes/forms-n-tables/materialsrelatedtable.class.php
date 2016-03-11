<?php
namespace RAAS\CMS;
use \RAAS\Column;

class MaterialsRelatedTable extends MaterialsTable
{
    public function __construct(array $params = array())
    {
        parent::__construct($params);
        // 2016-03-11, AVS: убрал удаление контекстного меню — непонятно, зачем удалял, клиентам неудобно админить отзывы
        unset($this->columns['priority']/*, $this->columns[' ']*/);
    }
}