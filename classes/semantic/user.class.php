<?php
namespace RAAS\CMS;

class User extends \SOME\SOME
{
    protected static $tablename = 'cms_users';
    protected static $defaultOrderBy = "login";
    protected static $cognizableVars = array('fields');

    protected static $links = array('social' => array('tablename' => 'cms_users_social', 'field_from' => 'uid', 'field_to' => 'url'));

    public function __get($var)
    {
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
                    $temp = array_values(array_filter($temp, function($x) { return isset($x->vis) && $x->vis; }));
                }
                return $temp;
            }
        }
    }    


    public function commit()
    {
        if (!$this->id) {
            $this->post_date = date('Y-m-d H:i:s');
        }
        parent::commit();
        $this->exportSocial();
    }


    private function exportSocial()
    {
        if (isset($this->meta_social)) {
            $SQL_query = "DELETE FROM " . self::_dbprefix() . self::$links['social']['tablename'] 
                       . " WHERE " . self::$links['social']['field_from'] . " = " . (int)$this->id;
            self::$SQL->query($SQL_query);
            $id = (int)$this->id;
            $socialRef = self::$links['social'];
            $arr = array_map(
                function($x) use ($id, $socialRef) { return array($socialRef['field_from'] => $id, $socialRef['field_to'] => $x); }, 
                (array)$this->meta_social
            );
            unset($this->meta_social);
            self::$SQL->add(self::$dbprefix . $socialRef['tablename'], $arr);
        }
    }

    

    public static function delete(self $object)
    {
        foreach ($object->fields as $row) {
            $row->deleteValues();
        }
        parent::delete($object);
    }
    
    
    protected function _fields()
    {
        $temp = User_Field::getSet();
        $arr = array();
        foreach ($temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    public function checkLoginExists($login)
    {
        $SQL_query = "SELECT COUNT(*) FROM " . static::_tablename() . " WHERE login = ?";
        $SQL_bind = array($login);
        if ($this->id) {
            $SQL_query .= " AND id != ?";
            $SQL_bind[] = (int)$this->id;
        }
        $SQL_result = static::$SQL->getvalue(array($SQL_query, $SQL_bind));
        return (bool)$SQL_result;
    }


    public function checkEmailExists($email)
    {
        $SQL_query = "SELECT COUNT(*) FROM " . static::_tablename() . " WHERE email = ?";
        $SQL_bind = array($email);
        if ($this->id) {
            $SQL_query .= " AND id != ?";
            $SQL_bind[] = (int)$this->id;
        }
        $SQL_result = static::$SQL->getvalue(array($SQL_query, $SQL_bind));
        return (bool)$SQL_result;
    }
}