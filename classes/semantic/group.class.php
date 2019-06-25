<?php
/**
 * Группа пользователей
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс группы пользователей
 * @property-read Group $parent Родительская группа
 * @property-read array<Group> $parents Список родительских групп
 * @property-read array<Group> $children Список дочерних групп
 * @property-read array<User> $users Список пользователей в группе
 * @property-read array<Group> $selfAndChildren Текущая и дочерние страницы
 * @property-read array<int> $selfAndChildrenIds ID# текущей и дочерних страницы
 * @property-read array<Group> $selfAndParents Текущая и родительские страницы
 * @property-read array<int> $selfAndParentsIds ID# текущей и родительских
 *                                                  страниц
 */
class Group extends SOME
{
    use ImportByURNTrait;
    use RecursiveTrait;

    protected static $tablename = 'cms_groups';

    protected static $defaultOrderBy = "name";

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Group::class,
            'cascade' => true
        ]
    ];

    protected static $parents = ['parents' => 'parent'];

    protected static $children = [
        'children' => [
            'classname' => Group::class,
            'FK' => 'pid'
        ]
    ];

    protected static $links = [
        'users' => [
            'tablename' => 'cms_users_groups_assoc',
            'field_from' => 'gid',
            'field_to' => 'uid',
            'classname' => User::class
        ]
    ];

    protected static $cognizableVars = [
        'selfAndChildren',
        'selfAndChildrenIds',
        'selfAndParents',
        'selfAndParentsIds',
    ];

    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);
        parent::commit();
    }
}
