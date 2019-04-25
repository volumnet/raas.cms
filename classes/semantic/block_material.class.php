<?php
namespace RAAS\CMS;

use RAAS\User as RAASUser;

class Block_Material extends Block
{
    protected static $tablename2 = 'cms_blocks_material';

    protected static $references = [
        'author' => [
            'FK' => 'author_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
        'editor' => [
            'FK' => 'editor_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
        'Material_Type' => [
            'FK' => 'material_type',
            'classname' => Material_Type::class,
            'cascade' => true
        ],
    ];

    public static $filterRelations = [
        '=' => 'EQUALS',
        'LIKE' => 'CONTAINS',
        'CONTAINED' => 'CONTAINED',
        'FULLTEXT' => 'FULLTEXT',
        '<=' => 'EQUALS_OR_SMALLER',
        '>=' => 'EQUALS_OR_GREATER'
    ];

    public static $orderRelations = [
        'asc!' => 'ASCENDING_ONLY',
        'desc!' => 'DESCENDING_ONLY',
        'asc' => 'ASCENDING_FIRST',
        'desc' => 'DESCENDING_FIRST'
    ];

    public function __construct($import_data = null)
    {
        parent::__construct($import_data);
        $sqlQuery = "SELECT var, relation, field
                       FROM " . self::$dbprefix . "cms_blocks_material_filter
                      WHERE id = " . (int)$this->id
                  . " ORDER BY priority";
        $this->filter = self::$SQL->get($sqlQuery);
        $sqlQuery = "SELECT var, field, relation
                       FROM " . self::$dbprefix . "cms_blocks_material_sort
                      WHERE id = " . (int)$this->id
                  . " ORDER BY priority";
        $this->sort = self::$SQL->get($sqlQuery);
    }


    public function commit()
    {
        if ($this->id &&
            $this->updates['material_type'] &&
            ($this->updates['material_type'] != $this->properties['material_type'])
        ) {
            $oldMaterialTypeId = $this->properties['material_type'];
        }
        if (!$this->name && $this->Material_Type->id) {
            $this->name = $this->Material_Type->name;
        }
        parent::commit();
        $sqlQuery = "DELETE FROM " . self::$dbprefix . "cms_blocks_material_filter
                      WHERE id = " . (int)$this->id;
        self::$SQL->query($sqlQuery);
        $arr = [];
        if ($this->filter && is_array($this->filter)) {
            for ($i = 0; $i < count($this->filter); $i++) {
                if ($row = $this->filter[$i]) {
                    $arr[] = [
                        'id' => (int)$this->id,
                        'var' => (string)$row['var'],
                        'relation' => (string)$row['relation'],
                        'field' => (string)$row['field'],
                        'priority' => ($i + 1)
                    ];
                }
            }
        }
        if ($arr) {
            self::$SQL->add(
                self::$dbprefix . "cms_blocks_material_filter",
                $arr
            );
        }

        $sqlQuery = "DELETE FROM " . self::$dbprefix . "cms_blocks_material_sort
                      WHERE id = " . (int)$this->id;
        self::$SQL->query($sqlQuery);
        $arr = [];
        if ($this->sort && is_array($this->sort)) {
            for ($i = 0; $i < count($this->sort); $i++) {
                if ($row = $this->sort[$i]) {
                    $arr[] = [
                        'id' => (int)$this->id,
                        'var' => (string)$row['var'],
                        'field' => (string)$row['field'],
                        'relation' => (string)$row['relation'],
                        'priority' => ($i + 1)
                    ];
                }
            }
        }
        if ($arr) {
            self::$SQL->add(self::$dbprefix . "cms_blocks_material_sort", $arr);
        }

        // 2019-04-25, AVS: обновим связанные страницы
        if ($oldMaterialTypeId) {
            $oldMaterialType = new Material_Type($oldMaterialTypeId);
            Material_Type::updateAffectedPagesForMaterials($oldMaterialType);
            Material_Type::updateAffectedPagesForSelf($oldMaterialType);
        }
        Material_Type::updateAffectedPagesForMaterials($this->material_type);
        Material_Type::updateAffectedPagesForSelf($this->material_type);
    }


    public function getAddData()
    {
        return [
            'id' => (int)$this->id,
            'material_type' => (int)$this->material_type,
            'pages_var_name' => (string)$this->pages_var_name,
            'rows_per_page' => (int)$this->rows_per_page,
            'sort_var_name' => (string)$this->sort_var_name,
            'order_var_name' => (string)$this->order_var_name,
            'sort_field_default' => (string)$this->sort_field_default,
            'sort_order_default' => (string)$this->sort_order_default,
            'legacy' => (int)$this->legacy,
        ];
    }


    public static function delete(self $block)
    {
        $mtype = new Material_Type($block->material_type);
        parent::delete($block);
        Material_Type::updateAffectedPagesForMaterials($mtype);
        Material_Type::updateAffectedPagesForSelf($mtype);
    }
}
