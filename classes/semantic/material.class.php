<?php
namespace RAAS\CMS;

class Material extends \SOME\SOME
{
    protected static $tablename = 'cms_materials';
    protected static $defaultOrderBy = "post_date DESC";
    protected static $cognizableVars = array('fields', 'affectedPages', 'relatedMaterialTypes');

    protected static $references = array(
        'material_type' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Material_Type', 'cascade' => true),
        'author' => array('FK' => 'author_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'editor' => array('FK' => 'editor_id', 'classname' => 'RAAS\\User', 'cascade' => false),
    );
    protected static $links = array('pages' => array('tablename' => 'cms_materials_pages_assoc', 'field_from' => 'id', 'field_to' => 'pid', 'classname' => 'RAAS\\CMS\\Page'));
    
    public function __get($var)
    {
        switch ($var) {
            case 'parents':
                if ($this->pages) {
                    return $this->pages;
                } elseif ($this->affectedPages) {
                    return $this->affectedPages;
                } else {
                    return array();
                }
                break;
            case 'parents_ids':
                return array_map(function($x) { return (int)$x->id; }, $this->parents);
                break;
            case 'parent':
                if ($this->parents) {
                    if ((int)$this->page_id && in_array($this->page_id, $this->parents_ids)) {
                        return new Page((int)$this->page_id);
                    } else {
                        return $this->parents[0];
                    }
                }
                return new Page();
                break;
            default:
                $val = parent::__get($var);
                if ($val !== null) {
                    return $val;
                } else {
                    if (substr($var, 0, 3) == 'vis') {
                        $var = strtolower(substr($var, 3));
                        $vis = true;
                    }
                    if (isset($this->fields[$var]) && ($this->fields[$var] instanceof Material_Field)) {
                        $temp = $this->fields[$var]->getValues();
                        if ($vis) {
                            $temp = array_values(array_filter((array)$temp, function($x) { return isset($x->vis) && $x->vis; }));
                        }
                        return $temp;
                    }
                    if ((strtolower($var) == 'url') && !isset($temp)) {
                        // Размещаем сюда из-за большого количества баннеров, где URL задан явно
                        return $this->parent->url . $this->urn . '/';
                    }
                }
                break;
        }
    }
    
    
    public function commit()
    {
        $this->modify(false);
        $this->modify_date = date('Y-m-d H:i:s');
        if (!$this->id) {
            $this->post_date = $this->modify_date;
        }
        if ($this->pid && !$this->urn && $this->name) {
            $this->urn = \SOME\Text::beautify($this->name);
        }
        while (
            (int)self::$SQL->getvalue(array("SELECT COUNT(*) FROM " . self::_tablename() . " WHERE urn = ? AND id != ?", $this->urn, (int)$this->id)) ||
            (int)self::$SQL->getvalue(array("SELECT COUNT(*) FROM " . Page::_tablename() . " WHERE urn = ?", $this->urn))
        ) {
            $this->urn = '_' . $this->urn . '_';
        }
        parent::commit();
        $this->exportPages();
        $this->reload();
        foreach ($this->parents as $row) {
            $row->modify();
        }
    }
    
    
    public function visit()
    {
        $this->visit_counter++;
        parent::commit();
    }


    public function modify($commit = true)
    {
        $d0 = time();
        $d1 = strtotime($this->modify_date);
        $d2 = strtotime($this->last_modified);
        if ((time() - $d1 >= 3600) && (time() - $d2 >= 3600)) {
            $this->last_modified = date('Y-m-d H:i:s');
            $this->modify_counter++;
            if ($commit) {
                parent::commit();
            }
        }
    }
    
    
    private function exportPages()
    {
        if ($this->cats) {
            $SQL_query = "DELETE FROM " . self::_dbprefix() . self::$links['pages']['tablename'] . " WHERE id = " . (int)$this->id;
            self::$SQL->query($SQL_query);
            $id = (int)$this->id;
            $arr = array_map(function($x) use ($id) { return array('id' => $id, 'pid' => $x); }, (array)$this->cats);
            unset($this->cats);
            self::$SQL->add(self::$dbprefix . self::$links['pages']['tablename'], $arr);
        } elseif ($this->material_type->global_type) {
            $SQL_query = "DELETE FROM " . self::_dbprefix() . self::$links['pages']['tablename'] . " WHERE id = " . (int)$this->id;
            self::$SQL->query($SQL_query);
        }
    }
    
    
    public static function delete(self $object)
    {
        foreach ($object->fields as $row) {
            $row->deleteValues();
        }
        parent::delete($object);
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

    
    protected function _fields()
    {
        $temp = $this->material_type->fields;
        $arr = array();
        foreach ($temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    protected function _affectedPages()
    {
        $SQL_query = "SELECT tP.*
                        FROM " . Page::_tablename() . " AS tP
                        JOIN " . self::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                        JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id
                        JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.id = tB.id
                        JOIN " . Material_Type::_tablename() . " AS tMt ON tMt.id = tBM.material_type
                       WHERE tB.vis AND tB.nat AND tMt.id = " . (int)$this->pid;
        $Set = Page::getSQLSet($SQL_query);
        return $Set;
    }


    protected function _relatedMaterialTypes()
    {
        $ids = array_merge(array(0, (int)$this->material_type->id), (array)$this->material_type->parents_ids);
        $SQL_query = "SELECT tMT.* 
                        FROM " . Material_Type::_tablename() . " AS tMT
                        JOIN " . Material_Field::_tablename() . " AS tF ON tF.classname = 'RAAS\\\\CMS\\\\Material_Type' AND tF.pid = tMT.id
                        WHERE tF.datatype = 'material' AND source IN (" . implode(", ", $ids) . ")";
        return Material_Type::getSQLSet($SQL_query);
    }

}