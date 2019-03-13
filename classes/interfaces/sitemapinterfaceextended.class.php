<?php
/**
 * Файл класса расширенного интерфейса sitemap.xml
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
 * Класс расширенного интерфейса sitemap.xml
 */
class SitemapInterfaceExtended extends SitemapInterface
{
    /**
     * Обрабатывает интерфейс
     * @param string $catalogMTypeURN URN типа материалов каталога
     * @param string $catalogPageURL Относительный путь страницы - корня каталога
     * @return text
     */
    public function process($catalogMTypeURN = 'catalog', $catalogPageUrl = '/catalog/')
    {
        Timer::add('sitemap.xml');
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
        foreach (array('sections', 'catalog', 'goods') as $key) {
            if (is_file(Application::i()->baseDir . '/sitemap.' . $key . '.xml')) {
                $text .= '<sitemap>'
                      .    '<loc>' . $this->getCurrentHostURL() . '/sitemap.' . $key . '.xml</loc>'
                      .    '<lastmod>'
                      .       date(DATE_W3C, filemtime(Application::i()->baseDir . '/sitemap.' . $key . '.xml'))
                      .    '</lastmod>'
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
     * @param string $catalogPageURL Относительный путь страницы - корня каталога
     */
    public function getAndSaveSitemaps($catalogMTypeURN = 'catalog', $catalogPageUrl = '/catalog/')
    {
        $page = $this->page->Domain;

        $catalogPage = Page::importByURL($page->domain . $catalogPageUrl);
        $catalogMType = Material_Type::importByURN($catalogMTypeURN);

        $sqlQuery = "SELECT id FROM " . Material_Type::_tablename();
        $allMTypesIds = Material_Type::_SQL()->getcol($sqlQuery);

        $catalogMTypesIds = $catalogMType->selfAndChildrenIds;
        $restMTypesIds = array_values(array_diff($allMTypesIds, $catalogMTypesIds));
        $catalogPagesIds = $catalogPage->selfAndChildrenIds;

        $sectionsSitemap = $this->getSectionsSitemap($catalogPagesIds, $restMTypesIds);
        file_put_contents(Application::i()->baseDir . '/sitemap.sections.xml', $sectionsSitemap);
        unset($sectionsSitemap);

        $catalogSitemap = $this->getCatalogSitemap($catalogPage);
        file_put_contents(Application::i()->baseDir . '/sitemap.catalog.xml', $catalogSitemap);
        unset($catalogSitemap);

        $goodsSitemap = $this->getGoodsSitemap($catalogPage, $catalogMTypesIds);
        file_put_contents(Application::i()->baseDir . '/sitemap.goods.xml', $goodsSitemap);
        unset($goodsSitemap);
    }


    /**
     * Получает содержимое файла sitemap.sections.xml
     * @param array<int> $catalogPagesIds Список ID# категорий каталога
     * @param array<int> $restMTypesIds Список ID# типов материалов кроме каталога
     * @return string
     */
    public function getSectionsSitemap($catalogPagesIds, $restMTypesIds)
    {
        $domainPage = $this->page->Domain;
        $domainPageData = $domainPage->getArrayCopy();
        $domainPageData['url'] = $domainPage->domain . $domainPageData['cache_url'];

        $pages = array_merge(
            [trim($domainPage->id) => $domainPageData],
            $this->getPages([$domainPage->id], $catalogPagesIds)
        );
        $content = $this->showMenu($pages) . $this->showMaterials($pages, $restMTypesIds);
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
        $pages = $this->getPages(array($catalogRoot->id));
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
    public function getGoodsSitemap(Page $catalogRoot, array $catalogMTypesIds = array())
    {
        $pages = $this->getPages(array($catalogRoot->id));
        $content = $this->showMaterials($pages, $catalogMTypesIds);
        $text = $this->getUrlSet($content);
        return $text;
    }
}
