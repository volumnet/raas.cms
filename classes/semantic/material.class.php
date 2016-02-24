<?php
namespace RAAS\CMS;

use \RAAS\Attachment;
use \RAAS\Application;

class Material extends \SOME\SOME implements IAccessible
{
    protected static $tablename = 'cms_materials';
    protected static $defaultOrderBy = "post_date DESC";
    protected static $objectCascadeDelete = true;
    protected static $cognizableVars = array('fields', 'affectedPages', 'relatedMaterialTypes');

    protected static $references = array(
        'material_type' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Material_Type', 'cascade' => true),
        'author' => array('FK' => 'author_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'editor' => array('FK' => 'editor_id', 'classname' => 'RAAS\\User', 'cascade' => false),
    );
    protected static $children = array(
        'access' => array('classname' => 'RAAS\\CMS\\CMSAccess', 'FK' => 'material_id'),
    );
    protected static $links = array(
        'pages' => array('tablename' => 'cms_materials_pages_assoc', 'field_from' => 'id', 'field_to' => 'pid', 'classname' => 'RAAS\\CMS\\Page'),
        'allowedUsers' => array('tablename' => 'cms_access_materials_cache', 'field_from' => 'material_id', 'field_to' => 'uid', 'classname' => 'RAAS\\CMS\\User'),
    );
    
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
                    // echo $var . ' = ' . $val; exit;
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
                        // 2015-06-21, AVS: заменили parent на affectedPages[0], т.к. зачастую, если новость задана и на главной и на странице новостей, 
                        // url по умолчанию ведет на главную, где нет nat'а
                        // 2016-02-09, AVS: делаем проверку, а вообще есть ли affectedPages
                        // Если нет, то и URL у материала по сути нет
                        return $this->affectedPages ? ($this->affectedPages[0]->url . $this->urn . '/') : null;
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
            $this->urn = $this->name;
        }
        if ($this->updates['urn']) {
            $this->urn = \SOME\Text::beautify($this->urn);
        }
        $need2UpdateURN = false;
        if ($this->checkForSimilarPages() || Package::i()->checkForSimilar($this)) {
            $need2UpdateURN = true;
        }
        parent::commit();
        if ($need2UpdateURN) {
            if (!preg_match('/-\\d+$/', $this->urn)) {
                $this->urn .= '-' . $this->id;
            }
            for ($i = 0; $this->checkForSimilarPages() || Package::i()->checkForSimilar($this); $i++) {
                $this->urn = Application::i()->getNewURN($this->urn, !$i);
            }
            parent::commit();
        }
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


    public function userHasAccess(User $user)
    {
        $a = CMSAccess::userHasCascadeAccess($this, $user);
        return ($a >= 0);
    }


    public function currentUserHasAccess()
    {
        return $this->userHasAccess(Controller_Frontend::i()->user);
    }


    public function getH1()
    {
        return trim($this->h1) ?: trim($this->name);
    }


    public function getMenuName()
    {
        return trim($this->menu_name) ?: trim($this->name);
    }
    
    
    public function getBreadcrumbsName()
    {
        return trim($this->breadcrumbs_name) ?: trim($this->name);
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
            if (in_array($row->datatype, array('image', 'file'))) {
                foreach ($row->getValues(true) as $att) {
                    Attachment::delete($att);
                }
            }
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
        foreach ((array)$temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    protected function _affectedPages()
    {
        // 2015-07-13, AVS: Добавил, поменял tMt.id = " . (int)$this->pid в запросе на tMt.id IN (" . implode(", ", $types) . "), чтобы рассматривались
        // также блоки, связанные с родительскими типами материалов
        $MType = $this->material_type;
        $types = array_merge(array((int)$MType->id), (array)$MType->parents_ids);
        $SQL_query = "SELECT tP.*
                        FROM " . Page::_tablename() . " AS tP
                        JOIN " . self::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                        JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id
                        JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.id = tB.id
                        JOIN " . Material_Type::_tablename() . " AS tMt ON tMt.id = tBM.material_type 
                       WHERE tB.vis AND tB.nat AND tMt.id IN (" . implode(", ", $types) . ") ";
        // 2015-06-21, AVS: добавил, т.к. иначе предлагает выбрать основную страницу из всех, на которых есть блок, без учета страниц материала
        if ($this->pages) {
            $SQL_query .= " AND tP.id IN (" . implode(", ", $this->pages_ids) . ")";
        }
        $Set = Page::getSQLSet($SQL_query);
        // 2015-08-21, AVS: добавил сортировку, т.к. выбранная по умолчанию страница должна быть первой, 
        // в частности для реализации $this->url, где используется $this->affectedPages[0]
        if ($dpid = $this->page_id) {
            usort(
                $Set, 
                function($a, $b) use ($dpid) { 
                    if (($a->id == $dpid) && ($b->id != $dpid)) {
                        return -1;
                    } elseif (($b->id == $dpid) && ($a->id != $dpid)) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            );
        }

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


    /**
     * Ищет страницы с таким же URN, как и текущий материал (для проверки на уникальность)
     * @return bool TRUE, если есть страница с таким URN, как и текущий материал, FALSE в противном случае
     */
    protected function checkForSimilarPages()
    {
        $SQL_query = "SELECT COUNT(*) FROM " . Page::_tablename() . " WHERE urn = ?";
        $SQL_result = self::$SQL->getvalue(array($SQL_query, $this->urn));
        $c = (bool)(int)$SQL_result;
        return $c;
    }
}