<?php
/**
 * Тип материалов
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс типа материалов
 * @property-read Material_Type $parent Родительский тип
 * @property-read Material_Type[] $parents Список родительских типов
 * @property-read Material_Type[] $children Список дочерних типов
 * @property-read Page[] $affectedPages Список связанных страниц
 * @property-read Material_Field[] $fields Список полей
 * @property-read Material_Field[] $selfFields Список собственных полей
 *                                                 (без учета родительских)
 * @property-read Material_Type[] $selfAndChildren Тип и все родительские
 * @property-read int[] $selfAndChildrenIds ID# типа и всех родительских
 * @property-read Material_Type[] $selfAndParents Тип и все дочерние
 * @property-read int[] $selfAndParentsIds ID# типа и всех дочерних
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
        'visFields',
        'selfFields',
        'visSelfFields',
        'formFields',
        'formFields_ids',
        'selfAndChildren',
        'selfAndChildrenIds',
        'selfAndParents',
        'selfAndParentsIds',
        'fieldGroups',
        'selfFieldGroups',
    ];

    /**
     * Кэш собственных полей
     * Сделано для ускорения отображения списков одинаковых материалов,
     * когда отдельно получается тип и отдельно поля к нему
     * @var array <pre><code>array<
     *     string[] ID# типа материалов => array<string[] URN поля => Material_Field>
     * ></code></pre>
     */
    public static $selfFieldsCache = [];

    /**
     * Кэш собственных видимых полей
     * Сделано для ускорения отображения списков одинаковых материалов,
     * когда отдельно получается тип и отдельно поля к нему
     * @var array <pre><code>array<
     *     string[] ID# типа материалов => array<string[] URN поля => Material_Field>
     * ></code></pre>
     */
    public static $visSelfFieldsCache = [];

    /**
     * Кэш полей
     * Сделано для ускорения отображения списков одинаковых материалов,
     * когда отдельно получается тип и отдельно поля к нему
     * @var array <pre><code>array<
     *     string[] ID# типа материалов => array<string[] URN поля => Material_Field>
     * ></code></pre>
     */
    public static $fieldsCache = [];

    /**
     * Кэш видимых полей
     * Сделано для ускорения отображения списков одинаковых материалов,
     * когда отдельно получается тип и отдельно поля к нему
     * @var array <pre><code>array<
     *     string[] ID# типа материалов => array<string[] URN поля => Material_Field>
     * ></code></pre>
     */
    public static $visFieldsCache = [];

    /**
     * Кэш собственных групп полей
     * Сделано для ускорения отображения списков одинаковых материалов,
     * когда отдельно получается тип и отдельно поля к нему
     * @var array <pre><code>array<
     *     string[] ID# типа материалов => array<string[] URN поля => MaterialFieldGroup>
     * ></code></pre>
     */
    public static $selfFieldGroupsCache = [];

    /**
     * Кэш групп полей
     * Сделано для ускорения отображения списков одинаковых материалов,
     * когда отдельно получается тип и отдельно поля к нему
     * @var array <pre><code>array<
     *     string[] ID# типа материалов => array<string[] URN поля => MaterialFieldGroup>
     * ></code></pre>
     */
    public static $fieldGroupsCache = [];

    public function commit()
    {
        $new = !$this->id;

        // Подготовим старые и новые поля для формы в случае смены родителя
        $formFieldsToSet = [];
        if (!$new &&
            $this->updates['pid'] &&
            ($this->updates['pid'] != $this->properties['pid'])
        ) {
            $oldParentType = new Material_Type($this->properties['pid']);
            $newParentType = new Material_Type($this->updates['pid']);
            $oldParentFields = $oldParentType->fields;
            $newParentFields = $newParentType->fields;
            $newParentFormFields = $newParentType->formFields;
            $oldParentFieldsIds = array_map(function ($x) {
                return (int)$x->id;
            }, $oldParentFields);
            $newParentFieldsIds = array_map(function ($x) {
                return (int)$x->id;
            }, $newParentFields);
            $newParentFormFieldsIds = array_map(function ($x) {
                return (int)$x->id;
            }, $newParentFormFields);
            $formFieldsIdsToDelete = array_diff(
                $oldParentFieldsIds,
                $newParentFieldsIds
            );
            $formFieldsIdsToAdd = array_intersect(
                array_diff($newParentFieldsIds, $oldParentFieldsIds),
                $newParentFormFieldsIds
            );
            foreach ($formFieldsIdsToDelete as $formFieldIdToDelete) {
                $formFieldsToSet[trim($formFieldIdToDelete)] = [
                    'vis' => false,
                    'inherit' => true,
                ];
            }
            foreach ($formFieldsIdsToAdd as $formFieldIdToAdd) {
                $formFieldsToSet[trim($formFieldIdToAdd)] = [
                    'vis' => true,
                    'inherit' => true,
                ];
            }
        }

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
        if ($formFieldsToSet) {
            $this->setFormFieldsIds($formFieldsToSet);
        }
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
            if ($this->pid) { // Не корневая
                // Добавим видимости формы по полям
                $sqlQuery = "INSERT INTO " . self::_dbprefix() . "cms_fields_form_vis (pid, fid)
                             SELECT ? AS pid,
                                    fid
                               FROM " . self::_dbprefix() . "cms_fields_form_vis
                              WHERE pid = ?";
                $sqlBind = [$this->id, $this->pid];
                static::_SQL()->query([$sqlQuery, $sqlBind]);
            }
            static::updateAffectedPagesForMaterials($this);
            static::updateAffectedPagesForSelf($this);
        }
    }


    /**
     * Устанавливает видимость в форме для полей
     * @param array $formVisibility <pre><code>array<
     *     string[] ID# поля => [
     *         'vis' => bool Видимость в форме,
     *         'inherit' => bool Наследовать на дочерние типы,
     *     ]
     * ></code></pre>
     */
    public function setFormFieldsIds(array $formVisibility = [])
    {
        $thisId = (int)$this->id;
        $allChildrenIds = array_map('intval', $this->all_children_ids);

        $sqlDeleteArr = [];
        $sqlArr = [];
        foreach ($formVisibility as $fieldId => $fieldData) {
            $mTypesIdsToUpdate = [$thisId];
            if ($fieldData['inherit']) {
                $mTypesIdsToUpdate = array_merge(
                    $mTypesIdsToUpdate,
                    $allChildrenIds
                );
            }
            foreach ($mTypesIdsToUpdate as $mTypeId) {
                $sqlRow = [
                    'pid' => $mTypeId,
                    'fid' => $fieldId,
                ];
                if ($fieldData['vis']) {
                    $sqlArr[] = $sqlRow;
                } else {
                    $sqlDeleteArr[] = $sqlRow;
                }
            }
        }

        $sqlDeleteArr = array_map(function ($sqlRow) {
            return "(
                            pid = " . (int)$sqlRow['pid'] . "
                        AND fid = " . (int)$sqlRow['fid'] . "
                    )";
        }, $sqlDeleteArr);

        if ($sqlDeleteArr) {
            $sqlQuery = "DELETE FROM " . self::_dbprefix() . "cms_fields_form_vis
                          WHERE " . implode(" OR ", $sqlDeleteArr);
            static::_SQL()->query($sqlQuery);
        }
        if ($sqlArr) {
            static::_SQL()->add(
                self::_dbprefix() . "cms_fields_form_vis",
                $sqlArr
            );
        }
    }


    public static function delete(SOME $object)
    {
        $mtype = new Material_Type($object->id);
        $selfAndChildrenIds = (array)$mtype->selfAndChildrenIds;
        foreach ($object->selfFields as $row) {
            Material_Field::delete($row);
        }
        $sqlQuery = "DELETE FROM " . self::_dbprefix() . "cms_fields_form_vis
                      WHERE pid = ?";
        $sqlBind = [(int)$object->id];
        static::_SQL()->query([$sqlQuery, $sqlBind]);

        parent::delete($object);
        static::updateAffectedPagesForMaterials();
        static::updateAffectedPagesForSelf();
        // 2020-05-07, AVS: Удаление блоков делаем после основного,
        // иначе в методе SOME:ondelete класс Block_Material подхватывается
        // в качестве ссылки, а поскольку там ссылка на Material_Type идет из вторичной
        // таблицы, возникает ошибка MySQL
        $sqlQuery = "SELECT id
                      FROM " . Block::_dbprefix() . "cms_blocks_material
                     WHERE material_type IN (" . implode(", ", $selfAndChildrenIds) . ")";
        $blocksIds = Block_Material::_SQL()->getcol($sqlQuery);
        foreach ($blocksIds as $blockId) {
            $block = new Block_Material($blockId);
            Block_Material::delete($block);
        }
    }


    /**
     * Собственные поля (без учета родительских)
     * @return Material_Field[]
     */
    protected function _selfFields()
    {
        if (!isset(static::$selfFieldsCache[$this->id]) || !static::$selfFieldsCache[$this->id]) {
            $sqlQuery = "SELECT *
                           FROM " . Material_Field::_tablename()
                      . " WHERE classname = ?
                            AND pid = ?
                       ORDER BY priority";
            $sqlBind = [get_class($this), (int)$this->id];
            $temp = Material_Field::getSQLSet([$sqlQuery, $sqlBind]);
            $arr = [];
            foreach ($temp as $row) {
                $arr[trim($row->urn)] = $row;
            }
            static::$selfFieldsCache[trim($this->id)] = $arr;
        }
        return static::$selfFieldsCache[$this->id];
    }


    /**
     * Собственные видимые поля (без учета родительских)
     * @return Material_Field[]
     */
    protected function _visSelfFields()
    {
        if (!static::$visSelfFieldsCache[$this->id]) {
            static::$visSelfFieldsCache[trim($this->id)] = array_filter(
                function ($x) {
                    return $x->vis;
                },
                $this->selfFields
            );
        }
        return static::$visSelfFieldsCache[$this->id];
    }


    /**
     * Поля, отображаемые в форме
     * @return Material_Field[]
     */
    protected function _formFields()
    {
        $sqlQuery = "SELECT fid
                       FROM " . self::_dbprefix() . "cms_fields_form_vis
                      WHERE pid = ?";
        $formFieldsIds = self::_SQL()->getcol([$sqlQuery, [$this->id]]);
        $result = array_filter($this->fields, function ($x) use ($formFieldsIds) {
            return in_array($x->id, $formFieldsIds);
        });
        return $result;
    }


    /**
     * ID# полей, отображаемых в форме
     * @return int[]
     */
    protected function _formFields_ids()
    {
        $result = array_map(function ($x) {
            return (int)$x->id;
        }, $this->formFields);
        return $result;
    }


    /**
     * Собственные группы полей (без учета родительских)
     * @return MaterialFieldGroup[]
     */
    protected function _selfFieldGroups()
    {
        if (!isset(static::$selfFieldGroupsCache[$this->id]) ||
            !static::$selfFieldGroupsCache[$this->id]
        ) {
            $sqlQuery = "SELECT *
                           FROM " . MaterialFieldGroup::_tablename()
                      . " WHERE classname = ?
                            AND pid = ?
                       ORDER BY priority";
            $sqlBind = [static::class, (int)$this->id];
            $temp = MaterialFieldGroup::getSQLSet([$sqlQuery, $sqlBind]);
            $arr = [];
            foreach ($temp as $row) {
                $arr[$row->urn] = $row;
            }
            $result = static::$selfFieldGroupsCache[trim($this->id)] = $arr;
        }

        $result = array_merge(
            [
                '' => new MaterialFieldGroup([
                    'classname' => static::class,
                    'pid' => (int)$this->id
                ])
            ],
            $result
        );
        return $result;
    }


    /**
     * Список полей (включая родительские)
     * @return MaterialFieldGroup[]
     */
    protected function _fieldGroups()
    {
        if (!isset(static::$fieldGroupsCache[$this->id]) ||
            !static::$fieldGroupsCache[$this->id]
        ) {
            $arr1 = [];
            if ($this->parent->id) {
                $arr1 = (array)$this->parent->fieldGroups;
            }
            $arr2 = (array)$this->selfFieldGroups;
            $arr = array_merge($arr1, $arr2);
            $result = static::$fieldGroupsCache[trim($this->id)] = $arr;
        }
        unset($result['']);
        $result = array_merge(
            [
                '' => new MaterialFieldGroup([
                    'classname' => static::class,
                    'pid' => (int)$this->id
                ])
            ],
            static::$fieldGroupsCache[$this->id]
        );
        return $result;
    }


    /**
     * Список видимых полей (включая родительские)
     * @return Material_Field[]
     */
    protected function _visFields()
    {
        if (!static::$visFieldsCache[$this->id]) {
            static::$visFieldsCache[trim($this->id)] = array_filter($this->fields, function ($x) {
                return $x->vis;
            });
        }
        return static::$visFieldsCache[$this->id];
    }


    /**
     * Список групп полей (включая родительские)
     * @return Material_Field[]
     */
    protected function _fields()
    {
        if (!isset(static::$fieldsCache[$this->id]) ||
            !static::$fieldsCache[$this->id]
          ) {
            $arr1 = [];
            if ($this->parent->id) {
                $arr1 = (array)$this->parent->fields;
            }
            $arr2 = (array)$this->selfFields;
            $arr = array_merge($arr1, $arr2);
            static::$fieldsCache[trim($this->id)] = $arr;
        }
        return static::$fieldsCache[$this->id];
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
        if ($materialTypeId = ($materialType->id ?? 0)) {
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

        $sqlQuery = "START TRANSACTION";
        static::_SQL()->query($sqlQuery);

        $sqlQuery = "DELETE FROM " . static::$dbprefix . "cms_material_types_affected_pages_for_materials_cache";
        if ($materialTypeId) {
            $sqlQuery .= " WHERE material_type_id IN (" . implode(", ", $materialTypesIds) . ")";
        }
        static::_SQL()->query($sqlQuery);
        // 2022-07-04, AVS: разделим по 1000 записей, чтобы база не падала
        for ($i = 0; $i < ceil(count($sqlArr) / 1000); $i++) {
            $sqlChunk = array_slice($sqlArr, $i * 1000, 1000);
            static::_SQL()->add(
                static::$dbprefix . 'cms_material_types_affected_pages_for_materials_cache',
                $sqlChunk
            );
        }

        $sqlQuery = "COMMIT";
        static::_SQL()->query($sqlQuery);

        // 2021-07-07, AVS: очистим память
        unset($sqlArr);

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
        if ($materialTypeId = ($materialType->id ?? 0)) {
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
