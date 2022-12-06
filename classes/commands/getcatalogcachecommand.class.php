<?php
/**
 * Файл класса команды получения кэша каталога
 */
namespace RAAS\CMS;

use RAAS\Command;

/**
 * Команда получения кэша каталога
 */
class GetCatalogCacheCommand extends Command
{
    /**
     * Выполнение команды
     * @param string|null $mtypeURN URN типа материалов кэша
     * @param bool $forceUpdate Принудительно выполнить обновление,
     *     даже если материалы не были обновлены
     * @param bool $forceLockUpdate Принудительно выполнить обновление,
     *     даже если есть параллельный процесс {@deprecated больше не используется}
     */
    public function process(
        $mtypeURN = 'catalog',
        $forceUpdate = false,
        $forceLockUpdate = false
    ) {
        $mtype = Material_Type::importByURN($mtypeURN);
        if (!$mtype->id) {
            $this->controller->doLog(
                'Material type ' . $mtypeURN . ' doesn\t exist'
            );
            return;
        }
        if ($mtypesIds = $mtype->selfAndChildrenIds) {
            $sqlQuery = "SELECT MAX(UNIX_TIMESTAMP(last_modified))
                           FROM " . Material::_tablename()
                      . " WHERE pid IN (" . implode(", ", $mtypesIds) . ")";
            $lastModifiedMaterialTimestamp = Material::_SQL()->getvalue($sqlQuery);
            $cc = new Catalog_Cache($mtype);
            $cacheFilename = $cc->getFilename();
            $cacheModifiedTimestamp = is_file($cacheFilename)
                                    ? filemtime($cacheFilename)
                                    : 0;
            if ($forceUpdate ||
                ($lastModifiedMaterialTimestamp > $cacheModifiedTimestamp)
            ) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $result = $cc->getCache();
            } else {
                $this->controller->doLog('Data is actual');
                return;
            }
        }
    }
}
