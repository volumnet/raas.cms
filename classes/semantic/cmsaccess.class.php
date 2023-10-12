<?php
/**
 * Правило доступа
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс правила доступа
 * @property-read Page $page Для какой страницы установлено правило
 * @property-read Material $material Для какого материала установлено правило
 * @property-read Block $block Для какого блока установлено правило
 * @property-read User $user Для какого пользователя установлено правило
 * @property-read Group $group Для какой группы установлено правило
 */
class CMSAccess extends SOME
{
    /**
     * Для всех
     */
    const TO_ALL = 0;

    /**
     * Для незарегистрированных
     */
    const TO_UNREGISTERED = 1;

    /**
     * Для зарегистрированных
     */
    const TO_REGISTERED = 2;

    /**
     * Для пользователя
     */
    const TO_USER = 3;

    /**
     * Для группы
     */
    const TO_GROUP = 4;

    protected static $tablename = 'cms_access';

    protected static $defaultOrderBy = "priority";

    protected static $references = [
        'page' => [
            'FK' => 'page_id',
            'classname' => Page::class,
            'cascade' => true
        ],
        'material' => [
            'FK' => 'material_id',
            'classname' => Material::class,
            'cascade' => true
        ],
        'block' => [
            'FK' => 'block_id',
            'classname' => Block::class,
            'cascade' => true
        ],
        'user' => [
            'FK' => 'uid',
            'classname' => User::class,
            'cascade' => true
        ],
        'group' => [
            'FK' => 'gid',
            'classname' => Group::class,
            'cascade' => true
        ],
    ];


    /**
     * Разрешает ли правило доступ для заданного пользователя
     * @param User $user Пользователь для проверки
     * @return -1|0|1 -1 - запрещено, 0 - не определено, 1 - разрешено
     */
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


    /**
     * Имеет ли пользователь каскадный доступ к сущности
     * @param SOME $entity Сущность
     * @param User $user Пользователь
     * @return -1|0|1 -1 - запрещено, 0 - не определено, 1 - разрешено
     */
    public static function userHasCascadeAccess(SOME $entity, User $user)
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
    public static function refreshPagesAccessCache(
        User $user = null,
        Page $page = null
    ) {
        $tablename = Page::_links();
        $tablename = Page::_dbprefix() . $tablename['allowedUsers']['tablename'];
        $sqlQuery = "DELETE FROM " . $tablename . " WHERE 1";
        if ($user && $user->id) {
            $sqlQuery .= " AND uid = " . (int)$user->id;
        }
        if ($page && $page->id) {
            $sqlQuery .= " AND page_id = " . (int)$page->id;
        }
        self::_SQL()->query($sqlQuery);

        if ($user && (int)$user->id) {
            $usersIds = [(int)$user->id];
        } else {
            $sqlQuery = "SELECT tU.id FROM " . User::_tablename() . " AS tU";
            $usersIds = self::_SQL()->getcol($sqlQuery);
            $usersIds[] = 0;
        }

        $sqlQuery = "SELECT tP.id
                       FROM " . Page::_tablename() . " AS tP
                       JOIN " . self::_tablename() . " AS tA ON tA.page_id = tP.id
                      WHERE 1";
        if ($page && (int)$page->id) {
            $sqlQuery .= " AND tP.id = " . (int)$page->id;
        }
        $sqlQuery .= " GROUP BY tP.id";
        $pagesIds = self::_SQL()->getcol($sqlQuery);
        $sqlArr = [];
        foreach ($pagesIds as $pid) {
            foreach ($usersIds as $uid) {
                $row = new Page($pid);
                $a = $row->userHasAccess(new User($uid));
                $sqlArr[] = ['uid' => (int)$uid, 'page_id' => (int)$pid, 'allow' => (int)$a];
            }
        }
        self::_SQL()->add($tablename, $sqlArr);
    }


    /**
     * Обновляет кэш доступа к материалам
     * @param User $user фильтр по пользователю
     * @param Material $material фильтр по материалу
     */
    public static function refreshMaterialsAccessCache(User $user = null, Material $material = null)
    {
        $tablename = Material::_links();
        $tablename = Material::_dbprefix()
                   . $tablename['allowedUsers']['tablename'];
        $sqlQuery = "DELETE FROM " . $tablename . " WHERE 1";
        if ($user && $user->id) {
            $sqlQuery .= " AND uid = " . (int)$user->id;
        }
        if ($material && $material->id) {
            $sqlQuery .= " AND material_id = " . (int)$material->id;
        }
        self::_SQL()->query($sqlQuery);

        if ($user && (int)$user->id) {
            $usersIds = [(int)$user->id];
        } else {
            $sqlQuery = "SELECT tU.id FROM " . User::_tablename() . " AS tU";
            $usersIds = self::_SQL()->getcol($sqlQuery);
            $usersIds[] = 0;
        }

        $sqlQuery = "SELECT tM.id
                       FROM " . Material::_tablename() . " AS tM
                       JOIN " . self::_tablename() . " AS tA ON tA.material_id = tM.id
                      WHERE 1";
        if ($material && (int)$material->id) {
            $sqlQuery .= " AND tM.id = " . (int)$material->id;
        }
        $sqlQuery .= " GROUP BY tM.id";
        $materialsIds = self::_SQL()->getcol($sqlQuery);
        $sqlArr = [];
        foreach ($materialsIds as $mid) {
            foreach ($usersIds as $uid) {
                $row = new Material($mid);
                $a = $row->userHasAccess(new User($uid));
                $sqlArr[] = ['uid' => (int)$uid, 'material_id' => (int)$mid, 'allow' => (int)$a];
            }
        }
        self::_SQL()->add($tablename, $sqlArr);
    }


    /**
     * Обновляет кэш доступа к блокам
     * @param User $user фильтр по пользователю
     * @param Block $block фильтр по блоку
     */
    public static function refreshBlocksAccessCache(User $user = null, Block $block = null)
    {
        $tablename = Block::_links();
        $tablename = Block::_dbprefix()
                   . $tablename['allowedUsers']['tablename'];
        $sqlQuery = "DELETE FROM " . $tablename . " WHERE 1";
        if ($user->id ?? null) {
            $sqlQuery .= " AND uid = " . (int)$user->id;
        }
        if ($block->id ?? null) {
            $sqlQuery .= " AND block_id = " . (int)$block->id;
        }
        self::_SQL()->query($sqlQuery);

        if ((int)($user->id ?? 0)) {
            $usersIds = [(int)$user->id];
        } else {
            $sqlQuery = "SELECT tU.id FROM " . User::_tablename() . " AS tU";
            $usersIds = self::_SQL()->getcol($sqlQuery);
            $usersIds[] = 0;
        }

        $sqlQuery = "SELECT tM.id
                       FROM " . Block::_tablename() . " AS tM
                       JOIN " . self::_tablename() . " AS tA ON tA.block_id = tM.id
                      WHERE 1";
        if ((int)($block->id ?? 0)) {
            $sqlQuery .= " AND tM.id = " . (int)$block->id;
        }
        $sqlQuery .= " GROUP BY tM.id";
        $blocksIds = self::_SQL()->getcol($sqlQuery);
        $sqlArr = [];
        foreach ($blocksIds as $bid) {
            foreach ($usersIds as $uid) {
                $row = Block::spawn($bid);
                $a = $row->userHasAccess(new User($uid));
                $sqlArr[] = ['uid' => (int)$uid, 'block_id' => (int)$bid, 'allow' => (int)$a];
            }
        }
        self::_SQL()->add($tablename, $sqlArr);
    }
}
