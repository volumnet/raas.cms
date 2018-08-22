<?php
/**
 * Файл класса интерфейса sitemap.xml
 */
namespace RAAS\CMS;

use RAAS\Timer;

/**
 * Класс интерфейса sitemap.xml
 */
class SitemapInterface extends AbstractInterface
{
    public function process()
    {
        Timer::add('sitemap.xml');
        $page = $this->page->Domain;

        $pagesData = array();
        $pages = $this->getPages();
        $content = $this->showMenu($pages) . $this->showMaterials($pages);
        $text = $this->getUrlSet($content)
              . '<!-- ' . Timer::get('sitemap.xml')->time . ' -->';
        return $text;
    }


    /**
     * Получает список данных страниц, пригодных для отображения
     * @param array<int> $parentsIds ID# родительских страниц
     * @param array<int> $ignoredIds ID# игнорируемых страниц
     * @return array<array<string[] => mixed>>
     */
    public function getPages(array $parentsIds = array(), array $ignoredIds = array(), array &$pagesData = array())
    {
        $sqlQuery = "SELECT id,
                            pid,
                            urn,
                            changefreq,
                            post_date,
                            modify_counter,
                            last_modified,
                            sitemaps_priority
                       FROM " . Page::_tablename()
                  . " WHERE vis
                        AND pvis
                        AND NOT response_code
                        AND ";
        if ($parentsIds) {
            $sqlQuery .= " pid IN (" . implode(", ", $parentsIds) . ") ";
        } else {
            $sqlQuery .= " NOT pid";
        }
        if ($ignoredIds) {
            $sqlQuery .= " AND id NOT IN (" . implode(", ", $ignoredIds) . ") ";
        }
        $sqlResult = Page::_SQL()->get($sqlQuery);
        $pagesIds = array();
        $result = array();
        foreach ($sqlResult as $sqlRow) {
            $pagesIds[] = $sqlRow['id'];
            if ($sqlRow['pid'] && $pagesData[$sqlRow['pid']]) {
                $sqlRow['url'] = $pagesData[$sqlRow['pid']]['url'] . $sqlRow['urn'] . '/';
            } else {
                $row = new Page($sqlRow);
                $sqlRow['url'] = $row->domain . $row->url;
            }
            $result[$sqlRow['id']] = $sqlRow;
        }
        $childrenResult = array();
        if ($pagesIds) {
            $childrenResult = $this->getPages($pagesIds, $ignoredIds, $result);
        }
        $result = $result + $childrenResult;
        return $result;
    }


    /**
     * Получает <urlset> с указанным содержимым
     * @param string $content Содержимое
     * @return string
     */
    public function getUrlSet($content)
    {
        $text =  '<?xml version="1.0" encoding="UTF-8"?' . '>
                  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                    ' . $content . '
                  </urlset>';
        return $text;
    }


    /**
     * Получает блок <changefreq>
     * @param array<string[] => mixed> $itemData Данные по странице или материалу,
     *                                 для которого получаем
     * @return string
     */
    public function getChangeFreq($itemData)
    {
        $text = '';
        if ($itemData['changefreq']) {
            $text .= '<changefreq>'
                  .     htmlspecialchars($itemData['changefreq'])
                  .  '</changefreq>';
        } else {
            $d0 = max(0, strtotime($itemData['post_date']));
            $s = ((time() - $d0) / $itemData['modify_counter']);
            $text .= '<changefreq>';
            if ($s < 1800) {
                $text .= 'always';
            } elseif ($s < 2 * 3600) {
                $text .= 'hourly';
            } elseif ($s < 2 * 86400) {
                $text .= 'daily';
            } elseif ($s < 2 * 7 * 86400) {
                $text .= 'weekly';
            } elseif ($s < 2 * 30 * 86400) {
                $text .= 'monthly';
            } elseif ($s < 2 * 365 * 86400) {
                $text .= 'yearly';
            } else {
                $text .= 'never';
            }
            $text .= '</changefreq>';
        }
        return $text;
    }


    /**
     * Получить блок <url>
     * @param Page|Material $item Страница или материал, для которого получаем
     * @return string
     */
    public function getUrl($itemData)
    {
        $text = '<url>'
              .   '<loc>' . htmlspecialchars($itemData['url']) . '</loc>';
        if (strtotime($itemData['last_modified']) > 0) {
            $text .= '<lastmod>'
                  .     date(DATE_W3C, strtotime($itemData['last_modified']))
                  .  '</lastmod>';
        }
        $text .= $this->getChangeFreq($itemData);
        $text .= '<priority>'
              .     str_replace(',', '.', (float)$itemData['sitemaps_priority'])
              .  '</priority>';
        $text .= '</url>';
        return $text;
    }


    /**
     * Получить список блоков <url> для дерева страниц (включая материалы)
     * @param array<array<string[] => mixed>> $pagesData Данные страниц для отображения
     * @return string
     */
    public function showMenu(array $pagesData)
    {
        foreach ($pagesData as $pageRow) {
            $text .= $this->getUrl($pageRow);
        }
        return $text;
    }


    /**
     * Получить список блоков <url> для материалов
     * @param array<array<string[] => mixed>> $pagesData Данные страниц для отображения
     * @param array<int> $materialTypesIds ID# типов материалов (ограничение)
     * @return string
     */
    public function showMaterials(array $pagesData = array(), array $materialTypesIds = array())
    {
        $pagesIds = array_map(function ($x) {
            return $x['id'];
        }, $pagesData);
        $sqlQuery = "SELECT *
                       FROM " . Material::_tablename()
                  . " WHERE 1 ";
        if ($materialTypesIds) {
            $sqlQuery .= " AND pid IN (" . implode(", ", $materialTypesIds) . ") ";
        }
        $sqlQuery .= " ORDER BY priority";
        $sqlResult = Material::_SQL()->query($sqlQuery);
        $text = '';
        foreach ($sqlResult as $sqlRow) {
            $material = new Material($sqlRow);
            $affectedPages = $material->affectedPages;
            if ($affectedPages[0] && in_array($affectedPages[0]->id, $pagesIds)) {
                $sqlRow['url'] = $pagesData[$affectedPages[0]->id]['url'] . $sqlRow['urn'] . '/';
                $text .= $this->getUrl($sqlRow);
            }
        }
        return $text;
    }
}
