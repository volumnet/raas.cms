<?php
/**
 * Файл класса команды получения кэша страниц
 */
namespace RAAS\CMS;

use RAAS\LockCommand;

/**
 * Команда получения кэша страниц
 */
class GetPageCacheCommand extends LockCommand
{
    /**
     * Выполнение команды
     * @param bool $forceUpdate Принудительно выполнить обновление,
     *                          даже если материалы не были обновлены
     * @param bool $forceLockUpdate Принудительно выполнить обновление,
     *                              даже если есть параллельный процесс
     * @param bool $clearBlocksCache Очистить кэши блоков
     * @param int|string[] $pagesLimit Ограничение числа страниц,
     *                                 либо перечисление страниц
     *                                 (отдельными аргументами)
     * @param float|null $minFreeSpace Минимальное дисковое пространство,
     *                                 которое нужно оставить (в МБ)
     */
    public function process(
        $forceUpdate = false,
        $forceLockUpdate = false,
        $clearBlocksCache = true
    ) {
        if (!$forceLockUpdate && $this->checkLock()) {
            return;
        }
        $args = func_get_args();
        $args = array_slice($args, 3);
        $pagesToUpdate = [];
        $limit = 0;
        $this->lock();
        if ($clearBlocksCache) {
            Package::i()->clearBlocksCache();
            $this->controller->doLog('Blocks caches cleared');
        }
        $minFreeSpace = 0;
        if ($args[0] && !is_numeric($args[0])) {
            if ($args[count($args) - 1] && is_numeric($args[count($args) - 1])) {
                $minFreeSpace = (float)$args[count($args) - 1];
                $args = array_slice($args, 0, -1);
            }
            foreach ($args as $url) {
                if (!$this->checkFreeSpace($minFreeSpace, true)) {
                    break;
                }
                $page = $this->importByURL($url);
                if ($page->id) {
                    if ($page->Material->id) {
                        $this->updateMaterial($page->Material, $forceUpdate);
                    } else {
                        $this->updatePage($page, $forceUpdate);
                    }
                }
            }
        } else {
            $limit = (int)$args[0];
            if ($args[1] && is_numeric($args[1])) {
                $minFreeSpace = (float)$args[1];
            }
            $sqlQuery = "( SELECT id,
                                  'p' AS datatype,
                                  visit_counter,
                                  last_modified
                             FROM " . Page::_tablename()
                      . " ) UNION ALL (
                           SELECT id,
                                  'm' AS datatype,
                                  visit_counter,
                                  last_modified
                             FROM " . Material::_tablename()
                      . "   WHERE cache_url != ''
                          )
                          ORDER BY (datatype = 'p') DESC, visit_counter DESC";
            $sqlResult = Page::_SQL()->get($sqlQuery);
            $i = 0;
            foreach ($sqlResult as $sqlRow) {
                if (!$this->checkFreeSpace($minFreeSpace, true)) {
                    break;
                }
                if ($limit && ($i > $limit)) {
                    break;
                }
                $result = false;
                if ($sqlRow['datatype'] == 'p') {
                    $page = new Page($sqlRow['id']);
                    $result = $this->updatePage($page, $forceUpdate);
                } else {
                    $material = new Material($sqlRow['id']);
                    $result = $this->updateMaterial($material, $forceUpdate);
                }
                if ($result) {
                    $i++;
                }
            }
        }
        $this->unlock();
    }


    /**
     * Обновить кэш страницы
     * @param Page $page Страница
     * @param bool $forceUpdate Принудительно выполнить обновление,
     *                          даже если страница не была обновлена
     * @return bool Обновлен ли кэш страницы
     */
    public function updatePage(Page $page, $forceUpdate)
    {
        if (!$page->cache) {
            $this->controller->doLog(
                'Page #' . (int)$page->id . ' "' . $page->url . '": is not cached'
            );
            return false;
        }
        $cachefile = $page->cacheFile;
        $mt = strtotime($page->last_modified);
        if (is_file($cachefile)) {
            $ft = filemtime($cachefile);
            if (($ft >= $mt) && !$forceUpdate) {
                $this->controller->doLog(
                    'Page #' . (int)$page->id . ' "' . $page->url . '": data is actual'
                );
                return false;
            }
        }
        $page->rebuildCache();
        $this->controller->doLog(
            'Page #' . (int)$page->id . ' "' . $page->url . '": (' .
            ($ft > 0 ? date('Y-m-d H:i:s ', $ft) : '') .
            '->' .
            ($mt > 0 ? date(' Y-m-d H:i:s', $mt) : '') .
            ') updated'
        );
        return true;
    }


    /**
     * Обновить кэш материала
     * @param Material $material Материал
     * @param bool $forceUpdate Принудительно выполнить обновление,
     *                          даже если материал не были обновлен
     * @return bool Обновлен ли кэш материала
     */
    public function updateMaterial(Material $material, $forceUpdate)
    {
        if (!$material->cache_url) {
            $this->controller->doLog(
                'Material #' . (int)$material->id . ' "' . $page->url .
                '": has no actual URL'
            );
            return false;
        }
        $page = $material->urlParent;
        if (!$page->cache) {
            $this->controller->doLog(
                'Page #' . (int)$page->id . ' "' . $page->url . '": is not cached'
            );
            return false;
        }
        $page->Material = $material;
        $cachefile = $page->cacheFile;
        $mt = strtotime($material->last_modified);
        if (is_file($cachefile)) {
            $ft = filemtime($cachefile);
            if (($ft >= $mt) && !$forceUpdate) {
                $this->controller->doLog(
                    'Material #' . (int)$material->id . ' "' . $material->url .
                    '": data is actual'
                );
                return false;
            }
        }
        $page->rebuildCache();
        $this->controller->doLog(
            'Material #' . (int)$material->id . ' "' . $material->url . '": (' .
            ($ft > 0 ? date('Y-m-d H:i:s ', $ft) : '') .
            '->' .
            ($mt > 0 ? date(' Y-m-d H:i:s', $mt) : '') .
            ') updated'
        );
        return true;
    }




    /**
     * Импортирует страницу, возможно с материалом, по url
     * @param string $url URL для импорта
     * @return Page
     */
    public function importByURL($url)
    {
        $page = Page::importByURL($url);
        if ($page->id) {
            $initUrl = parse_url($url, PHP_URL_PATH);
            $initUrl = str_replace('\\', '/', $initUrl);
            $page->initialURL = $initUrl;
            if (count($page->additionalURLArray) == 1) {
                $material = Material::importByURN($page->additionalURLArray[0]);
                // 2016-02-24, AVS: Добавил проверку in_array(...),
                // т.к. странице присваивались материалы, которых на ней
                // в принципе быть не может
                if ($material
                    && $material->id
                    && in_array($page->id, array_map(function ($x) {
                        return $x->id;
                    }, $material->affectedPages))) {
                    $page->Material = $material;
                }
            }
        }
        return $page;
    }


    /**
     * Проверяет наличие необходимого свободного места на диске
     * @param float $minFreeSpace Количество необходимого места, которое нужно
     *                            оставить
     * @param bool $doLog Выводить ошибку в случае, если места недостаточно
     * @return bool true, если места достаточно, false в противном случае
     */
    public function checkFreeSpace($minFreeSpace = 0, $doLog = false)
    {
        if (!$minFreeSpace) {
            return true;
        }
        $minFreeSpace = (float)$minFreeSpace;
        $diskFreeSpace = @disk_free_space('/');
        $diskFreeSpace = (float)$diskFreeSpace / 1024 / 1024;
        $isEnough = ($diskFreeSpace > $minFreeSpace);
        if ($doLog && !$isEnough) {
            $message = 'There is no enough space for cache building: '
                     . 'free: ' . (int)$diskFreeSpace . 'MB, '
                     . 'required: ' . (int)$minFreeSpace . 'MB';
            $this->controller->doLog($message);
        }
        return $isEnough;
    }
}
