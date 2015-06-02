<?php 
namespace RAAS\CMS;

class CMSAccess extends \SOME\SOME
{
    const TO_ALL = 0;
    const TO_UNREGISTERED = 1;
    const TO_REGISTERED = 2;
    const TO_USER = 3;
    const TO_GROUP = 4;

    protected static $tablename = 'cms_access';
    protected static $defaultOrderBy = "priority";
    protected static $cognizableVars = array();

    protected static $references = array(
        'page' => array('FK' => 'page_id', 'classname' => 'RAAS\\CMS\\Page', 'cascade' => true),
        'material' => array('FK' => 'material_id', 'classname' => 'RAAS\\CMS\\Material', 'cascade' => true),
        'block' => array('FK' => 'block_id', 'classname' => 'RAAS\\CMS\\Block', 'cascade' => true),
        'user' => array('FK' => 'uid', 'classname' => 'RAAS\\CMS\\User', 'cascade' => true),
        'group' => array('FK' => 'gid', 'classname' => 'RAAS\\CMS\\Group', 'cascade' => true),
    );


    public function userHasAccess(User $user)
    {
        switch ($this->to_type) {
            case self::TO_ALL:
                $match = true;
                break;
            case self::TO_UNREGISTERED:
                $match = !$user->id;
                break;
            case self::TO_REGISTERED:
                $match = (bool)(int)$user->id;
                break;
            case self::TO_USER:
                $match = ($user->id == $this->uid);
                break;
            case self::TO_GROUP:
                $match = in_array($this->gid, $user->groups_ids);
                break;
        }
        if ($match) {
            return ($this->allow * 2) - 1;
        }
        return 0;
    }


    public function userHasCascadeAccess(\SOME\SOME $entity, User $user)
    {
        if (is_array($entity->access)) {
            $accessSet = array_reverse($entity->access);
            foreach ($accessSet as $access) {
                $a = $access->userHasAccess($user);
                if ($a) {
                    return $a;
                }
            }
        }
        return 0;
    }


    /**
     * Обновляет кэш доступа к материалам
     * @param User $user фильтр по пользователю
     * @param Material $material фильтр по материалу
     */
    public static function refreshMaterialsAccessCache(User $user = null, Material $material = null)
    {
        $tablename = Material::_links();
        $tablename = Material::_dbprefix() . $tablename['allowedUsers']['tablename'];
        $SQL_query = "DELETE FROM " . $tablename . " WHERE 1";
        if ($user->id) {
            $SQL_query .= " AND uid = " . (int)$user->id;
        }
        if ($material->id) {
            $SQL_query .= " AND material_id = " . (int)$material->id;
        }
        self::_SQL()->query($SQL_query);

        if ((int)$user->id) {
            $usersIds = array((int)$user->id);
        } else {
            $SQL_query = "SELECT tU.id FROM " . User::_tablename() . " AS tU WHERE 1";
            $usersIds = self::_SQL()->getcol($SQL_query);
            $usersIds[] = 0;
        }

        $SQL_query = "SELECT tM.id FROM " . Material::_tablename() . " AS tM JOIN " . self::_tablename() . " AS tA ON tA.material_id = tM.id WHERE 1";
        if ((int)$material->id) {
            $SQL_query .= " AND tM.id = " . (int)$material->id;
        }
        $SQL_query .= " GROUP BY tM.id";
        $materialsIds = self::_SQL()->getcol($SQL_query);
        foreach ($materialsIds as $mid) {
            foreach ($usersIds as $uid) {
                $row = new Material($mid);
                $u = new User($uid);
                $a = $row->userHasAccess($u);
                self::_SQL()->add($tablename, array('uid' => (int)$u->id, 'material_id' => (int)$row->id, 'allow' => (int)$a));
            }
        }
    }
}