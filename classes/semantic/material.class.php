<?php
/**
 * Материал
 */
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Attachment;
use RAAS\Application;
use RAAS\User as RAASUser;

/**
 * Класс материала
 * @property-read array<Page_Field> $fields Поля страницы с указанным $Owner
 * @property-read array<Page> $affectedPages Связанные страницы
 * @property-read array<Material_Type> $relatedMaterialTypes Связанные типы
 *                                                           материалов
 * @property-read Material_Type $material_type Тип материала
 * @property-read RAASUser $author Автор материала
 * @property-read RAASUser $editor Редактор материала
 * @property-read Page $urlParent Родитель по URL
 * @property-read array<CMSAccess> $access Доступы
 * @property-read array<Page> $pages Страницы, на которых размещен материал
 * @property-read array<User> $allowedUsers Пользователи, которым разрешен
 *                                          просмотр материала
 * @property-read array<Page> $parents Страницы, на которых размещен материал,
 *                                     либо, при отсутствии, связанные страницы
 * @property-read array<int> $parents_ids ID# страниц, на которых размещен
 *                                        материал, либо, при отсутствии,
 *                                        связанных страниц
 * @property-read Page $parent Первый из массива $parents
 */
class Material extends SOME
{
    use AccessibleTrait;
    use PageoidTrait;
    use ImportByURNTrait;

    protected static $tablename = 'cms_materials';

    protected static $defaultOrderBy = "post_date DESC";

    protected static $objectCascadeDelete = true;

    protected static $cognizableVars = [
        'fields',
        'affectedPages',
        'relatedMaterialTypes'
    ];

    protected static $references = [
        'material_type' => [
            'FK' => 'pid',
            'classname' => Material_Type::class,
            'cascade' => true
        ],
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
        'urlParent' => [
            'FK' => 'cache_url_parent_id',
            'classname' => Page::class,
            'cascade' => false,
        ]
    ];

    protected static $children = [
        'access' => [
            'classname' => CMSAccess::class,
            'FK' => 'material_id'
        ],
    ];

    protected static $links = [
        'pages' => [
            'tablename' => 'cms_materials_pages_assoc',
            'field_from' => 'id',
            'field_to' => 'pid',
            'classname' => Page::class
        ],
        'allowedUsers' => [
            'tablename' => 'cms_access_materials_cache',
            'field_from' => 'material_id',
            'field_to' => 'uid',
            'classname' => User::class
        ],
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'parents':
                if ($this->pages) {
                    return $this->pages;
                } elseif ($this->affectedPages) {
                    return $this->affectedPages;
                } else {
                    return [];
                }
                break;
            case 'parents_ids':
                return array_map(
                    function ($x) {
                        return (int)$x->id;
                    },
                    $this->parents
                );
                break;
            case 'parent':
                if ($this->parents) {
                    if ($this->urlParent->id) {
                        return $this->urlParent;
                    } else {
                        return $this->parents[0];
                    }
                }
                return new Page();
                break;
            default:
                $val = parent::__get($var);
                    // echo $var . ' = ' . $val; exit;
                if ($val !== null) {
                    return $val;
                }
                if (substr($var, 0, 3) == 'vis') {
                    $var = strtolower(substr($var, 3));
                    $vis = true;
                }
                // 2019-02-11, AVS: Заменил isset($this->fields[$var])
                // на $this->fields[$var]->id, т.к. на TimeWeb'е на PHP5.6
                // isset($this->fields[$var]) выдает false, хотя на локальном
                // PHP5.6 выдает true - хз почему
                if ($this->fields[$var]->id &&
                    ($this->fields[$var] instanceof Material_Field)
                ) {
                    $temp = $this->fields[$var]->getValues();
                    if ($vis) {
                        $temp = array_values(
                            array_filter(
                                (array)$temp,
                                function ($x) {
                                    return isset($x->vis) && $x->vis;
                                }
                            )
                        );
                    }
                    return $temp;
                }
                if ((strtolower($var) == 'url') && !isset($temp)) {
                    // Размещаем сюда из-за большого количества баннеров,
                    // где URL задан явно
                    // 2015-06-21, AVS: заменили parent на affectedPages[0],
                    // т.к. зачастую, если новость задана и на главной и
                    // на странице новостей,
                    // url по умолчанию ведет на главную, где нет nat'а
                    // 2016-02-09, AVS: делаем проверку, а вообще
                    // есть ли affectedPages
                    // Если нет, то и URL у материала по сути нет
                    return $this->cache_url ?: null;
                }
                break;
        }
    }


    public function commit()
    {
        if ($this->id &&
            $this->updates['pid'] &&
            ($this->updates['pid'] != $this->properties['pid'])
        ) {
            $oldMaterialTypeId = $this->properties['pid'];
        }
        $this->modify(false);
        $this->modify_date = date('Y-m-d H:i:s');
        if (!$this->id) {
            $this->post_date = $this->modify_date;
        }
        if ($this->pid && !$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        if ($this->updates['urn']) {
            $this->urn = \SOME\Text::beautify($this->urn, '-');
            $this->urn = preg_replace('/\\-\\-/umi', '-', $this->urn);
            $this->urn = trim($this->urn, '-');
        }
        $need2UpdateURN = false;
        if ($this->checkForSimilarPages() ||
            Package::i()->checkForSimilar($this)
        ) {
            $need2UpdateURN = true;
        }
        parent::commit();
        if ($need2UpdateURN) {
            if (!preg_match('/-\\d+$/', $this->urn)) {
                $this->urn .= '-' . $this->id;
            }
            for ($i = 0; $this->checkForSimilarPages() || Package::i()->checkForSimilar($this); $i++) {
                $this->urn = Application::i()->getNewURN($this->urn, !$i, '-');
            }
            parent::commit();
        }
        $this->exportPages();

        if (!$this->meta['dontUpdateAffectedPages']) {
            // 2019-04-25, AVS: обновим связанные страницы
            // 2020-02-10, AVS: добавил условие для загрузчика прайсов
            // (чтобы было быстрее)
            static::updateAffectedPages(null, $this);
            if ($oldMaterialTypeId) {
                Material_Type::updateAffectedPagesForSelf(
                    new Material_Type($oldMaterialTypeId)
                );
            }
            Material_Type::updateAffectedPagesForSelf($this->material_type);
        }

        if ($parentsIds = $this->parents_ids) {
            $sqlQuery = "UPDATE " . Page::_tablename()
                      .   " SET last_modified = NOW(),
                                modify_counter = modify_counter + 1
                          WHERE id IN (" . implode(", ", $parentsIds) . ")";
            Page::_SQL()->query($sqlQuery);
        }

        // 2020-02-10, AVS: Заменил reload на rollback
        // для ускорения загрузчика прайсов
        $this->rollback();
    }


    /**
     * Ассоциировать материал со страницей.
     * Работает только для неглобальных материалов
     * @param Page $page Страница, на которую нужно разместить материал
     * @return bool true в случае успешного завершения, false в случае неудачи
     */
    public function assoc(Page $page)
    {
        if ($this->material_type->global_type) {
            return false;
        }
        if (in_array($page->id, (array)$this->pages_ids)) {
            return false;
        }
        $arr = ['id' => (int)$this->id, 'pid' => (int)$page->id];
        self::$SQL->add(
            self::$dbprefix . self::$links['pages']['tablename'],
            $arr
        );

        // 2020-03-24, AVS: обновим дату связанного изменения
        $this->modify();

        // 2019-04-25, AVS: обновим связанные страницы
        static::updateAffectedPages(null, $this);
        Material_Type::updateAffectedPagesForSelf($this->material_type);

        return true;
    }


    /**
     * Убрать материал со страницы. Работает только для неглобальных материалов
     * @param Page $page Страница, на которую нужно разместить материал
     * @return bool true в случае успешного завершения, false в случае неудачи
     */
    public function deassoc(Page $page)
    {
        if ($this->material_type->global_type) {
            return false;
        }
        if (!in_array($page->id, (array)$this->pages_ids)) {
            return false;
        }
        $sqlQuery = "DELETE FROM " . self::_dbprefix() . self::$links['pages']['tablename']
                   . " WHERE id = " . (int)$this->id
                   . "   AND pid = " . (int)$page->id;
        self::$SQL->query($sqlQuery);

        // 2020-03-24, AVS: обновим дату связанного изменения
        $this->modify();

        // 2019-04-25, AVS: обновим связанные страницы
        static::updateAffectedPages(null, $this);
        Material_Type::updateAffectedPagesForSelf($this->material_type);

        return true;
    }


    /**
     * Сохранить привязку к страницам
     */
    private function exportPages()
    {
        $tablename = self::_dbprefix() . self::$links['pages']['tablename'];
        if ($this->meta['cats']) {
            $sqlQuery = "DELETE FROM " . $tablename
                       . " WHERE id = " . (int)$this->id;
            self::$SQL->query($sqlQuery);
            $id = (int)$this->id;
            $arr = array_map(
                function ($x) use ($id) {
                    return ['id' => $id, 'pid' => $x];
                },
                (array)$this->meta['cats']
            );
            unset($this->meta['cats']);
            self::$SQL->add($tablename, $arr);
        } elseif (!$this->meta['dontCheckPages']) {
            // 2020-02-10, AVS: добавил условие dontCheckPages
            // для ускорения загрузчика прайсов
            if ($this->material_type->global_type) {
                $sqlQuery = "DELETE FROM " . $tablename
                           . " WHERE id = " . (int)$this->id;
                self::$SQL->query($sqlQuery);
            }
        }
    }


    public static function delete(self $object)
    {
        $mtype = $object->material_type;
        $material = new Material($object->id);

        // Удалим файловые поля с проверкой на совместное использование
        // Найдем используемые значения файловых полей в данном материале
        $sqlQuery = "SELECT tD.value
                       FROM " . static::_dbprefix() . "cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                      WHERE tD.pid = ?
                        AND tF.classname = ?
                        AND tF.pid
                        AND tF.datatype IN ('image', 'file')";
        $sqlResult = static::_SQL()->getcol([
            $sqlQuery,
            (int)$object->id,
            Material_Type::class
        ]);
        $affectedAttachmentsIds = array_map(function ($x) {
            $json = (array)json_decode($x, true);
            $attachmentId = (int)$json['attachment'];
            return $attachmentId;
        }, $sqlResult);
        $affectedAttachmentsIds = array_filter($affectedAttachmentsIds);
        $affectedAttachmentsIds = array_values($affectedAttachmentsIds);

        // Найдем используемые значения файловых полей вне данного материала
        $sqlQuery = "SELECT tD.value
                       FROM " . static::_dbprefix() . "cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                      WHERE NOT (
                                    tD.pid = ?
                                AND tF.classname = ?
                                AND tF.pid
                            )
                        AND tF.datatype IN ('image', 'file')";
        $sqlResult = static::_SQL()->getcol([
            $sqlQuery,
            (int)$object->id,
            Material_Type::class
        ]);
        $otherAttachmentsIds = array_map(function ($x) {
            $json = (array)json_decode($x, true);
            $attachmentId = (int)$json['attachment'];
            return $attachmentId;
        }, $sqlResult);
        $otherAttachmentsIds = array_filter($otherAttachmentsIds);
        $otherAttachmentsIds = array_values($otherAttachmentsIds);

        $attachmentsToDelete = array_diff(
            $affectedAttachmentsIds,
            $otherAttachmentsIds
        );

        foreach ($attachmentsToDelete as $attachmentToDelete) {
            $att = new Attachment($attachmentToDelete);
            Attachment::delete($att);
        }

        // Удалим данные из cms_data
        $sqlQuery = "DELETE tD
                       FROM " . static::_dbprefix() . "cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                      WHERE tD.pid = ?
                        AND tF.classname = ?
                        AND tF.pid";
        $sqlResult = static::_SQL()->getcol([
            $sqlQuery,
            (int)$object->id,
            Material_Type::class
        ]);

        parent::delete($object);

        // 2019-01-24, AVS: добавил удаление из связанных данных
        // несуществующих материалов
        $sqlQuery = "DELETE tD
                       FROM cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                  LEFT JOIN " . static::_tablename() . " AS tM ON tM.id = tD.value
                      WHERE tF.datatype = ?
                        AND tM.id IS NULL";
        $result = static::$SQL->query([$sqlQuery, ['material']]);

        // 2019-04-25, AVS: обновим связанные страницы
        static::updateAffectedPages(null, $material);
        Material_Type::updateAffectedPagesForSelf($mtype);
    }


    /**
     * Возвращает поля материала с указанным свойством $Owner
     * @return array<Material_Field>
     */
    protected function _fields()
    {
        $temp = $this->material_type->fields;
        $arr = [];
        foreach ((array)$temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    /**
     * Возвращает связанные страницы (где материал может раскрываться,
     * начиная с явно указанной в page_id)
     * @return array<Page>
     */
    protected function _affectedPages()
    {
        $sqlQuery = "SELECT tP.*
                       FROM " . Page::_tablename() . " AS tP
                       JOIN " . static::$dbprefix . "cms_materials_affected_pages_cache
                         AS tMAP
                         ON tMAP.page_id = tP.id
                      WHERE tMAP.material_id = ?
                   ORDER BY (tMAP.page_id = ?) DESC, tP.priority ASC";
        $set = Page::getSQLSet([$sqlQuery, [(int)$this->id, (int)$this->page_id]]);
        return $set;
    }


    /**
     * Обновляет связанные страницы
     * @param Material_Type $materialType Ограничить обновление одним
     *                                    типом материалов
     * @param Material $material Ограничить обновление одним материалом
     */
    public static function updateAffectedPages(
        Material_Type $materialType = null,
        Material $material = null
    ) {
        $materialId = $material->id;
        if ($materialTypeId = $materialType->id) {
            $materialTypesIds = $materialType->selfAndChildrenIds;
        }
        $sqlQuery = "DELETE ";
        if (!$materialId && $materialTypeId) {
            $sqlQuery .= " tMAP ";
        }
        $sqlQuery .= " FROM " . static::$dbprefix . "cms_materials_affected_pages_cache ";
        if (!$materialId && $materialTypeId) {
            $sqlQuery .= " AS tMAP
                    LEFT JOIN " . static::_tablename() . " AS tM ON tM.id = tMAP.material_id ";
        }
        $sqlQuery .= " WHERE 1 ";
        if ($materialId = $material->id) {
            $sqlQuery .= " AND material_id = " . (int)$materialId;
        } elseif ($materialTypeId) {
            $sqlQuery .= " AND (
                                (tM.pid IN (" . implode(", ", $materialTypesIds) . "))
                             OR (tM.pid IS NULL)
                           )";
        }
        static::_SQL()->query($sqlQuery);

        // Выберем привязку типов материалов к страницам (фильтрация по NAT)
        $materialTypesToPagesAssoc = [];
        $sqlQuery = "SELECT material_type_id, page_id
                       FROM " . static::$dbprefix . "cms_material_types_affected_pages_for_materials_cache
                      WHERE nat";
        if ($materialTypeId) {
            $sqlQuery .= " AND material_type_id IN (" . implode(", ", $materialTypesIds) . ")";
        }
        $sqlResult = Material::_SQL()->query($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            $materialTypesToPagesAssoc[trim($sqlRow['material_type_id'])][trim($sqlRow['page_id'])] = (int)$sqlRow['page_id'];
        }

        // Выберем собственную привязку материалов к страницам
        $materialsToPagesAssoc = [];
        $sqlQuery = "SELECT id, pid
                       FROM " . static::$dbprefix . "cms_materials_pages_assoc";
        if ($materialId) {
            $sqlQuery .= " WHERE id = " . (int)$materialId;
        }
        $sqlResult = static::_SQL()->query($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            $materialsToPagesAssoc[trim($sqlRow['id'])][trim($sqlRow['pid'])] = (int)$sqlRow['pid'];
        }

        // Выберем привязку материалов к типам материалов
        $materialsToMaterialTypesAssoc = [];
        $sqlQuery = "SELECT id, pid FROM cms_materials";
        if ($materialId) {
            $sqlQuery .= " WHERE id = " . (int)$materialId;
        } elseif ($materialTypeId) {
            $sqlQuery .= " WHERE pid IN (" . implode(", ", $materialTypesIds) . ")";
        }
        $sqlResult = Material::_SQL()->query($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            $materialsToMaterialTypesAssoc[trim($sqlRow['id'])] = (int)$sqlRow['pid'];
        }

        // Соберем привязку материалов к страницам
        $realMaterialsToPagesAssoc = [];
        foreach ((array)$materialsToMaterialTypesAssoc as $mId => $mtId) {
            foreach ((array)$materialTypesToPagesAssoc[$mtId] as $mtPageId) {
                if (!isset($materialsToPagesAssoc[$mId]) ||
                    isset($materialsToPagesAssoc[$mId][$mtPageId])
                ) {
                    $realMaterialsToPagesAssoc[trim($mId)][trim($mtPageId)] = (int)$mtPageId;
                }
            }
        }

        // Сформируем массив для записи в базу
        $sqlArr = [];
        foreach ($realMaterialsToPagesAssoc as $mId => $mPagesIds) {
            foreach ($mPagesIds as $mPageId) {
                $sqlArr[] = [
                    'material_id' => (int)$mId,
                    'page_id' => (int)$mPageId
                ];
            }
        }
        if ($sqlArr) {
            static::_SQL()->add(
                static::$dbprefix . "cms_materials_affected_pages_cache",
                $sqlArr
            );
        }

        // Определим родителей по URL
        $sqlQuery = "UPDATE " . static::_tablename() . " AS tM
                        SET tM.cache_url_parent_id = IFNULL((
                            SELECT tP.id
                              FROM " . static::$dbprefix . "cms_materials_affected_pages_cache AS tMAP
                              JOIN " . Page::_tablename() . " AS tP ON tP.id = tMAP.page_id
                             WHERE tMAP.material_id = tM.id
                          ORDER BY (tMAP.page_id = tM.page_id) DESC,
                                   (tMAP.page_id = tM.cache_url_parent_id) DESC,
                                   tP.priority ASC
                             LIMIT 1
                        ), 0)";
        if ($materialId) {
            $sqlQuery .= " WHERE tM.id = " . (int)$materialId;
        } elseif ($materialTypeId) {
            $sqlQuery .= " WHERE tM.pid IN (" . implode(", ", $materialTypesIds) . ")";
        }
        static::_SQL()->query($sqlQuery);

        // Определим URL
        $sqlQuery = "UPDATE " . static::_tablename() . " AS tM
                  LEFT JOIN " . Page::_tablename() . " AS tP
                         ON tP.id = tM.cache_url_parent_id
                        SET tM.cache_url = IF(
                                tP.id IS NOT NULL,
                                CONCAT(tP.cache_url, tM.urn, '/'),
                                ''
                            )";
        if ($materialId) {
            $sqlQuery .= " WHERE tM.id = " . (int)$materialId;
        } elseif ($materialTypeId) {
            $sqlQuery .= " WHERE tM.pid IN (" . implode(", ", $materialTypesIds) . ")";
        }
        static::_SQL()->query($sqlQuery);
    }


    /**
     * Возвращает связанные типы материалов (прикрепленные материалы в полях)
     * @return array<Material_Type>
     */
    protected function _relatedMaterialTypes()
    {
        $ids = array_merge([0], (array)$this->material_type->selfAndParentsIds);
        $sqlQuery = "SELECT tMT.*
                       FROM " . Material_Type::_tablename() . " AS tMT
                       JOIN " . Material_Field::_tablename() . " AS tF
                         ON tF.classname = ?
                        AND tF.pid = tMT.id
                      WHERE tF.datatype = ?
                        AND source IN (" . implode(", ", $ids) . ")";
        $sqlBind = [Material_Type::class, 'material'];
        return Material_Type::getSQLSet([$sqlQuery, $sqlBind]);
    }


    /**
     * Ищет страницы с таким же URN, как и текущий материал
     * (для проверки на уникальность)
     * @return bool true, если есть страница с таким URN, как и текущий материал,
     *              false в противном случае
     */
    protected function checkForSimilarPages()
    {
        $sqlQuery = "SELECT COUNT(*)
                        FROM " . Page::_tablename()
                   . " WHERE urn = ?";
        $sqlResult = self::$SQL->getvalue([$sqlQuery, $this->urn]);
        return (bool)(int)$sqlResult;
    }


    /**
     * Очистить кэши материала
     */
    public function clearCache()
    {
        $globUrl = Package::i()->cacheDir . '/' . Package::i()->cachePrefix
                 . '.*' . urlencode('/') . $this->urn . urlencode('/') . '*.php';
        $glob = glob($globUrl);
        foreach ($glob as $file) {
            @unlink($file);
        }
    }
}
