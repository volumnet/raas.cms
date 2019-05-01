<?php
/**
 * Справочник
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Dictionary as RAASDictionary;

/**
 * Класс справочника
 * @property-read Dictionary $parent Родительский справочник
 * @property-read array<Dictionary> $children Дочерний справочник
 * @property-read array<Dictionary> $selfAndChildren Текущая и дочерние страницы
 * @property-read array<int> $selfAndChildrenIds ID# текущей и дочерних страницы
 * @property-read array<Dictionary> $selfAndParents Текущая и родительские страницы
 * @property-read array<int> $selfAndParentsIds ID# текущей и родительских
 *                                                  страниц
 * @property-read array<Dictionary> $visChildren Список видимых дочерних
 *                                               справочников
 */
class Dictionary extends RAASDictionary
{
    use RecursiveTrait;
    use ImportByURNTrait;

    protected static $tablename = 'cms_dictionaries';

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Dictionary::class,
            'cascade' => true
        ]
    ];

    protected static $children = [
        'children' => [
            'classname' => Dictionary::class,
            'FK' => 'pid'
        ]
    ];

    protected static $caches = [
        'pvis' => [
            'affected' => ['parent'],
            'sql' => "IF(parent.id, (parent.vis AND parent.pvis), 1)"
        ]
    ];

    protected static $cognizableVars = [
        'selfAndChildren',
        'selfAndChildrenIds',
        'selfAndParents',
        'selfAndParentsIds',
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'visChildren':
                return array_values(array_filter(
                    $this->children,
                    function ($x) {
                        return $x->vis;
                    }
                ));
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function commit()
    {
        if (!$this->pid) {
            if (!$this->urn && $this->name) {
                $this->urn = $this->name;
            }
            $this->urn = \SOME\Text::beautify($this->urn);
            for ($i = 0; $this->checkForSimilar($this); $i++) {
                $this->urn = Application::i()->getNewURN($this->urn, !$i);
            }
        }
        parent::commit();
    }


    /**
     * Ищет справочники с таким же URN, как и текущий на том же уровне
     * (для проверки на уникальность)
     * @return bool true, если есть справочник с таким URN, как и текущий,
     *                    на том же уровне,
     *              false в противном случае
     */
    public function checkForSimilar()
    {
        $sqlQuery = "SELECT COUNT(*)
                        FROM " . self::_tablename()
                   . " WHERE urn = ?
                         AND id != ?
                         AND pid = ?";
        $sqlResult = self::_SQL()->getvalue([
            $sqlQuery,
            $this->urn,
            (int)$this->id,
            (int)$this->pid
        ]);
        $c = (bool)(int)$sqlResult;
        return $c;
    }
}
