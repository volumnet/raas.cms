<?php
/**
 * Пользователь сайта
 */
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Application;
use RAAS\Attachment;

/**
 * Класс пользователя сайта
 * @property-read array<User_Field> $fields Поля формы с установленным свойством
 *                                          $Owner
 * @property-read array<string> $social Адреса соц. сетей
 * @property-read array<Group> $groups Группы, в которых состоит пользователь
 * @property-read array<Page> $allowedPages Страницы, разрешенные пользователю
 *                                          к просмотру
 * @property-read array<Material> $allowedMaterials Материалы, доступные
 *                                                  пользователю
 * @property-read array<Block> $allowedBlocks Блоки, доступные пользователю
 * @property-read string $activationKey Ключ активации
 * @property-read string $recoveryKey Ключ восстановления пароля
 * @property-read string $loginKey Ключ входа в систему
 *
 */
class User extends SOME
{
    protected static $tablename = 'cms_users';

    protected static $defaultOrderBy = "login";

    protected static $objectCascadeDelete = true;

    protected static $cognizableVars = ['fields'];

    protected static $links = [
        'social' => [
            'tablename' => 'cms_users_social',
            'field_from' => 'uid',
            'field_to' => 'url'
        ],
        'groups' => [
            'tablename' => 'cms_users_groups_assoc',
            'field_from' => 'uid',
            'field_to' => 'gid',
            'classname' => Group::class
        ],
        'allowedPages' => [
            'tablename' => 'cms_access_pages_cache',
            'field_from' => 'uid',
            'field_to' => 'page_id',
            'classname' => Page::class
        ],
        'allowedMaterials' => [
            'tablename' => 'cms_access_materials_cache',
            'field_from' => 'uid',
            'field_to' => 'material_id',
            'classname' => Material::class
        ],
        'allowedBlocks' => [
            'tablename' => 'cms_access_blocks_cache',
            'field_from' => 'uid',
            'field_to' => 'block_id',
            'classname' => Block::class
        ],
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'activationKey':
                $key = 'activation'
                     . $this->id
                     . $this->login
                     . $this->email
                     . $this->password_md5;
                return $this->id . Application::i()->md5It($key);
                break;
            case 'recoveryKey':
                $key = 'recovery'
                     . $this->id
                     . $this->login
                     . $this->email
                     . $this->password_md5;
                return $this->id . Application::i()->md5It($key);
                break;
            case 'loginKey':
                $key = 'login'
                     . $this->id
                     . $this->login
                     . $this->email
                     . $this->password_md5;
                return $this->id . Application::i()->md5It($key);
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
                    if (isset($this->fields[$var]) &&
                        ($this->fields[$var] instanceof User_Field)
                    ) {
                        $temp = $this->fields[$var]->getValues();
                        if ($vis) {
                            $temp = array_values(
                                array_filter(
                                    $temp,
                                    function ($x) {
                                        return isset($x->vis) && $x->vis;
                                    }
                                )
                            );
                        }
                        return $temp;
                    } elseif ($var == 'full_name') {
                        $temp = [];
                        foreach ([
                            'last_name',
                            'first_name',
                            'second_name'
                        ] as $key) {
                            if (isset($this->fields[$key])) {
                                $temp[] = $this->fields[$key]->doRich();
                            }
                        }
                        return implode(' ', $temp);
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


    /**
     * Экспортирует привязки к социальным сетям
     */
    private function exportSocial()
    {
        if (isset($this->meta_social)) {
            $sqlQuery = "DELETE FROM " . static::_dbprefix() . static::$links['social']['tablename']
                      . " WHERE " . static::$links['social']['field_from'] . " = ?";
            static::$SQL->query([$sqlQuery, (int)$this->id]);
            $id = (int)$this->id;
            $arr = [];
            foreach ((array)$this->meta_social as $val) {
                $tmp_user = static::importBySocialNetwork($val);
                if (!$tmp_user || ($tmp_user->id == $this->id)) {
                    $arr[] = [
                        static::$links['social']['field_from'] => $this->id,
                        static::$links['social']['field_to'] => trim($val)
                    ];
                }
            }
            unset($this->meta_social);
            static::$SQL->add(
                static::$dbprefix . static::$links['social']['tablename'],
                $arr
            );
        }
    }


    /**
     * Добавляет адрес социальной сети
     * @param string $social Адрес социальной сети
     */
    public function addSocial($social)
    {
        $social = trim($social);
        if (!in_array($social, $this->meta_social)) {
            $arr = [
                static::$links['social']['field_from'] => $this->id,
                static::$links['social']['field_to'] => trim($social)
            ];
            static::$SQL->add(
                static::$dbprefix . static::$links['social']['tablename'],
                $arr
            );
        }
        $this->reload();
    }


    /**
     * Удаляет адрес социальной сети
     * @param string $social Адрес социальной сети
     */
    public function deleteSocial($social)
    {
        $social = trim($social);
        if (in_array($social, $this->meta_social)) {
            $sqlQuery = "DELETE FROM " . static::_dbprefix() . static::$links['social']['tablename']
                      . " WHERE " . static::$links['social']['field_from'] . " = ?
                            AND " . static::$links['social']['field_to'] . " = ?";
            static::$SQL->query([$sqlQuery, (int)$this->id, $social]);
        }
        $this->reload();
    }


    public static function delete(self $object)
    {
        foreach ($object->fields as $row) {
            if (in_array($row->datatype, ['image', 'file'])) {
                foreach ($row->getValues(true) as $att) {
                    Attachment::delete($att);
                }
            }
            $row->deleteValues();
        }
        parent::delete($object);
    }


    /**
     * Добавляет пользователя в группу
     * @param Group $group Группа для добавления
     */
    public function associate(Group $group)
    {
        if ($this->id && $group->id) {
            $sqlQuery = " INSERT IGNORE INTO " . self::_dbprefix() . "cms_users_groups_assoc (uid, gid)
                          VALUES (?, ?)";
            self::$SQL->query([$sqlQuery, (int)$this->id, (int)$group->id]);
            $this->commit();
            CMSAccess::refreshPagesAccessCache($this);
            CMSAccess::refreshMaterialsAccessCache($this);
            CMSAccess::refreshBlocksAccessCache($this);
        }
    }


    /**
     * Удаляет пользователя из группы
     * @param Group $group Группа, из которой удаляем
     */
    public function deassociate(Group $group)
    {
        if ($this->id && $group->id) {
            $sqlQuery = " DELETE FROM " . self::_dbprefix() . "cms_users_groups_assoc
                           WHERE uid = ? AND gid = ?";
            self::$SQL->query([$sqlQuery, (int)$this->id, (int)$group->id]);
            $this->commit();
            CMSAccess::refreshPagesAccessCache($this);
            CMSAccess::refreshMaterialsAccessCache($this);
            CMSAccess::refreshBlocksAccessCache($this);
        }
    }


    /**
     * Получает поля пользователя, с установленным свойством $Owner
     * @return array<User_Field>
     */
    protected function _fields()
    {
        $temp = User_Field::getSet();
        $arr = [];
        foreach ($temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    /**
     * Проверяет, существует ли уже в системе пользователь с таким логином
     * @return bool
     */
    public function checkLoginExists($login)
    {
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . static::_tablename()
                  . " WHERE login = ?";
        $sqlBind = [$login];
        if ($this->id) {
            $sqlQuery .= " AND id != ?";
            $sqlBind[] = (int)$this->id;
        }
        $sqlResult = static::$SQL->getvalue([$sqlQuery, $sqlBind]);
        return (bool)$sqlResult;
    }


    /**
     * Проверяет, существует ли уже в системе пользователь с таким e-mail
     * @return bool
     */
    public function checkEmailExists($email)
    {
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . static::_tablename()
                  . " WHERE email = ?";
        $sqlBind = [$email];
        if ($this->id) {
            $sqlQuery .= " AND id != ?";
            $sqlBind[] = (int)$this->id;
        }
        $sqlResult = static::$SQL->getvalue([$sqlQuery, $sqlBind]);
        return (bool)$sqlResult;
    }


    /**
     * Пометить пользователя как просмотренного
     */
    public function visit()
    {
        if ($this->id) {
            $this->new = 0;
            $this->commit();
        }
    }


    /**
     * Сменить статус активации пользователя
     */
    public function chvis()
    {
        if ($this->id) {
            $this->vis = (int)!$this->chvis;
            $this->commit();
        }
    }


    /**
     * Импортировать пользователя по ключу активации
     * @param string $key Ключ активации
     * @return static|null null, если не найден
     */
    public static function importByActivationKey($key)
    {
        $id = (int)substr($key, 0, -32);
        $Set = static::getSet(['where' => ["NOT vis", "id = " . $id]]);
        if ($Set) {
            $User = array_shift($Set);
            if ($User->activationKey == $key) {
                return $User;
            }
        }
        return null;
    }


    /**
     * Импортировать пользователя по ключу восстановления пароля
     * @param string $key Ключ восстановления пароля
     * @return static|null null, если не найден
     */
    public static function importByRecoveryKey($key)
    {
        $id = (int)substr($key, 0, -32);
        $Set = static::getSet(['where' => "id = " . $id]);
        if ($Set) {
            $User = array_shift($Set);
            if ($User->recoveryKey == $key) {
                return $User;
            }
        }
        return null;
    }


    /**
     * Импортировать пользователя по ключу входа в систему
     * @param string $key Ключ входа в систему
     * @return static|null null, если не найден
     */
    public static function importByLoginKey($key)
    {
        $id = (int)substr($key, 0, -32);
        $Set = static::getSet(['where' => ['vis', "id = " . $id]]);
        if ($Set) {
            $User = array_shift($Set);
            if ($User->loginKey == $key) {
                return $User;
            }
        }
        return null;
    }


    /**
     * Импортировать пользователя по логину или e-mail
     * @param string $login Логин или e-mail
     * @return static|null null, если не найден
     */
    public static function importByLoginOrEmail($login)
    {
        $login = static::$SQL->real_escape_string(trim($login));
        $Set = static::getSet([
            'where' => "login = '" . $login . "' OR email = '" . $login . "'"
        ]);
        if ($Set) {
            $User = array_shift($Set);
            return $User;
        }
        return null;
    }


    /**
     * Импортировать пользователя по логину и паролю
     * @param string $login Логин
     * @param string $password Пароль
     * @return static|null null, если не найден
     */
    public static function importByLoginPassword($login, $password)
    {
        $login = static::$SQL->real_escape_string(trim($login));
        $Set = static::getSet(['where' => "login = '" . $login . "'"]);
        if ($Set) {
            $User = array_shift($Set);
            if (Application::i()->md5It($password) == $User->password_md5) {
                return $User;
            }
        }
        return null;
    }


    /**
     * Импортировать пользователя по адресу социальной сети
     * @param string $profile Адрес социальной сети
     * @return static|null null, если не найден
     */
    public static function importBySocialNetwork($profile)
    {
        $sqlQuery = "SELECT tU.*
                        FROM " . static::_tablename()
                  . "     AS tU
                        JOIN " . static::_dbprefix() . static::$links['social']['tablename']
                  . "     AS tUS
                          ON tUS." . static::$links['social']['field_from'] . " = tU.id
                       WHERE " . static::$links['social']['field_to'] . " = ?
                       LIMIT 1";
        $User = static::getSQLObject([$sqlQuery, trim($profile)]);
        if ($User->id) {
            return $User;
        }
        return null;
    }
}
