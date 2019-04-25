<?php
namespace RAAS\CMS;

class Material_Type extends \SOME\SOME
{
    protected static $tablename = 'cms_material_types';
    protected static $defaultOrderBy = "name";
    protected static $objectCascadeDelete = true;
    protected static $references = array(
        'parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Material_Type', 'cascade' => true),
    );
    protected static $parents = array('parents' => 'parent');
    protected static $children = array('children' => array('classname' => 'RAAS\\CMS\\Material_Type', 'FK' => 'pid'));
    protected static $cognizableVars = array('fields', 'selfFields', 'affectedPages', 'selfAndChildrenIds', 'selfAndParentsIds');

    /**
     * Сохраняет сущность
     */
    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);
        $globDirection = 0; // Направление глобализации
        if (isset($this->updates['global_type'])) {
            if ($this->properties['global_type'] && !$this->updates['global_type']) {
                $globDirection = -1;
            } elseif (!$this->properties['global_type'] && $this->updates['global_type']) {
                $globDirection = 1;
            }
        }
        parent::commit();
        if ($globDirection) {
            $SQL_query = "SELECT id FROM " . Material::_tablename() . " WHERE pid = " . (int)$this->id;
            $materialsIds = static::_SQL()->getcol($SQL_query);
            if ($globDirection == -1) {
                $pagesIds = array_map(function ($x) {
                    return (int)$x->id;
                }, $this->affectedPages);

                $arr = array();
                foreach ($pagesIds as $pageId) {
                    foreach ($materialsIds as $materialId) {
                        $arr[] = array('id' => (int)$materialId, 'pid' => (int)$pageId);
                    }
                }
                if ($arr) {
                    static::_SQL()->add('cms_materials_pages_assoc', $arr);
                }
            } elseif ($globDirection == 1) {
                if ($materialsIds) {
                    $SQL_query = "DELETE FROM cms_materials_pages_assoc WHERE id IN (" . implode(", ", $materialsIds) . ")";
                    static::_SQL()->query($SQL_query);
                }
            }
            foreach ($this->children as $row) {
                if ($row->global_type != $this->global_type) {
                    $row->global_type = (int)$this->global_type;
                    $row->commit();
                }
            }
        }
    }


    public static function delete(self $object)
    {
        foreach ($object->selfFields as $row) {
            Material_Field::delete($row);
        }
        parent::delete($object);
    }


    protected function _selfFields()
    {
        $SQL_query = "SELECT * FROM " . Material_Field::_tablename() . " WHERE classname = ? AND pid = ? ORDER BY priority";
        $SQL_bind = array(get_class($this), (int)$this->id);
        $temp = Material_Field::getSQLSet(array($SQL_query, $SQL_bind));
        $arr = array();
        foreach ($temp as $row) {
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    protected function _fields()
    {
        $arr1 = array();
        if ($this->parent->id) {
            $arr1 = (array)$this->parent->fields;
        }
        $arr2 = (array)$this->selfFields;
        $arr = array_merge($arr1, $arr2);
        return $arr;
    }


    public static function importByURN($urn)
    {
        $SQL_query = "SELECT * FROM " . self::_tablename() . " WHERE urn = ?";
        $SQL_bind = array($urn);
        if ($SQL_result = self::$SQL->getline(array($SQL_query, $SQL_bind))) {
            return new self($SQL_result);
        } else {
            return new self();
        }
    }


    /**
     * Обновляет связанные страницы для материалов
     * @param Material_Type $materialType Ограничить обновление одним типом материалов
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
                            tMt.id AS material_type_id
                        FROM " . Page::_tablename() . " AS tP
                        JOIN " . static::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                        JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id
                        JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.id = tB.id
                        JOIN " . Material_Type::_tablename() . " AS tMt ON tMt.id = tBM.material_type
                       WHERE tB.vis
                         AND tB.nat";
        if ($materialTypeId) {
            $sqlQuery .= " AND tMt.id IN (" . implode(", ", $materialTypesIds) . ")";
        }
        $sqlQuery .= " ORDER BY tP.id";
        $sqlResult = static::_SQL()->get($sqlQuery);
        $sqlArr = [];
        $pagesByTypes = [];
        foreach ($sqlResult as $sqlRow) {
            $sqlArr[] = [
                'material_type_id' => (int)$sqlRow['material_type_id'],
                'page_id' => (int)$sqlRow['page_id']
            ];
            foreach ($mtCache->getChildrenIds($sqlRow['material_type_id']) as $childMTypeId) {
                $sqlArr[] = [
                    'material_type_id' => (int)$childMTypeId,
                    'page_id' => (int)$sqlRow['page_id']
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
     * @param Material_Type $materialType Ограничить обновление одним типом материалов
     */
    public static function updateAffectedPagesForSelf(Material_Type $materialType = null)
    {
        $sqlQuery = "DELETE FROM cms_material_types_affected_pages_for_self_cache";
        if ($materialTypeId = $materialType->id) {
            $sqlQuery .= " WHERE material_type_id = " . (int)$materialTypeId;
        }
        static::_SQL()->query($sqlQuery);

        // Запишем данные по материалам
        $sqlQuery = "INSERT INTO " . static::$dbprefix . "cms_material_types_affected_pages_for_self_cache
                            (material_type_id, page_id)
                     SELECT tMT.id AS material_type_id,
                            tP.id AS page_id
                       FROM " . Page::_tablename() . " AS tP
                       JOIN " . static::$dbprefix . "cms_materials_pages_assoc AS tMPA ON tMPA.pid = tP.id
                       JOIN " . Material::_tablename() . " AS tM ON tM.id = tMPA.id
                       JOIN " . Material_Type::_tablename() . " AS tMT ON tMT.id = tM.pid
                      WHERE tMT.global_type";
        if ($materialTypeId) {
            $sqlQuery .= " AND tMT.id = " . $materialTypeId;
        }
        static::_SQL()->query($sqlQuery);

        $sqlQuery = "INSERT INTO " . static::$dbprefix . "cms_material_types_affected_pages_for_self_cache
                            (material_type_id, page_id)
                     SELECT tMT.id AS material_type_id,
                            tP.id AS page_id
                       FROM " . Page::_tablename() . " AS tP
                       JOIN " . static::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                       JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id
                       JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.id = tB.id
                       JOIN " . Material_Type::_tablename() . " AS tMT ON tMT.id = tBM.material_type";
        if ($materialTypeId) {
            $sqlQuery .= " AND tMT.material_type = " . $materialTypeId;
        }
        static::_SQL()->query($sqlQuery);
    }


    protected function _affectedPages()
    {
        if (!$this->global_type) {
            $SQL_query = "SELECT tP.id
                            FROM " . Page::_tablename() . " AS tP
                            JOIN " . self::$dbprefix . "cms_materials_pages_assoc AS tMPA ON tMPA.pid = tP.id
                            JOIN " . Material::_tablename() . " AS tM ON tM.id = tMPA.id
                           WHERE tM.pid = " . (int)$this->id . "
                        ORDER BY tP.priority";
            $col1 = (array)self::$SQL->getcol($SQL_query);
        } else {
            $col1 = array();
        }
        $SQL_query = "SELECT tP.id
                        FROM " . Page::_tablename() . " AS tP
                        JOIN " . self::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                        JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id
                        JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.id = tB.id
                       WHERE tBM.material_type = " . (int)$this->id;
        $col2 = (array)self::$SQL->getcol($SQL_query);
        $Set = array_values(array_unique(array_merge($col1, $col2)));
        $Set = array_map(
            function ($x) {
                return new \RAAS\CMS\Page($x);
            },
            $Set
        );
        return $Set;
    }


    protected function _selfAndChildrenIds()
    {
        return array_merge(array($this->id), (array)$this->all_children_ids);
    }


    protected function _selfAndParentsIds()
    {
        return array_merge(array($this->id), (array)$this->parents_ids);
    }
}
