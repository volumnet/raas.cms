<?php
/**
 * Расширенный интерфейс sitemap.xml
 *
 * Создает четыре файла - sitemap.xml (корневой),
 *                        sitemap.sections.xml (разделы, кроме каталога),
 *                        sitemap.catalog.xml (каталог),
 *                        sitemap.goods.xml (товары)
 */
namespace RAAS\CMS;

use RAAS\Timer;
use RAAS\Application;

/**
 * Расширенный интерфейс sitemap.xml
 */
class SitemapInterfaceExtended extends SitemapInterface
{
    /**
     * Обрабатывает интерфейс
     * @param string $catalogMTypeURN URN типа материалов каталога
     * @param string $catalogPageURL Относительный путь страницы -
     *                               корня каталога
     * @return string
     */
    public function process(
        $catalogMTypeURN = 'catalog',
        $catalogPageUrl = '/catalog/'
    ) {
        Timer::add('sitemap.xml');
        $this->prepareMetaData();
        $page = $this->page->Domain;

        $this->getAndSaveSitemaps($catalogMTypeURN, $catalogPageUrl);
        $text = $this->getAndSaveIndex();
        $text .= '<!-- ' . Timer::get('sitemap.xml')->time . ' -->';
        return $text;
    }


    /**
     * Формирует, сохраняет и выдает индекс sitemap.xml
     * @return string Текст индекса
     */
    public function getAndSaveIndex()
    {
        $text = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $host = $this->getCurrentHostURL();
        foreach (['sections', 'catalog', 'goods'] as $key) {
            $filename = Application::i()->baseDir . '/sitemap.' . $key . '.xml';
            if (is_file($filename)) {
                $fileURL = $host . '/sitemap.' . $key . '.xml';
                $date = date(DATE_W3C, filemtime($filename));
                $text .= '<sitemap>'
                      .    '<loc>' . $fileURL . '</loc>'
                      .    '<lastmod>' . $date . '</lastmod>'
                      .  '</sitemap>';
            }
        }
        $text .= '</sitemapindex>';
        file_put_contents(Application::i()->baseDir . '/sitemap.xml', $text);
        return $text;
    }


    /**
     * Получает и сохраняет семейство sitemap
     * @param string $catalogMTypeURN URN типа материалов каталога
     * @param string $catalogPageURL Относительный путь страницы -
     *                               корня каталога
     */
    public function getAndSaveSitemaps(
        $catalogMTypeURN = 'catalog',
        $catalogPageUrl = '/catalog/'
    ) {
        $page = $this->page->Domain;

        $catalogPage = Page::importByURL($page->domain . $catalogPageUrl);
        $catalogMType = Material_Type::importByURN($catalogMTypeURN);

        $sqlQuery = "SELECT id FROM " . Material_Type::_tablename();
        $allMTypesIds = Material_Type::_SQL()->getcol($sqlQuery);

        $catalogMTypesIds = $catalogMType->selfAndChildrenIds;
        $restMTypesIds = array_values(
            array_diff($allMTypesIds, $catalogMTypesIds)
        );

        $catalogPagesIds = $catalogPage->selfAndChildrenIds;
        file_put_contents(
            Application::i()->baseDir . '/sitemap.sections.xml',
            $this->getSectionsSitemap($catalogPagesIds, $restMTypesIds)
        );

        file_put_contents(
            Application::i()->baseDir . '/sitemap.catalog.xml',
            $this->getCatalogSitemap($catalogPage)
        );

        file_put_contents(
            Application::i()->baseDir . '/sitemap.goods.xml',
            $this->getGoodsSitemap($catalogPage, $catalogMTypesIds)
        );
    }


    /**
     * Получает содержимое файла sitemap.sections.xml
     * @param array<int> $catalogPagesIds Список ID# категорий каталога
     * @param array<int> $restMTypesIds Список ID# типов материалов
     *                                  кроме каталога
     * @return string
     */
    public function getSectionsSitemap($catalogPagesIds, $restMTypesIds)
    {
        $domainPage = $this->page->Domain;
        $domainPageData = $domainPage->getArrayCopy();
        $domainPageData['entry_type'] = 'page';
        $domainPageData['url'] = $domainPage->domain
                               . $domainPageData['cache_url'];

        $pages = array_merge(
            [trim($domainPage->id) => $domainPageData],
            $this->getPages([$domainPage->id], $catalogPagesIds)
        );
        $content = $this->showMenu($pages)
                 . $this->showMaterials($pages, $restMTypesIds);
        $text = $this->getUrlSet($content);
        return $text;
    }


    /**
     * Получает содержимое файла sitemap.catalog.xml
     * @param Page $catalogRoot Страница - корень каталога
     * @return string
     */
    public function getCatalogSitemap(Page $catalogRoot)
    {
        $pageCache = PageRecursiveCache::i();
        $catalogPageData = $pageCache->cache[$catalogRoot->id] ?? [];
        $tmpPageParentsIds = $pageCache->getParentsIds($catalogRoot->id);
        $domainId = array_shift($tmpPageParentsIds);
        if ($this->server['HTTP_HOST']) {
            $domainURL = 'http' . ($this->server['HTTPS'] ? 's' : '') . '://'
                . $this->server['HTTP_HOST'];
        } else {
            $domainPage = new Page($domainId);
            $domainURL = $domainPage->domain;
        }
        $catalogPageData['entry_type'] = 'page';
        $catalogPageData['url'] = $domainURL . ($catalogPageData['cache_url'] ?? '');

        $pages = array_merge(
            [trim($catalogPageData['id'] ?? 0) => $catalogPageData],
            $this->getPages([$catalogRoot->id])
        );
        $content = $this->showMenu($pages);
        $text = $this->getUrlSet($content);
        return $text;
    }


    /**
     * Получает содержимое файла sitemap.goods.xml
     * @param Page $catalogRoot Страница - корень каталога
     * @param array<int> $catalogMTypesIds Список ID# типов материалов каталога
     * @return string
     */
    public function getGoodsSitemap(
        Page $catalogRoot,
        array $catalogMTypesIds = []
    ) {
        $pages = $this->getPages([$catalogRoot->id]);
        $content = $this->showMaterials($pages, $catalogMTypesIds);
        $text = $this->getUrlSet($content);
        return $text;
    }
}
