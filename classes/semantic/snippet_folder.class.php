<?php
/**
 * Папка сниппетов
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс папки сниппетов
 * @property-read Snippet_Folder $parent Родительская папка
 * @property-read array<Snippet_Folder> $parents Родительские папки
 * @property-read array<Snippet_Folder> $children Дочерние папки
 * @property-read array<Snippet> $snippets Сниппеты в папке
 * @property-read array<Snippet_Folder> $selfAndChildren Текущая и дочерние страницы
 * @property-read array<int> $selfAndChildrenIds ID# текущей и дочерних страницы
 * @property-read array<Snippet_Folder> $selfAndParents Текущая и родительские страницы
 * @property-read array<int> $selfAndParentsIds ID# текущей и родительских
 *                                                  страниц
 */
class Snippet_Folder extends SOME
{
    use ImportByURNTrait;
    use RecursiveTrait;

    protected static $tablename = 'cms_snippet_folders';

    protected static $defaultOrderBy = "name";

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Snippet_Folder::class,
            'cascade' => true
        ],
    ];

    protected static $parents = ['parents' => 'parent'];

    protected static $children = [
        'children' => [
            'classname' => Snippet_Folder::class,
            'FK' => 'pid'
        ],
        'snippets' => [
            'classname' => Snippet::class,
            'FK' => 'pid'
        ],
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
