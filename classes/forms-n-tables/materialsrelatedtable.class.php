<?php
namespace RAAS\CMS;
use \RAAS\Column;

class MaterialsRelatedTable extends MaterialsTable
{
    public function __construct(array $params = array())
    {
        parent::__construct($params);
        unset($this->columns['priority'], $this->columns[' ']);
    }
}