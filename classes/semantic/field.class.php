<?php
namespace RAAS\CMS;
use \RAAS\Attachment as Attachment;
use \RAAS\Application as Application;

abstract class Field extends \RAAS\CustomField
{
    const data_table = 'cms_data';
    const DictionaryClass = '\\RAAS\\CMS\\Dictionary';
    
    protected static $tablename = 'cms_fields';
    
    public function __get($var)
    {
        switch ($var) {
            case 'Field':
                $t = $this;
                $f = parent::__get($var);
                switch ($t->datatype) {
                    case 'file': case 'image':
                        $f->template = 'cms/field.inc.php';
                        if ($f->required) {
                            $f->check = function($Field) {
                                $localError = array();
                                $ok = false;
                                if ($Field->multiple) {
                                    if ((array)$_FILES[$Field->name]['tmp_name']) {
                                        foreach ((array)$_FILES[$Field->name]['tmp_name'] as $i => $val) {
                                            if (isset($_POST[$Field->name . '@attachment'][$i]) && $_POST[$Field->name . '@attachment'][$i]) {
                                                $ok = true;
                                                break;
                                            }
                                        }
                                    }
                                } else {
                                    if (!is_uploaded_file($_FILES[$Field->name]['tmp_name']) && isset($_POST[$Field->name . '@attachment']) && trim($_POST[$Field->name . '@attachment'])) {
                                        $ok = true;
                                    }
                                }
                                if ($ok) {
                                    return array(); 
                                }
                                return $Field->getErrors();
                            };
                        }
                        break;
                    case 'material':
                        $f->template = 'cms/field.inc.php';
                        break;
                }
                $f->oncommit = function($Field) use ($t) {
                    switch ($t->datatype) {
                        case 'file': case 'image':
                            $t->deleteValues();
                            if ($Field->multiple) {
                                foreach ($_FILES[$Field->name]['tmp_name'] as $key => $val) {
                                    $row2 = array(
                                        'vis' => (int)$_POST[$Field->name . '@vis'][$key], 
                                        'name' => (string)$_POST[$Field->name . '@name'][$key],
                                        'description' => (string)$_POST[$Field->name . '@description'][$key],
                                        'attachment' => (int)$_POST[$Field->name . '@attachment'][$key]
                                    );
                                    if (is_uploaded_file($_FILES[$Field->name]['tmp_name'][$key]) && $t->validate($_FILES[$Field->name]['tmp_name'][$key])) {
                                        $att = new Attachment((int)$row2['attachment']);
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
                                        $row2['attachment'] = (int)$att->id;
                                        $t->addValue(json_encode($row2));
                                    } elseif ($row2['attachment']) {
                                        $t->addValue(json_encode($row2));
                                    }
                                    unset($att, $row2);
                                }
                            } else {
                                $row2 = array(
                                    'vis' => (int)$_POST[$Field->name . '@vis'], 
                                    'name' => (string)$_POST[$Field->name . '@name'], 
                                    'description' => (string)$_POST[$Field->name . '@description'],
                                    'attachment' => (int)$_POST[$Field->name . '@attachment']
                                );
                                if (is_uploaded_file($_FILES[$Field->name]['tmp_name']) && $t->validate($_FILES[$Field->name]['tmp_name'])) {
                                    $att = new Attachment((int)$row2['attachment']);
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
                                    $t->addValue($val);
                                }
                            }
                            break;
                    }
                };
                return $f;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }
    
    public function getValue($index = 0)
    {
        if (!$this->Owner || !static::data_table) {
            return null;
        }
        switch ($this->datatype) {
            case 'image': case 'file':
                $SQL_query = "SELECT value FROM " . static::$dbprefix . static::data_table . " WHERE pid = ? AND fid = ? AND fii = ?";
                $SQL_bind = array((int)$this->Owner->id, (int)$this->id, (int)$index);
                $y = (array)json_decode(self::$SQL->getvalue(array($SQL_query, $SQL_bind)), true); 
                $att = new Attachment((int)(isset($y['attachment']) ? $y['attachment'] : 0));
                foreach ($y as $key => $val) {
                    $att->$key = $val;
                } 
                return $att;
                break;
            case 'material':
                return new Material(parent::getValue($index));
                break;
            default:
                return parent::getValue($index);
                break;
        }
    }
    
    
    public function getValues($forceArray = false)
    {
        if (!$this->Owner || !static::data_table) {
            return null;
        }
        if (!$this->multiple && !$forceArray) {
            return $this->getValue();
        }
        switch ($this->datatype) {
            case 'image': case 'file':
                $SQL_query = "SELECT value FROM " . static::$dbprefix . static::data_table . " WHERE pid = ? AND fid = ? ORDER BY fii ASC";
                $SQL_bind = array((int)$this->Owner->id, (int)$this->id);
                $values = array_map(function($x) { 
                    $y = (array)json_decode($x, true); 
                    $att = new Attachment((int)(isset($y['attachment']) ? $y['attachment'] : 0));
                    foreach ($y as $key => $val) {
                        $att->$key = $val;
                    } 
                    return $att;
                }, self::$SQL->getcol(array($SQL_query, $SQL_bind)));
                return $values;
                break;
            case 'material':
                return array_map(function($x) { return new Material($x); }, parent::getValues($forceArray));
                break;
            default:
                return parent::getValues($forceArray);
                break;
        }
    }
    
    
    public function clearLostAttachments()
    {
        if (in_array($this->datatype, array('file', 'image'))) {
            $SQL_query = "SELECT value FROM " . static::$dbprefix . static::data_table . " WHERE fid = " . (int)$this->id;
            $SQL_result = self::$SQL->getcol($SQL_query);
            $SQL_result = array_map(function($x) { $x = @(array)json_decode($x, true); return @(int)$x['attachment']; }, $SQL_result);
            $SQL_result = array_filter($SQL_result, 'intval');
            $SQL_query = "SELECT * FROM " . Attachment::_tablename() . " 
                           WHERE classname = '" . self::$SQL->real_escape_string(get_class($this)) . "' AND pid = " . (int)$this->id;
            if ($SQL_result) {
                $SQL_query .= " AND id NOT IN (" . implode(", ", $SQL_result) . ")";
            }
            $SQL_result = Attachment::getSQLSet($SQL_query);
            if ($SQL_result) {
                foreach ($SQL_result as $row) {
                    Attachment::delete($row);
                }
            }
        }
    }


    public function show_in_table()
    {
        $this->show_in_table = (int)!(bool)$this->show_in_table;
        $this->commit();
    }


    public static function getSet()
    {
        $args = func_get_args();
        if (!isset($args[0]['where'])) {
            $args[0]['where'] = array();
        } else {
            $args[0]['where'] = (array)$args[0]['where'];
        }
        $args[0]['where'][] = "classname = '" . static::$SQL->real_escape_string(static::$references['parent']['classname']) . "'";
        return call_user_func_array('parent::getSet', $args);
    }
}