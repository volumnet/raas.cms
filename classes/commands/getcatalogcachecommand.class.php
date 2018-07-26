<?php
/**
 * Файл класса команды получения кэша каталога
 */
namespace RAAS\CMS;

use RAAS\LockCommand;

/**
 * Класс команды получения кэша каталога
 */
class GetCatalogCacheCommand extends LockCommand
{
    /**
     * Выполнение команды
     * @param string|null $mtypeURN URN типа материалов кэша
     * @param int|null $lockFileExpiration Время, через которое блокировка становится неактуальной, в секундах
     * @param bool $forceUpdate Принудительно выполнить обновление, даже если материалы не были обновлены
     * @param bool $forceLockUpdate Принудительно выполнить обновление, даже если есть параллельный процесс
     */
    public function process(
        $mtypeURN = 'catalog',
        $forceUpdate = false,
        $forceLockUpdate = false
    ) {
        if (!$forceLockUpdate && $this->checkLock()) {
            return;
        }
        $mtype = Material_Type::importByURN($mtypeURN);
        if ($mtypesIds = $mtype->selfAndChildrenIds) {
            $sqlQuery = "SELECT MAX(UNIX_TIMESTAMP(modify_date))
                           FROM " . Material::_tablename()
                      . " WHERE pid IN (" . implode(", ", $mtypesIds) . ")";
            $lastModifiedMaterialTimestamp = Material::_SQL()->getvalue($sqlQuery);
            $cc = new Catalog_Cache($mtype);
            $cacheFilename = $cc->getFilename();
            $cacheModifiedTimestamp = is_file($cacheFilename) ? filemtime($cacheFilename) : 0;
            if ($forceUpdate || ($lastModifiedMaterialTimestamp > $cacheModifiedTimestamp)) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $this->lock();
                $result = $cc->getCache();
                $this->unlock();
            } else {
                $this->controller->doLog('Data is actual');
                return;
            }
        }
    }
}
