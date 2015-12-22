<?php
namespace RAAS\CMS;
use \RAAS\Attachment;

class Feedback extends \SOME\SOME
{
    protected static $tablename = 'cms_feedback';
    protected static $defaultOrderBy = "post_date DESC";
    protected static $objectCascadeDelete = true;
    protected static $cognizableVars = array('fields');

    protected static $references = array(
        'user' => array('FK' => 'uid', 'classname' => 'RAAS\\CMS\\User', 'cascade' => true),
        'parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Form', 'cascade' => true),
        'page' => array('FK' => 'page_id', 'classname' => 'RAAS\\CMS\\Page', 'cascade' => false),
        'material' => array('FK' => 'material_id', 'classname' => 'RAAS\\CMS\\Material', 'cascade' => false),
        'viewer' => array('FK' => 'vis', 'classname' => 'RAAS\\User', 'cascade' => false),
    );
    
    public function __get($var)
    {
        switch ($var) {
            case 'domain':
                return $this->page->domain;
                break;
            case 'description':
                foreach ($this->fields as $field) {
                    $values = $field->getValues(true);
                    $arr = array();
                    foreach ($values as $key => $val) {
                        $val = $field->doRich($val);
                        switch ($field->datatype) {
                            case 'date':
                                $arr[$key] = date(DATEFORMAT, strtotime($val));
                                break;
                            case 'datetime-local':
                                $arr[$key] = date(DATETIMEFORMAT, strtotime($val));
                                break;
                            case 'email': case 'url':
                                $arr[$key] .= $val;
                                break;
                            case 'htmlarea':
                                $arr[$key] = strip_tags($val);
                                break;
                            case 'file': case 'image': case 'material':
                                break;
                            default:
                                if (!$field->multiple && ($field->datatype == 'checkbox')) {
                                    $arr[$key] = $val ? _YES : _NO;
                                } else {
                                    $arr[$key] = $val;
                                }
                                break;
                        }
                        $arr[$key] = \SOME\Text::cuttext(preg_replace('/\\s/i', ' ', $arr[$key]), 32);
                    }
                    $arr = array_filter($arr, 'trim');
                    if ($arr) {
                        $text .= /*$field->name . ': ' . */implode(', ', $arr) . '; ';
                    }
                    if (mb_strlen($text) > 256) {
                        break;
                    }
                }
                $text = \SOME\Text::cuttext($text, 256);
                return $text;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }
    
    public function commit()
    {
        if (!$this->id) {
            $this->post_date = date('Y-m-d H:i:s');
        }
        parent::commit();
    }
    
    
    public static function delete(self $object)
    {
        foreach ($object->fields as $row) {
            if (in_array($row->datatype, array('image', 'file'))) {
                foreach ($row->getValues(true) as $att) {
                    Attachment::delete($att);
                }
            }
            $row->deleteValues();
        }
        parent::delete($object);
    }
    
    
    protected function _fields()
    {
        $temp = $this->parent->fields;
        $arr = array();
        foreach ($temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }

    public static function unreadFeedbacks()
    {
        $SQL_query = "SELECT COUNT(*) FROM " . static::_tablename() . " WHERE NOT vis";
        return static::$SQL->getvalue($SQL_query);
    }
}