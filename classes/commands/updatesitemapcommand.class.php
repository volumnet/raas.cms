<?php
/**
 * Файл класса команды обновления файла sitemap.xml
 */
namespace RAAS\CMS;

use SOME\EventProcessor;
use RAAS\Application;
use RAAS\LockCommand;

/**
 * Класс команды обновления файла Яндекс-Маркета
 */
class UpdateSitemapCommand extends LockCommand
{
    /**
     * Выполнение команды
     * @param string $catalogMTypeURN URN типа материалов каталога
     * @param string $catalogPageURL Относительный путь страницы -
     *                               корня каталога
     * @param bool $https Включен ли HTTPS
     * @param bool $forceUpdate Принудительно выполнить обновление,
     *                          даже если материалы не были обновлены
     * @param bool $forceLockUpdate Принудительно выполнить обновление,
     *                              даже если есть параллельный процесс
     */
    public function process(
        $catalogMTypeURN = 'catalog',
        $catalogPageUrl = '/catalog/',
        $https = false,
        $forceUpdate = false,
        $forceLockUpdate = false
    ) {
        $t = $this;
        if (!$forceLockUpdate && $this->checkLock()) {
            return;
        }
        $outputFile = Application::i()->baseDir . '/sitemap.xml';
        if (!$forceUpdate) {
            $sqlQuery = "SELECT MAX(UNIX_TIMESTAMP(last_modified))
                           FROM " . Material::_tablename()
                      . " WHERE 1";
            $lastModifiedMaterialTimestamp = Material::_SQL()->getvalue($sqlQuery);
            $sqlQuery = "SELECT MAX(UNIX_TIMESTAMP(last_modified))
                           FROM " . Page::_tablename()
                      . " WHERE 1";
            $lastModifiedPageTimestamp = Material::_SQL()->getvalue($sqlQuery);
            if (is_file($outputFile)) {
                if (filemtime($outputFile) >= max(
                    $lastModifiedMaterialTimestamp,
                    $lastModifiedPageTimestamp
                )) {
                    $this->controller->doLog('Data is actual');
                    return;
                }
            }
        }
        $this->lock();
        $pages = Page::getSet(['where' => "NOT pid"]);
        $page = array_shift($pages);
        if ($page->id) {
            if ($https) {
                $_SERVER['HTTPS'] = 'on';
            }
            $_SERVER['HTTP_HOST'] = parse_url($page->domain, PHP_URL_HOST);
            $interface = new SitemapInterfaceExtended(
                null,
                $page,
                [],
                [],
                [],
                [],
                $_SERVER
            );
            EventProcessor::on(
                SitemapInterface::class . ':' . 'showMenu:startpage',
                null,
                function ($pageId, $data) use ($t) {
                    if (!($data['index'] % 100)) {
                        $t->controller->doLog(
                            'Page #' . $pageId . ' started - ' .
                            (int)($data['index'] * 100 / $data['size']) . '%'
                        );
                    }
                }
            );
            EventProcessor::on(
                SitemapInterface::class . ':' . 'showMaterials:startmaterial',
                null,
                function ($materialId, $data) use ($t) {
                    if (!($data['index'] % 100)) {
                        $t->controller->doLog(
                            'Material #' . $materialId . ' started - ' .
                            (int)($data['index'] * 100 / $data['size']) . '%'
                        );
                    }
                }
            );
            $interface->process($catalogMTypeURN, $catalogPageUrl);
        } else {
            $this->controller->doLog('Root page not found');
        }
        $this->unlock();
    }
}
