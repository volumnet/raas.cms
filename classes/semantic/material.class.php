<?php
/**
 * Материал
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\SOME;
use SOME\Text;
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
 * @property-read string $url URL материала
 * @property-read string $fullURL полный URL материала (с указанием домена)
 * @property-read string $conditionalDomainURL URL материала с указанием домена,
 *     если текущий домен отличается от набора стандартных
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
 * @property-read string $cacheFile Путь к файлу кэша
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
        'visFields',
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
            case 'fullURL':
                $urlParent = $this->urlParent;
                if (!$urlParent->id || !$this->url) {
                    return '';
                }
                if (in_array($_SERVER['HTTP_HOST'], $urlParent->domains)) {
                    $result = '//' . $_SERVER['HTTP_HOST'];
                } else {
                    $result = $urlParent->domain;
                }
                $result .= $this->url;
                return $result;
                break;
            case 'conditionalDomainURL':
                $urlParent = $this->urlParent;
                if (!$urlParent->id || !$this->url) {
                    return '';
                }
                $result = '';
                if (!in_array($_SERVER['HTTP_HOST'], $urlParent->domains)) {
                    $result .= $urlParent->domain;
                }
                $result .= $this->url;
                return $result;
                break;
            case 'cacheFile':
                $url = 'http'
                     . (mb_strtolower($_SERVER['HTTPS'] == 'on') ? 's' : '')
                     . ':' . $this->fullURL;
                $file = Package::i()->cacheDir . '/' . Package::i()->cachePrefix
                    . '.' . urlencode($url) . '.php';
                return $file;
                break;
            // 2023-01-26, AVS: вынес сюда, чтобы не было путаницы
            // Эти поля точно переопределять нельзя!
            case 'id':
            case 'pid':
            case 'fields':
                return parent::__get($var);
                break;
            default:
                $origVar = $var;
                $val = parent::__get($var);
                $vis = false;
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
                $st = microtime(true);
                // 2023-01-26 Нельзя isset или ??, т.к. считает что переменная не определена явно
                $fields = $this->fields;
                $field = isset($fields[$var]) ? $fields[$var] : null;
                if (isset($field) && $field->id && ($field instanceof Material_Field)) {
                    $temp = $field->getValues();
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
            isset($this->updates['pid']) &&
            $this->updates['pid'] &&
            ($this->updates['pid'] != ($this->properties['pid'] ?? null))
        ) {
            $oldMaterialTypeId = ($this->properties['pid'] ?? null);
        }
        $this->modify(false);
        $this->modify_date = date('Y-m-d H:i:s');
        if (!$this->id) {
            $this->post_date = $this->modify_date;
        }
        if ($this->pid && !$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        if ($this->updates['urn'] ?? false) {
            $this->urn = Text::beautify($this->urn, '-');
            $this->urn = preg_replace('/\\-\\-/umi', '-', $this->urn);
            $this->urn = trim((string)$this->urn, '-');
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

        if (!($this->meta['dontUpdateAffectedPages'] ?? false)) {
            // 2019-04-25, AVS: обновим связанные страницы
            // 2020-02-10, AVS: добавил условие для загрузчика прайсов
            // (чтобы было быстрее)
            static::updateAffectedPages(null, $this);
            if ($oldMaterialTypeId ?? false) {
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

        // 2021-07-06, AVS: добавили условие для скоростного обновления
        if (!$this->meta['dontUpdateAffectedPages']) {
            // 2019-04-25, AVS: обновим связанные страницы
            static::updateAffectedPages(null, $this);
            Material_Type::updateAffectedPagesForSelf($this->material_type);
        }

        return true;
    }


    /**
     * Убрать материал со страницы. Работает только для неглобальных материалов
     * @param Page $page Страница, на которую нужно разместить материал
     * @return bool true в случае успешного завершения, false в случае неудачи
     */
    public function deassoc(Page $page)
    {
        $materialType = $this->material_type;
        $pagesIds = (array)$this->pages_ids;
        if ($materialType->global_type) {
            return false;
        }
        if ((count($pagesIds) < 2) || !in_array($page->id, $pagesIds)) {
            return false;
        }
        $sqlQuery = "DELETE FROM " . self::_dbprefix() . self::$links['pages']['tablename']
                   . " WHERE id = " . (int)$this->id
                   . "   AND pid = " . (int)$page->id;
        self::$SQL->query($sqlQuery);

        // 2020-03-24, AVS: обновим дату связанного изменения
        $this->modify();

        // 2021-07-06, AVS: добавили условие для скоростного обновления
        if (!$this->meta['dontUpdateAffectedPages']) {
            // 2019-04-25, AVS: обновим связанные страницы
            static::updateAffectedPages(null, $this);
            Material_Type::updateAffectedPagesForSelf($materialType);
        }

        return true;
    }


    /**
     * Сохранить привязку к страницам
     */
    private function exportPages()
    {
        $tablename = self::_dbprefix() . self::$links['pages']['tablename'];
        if ($this->meta['cats'] ?? []) {
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
        } elseif (!($this->meta['dontCheckPages'] ?? false)) {
            // 2020-02-10, AVS: добавил условие dontCheckPages
            // для ускорения загрузчика прайсов
            if ($this->material_type->global_type) {
                $sqlQuery = "DELETE FROM " . $tablename
                           . " WHERE id = " . (int)$this->id;
                self::$SQL->query($sqlQuery);
            }
        }
    }


    public static function batchDelete(array $objects)
    {
        $st = microtime(true);
        if (!$objects) {
            return;
        }

        $objectsIds = array_map(function ($x) {
            return (int)$x->id;
        }, $objects);

        // Удалим файловые поля с проверкой на совместное использование
        // Найдем все файловые поля и файловые поля материалов
        $sqlQuery = "SELECT id FROM " . Field::_tablename() . " WHERE datatype IN ('image', 'file')";
        $allFilesFieldsIds = array_map('intval', Field::_SQL()->getcol($sqlQuery));


        $sqlQuery = "SELECT id
                       FROM " . Field::_tablename()
                  . " WHERE classname = ?
                        AND pid
                        AND datatype IN ('image', 'file')";
        $materialFilesFieldsIds = array_map('intval', Field::_SQL()->getcol([$sqlQuery, Material_Type::class]));

        $affectedAttachmentsIds = [];
        if ($materialFilesFieldsIds) {
            // Найдем используемые значения файловых полей в данном материале
            $sqlQuery = "SELECT value
                           FROM " . static::_dbprefix() . "cms_data
                          WHERE pid IN (" . implode(", ", $objectsIds) . ")
                            AND fid IN (" . implode(", ", $materialFilesFieldsIds) . ")";
            $sqlResult = static::_SQL()->getcol($sqlQuery);

            $affectedAttachmentsIds = array_values(array_filter(array_map(function ($x) {
                $json = (array)json_decode($x, true);
                $attachmentId = (int)$json['attachment'];
                return $attachmentId;
            }, $sqlResult)));
        }

        // Удалим данные из cms_data
        $sqlQuery = "DELETE tD
                       FROM " . static::_dbprefix() . "cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                      WHERE tD.pid IN (" . implode(", ", $objectsIds) . ")
                        AND tF.classname = ?
                        AND tF.pid";
        $sqlResult = static::_SQL()->getcol([$sqlQuery, Material_Type::class]);

        parent::batchDelete($objects);

        if ($affectedAttachmentsIds) {
            // Найдем используемые значения файловых полей вне данного материала
            $sqlQuery = "SELECT value
                           FROM " . static::_dbprefix() . "cms_data
                          WHERE fid IN (" . implode(", ", $allFilesFieldsIds) . ")";

            $sqlResult = static::_SQL()->getcol([$sqlQuery]);
            $otherAttachmentsIds = array_values(array_filter(array_map(function ($x) {
                $json = (array)json_decode($x, true);
                $attachmentId = (int)$json['attachment'];
                return $attachmentId;
            }, $sqlResult)));

            $attachmentsToDelete = array_diff($affectedAttachmentsIds, $otherAttachmentsIds);

            foreach ($attachmentsToDelete as $attachmentToDelete) {
                $att = new Attachment($attachmentToDelete);
                Attachment::delete($att);
            }
        }

        // 2019-01-24, AVS: добавил удаление из связанных данных
        // несуществующих материалов
        // 2023-04-28, AVS: переделал на отдельные запросы для ускорения (было 44сек, стало 4.8сек),
        $sqlQuery = "SELECT id FROM " . Field::_tablename() . " WHERE datatype = 'material'";
        $materialFieldsIds = array_map('intval', Material::_SQL()->getcol($sqlQuery));
        if ($materialFieldsIds) {
            $sqlQuery = "SELECT id FROM " . static::_tablename();
            $allMaterialsIds = array_map('trim', Material::_SQL()->getcol($sqlQuery));
            if ($allMaterialsIds) {
                $sqlQuery = "DELETE tD
                               FROM cms_data AS tD
                              WHERE tD.fid IN (" . implode(", ", $materialFieldsIds) . ")
                                AND tD.value NOT IN (" . implode(", ", array_fill(0, count($allMaterialsIds), '?')) . ")";
                $sqlBind = $allMaterialsIds;
                $result = static::$SQL->query([$sqlQuery, $sqlBind]);
            }
        }

        $dontUpdateAffectedPagesArr = array_filter($objects, function ($x) {
            return (bool)($x->meta['dontUpdateAffectedPages'] ?? false);
        });
        $dontUpdateAffectedPages = (count($dontUpdateAffectedPagesArr) == count($objects));
        // 2021-07-06, AVS: добавили условие для скоростного обновления
        if (!$dontUpdateAffectedPages) {
            $mtype = $material = null;
            $mTypesIds = array_values(array_unique(array_map(function ($x) {
                return (int)$x->pid;
            }, $objects)));
            if (count($mTypesIds) == 1) {
                $mtype = new Material_Type($mTypesIds[0]);
            }

            // 2019-04-25, AVS: обновим связанные страницы
            if (count($objects) == 1) {
                $materials = array_values($objects);
                static::updateAffectedPages(null, $materials[0]->deepClone());
            } else {
                static::updateAffectedPages($mtype);
            }
            Material_Type::updateAffectedPagesForSelf($mtype);
        }
    }


    /**
     * Возвращает поля материала с указанным свойством $Owner
     * @return array<Material_Field>
     */
    protected function _fields()
    {
        if (!isset(Material_Type::$fieldsCache[$this->pid]) ||
            !($temp = Material_Type::$fieldsCache[$this->pid])
        ) {
            $temp = $this->material_type->fields;
        }
        $arr = [];
        foreach ((array)$temp as $fieldURN => $field) {
            $field = $field->deepClone();
            $field->Owner = $this;
            $arr[trim((string)$fieldURN)] = $field;
        }
        return $arr;
    }


    /**
     * Список видимых полей
     * @return Material_Field[]
     */
    protected function _visFields()
    {
        if (!($temp = Material_Type::$visFieldsCache[$this->pid])) {
            $temp = $this->material_type->visFields;
        }
        $arr = [];
        foreach ((array)$temp as $fieldURN => $field) {
            $field = $field->deepClone();
            $field->Owner = $this;
            $arr[$fieldURN] = $field;
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
     * @param Material_Type $materialType Ограничить обновление одним типом материалов
     * @param Material $material Ограничить обновление одним материалом
     */
    public static function updateAffectedPages(Material_Type $materialType = null, Material $material = null)
    {
        $materialId = ($material->id ?? 0);
        if ($materialTypeId = ($materialType->id ?? 0)) {
            $materialTypesIds = $materialType->selfAndChildrenIds;
        }

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
            $materialTypesToPagesAssoc[trim((string)(string)$sqlRow['material_type_id'])][trim((string)(string)$sqlRow['page_id'])] = (int)$sqlRow['page_id'];
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
            $materialsToPagesAssoc[trim((string)$sqlRow['id'])][trim((string)$sqlRow['pid'])] = (int)$sqlRow['pid'];
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
            $materialsToMaterialTypesAssoc[trim((string)(string)$sqlRow['id'])] = (int)$sqlRow['pid'];
        }

        // Соберем привязку материалов к страницам
        $realMaterialsToPagesAssoc = [];
        $i = 0;
        foreach ((array)$materialsToMaterialTypesAssoc as $mId => $mtId) {
            if (!isset($materialTypesToPagesAssoc[$mtId])) {
                continue;
            }
            if (!isset($materialsToPagesAssoc[$mId])) {
                $realMaterialsToPagesAssoc[trim((string)$mId)] = $materialTypesToPagesAssoc[$mtId];
            } else {
                $realMaterialsToPagesAssoc[trim((string)$mId)] = array_intersect_key(
                    $materialTypesToPagesAssoc[$mtId],
                    $materialsToPagesAssoc[$mId]
                );
            }
        }

        // 2021-07-07, AVS: очистим память
        unset($materialTypesToPagesAssoc, $materialsToPagesAssoc, $materialsToMaterialTypesAssoc);

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

        // 2021-07-07, AVS: очистим память
        unset($realMaterialsToPagesAssoc);

        $sqlQuery = "START TRANSACTION";
        static::_SQL()->query($sqlQuery);

        // 2022-07-05, AVS: Очистим предыдущие данные - перенесли сюда чтобы упаковать в транзакцию
        $sqlQuery = "DELETE ";
        if (!$materialId && $materialTypeId) {
            $sqlQuery .= " tMAP ";
        }
        $sqlQuery .= " FROM " . static::$dbprefix . "cms_materials_affected_pages_cache ";
        if (!$materialId && $materialTypeId) {
            $sqlQuery .= " AS tMAP
                    LEFT JOIN " . static::_tablename() . " AS tM ON tM.id = tMAP.material_id ";
        }
        if ($materialId) {
            $sqlQuery .= " WHERE material_id = " . (int)$materialId;
        } elseif ($materialTypeId) {
            $sqlQuery .= " WHERE (
                                (tM.pid IN (" . implode(", ", $materialTypesIds) . "))
                             OR (tM.pid IS NULL)
                           )";
        }
        static::_SQL()->query($sqlQuery);

        if ($sqlArr) {
            // 2021-07-07, AVS: разделим по 1000 записей, чтобы база не падала
            for ($i = 0; $i < ceil(count($sqlArr) / 1000); $i++) {
                $sqlChunk = array_slice($sqlArr, $i * 1000, 1000);
                static::_SQL()->add(
                    static::$dbprefix . "cms_materials_affected_pages_cache",
                    $sqlChunk
                );
            }
        }

        $sqlQuery = "COMMIT";
        static::_SQL()->query($sqlQuery);

        // 2021-07-07, AVS: очистим память
        unset($sqlArr);

        // Соберем информацию о материалах
        $sqlQuery = "SELECT id, urn, page_id, cache_url_parent_id, cache_url
                       FROM " . static::_tablename();
        if ($materialId) {
            $sqlQuery .= " WHERE id = " . (int)$materialId;
        } elseif ($materialTypeId) {
            $sqlQuery .= " WHERE pid IN (" . implode(", ", $materialTypesIds) . ")";
        }
        $sqlResult = static::_SQL()->get($sqlQuery);
        $materialsData = [];
        $pagesData = [];
        foreach ($sqlResult as $sqlRow) {
            $materialsData[trim((string)$sqlRow['id'])] = [
                'id' => (int)$sqlRow['id'],
                'urn' => trim((string)$sqlRow['urn']),
                'page_id' => (int)$sqlRow['page_id'],
                'cache_url_parent_id' => (int)$sqlRow['cache_url_parent_id'],
                'cache_url' => trim((string)$sqlRow['cache_url']),
            ];
            $pagesData[trim((string)$sqlRow['page_id'])] = [
                'id' => (int)$sqlRow['page_id'],
            ];
            $pagesData[trim((string)$sqlRow['cache_url_parent_id'])] = [
                'id' => (int)$sqlRow['cache_url_parent_id'],
            ];
        }

        if ($materialsData) {
            $sqlQuery = "SELECT material_id, page_id
                           FROM " . static::$dbprefix . "cms_materials_affected_pages_cache
                          WHERE material_id IN (" . implode(", ", array_keys($materialsData)) . ")";
            $sqlResult = static::_SQL()->get($sqlQuery);
            foreach ($sqlResult as $sqlRow) {
                $materialsData[trim((string)$sqlRow['material_id'])]['affectedPages'][trim((string)$sqlRow['page_id'])] = [
                    'id' => (int)$sqlRow['page_id'],
                ];
                $pagesData[trim((string)$sqlRow['page_id'])] = [
                    'id' => (int)$sqlRow['page_id'],
                ];
            }
        }
        if ($pagesData) {
            $sqlQuery = "SELECT id, cache_url, priority
                           FROM " . Page::_tablename()
                      . " WHERE id IN (" . implode(", ", array_keys($pagesData)) . ")";
            $sqlResult = static::_SQL()->get($sqlQuery);
            foreach ($sqlResult as $sqlRow) {
                $pagesData[trim((string)$sqlRow['id'])] = [
                    'id' => (int)$sqlRow['id'],
                    'cache_url' => trim((string)$sqlRow['cache_url']),
                    'priority' => (int)$sqlRow['priority'],
                ];
            }
        }
        foreach ($materialsData as $materialId => $materialData) {
            if (($materialData['page_id'] ?? null) &&
                ($materialData['affectedPages'][$materialData['page_id']] ?? null)
            ) {
                $materialsData[$materialId]['new_cache_url_parent_id'] = $materialData['page_id'];
            } elseif ($materialData['cache_url_parent_id'] &&
                ($materialData['affectedPages'][$materialData['cache_url_parent_id']] ?? 0)
            ) {
                $materialsData[$materialId]['new_cache_url_parent_id'] = $materialData['cache_url_parent_id'];
            } elseif ($affectedPages = ($materialData['affectedPages'] ?? [])) {
                usort($affectedPages, function ($a, $b) use ($pagesData) {
                    $aPriority = $pagesData[$a['id']]['priority'];
                    $bPriority = $pagesData[$b['id']]['priority'];
                    return $aPriority - $bPriority;
                });
                $materialsData[$materialId]['new_cache_url_parent_id'] = $affectedPages[0]['id'];
            } else {
                $materialsData[$materialId]['new_cache_url_parent_id'] = 0;
            }

            if ($materialsData[$materialId]['new_cache_url_parent_id'] &&
                $pagesData[$materialsData[$materialId]['new_cache_url_parent_id']]
            ) {
                $materialsData[$materialId]['new_cache_url'] = $pagesData[$materialsData[$materialId]['new_cache_url_parent_id']]['cache_url']
                    . $materialsData[$materialId]['urn'] . '/';
            } else {
                $materialsData[$materialId]['new_cache_url'] = '';
            }
        }
        $sqlArr = [];
        foreach ($materialsData as $materialId => $materialData) {
            if (/*true ||*/ // Для теста, убрать
                ($materialData['new_cache_url_parent_id'] != $materialData['cache_url_parent_id']) ||
                ($materialData['new_cache_url'] != $materialData['cache_url'])
            ) {
                $sqlArr[] = [
                    'id' => $materialId,
                    'cache_url_parent_id' => $materialData['new_cache_url_parent_id'],
                    'cache_url' => $materialData['new_cache_url'],
                ];
            }
        }
        if ($sqlArr) {
            // 2022-06-06, AVS: разделим по 1000 записей, чтобы база не падала
            for ($i = 0; $i < ceil(count($sqlArr) / 1000); $i++) {
                $sqlChunk = array_slice($sqlArr, $i * 1000, 1000);
                static::_SQL()->add(static::_tablename(), $sqlChunk, [
                    'cache_url_parent_id' => (object)'VALUES(cache_url_parent_id)',
                    'cache_url' => (object)'VALUES(cache_url)',
                ]);
            }
        }
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
                        AND tF.source IN (" . implode(", ", $ids) . ")
                   GROUP BY tMT.id";
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
        $globPrefix = Package::i()->cacheDir . '/' . Package::i()->cachePrefix;
        if ($this->cache_url) {
            $globs = [];
            foreach ($this->urlParent->domains as $domain) {
                $globs[] = $globPrefix . '*'
                    . urlencode('//' . $domain . $this->cache_url) . '.php';
                $globs[] = $globPrefix
                    . '*' . urlencode('//' . $domain . $this->cache_url . '?')
                    . '*.php';
            }
            $files = [];
            foreach ($globs as $glob) {
                $files = array_merge($files, glob($glob));
            }
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }


    /**
     * Перестроить кэш материала
     * @return string|null Текст страницы
     */
    public function rebuildCache()
    {
        if (!$this->url) {
            return;
        }
        $this->clearCache();
        $url = $this->fullURL;
        $url = preg_replace('/^(http(s)?:)?\/\//umis', '', $url);
        $url = 'http' . (($_SERVER['HTTPS'] == 'on') ? 's' : '') . '://' . $url;
        $text = @file_get_contents($url, false, stream_context_create([
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]));
        return $text;
    }
}
