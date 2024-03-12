<?php
/**
 * Файл абстрактного рекурсивного кэша с видимостью
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\Singleton;
use SOME\SOME;
use SOME\AbstractRecursiveCache;

/**
 * Класс абстрактного рекурсивного кэша с видимостью
 *
 * @property-read array<
 *     string ID# сущности =>
 *     int ID# сущности
 * > $visibleIds Набор ID# видимых сущностей
 * @property-read array<
 *     string[] ID# родительской сущности =>
 *     array<string ID# дочерней сущности => int ID# дочерней сущности>
 * > $visChildrenIds Видимые дочерние ID# первого уровня
 * @property-read array<
 *     string[] ID# родительской сущности =>
 *     array<string ID# дочерней сущности => int ID# дочерней сущности>
 * > $visAllChildrenIds Дочерние ID# всех уровней
 */
abstract class VisibleRecursiveCache extends AbstractRecursiveCache
{
    protected static $instance;

    protected static $classname;

    /**
     * Набор ID# видимых сущностей
     * @var array<string ID# сущности => int ID# сущности>
     */
    protected $visibleIds = [];

    /**
     * Видимые дочерние ID# первого уровня
     * @var array <pre><code>array<
     *     string[] ID# родительской сущности =>
     *     array<string ID# дочерней сущности => int ID# дочерней сущности>
     * ></code></pre>
     */
    protected $visChildrenIds = [];

    /**
     * Дочерние ID# всех уровней
     * @var array<
     *          string[] ID# родительской сущности =>
     *          array<string ID# дочерней сущности => int ID# дочерней сущности>
     *      >
     */
    protected $visAllChildrenIds = [];

    public function __get($var)
    {
        switch ($var) {
            case 'visibleIds':
            case 'visChildrenIds':
            case 'visAllChildrenIds':
                return $this->$var;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    /**
     * Получает ID# видимых дочерних сущностей
     * @param SOME|int|SOME[]|int[] $data Родительская сущность
     *     или ID# родительской сущности
     *     или их массив
     * @param int $assoc Возвращать массив с ассоциацией по ID# (битовая маска)
     *     0 - нет
     *     1 - на уровне родительских ID#
     *     2 - на уровне дочерних ID#
     * @return array <pre><code>array<int|array<int>></code></pre>
     */
    public function getVisChildrenIds($data, int $assoc = 2): array
    {
        return $this->getVarIds('visChildrenIds', $data, $assoc);
    }


    /**
     * Получает видимые дочерние сущности
     * @param SOME|int|array<SOME|int> $data Родительская сущность или ID# родительской сущности или их массив
     * @param int $assoc Возвращать массив с ассоциацией по ID# (битовая маска)
     *     0 - нет
     *     1 - на уровне родительских ID#
     *     2 - на уровне дочерних ID#
     * @return array <pre><code>array<SOME|array<SOME>></code></pre>
     */
    public function getVisChildren($data, int $assoc = 0): array
    {
        return $this->getVar('visChildrenIds', $data, $assoc);
    }


    /**
     * Получает ID# видимых дочерних сущностей всех уровней
     * @param SOME|int|array<SOME|int> $data Родительская сущность или ID# родительской сущности или их массив
     * @param int $assoc Возвращать массив с ассоциацией по ID# (битовая маска)
     *     0 - нет
     *     1 - на уровне родительских ID#
     *     2 - на уровне дочерних ID#
     * @return array<int|array<int>>
     */
    public function getVisAllChildrenIds($data, int $assoc = 2): array
    {
        return $this->getVarIds('visAllChildrenIds', $data, $assoc);
    }


    /**
     * Получает видимые дочерние сущности всех уровней
     * @param SOME|int|array<SOME|int> $data Родительская сущность или ID# родительской сущности или их массив
     * @param int $assoc Возвращать массив с ассоциацией по ID# (битовая маска)
     *     0 - нет
     *     1 - на уровне родительских ID#
     *     2 - на уровне дочерних ID#
     * @return array <pre><code>array<SOME|array<SOME>></code></pre>
     */
    public function getVisAllChildren($data, int $assoc = 0): array
    {
        return $this->getVar('visAllChildrenIds', $data, $assoc);
    }


    /**
     * Обновляет все данные кэша
     */
    public function refresh()
    {
        $this->setCache();
        $this->setParentsChildrenIds();
        $this->setVisibleIds();
        $this->setVisParentsChildrenIds();
    }


    /**
     * Получает и устанавливает список видимых ID#
     */
    protected function setVisibleIds()
    {
        $this->visibleIds = [];
        foreach ($this->cache as $id => $cacheData) {
            if (!isset($cacheData['vis']) || (int)$cacheData['vis']) {
                $this->visibleIds[(string)$id] = (int)$id;
            }
        }
    }


    /**
     * Получает и устанавливает соответствие видимых дочерних и родительских ID#
     */
    protected function setVisParentsChildrenIds()
    {
        $this->visChildrenIds =
        $this->visAllChildrenIds = [];

        foreach ($this->childrenIds as $pid => $childrenIds) {
            $this->visChildrenIds[$pid] = array_intersect_key(
                $childrenIds,
                $this->visibleIds
            );
        }

        // Установим наборы всех дочерних ID#
        $ch = array_keys(array_filter($this->childrenIds, function ($x) {
            return !$x;
        }));
        while ($ch) {
            foreach ($ch as $chId) {
                if ($this->childrenIds[$chId]) {
                    $allGrandChildrenIdsByIds = array_map(function ($x) {
                        return (array)(isset($this->visAllChildrenIds[$x]) ? $this->visAllChildrenIds[$x] : []);
                    }, $this->visChildrenIds[$chId]);
                    $allGrandChildrenIds = array_reduce(
                        $allGrandChildrenIdsByIds,
                        function ($a, $b) {
                            return $a + $b;
                        },
                        []
                    );
                    $this->visAllChildrenIds[(string)$chId] = $this->visChildrenIds[$chId]
                                                            + $allGrandChildrenIds;
                } else {
                    $this->visAllChildrenIds[(string)$chId] = [];
                }
            }
            $ch = array_map(function ($x) {
                return isset($this->parentId[$x]) ? $this->parentId[$x] : null;
            }, $ch);
            $ch = array_filter($ch, function ($x) {
                return $x !== null;
            });
            $ch = array_values(array_unique($ch));
        };
    }
}
