<?php
/**
 * Файл трейта импорта по URN
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Трейт импорта по URN
 */
trait ImportByURNTrait
{
    /**
     * Импортировать сущность по URN
     * @param string $urn URN для импорта
     * @return static|null
     */
    public static function importByURN(string $urn)
    {
        $sqlQuery = "SELECT * FROM " . static::_tablename() . " WHERE urn = ?";
        if ($sqlResult = static::$SQL->getline([$sqlQuery, [$urn]])) {
            $result = new static($sqlResult);
            $result->trust();
            return $result;
        }
        return null;
    }
}
