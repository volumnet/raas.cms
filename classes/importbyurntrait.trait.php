<?php
/**
 * Файл трейта импорта по URN
 */
namespace RAAS\CMS;

/**
 * Трейт импорта по URN
 */
trait ImportByURNTrait
{
    /**
     * Импортировать сущность по URN
     * @param string $urn URN для импорта
     * @return static
     */
    public static function importByURN($urn)
    {
        $sqlQuery = "SELECT * FROM " . static::_tablename() . " WHERE urn = ?";
        if ($sqlResult = static::$SQL->getline([$sqlQuery, [$urn]])) {
            return new static($sqlResult);
        }
        return null;
    }
}
