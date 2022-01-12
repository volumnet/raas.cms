<?php
/**
 * Поле
 */
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Attachment;
use RAAS\Application;
use RAAS\CustomField;
use RAAS\Field as RAASField;

/**
 * Класс поля
 * @property-read RAASField $Field Поле для формы редактирования
 * @property-read Snippet $Preprocessor Препроцессор поля
 * @property-read Snippet $Postprocessor Постпроцессор поля
 */
class Field extends CustomField
{
    /**
     * Таблица данных
     */
    const data_table = 'cms_data';

    /**
     * Класс справочника
     */
    const DictionaryClass = '\\RAAS\\CMS\\Dictionary';

    protected static $objectCascadeDelete = true;

    protected static $references = [
        'Preprocessor' => [
            'FK' => 'preprocessor_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
        'Postprocessor' => [
            'FK' => 'postprocessor_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
    ];

    protected static $tablename = 'cms_fields';

    public function __get($var)
    {
        switch ($var) {
            case 'Field':
                $t = $this;
                $f = parent::__get($var);
                switch ($t->datatype) {
                    case 'file':
                    case 'image':
                        $f->template = 'cms/field.inc.php';
                        $f->check = function ($Field) {
                            $localError = [];
                            $ok = !$Field->required;
                            $allowedExtensions = preg_split(
                                '/\\W+/umis',
                                $this->source
                            );
                            $allowedExtensions = array_map(
                                'mb_strtolower',
                                array_filter($allowedExtensions, 'trim')
                            );
                            if ($Field->multiple) {
                                if ((array)$_FILES[$Field->name]['tmp_name']) {
                                    if ($Field->required) {
                                        foreach ((array)$_FILES[$Field->name]['tmp_name'] as $i => $val) {
                                            if (isset($_POST[$Field->name . '@attachment'][$i]) &&
                                                $_POST[$Field->name . '@attachment'][$i]
                                            ) {
                                                $ok = true;
                                                break;
                                            }
                                        }
                                    }
                                    foreach ((array)$_FILES[$Field->name]['tmp_name'] as $i => $val) {
                                        if ($allowedExtensions &&
                                            is_uploaded_file($_FILES[$Field->name]['tmp_name'][$i])
                                        ) {
                                            $ext = pathinfo(
                                                $_FILES[$Field->name]['name'][$i],
                                                PATHINFO_EXTENSION
                                            );
                                            $ext = mb_strtolower($ext);
                                            if (!in_array($ext, $allowedExtensions)) {
                                                $localError[] = [
                                                    'name' => 'INVALID',
                                                    'value' => $this->name,
                                                    'description' => sprintf(
                                                        View_Web::i()->_('INVALID_FILE_EXTENSION'),
                                                        implode(', ', $allowedExtensions)
                                                    )
                                                ];
                                                $ok = false;
                                                break;
                                            }
                                        }
                                    }
                                }
                            } else {
                                if (!is_uploaded_file($_FILES[$Field->name]['tmp_name']) &&
                                    isset($_POST[$Field->name . '@attachment']) &&
                                    trim($_POST[$Field->name . '@attachment'])) {
                                    $ok = true;
                                }
                                if ($allowedExtensions &&
                                    is_uploaded_file($_FILES[$Field->name]['tmp_name'])
                                ) {
                                    $ext = pathinfo(
                                        $_FILES[$Field->name]['name'],
                                        PATHINFO_EXTENSION
                                    );
                                    $ext = mb_strtolower($ext);
                                    if (!in_array($ext, $allowedExtensions)) {
                                        $localError[] = [
                                            'name' => 'INVALID',
                                            'value' => $this->name,
                                            'description' => sprintf(
                                                $this->view->_('INVALID_FILE_EXTENSION'),
                                                implode(', ', $allowedExtensions)
                                            )
                                        ];
                                        $ok = false;
                                    }
                                }
                            }
                            if ($ok) {
                                return [];
                            }
                            $originalErrors = $Field->getErrors();
                            return array_merge($originalErrors, $localError);
                        };
                        break;
                    case 'material':
                        $f->template = 'cms/field.inc.php';
                        break;
                }
                if ($t->defval) {
                    $f->default = $t->defval;
                }
                $f->oncommit = function ($Field) use ($t) {
                    if ($t->Preprocessor->id) {
                        $postProcess = false;
                        eval('?' . '>' . $t->Preprocessor->description);
                    }
                    switch ($t->datatype) {
                        case 'file':
                        case 'image':
                            $addedAttachments = [];
                            $t->deleteValues();
                            if ($Field->multiple) {
                                foreach ((array)$_FILES[$Field->name]['tmp_name'] as $key => $val) {
                                    $row2 = [
                                        'vis' => (int)$_POST[$Field->name . '@vis'][$key],
                                        'name' => (string)$_POST[$Field->name . '@name'][$key],
                                        'description' => (string)$_POST[$Field->name . '@description'][$key],
                                        'attachment' => (int)$_POST[$Field->name . '@attachment'][$key]
                                    ];
                                    if (is_uploaded_file($_FILES[$Field->name]['tmp_name'][$key]) &&
                                        $t->validate($_FILES[$Field->name]['tmp_name'][$key])
                                    ) {
                                        // 2017-09-05, AVS: убрал создание attachment'а
                                        // по ID#, чтобы не было конфликтов
                                        // в случае дублирования материалов
                                        // с одним attachment'ом
                                        // с текущего момента каждый новый
                                        // загруженный файл - это новый attachment
                                        $att = new Attachment();
                                        $att->upload = $_FILES[$Field->name]['tmp_name'][$key];
                                        $att->filename = $_FILES[$Field->name]['name'][$key];
                                        $att->mime = $_FILES[$Field->name]['type'][$key];
                                        $att->parent = $t;
                                        if ($t->datatype == 'image') {
                                            $att->image = 1;
                                            if ($temp = (int)Application::i()->context->registryGet('maxsize')) {
                                                $att->maxWidth = $att->maxHeight = $temp;
                                            }
                                            if ($temp = (int)Application::i()->context->registryGet('tnsize')) {
                                                $att->tnsize = $temp;
                                            }
                                        }
                                        $att->commit();
                                        $addedAttachments[] = $att;
                                        $row2['attachment'] = (int)$att->id;
                                        $t->addValue(json_encode($row2));
                                    } elseif ($row2['attachment']) {
                                        $t->addValue(json_encode($row2));
                                    }
                                    unset($att, $row2);
                                }
                            } else {
                                $row2 = [
                                    'vis' => (int)$_POST[$Field->name . '@vis'],
                                    'name' => (string)$_POST[$Field->name . '@name'],
                                    'description' => (string)$_POST[$Field->name . '@description'],
                                    'attachment' => (int)$_POST[$Field->name . '@attachment']
                                ];
                                if (is_uploaded_file($_FILES[$Field->name]['tmp_name']) &&
                                    $t->validate($_FILES[$Field->name]['tmp_name'])
                                ) {
                                    // 2017-09-05, AVS: убрал создание
                                    // attachment'а по ID#, чтобы не было
                                    // конфликтов в случае дублирования
                                    // материалов с одним attachment'ом
                                    // с текущего момента каждый новый
                                    // загруженный файл - это новый attachment
                                    $att = new Attachment();
                                    $att->upload = $_FILES[$Field->name]['tmp_name'];
                                    $att->filename = $_FILES[$Field->name]['name'];
                                    $att->mime = $_FILES[$Field->name]['type'];
                                    $att->parent = $t;
                                    if ($t->datatype == 'image') {
                                        $att->image = 1;
                                        if ($temp = (int)Application::i()->context->registryGet('maxsize')) {
                                            $att->maxWidth = $att->maxHeight = $temp;
                                        }
                                        if ($temp = (int)Application::i()->context->registryGet('tnsize')) {
                                            $att->tnsize = $temp;
                                        }
                                    }
                                    $att->commit();
                                    $addedAttachments[] = $att;
                                    $row2['attachment'] = (int)$att->id;
                                    $t->addValue(json_encode($row2));
                                } elseif ($_POST[$Field->name . '@attachment']) {
                                    $row2['attachment'] = (int)$_POST[$Field->name . '@attachment'];
                                    $t->addValue(json_encode($row2));
                                }
                                unset($att, $row2);
                            }
                            $t->clearLostAttachments();
                            break;
                        default:
                            $t->deleteValues();
                            if (isset($_POST[$Field->name])) {
                                foreach ((array)$_POST[$Field->name] as $val) {
                                    // 2019-01-24, AVS: добавил условие, чтобы
                                    // не добавлялись пустые слоты материалов
                                    if (($t->datatype == 'material') && !(int)$val) {
                                        continue;
                                    }
                                    $t->addValue($val);
                                }
                            }
                            break;
                    }
                    if ($t->Postprocessor->id) {
                        $postProcess = true;
                        eval('?' . '>' . $t->Postprocessor->description);
                    }
                };
                return $f;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    /**
     * Получает значение поля по заданному индексу
     * @param int $index Индекс
     * @return mixed
     */
    public function getValue($index = 0)
    {
        if (!$this->Owner || !static::data_table) {
            return null;
        }
        switch ($this->datatype) {
            case 'image':
            case 'file':
                $this->prefetchIfNotExists();
                $value = static::$cache[$this->Owner->id][$this->id][$index];
                $y = (array)json_decode($value, true);
                $att = new Attachment(
                    (int)(isset($y['attachment']) ? $y['attachment'] : 0)
                );
                foreach ($y as $key => $val) {
                    $att->$key = $val;
                }
                return $att;
                break;
            case 'number':
                return str_replace(',', '.', parent::getValue($index));
                break;
            case 'material':
                return new Material(parent::getValue($index));
                break;
            default:
                return parent::getValue($index);
                break;
        }
    }


    /**
     * Получает значения поля
     * @param bool $forceArray Представлять результат в виде массива, даже если
     *                         значение одно
     * @return mixed|array<mixed>
     */
    public function getValues($forceArray = false)
    {
        if (!$this->Owner || !static::data_table) {
            return null;
        }
        if (!$this->multiple && !$forceArray) {
            return $this->getValue();
        }
        switch ($this->datatype) {
            case 'image':
            case 'file':
                $this->prefetchIfNotExists();
                $values = (array)static::$cache[$this->Owner->id][$this->id];
                $values = array_map(function ($x) {
                    $y = (array)json_decode($x, true);
                    $att = new Attachment(
                        (int)(isset($y['attachment']) ? $y['attachment'] : 0)
                    );
                    foreach ($y as $key => $val) {
                        $att->$key = $val;
                    }
                    return $att;
                }, $values);
                return $values;
                break;
            case 'number':
                return array_map(function ($x) {
                    return str_replace(',', '.', $x);
                }, (array)parent::getValues($forceArray));
                break;
            case 'material':
                return array_map(function ($x) {
                    return new Material($x);
                }, (array)parent::getValues($forceArray));
                break;
            default:
                return parent::getValues($forceArray);
                break;
        }
    }


    /**
     * Очищает "потерянные" вложения
     */
    public function clearLostAttachments()
    {
        if (in_array($this->datatype, ['file', 'image'])) {
            $sqlQuery = "SELECT value
                           FROM " . static::$dbprefix . static::data_table
                      . " WHERE fid = ?";
            $sqlResult = static::$SQL->getcol([$sqlQuery, (int)$this->id]);
            $sqlResult = array_map(function ($x) {
                $x = @(array)json_decode($x, true);
                return @(int)$x['attachment'];
            }, $sqlResult);
            $sqlResult = array_filter($sqlResult, 'intval');

            $sqlQuery = "SELECT *
                           FROM " . Attachment::_tablename() . "
                          WHERE classname = ?
                            AND pid = ?";
            $sqlBind = [get_class($this), (int)$this->id];
            if ($sqlResult) {
                $sqlQuery .= " AND id NOT IN (" . implode(", ", $sqlResult) . ")";
            }
            $sqlResult = Attachment::getSQLSet([$sqlQuery, $sqlBind]);
            if ($sqlResult) {
                foreach ($sqlResult as $row) {
                    Attachment::delete($row);
                }
            }
        }
    }


    /**
     * Меняет значение свойства "отображать в таблице"
     */
    public function show_in_table()
    {
        $this->show_in_table = (int)!(bool)$this->show_in_table;
        $this->commit();
    }


    /**
     * Меняет значение свойства "обязательно для заполнения"
     */
    public function required()
    {
        $this->required = (int)!(bool)$this->required;
        $this->commit();
    }


    public static function delete(SOME $object)
    {
        $sqlQuery = "DELETE FROM " . static::$dbprefix . static::data_table
                  . " WHERE fid = ?";
        static::$SQL->query([$sqlQuery, (int)$object->id]);
        if (in_array($object->datatype, ['image', 'file'])) {
            $object->clearLostAttachments();
        }
        parent::delete($object);
    }
}
