<?php
/**
 * Тип материалов
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс типа материалов
 * @property-read Material_Type $parent Родительский тип
 * @property-read array<Material_Type> $parents Список родительских типов
 * @property-read array<Material_Type> $children Список дочерних типов
 * @property-read array<Page> $affectedPages Список связанных страниц
 * @property-read array<Material_Field> $fields Список полей
 * @property-read array<Material_Fiel> $selfFields Список собственных полей
 *                                                 (без учета родительских)
 * @property-read array<Material_Type> $selfAndChildren Тип и все родительские
 * @property-read array<int> $selfAndChildrenIds ID# типа и всех родительских
 * @property-read array<Material_Type> $selfAndParents Тип и все дочерние
 * @property-read array<int> $selfAndParentsIds ID# типа и всех дочерних
 */
class Material_Type extends SOME
{
    use RecursiveTrait;
    use ImportByURNTrait;

    protected static $tablename = 'cms_material_types';

    protected static $defaultOrderBy = "name";

    protected static $objectCascadeDelete = true;

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Material_Type::class,
            'cascade' => true
        ],
    ];

    protected static $parents = [
        'parents' => 'parent'
    ];

    protected static $children = [
        'children' => [
            'classname' => Material_Type::class,
            'FK' => 'pid'
        ]
    ];

    protected static $links = [
        'affectedPages' => [
            'tablename' => 'cms_material_types_affected_pages_for_self_cache',
            'field_from' => 'material_type_id',
            'field_to' => 'page_id',
            'classname' => Page::class
        ],
    ];

    protected static $cognizableVars = [
        'fields',
        'selfFields',
        'selfAndChildren',
        'selfAndChildrenIds',
        'selfAndParents',
        'selfAndParentsIds',
    ];

    public function commit()
    {
        $new = !$this->id;
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);
        $globDirection = 0; // Направление глобализации
        if (isset($this->updates['global_type'])) {
            if ($this->properties['global_type'] &&
                !$this->updates['global_type']
            ) {
                $globDirection = -1;
            } elseif (!$this->properties['global_type'] &&
                $this->updates['global_type']
            ) {
                $globDirection = 1;
            }
        }
        parent::commit();
        if ($globDirection) {
            $sqlQuery = "SELECT id
                            FROM " . Material::_tablename()
                       . " WHERE pid = " . (int)$this->id;
            $materialsIds = static::_SQL()->getcol($sqlQuery);
            if ($globDirection == -1) {
                $pagesIds = array_map(function ($x) {
                    return (int)$x->id;
                }, $this->affectedPages);

                $arr = [];
                foreach ($pagesIds as $pageId) {
                    foreach ($materialsIds as $materialId) {
                        $arr[] = [
                            'id' => (int)$materialId,
                            'pid' => (int)$pageId
                        ];
                    }
                }
                if ($arr) {
                    static::_SQL()->add('cms_materials_pages_assoc', $arr);
                }
            } elseif ($globDirection == 1) {
                if ($materialsIds) {
                    $sqlQuery = "DELETE FROM cms_materials_pages_assoc
                                  WHERE id IN (" . implode(", ", $materialsIds) . ")";
                    static::_SQL()->query($sqlQuery);
                }
            }
            foreach ($this->children as $row) {
                if ($row->global_type != $this->global_type) {
                    $row->global_type = (int)$this->global_type;
                    $row->commit();
                }
            }
        }
        if ($new) {
            static::updateAffectedPagesForMaterials($this);
            static::updateAffectedPagesForSelf($this);
        }
    }


    public static function delete(self $object)
    {
        $mtype = new Material_Type($object->id);
        foreach ($object->selfFields as $row) {
            Material_Field::delete($row);
        }
        parent::delete($object);
        static::updateAffectedPagesForMaterials();
        static::updateAffectedPagesForSelf();
    }


    /**
     * Собственные поля (без учета родительских)
     * @return array<Material_Field>
     */
    protected function _selfFields()
    {
        $sqlQuery = "SELECT *
                       FROM " . Material_Field::_tablename()
                  . " WHERE classname = ?
                        AND pid = ?
                   ORDER BY priority";
        $sqlBind = [get_class($this), (int)$this->id];
        $temp = Material_Field::getSQLSet([$sqlQuery, $sqlBind]);
        $arr = [];
        foreach ($temp as $row) {
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    /**
     * Список полей (включая родительские)
     * @return array<Material_Field>
     */
    protected function _fields()
    {
        $arr1 = [];
        if ($this->parent->id) {
            $arr1 = (array)$this->parent->fields;
        }
        $arr2 = (array)$this->selfFields;
        $arr = array_merge($arr1, $arr2);
        return $arr;
    }


    /**
     * Обновляет связанные страницы для материалов
     * @param Material_Type $materialType Ограничить обновление одним
     *                                    типом материалов
     */
    public static function updateAffectedPagesForMaterials(Material_Type $materialType = null)
    {
        $mtCache = MaterialTypeRecursiveCache::i();
        $mtCache->refresh();
        if ($materialTypeId = $materialType->id) {
            $materialTypesIds = array_merge(
                [$materialTypeId],
                $mtCache->getChildrenIds($materialTypeId)
            );
        }
        $sqlQuery = "SELECT tP.id AS page_id,
                            tMT.id AS material_type_id,
                            MAX(tB.nat) AS nat
                       FROM " . Page::_tablename() . " AS tP
                       JOIN " . static::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                       JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id
                       JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.id = tB.id
                       JOIN " . Material_Type::_tablename() . " AS tMT ON tMT.id = tBM.material_type
                      WHERE tB.vis";
        if ($materialTypeId) {
            $sqlQuery .= " AND tMT.id IN (" . implode(", ", $materialTypesIds) . ")";
        }
        $sqlQuery .= " GROUP BY tMT.id, tP.id
                       ORDER BY tP.id";
        $sqlResult = static::_SQL()->get($sqlQuery);
        $sqlArr = [];
        $pagesByTypes = [];
        foreach ($sqlResult as $sqlRow) {
            $sqlArr[] = [
                'material_type_id' => (int)$sqlRow['material_type_id'],
                'page_id' => (int)$sqlRow['page_id'],
                'nat' => (int)$sqlRow['nat'],
            ];
            foreach ($mtCache->getAllChildrenIds($sqlRow['material_type_id']) as $childMTypeId) {
                $sqlArr[] = [
                    'material_type_id' => (int)$childMTypeId,
                    'page_id' => (int)$sqlRow['page_id'],
                    'nat' => (int)$sqlRow['nat'],
                ];
            }
        }

        $sqlQuery = "DELETE FROM " . static::$dbprefix . "cms_material_types_affected_pages_for_materials_cache";
        if ($materialTypeId) {
            $sqlQuery .= " WHERE material_type_id IN (" . implode(", ", $materialTypesIds) . ")";
        }
        static::_SQL()->query($sqlQuery);
        static::_SQL()->add(
            static::$dbprefix . 'cms_material_types_affected_pages_for_materials_cache',
            $sqlArr
        );
        Material::updateAffectedPages($materialType);
    }


    /**
     * Обновляет связанные страницы для собственного использования (для админки)
     * @param Material_Type $materialType Ограничить обновление одним
     *                                    типом материалов
     */
    public static function updateAffectedPagesForSelf(Material_Type $materialType = null)
    {
        $sqlQuery = "DELETE FROM cms_material_types_affected_pages_for_self_cache";
        if ($materialTypeId = $materialType->id) {
            $sqlQuery .= " WHERE material_type_id = " . (int)$materialTypeId;
        }
        static::_SQL()->query($sqlQuery);

        // Запишем данные по материалам
        $sqlQuery = "REPLACE INTO " . static::$dbprefix . "cms_material_types_affected_pages_for_self_cache
                            (material_type_id, page_id)
                     SELECT tMT.id AS material_type_id,
                            tP.id AS page_id
                       FROM " . Page::_tablename() . " AS tP
                       JOIN " . static::$dbprefix . "cms_materials_pages_assoc
                         AS tMPA
                         ON tMPA.pid = tP.id
                       JOIN " . Material::_tablename() . "
                         AS tM
                         ON tM.id = tMPA.id
                       JOIN " . Material_Type::_tablename() . "
                         AS tMT
                         ON tMT.id = tM.pid
                      WHERE NOT tMT.global_type";
        if ($materialTypeId) {
            $sqlQuery .= " AND tMT.id = " . $materialTypeId;
        }
        $sqlQuery .= " GROUP BY tMT.id, tP.id";
        static::_SQL()->query($sqlQuery);

        $sqlQuery = "REPLACE INTO " . static::$dbprefix . "cms_material_types_affected_pages_for_self_cache
                            (material_type_id, page_id, nat)
                     SELECT tMT.id AS material_type_id,
                            tP.id AS page_id,
                            MAX(tB.nat) AS nat
                       FROM " . Page::_tablename() . " AS tP
                       JOIN " . static::$dbprefix . "cms_blocks_pages_assoc
                         AS tBPA
                         ON tBPA.page_id = tP.id
                       JOIN " . Block::_tablename() . "
                         AS tB
                         ON tB.id = tBPA.block_id
                       JOIN " . Block::_dbprefix() . "cms_blocks_material
                         AS tBM
                         ON tBM.id = tB.id
                       JOIN " . Material_Type::_tablename() . "
                         AS tMT
                         ON tMT.id = tBM.material_type";
        if ($materialTypeId) {
            $sqlQuery .= " AND tMT.id = " . $materialTypeId;
        }
        $sqlQuery .= " GROUP BY tMT.id, tP.id";
        static::_SQL()->query($sqlQuery);
    }
}
