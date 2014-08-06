<?php
namespace RAAS\CMS;
use \RAAS\Application;

class User extends \SOME\SOME
{
    protected static $tablename = 'cms_users';
    protected static $defaultOrderBy = "login";
    protected static $cognizableVars = array('fields');

    protected static $links = array('social' => array('tablename' => 'cms_users_social', 'field_from' => 'uid', 'field_to' => 'url'));

    public function __get($var)
    {
        switch ($var) {
            case 'activationKey':
                return $this->id . Application::md5It('activation' . $this->id . $this->login . $this->email . $this->password_md5);
                break;
            case 'recoveryKey':
                return $this->id . Application::md5It('recovery' . $this->id . $this->login . $this->email . $this->password_md5);
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
                            $temp = array_values(array_filter($temp, function($x) { return isset($x->vis) && $x->vis; }));
                        }
                        return $temp;
                    }
                }
                break;
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
            $SQL_query = "DELETE FROM " . static::_dbprefix() . static::$links['social']['tablename'] 
                       . " WHERE " . static::$links['social']['field_from'] . " = " . (int)$this->id;
            static::$SQL->query($SQL_query);
            $id = (int)$this->id;
            $arr = array();
            foreach ((array)$this->meta_social as $val) {
                $tmp_user = static::importBySocialNetwork($val);
                if (!$tmp_user || ($tmp_user->id == $this->id)) {
                    $arr[] = array(static::$links['social']['field_from'] => $this->id, static::$links['social']['field_to'] => trim($val));
                }
            }
            unset($this->meta_social);
            static::$SQL->add(static::$dbprefix . static::$links['social']['tablename'], $arr);
        }
    }


    public function addSocial($social)
    {
        $social = trim($social);
        if (!in_array($social, $this->meta_social)) {
            $arr = array(static::$links['social']['field_from'] => $this->id, static::$links['social']['field_to'] => trim($social));
            static::$SQL->add(static::$dbprefix . static::$links['social']['tablename'], $arr);
        }
        $this->reload();
    }


    public function deleteSocial($social)
    {
        $social = trim($social);
        if (in_array($social, $this->meta_social)) {
            $SQL_query = "DELETE FROM " . static::_dbprefix() . static::$links['social']['tablename'] 
                       . " WHERE " . static::$links['social']['field_from'] . " = " . (int)$this->id 
                       . "   AND " . static::$links['social']['field_to'] . " = '" . static::$SQL->real_escape_string($social) . "'";
            static::$SQL->query($SQL_query);
        }
        $this->reload();
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


    public function visit()
    {
        if ($this->id) {
            $this->new = 0;
            $this->commit();
        }
    }


    public static function importByActivationKey($key)
    {
        $id = (int)substr($key, 0, -32);
        $Set = static::getSet(array('where' => array("NOT vis", "id = " . $id)));
        if ($Set) {
            $User = array_shift($Set);
            if ($User->activationKey == $key) {
                return $User;
            }
        }
        return null;
    }


    public static function importByRecoveryKey($key)
    {
        $id = (int)substr($key, 0, -32);
        $Set = static::getSet(array('where' => "id = " . $id));
        if ($Set) {
            $User = array_shift($Set);
            if ($User->recoveryKey == $key) {
                return $User;
            }
        }
        return null;
    }


    public static function importByLoginOrEmail($login)
    {
        $login = static::$SQL->real_escape_string(trim($login));
        $Set = static::getSet(array('where' => "login = '" . $login . "' OR email = '" . $login . "'"));
        if ($Set) {
            $User = array_shift($Set);
            return $User;
        }
        return null;
    }


    public static function importBySocialNetwork($profile)
    {
        $SQL_query = "SELECT tU.* 
                        FROM " . static::_tablename() . " AS tU 
                        JOIN " . static::_dbprefix() . static::$links['social']['tablename'] . " AS tUS ON tUS." . static::$links['social']['field_from'] . " = tU.id 
                       WHERE " . static::$links['social']['field_to'] . " = '" . static::$SQL->real_escape_string(trim($profile)) . "'
                       LIMIT 1";
        $User = static::getSQLObject($SQL_query);
        if ($User->id) {
            return $User;
        }
        return null;
    }
}