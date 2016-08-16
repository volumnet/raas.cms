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


    public static function userHasCascadeAccess(\SOME\SOME $entity, User $user)
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
     * Обновляет кэш доступа к страницам
     * @param User $user фильтр по пользователю
     * @param Page $page фильтр по странице
     */
    public static function refreshPagesAccessCache(User $user = null, Page $page = null)
    {
        $tablename = Page::_links();
        $tablename = Page::_dbprefix() . $tablename['allowedUsers']['tablename'];
        $SQL_query = "DELETE FROM " . $tablename . " WHERE 1";
        if ($user->id) {
            $SQL_query .= " AND uid = " . (int)$user->id;
        }
        if ($page->id) {
            $SQL_query .= " AND page_id = " . (int)$page->id;
        }
        self::_SQL()->query($SQL_query);

        if ((int)$user->id) {
            $usersIds = array((int)$user->id);
        } else {
            $SQL_query = "SELECT tU.id FROM " . User::_tablename() . " AS tU WHERE 1";
            $usersIds = self::_SQL()->getcol($SQL_query);
            $usersIds[] = 0;
        }

        $SQL_query = "SELECT tP.id FROM " . Page::_tablename() . " AS tP JOIN " . self::_tablename() . " AS tA ON tA.page_id = tP.id WHERE 1";
        if ((int)$page->id) {
            $SQL_query .= " AND tP.id = " . (int)$page->id;
        }
        $SQL_query .= " GROUP BY tP.id";
        $pagesIds = self::_SQL()->getcol($SQL_query);
        foreach ($pagesIds as $pid) {
            foreach ($usersIds as $uid) {
                $row = new Page($pid);
                $u = new User($uid);
                $a = $row->userHasAccess($u);
                self::_SQL()->add($tablename, array('uid' => (int)$u->id, 'page_id' => (int)$row->id, 'allow' => (int)$a));
            }
        }
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


    /**
     * Обновляет кэш доступа к блокам
     * @param User $user фильтр по пользователю
     * @param Block $block фильтр по блоку
     */
    public static function refreshBlocksAccessCache(User $user = null, Block $block = null)
    {
        $tablename = Block::_links();
        $tablename = Block::_dbprefix() . $tablename['allowedUsers']['tablename'];
        $SQL_query = "DELETE FROM " . $tablename . " WHERE 1";
        if ($user->id) {
            $SQL_query .= " AND uid = " . (int)$user->id;
        }
        if ($block->id) {
            $SQL_query .= " AND block_id = " . (int)$block->id;
        }
        self::_SQL()->query($SQL_query);

        if ((int)$user->id) {
            $usersIds = array((int)$user->id);
        } else {
            $SQL_query = "SELECT tU.id FROM " . User::_tablename() . " AS tU WHERE 1";
            $usersIds = self::_SQL()->getcol($SQL_query);
            $usersIds[] = 0;
        }

        $SQL_query = "SELECT tM.id FROM " . Block::_tablename() . " AS tM JOIN " . self::_tablename() . " AS tA ON tA.block_id = tM.id WHERE 1";
        if ((int)$block->id) {
            $SQL_query .= " AND tM.id = " . (int)$block->id;
        }
        $SQL_query .= " GROUP BY tM.id";
        $blocksIds = self::_SQL()->getcol($SQL_query);
        foreach ($blocksIds as $bid) {
            foreach ($usersIds as $uid) {
                $row = Block::spawn($bid);
                $u = new User($uid);
                $a = $row->userHasAccess($u);
                self::_SQL()->add($tablename, array('uid' => (int)$u->id, 'block_id' => (int)$row->id, 'allow' => (int)$a));
            }
        }
    }
}
