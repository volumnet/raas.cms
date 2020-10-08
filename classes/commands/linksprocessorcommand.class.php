<?php
/**
 * Команда-обработчик ссылок
 */
namespace RAAS\CMS;

use Exception;
use RAAS\Application;
use RAAS\Attachment;
use RAAS\Command;

/**
 * Команда-обработчик ссылок
 */
class LinksProcessorCommand extends Command
{
    /**
     * Статус редиректа - нормальная страница
     */
    const STATUS_OK = 301;

    /**
     * Статус редиректа - страница не найдена
     */
    const STATUS_NOT_FOUND = 404;

    /**
     * Статус редиректа - страница невидимая
     */
    const STATUS_INVISIBLE = 406;

    /**
     * Статус редиректа - двусмысленность
     */
    const STATUS_AMBIGUOUS = 300;

    /**
     * Ограничить выполнение следующими доменами (ID#)
     * @var int[]
     */
    public $restrictDomainsIds = [];

    /**
     * Данные по доменам
     * @var array <pre>array<string[] URL домена => int ID# домена></pre>
     */
    public $domainsData = [];

    /**
     * Основные URL'ы доменов
     * @var array <pre>array<string[] ID# домена => string[] URL домена></pre>
     */
    public $domainsMainURLs = [];

    /**
     * Соответствия страниц доменам
     * @var array <pre>array<string[] ID# страницы => int ID# домена></pre>
     */
    public $pagesDomainsAssoc = [];

    /**
     * Соответствие URL'ов и сущностей
     * @var array <pre>array<string[] URL => array<string[] ID# домена => [
     *     'classname' => string Класс (страница или материал),
     *     'id' => int ID# сущности,
     *     'vis' => bool Видима ли сущность,
     * ]>>
     */
    public $urlsAssoc = [];

    /**
     * Соответствие материалов и URL-ов
     * @var array <pre>array<string[] ID# материала => string[] URL материала></pre>
     */
    public $materialsURLs = [];

    /**
     * Данные по ссылкам
     * @var array <pre>array<string[] URL ссылки => array<
     *     ('block'|'material'|'data'|'menu')[] Тип материала => array[] Данные по записи
     * >></pre>
     */
    public $linksData = [];


    /**
     * Соответствие URL'ов и ссылок редиректа
     * @var array <pre>array<string[] URL => array<string[] ID# домена => [
     *     'url' => string URL редиректа,
     *     'status' => int Статус редиректа в виде констант STATUS_...,
     * ]>>
     */
    public $redirects = [];


    public function process()
    {
        $args = func_get_args();
        $filename = $args[0];
        $domains = array_slice($args, 1);
        if ($domains) {
            $this->restrictDomainsIds = array_map('intval', $domains);
        }
        // var_dump($this->restrictDomainsIds); exit;
        $this->getPagesData();
        $this->getRawLinks();
        $this->getDomainsFromLinks();
        $this->controller->doLog('Data prepared');
        $this->getRedirects();
        $this->getLinksRedirects();
        $this->output($args[0]);

        // $result = $this->getCanonicalURL(
        //     // 'https://future-vision.ru/files/cms/common/1.jpg',
        //     // 'raas://material/2389/?aaa=bbb#ccc',
        //     // '/solutions/',
        //     '/aaa/',
        //     1
        // );
        // var_dump($result); exit;
    }


    /**
     * Возвращает данные, записывает SQL-запрос в файл
     */
    public function output($filename = '')
    {
        $okData = $sqlArr = $debugData = [];
        foreach ($this->linksData as $href => $linkData) {
            foreach ($linkData as $datatype => $entries) {
                foreach ($entries as $i => $entry) {
                    switch ($datatype) {
                        case 'blocks':
                            $debugEntry = 'Block#' . $entry['id'];
                            break;
                        case 'materials':
                            $debugEntry = 'Material#' . $entry['id'];
                            break;
                        case 'data':
                            $debugEntry = 'Data[pid=' . (int)$entry['pid']
                                        . ',fid=' . (int)$entry['fid']
                                        . ',fii=' . (int)$entry['fii'] . ']';
                            break;
                        case 'menu':
                            $debugEntry = 'Menu#' . $entry['id'];
                            break;
                        default:
                            continue;
                            break;
                    }

                    if ($entry['notFound']) {
                        $debugEntry .= ': ' . $href . ' not found at domains '
                                    .  implode(',', $entry['notFound']);
                        $debugData[] = $debugEntry;
                    } elseif ($entry['invisible']) {
                        $debugEntry .= ': ' . $href . " is invisible at domains:";
                        foreach ($entry['invisible'] as $domainId => $invisibleURL) {
                            $debugEntry .= "\n    " . $domainId . ' -> ' . $invisibleURL;
                        }
                        $debugData[] = $debugEntry;
                    } elseif ($entry['ambiguous']) {
                        $debugEntry .= ': ' . $href . " is ambiguous at domains:";
                        foreach ($entry['ambiguous'] as $domainId => $invisibleURL) {
                            $debugEntry .= "\n    " . $domainId . ' -> ' . $invisibleURL;
                        }
                        $debugData[] = $debugEntry;
                    } elseif ($entry['href']) {
                        switch ($datatype) {
                            case 'blocks':
                                $sqlQuery = "UPDATE cms_blocks_html"
                                          .   " SET description = REPLACE("
                                          .             "description, "
                                          .             "'" . Material::_SQL()->real_escape_string('href="' . $href . '"') . "', "
                                          .             "'" . Material::_SQL()->real_escape_string('href="' . $entry['href'] . '"') . "'"
                                          .         ")"
                                          . " WHERE id = " . (int)$entry['id']
                                          . ";";
                                break;
                            case 'materials':
                                $sqlQuery = "UPDATE " . Material::_tablename()
                                          .   " SET description = REPLACE("
                                          .             "description, "
                                          .             "'" . Material::_SQL()->real_escape_string('href="' . $href . '"') . "', "
                                          .             "'" . Material::_SQL()->real_escape_string('href="' . $entry['href'] . '"') . "'"
                                          .         ")"
                                          . " WHERE id = " . (int)$entry['id']
                                          . ";";
                                break;
                            case 'data':
                                $sqlQuery = "UPDATE cms_data"
                                          .   " SET value = REPLACE("
                                          .             "value, "
                                          .             "'" . Material::_SQL()->real_escape_string('href="' . $href . '"') . "', "
                                          .             "'" . Material::_SQL()->real_escape_string('href="' . $entry['href'] . '"') . "'"
                                          .         ")"
                                          . " WHERE pid = " . (int)$entry['pid']
                                          .   " AND fid = " . (int)$entry['fid']
                                          .   " AND fii = " . (int)$entry['fii']
                                          . ";";
                                break;
                            case 'menu':
                                $sqlQuery = "UPDATE " . Menu::_tablename()
                                          .   " SET description = REPLACE("
                                          .             "description, "
                                          .             "'" . Material::_SQL()->real_escape_string('href="' . $href . '"') . "', "
                                          .             "'" . Material::_SQL()->real_escape_string('href="' . $entry['href'] . '"') . "'"
                                          .         ")"
                                          . " WHERE id = " . (int)$entry['id']
                                          . ";";
                                break;
                            default:
                                continue;
                                break;
                        }
                        $debugEntry .= ': ' . $href . ' -> ' . $entry['href'];
                        $okData[] = $debugEntry;
                        if ($filename) {
                            $sqlArr[] = $sqlQuery;
                        }
                    }
                }
            }
        }
        if ($filename) {
            file_put_contents($filename, implode("\n", $sqlArr));
        }
        echo implode("\n", $okData) . "\n";
        echo implode("\n", $debugData) . "\n";
    }


    /**
     * Получает данные по страницам
     */
    public function getPagesData()
    {
        $domainsCache = PageRecursiveCache::i()->getChildrenCache(0);
        foreach ($domainsCache as $domainCache) {
            $domainId = $domainCache['id'];
            $domainURLs = explode(' ', $domainCache['urn']);
            $this->domainsMainURLs[trim($domainId)] = trim($domainURLs[0]);
            foreach ($domainURLs as $domainURL) {
                $domainURL = preg_replace('/^www\\./umis', '', $domainURL);
                $this->domainsData[trim($domainURL)] = (int)$domainId;
            }

            $domainChildren = PageRecursiveCache::i()->getSelfAndChildrenCache($domainId);
            foreach ($domainChildren as $pageData) {
                $this->pagesDomainsAssoc[trim($pageData['id'])] = (int)$domainId;
                $this->urlsAssoc[trim($pageData['cache_url'])][trim($domainId)] = [
                    'classname' => Page::class,
                    'id' => (int)$pageData['id'],
                    'vis' => ($pageData['vis'] && $pageData['pvis'])
                ];
            }
        }

        $sqlQuery = "SELECT tM.id,
                            tM.vis,
                            tM.cache_url_parent_id,
                            tM.cache_url,
                            GROUP_CONCAT(tMPA.pid SEPARATOR ',') AS pages_ids
                       FROM " . Material::_tablename() . " AS tM
                       JOIN cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id
                      WHERE tM.cache_url != ''
                   GROUP BY tM.id";
        $sqlResult = Material::_SQL()->get($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            foreach (explode(',', $sqlRow['pages_ids']) as $pageId) {
                if ($pageId) {
                    if ($sqlRow['cache_url'] && isset($this->pagesDomainsAssoc[$pageId])) {
                        $pageCache = PageRecursiveCache::i()->cache[$sqlRow['cache_url_parent_id']];
                        // if ($this->pagesDomainsAssoc[$pageId] == 3551 && $sqlRow['id'] == 1488) {
                        //     var_dump($sqlRow, $pageCache); exit;
                        // }
                        $this->urlsAssoc[$sqlRow['cache_url']][trim($this->pagesDomainsAssoc[$pageId])] = [
                            'classname' => Material::class,
                            'id' => $sqlRow['id'],
                            'vis' => (
                                $sqlRow['vis'] &&
                                $pageCache['vis'] &&
                                $pageCache['pvis']
                            ),
                        ];
                        $this->materialsURLs[trim($sqlRow['id'])] = $sqlRow['cache_url'];
                    }
                }
            }
        }
    }


    /**
     * Получает сырые данные по ссылкам
     */
    public function getRawLinks()
    {
        $result = [];
        $rx = '/href="(.*?)"/umis';
        $ignoreRx = '/^(((mailto|tel|skype|viber|history):)|(#))/umis';
        $sqlQuery = "SELECT tBH.id,
                           GROUP_CONCAT(tBPA.page_id SEPARATOR ',') AS pages_ids,
                           tBH.description
                      FROM cms_blocks_html AS tBH
                      JOIN cms_blocks_pages_assoc AS tBPA ON tBPA.block_id = tBH.id
                     WHERE tBH.description LIKE '%href=%'
                  GROUP BY tBH.id";
        $sqlResult = Material::_SQL()->get($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            preg_match_all($rx, $sqlRow['description'], $regs);
            $pagesIds = [];
            foreach (explode(',', $sqlRow['pages_ids']) as $pageId) {
                if ($pageId) {
                    $pagesIds[trim($pageId)] = (int)$pageId;
                }
            }
            foreach ($regs[1] as $href) {
                if (preg_match($ignoreRx, $href)) {
                    continue;
                }
                $result[$href]['blocks'][] = [
                    'id' => (int)$sqlRow['id'],
                    'pagesIds' => $pagesIds,
                ];
            }
        }

        $sqlQuery = "SELECT tM.id,
                            GROUP_CONCAT(tMPA.pid SEPARATOR ',') AS pages_ids,
                            tM.description
                       FROM " . Material::_tablename() . " AS tM
                       JOIN cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id
                      WHERE tM.description LIKE '%href=%'
                   GROUP BY tM.id";
        $sqlResult = Material::_SQL()->get($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            preg_match_all($rx, $sqlRow['description'], $regs);
            $pagesIds = [];
            foreach (explode(',', $sqlRow['pages_ids']) as $pageId) {
                if ($pageId) {
                    $pagesIds[trim($pageId)] = (int)$pageId;
                }
            }
            foreach ($regs[1] as $href) {
                if (preg_match($ignoreRx, $href)) {
                    continue;
                }
                $result[$href]['materials'][] = [
                    'id' => (int)$sqlRow['id'],
                    'pagesIds' => $pagesIds,
                ];
            }
        }

        $sqlQuery = "(
            SELECT tD.pid,
                   tD.fid,
                   tD.fii,
                   tD.value,
                   tD.pid AS pages_ids
              FROM cms_data AS tD
              JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
             WHERE tF.classname = ?
        ) UNION ALL (
            SELECT tD.pid,
                   tD.fid,
                   tD.fii,
                   tD.value,
                   GROUP_CONCAT(tMPA.pid SEPARATOR ',') AS pages_ids
              FROM cms_data AS tD
              JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
              JOIN cms_materials_pages_assoc AS tMPA ON tMPA.id = tD.pid
             WHERE tF.classname = ?
        )";
        $sqlBind = [Page::class, Material_Type::class];
        $sqlResult = Material::_SQL()->get([$sqlQuery, $sqlBind]);
        foreach ($sqlResult as $sqlRow) {
            preg_match_all($rx, $sqlRow['value'], $regs);
            $pagesIds = [];
            foreach (explode(',', $sqlRow['pages_ids']) as $pageId) {
                if ($pageId) {
                    $pagesIds[trim($pageId)] = (int)$pageId;
                }
            }
            foreach ($regs[1] as $href) {
                if (preg_match($ignoreRx, $href)) {
                    continue;
                }
                $entry = $sqlRow;
                unset($entry['value']);
                $entry['pagesIds'] = $pagesIds;
                $result[$href]['data'][] = $entry;
            }
        }

        $sqlQuery = "SELECT id,
                            url,
                            page_id AS pages_ids,
                            domain_id AS domains_ids
                       FROM " . Menu::_tablename()
                  . " WHERE NOT page_id";
        $sqlResult = Material::_SQL()->get($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            preg_match_all($rx, $sqlRow['url'], $regs);
            $pagesIds = $domainsIds = [];
            if ((int)$sqlRow['page_id']) {
                $pagesIds[trim($sqlRow['page_id'])] = (int)$sqlRow['page_id'];
            }
            if ((int)$sqlRow['domain_id']) {
                $domainsIds[trim($sqlRow['domain_id'])] = (int)$sqlRow['domain_id'];
            }
            foreach ($regs[1] as $href) {
                if (preg_match($ignoreRx, $href)) {
                    continue;
                }
                $result[$href]['menu'][] = [
                    'id' => (int)$sqlRow['id'],
                    'pagesIds' => $pagesIds,
                    'domainsIds' => $domainsIds,
                ];
            }
        };
        $this->linksData = $result;
    }


    /**
     * Получает домены по ссылкам
     */
    public function getDomainsFromLinks()
    {
        foreach ($this->linksData as $href => $hrefData) {
            $hrefDomains = [];
            foreach ($hrefData as $datatype => $datatypeData) {
                foreach ($datatypeData as $i => $entry) {
                    if (!$entry['domainsIds']) {
                        $domainsIds = [];
                        foreach ((array)$entry['pagesIds'] as $pageId) {
                            if ($domainId = $this->pagesDomainsAssoc[$pageId]) {
                                $domainsIds[trim($domainId)] = (int)$domainId;
                            }
                        }
                        $this->linksData[$href][$datatype][$i]['domainsIds'] = $domainsIds;
                    }
                    if ($this->restrictDomainsIds) {
                        $this->linksData[$href][$datatype][$i]['domainsIds'] = array_intersect(
                            (array)$this->linksData[$href][$datatype][$i]['domainsIds'],
                            $this->restrictDomainsIds
                        );
                    }
                    $hrefDomains += (array)$this->linksData[$href][$datatype][$i]['domainsIds'];
                }
            }
            $this->linksData[$href]['domainsIds'] = $hrefDomains;
        }
    }


    /**
     * Получает каноническую ссылку
     * @param string $url URL исходной ссылки
     * @param int $domainId ID# домена
     * @return array <pre>[
     *     'url' => Каноническая ссылка,
     *     'external' => bool Внешняя ссылка,
     *     'beforeRedirect' => string URL до редиректа
     *     'afterRedirect' => string URL после редиректа
     *     'changed' => bool Поменялась ли ссылка,
     *     'domainId' ?=> int ID# домена
     *     'classname' ?=> Page::class|Material::class|Attachment::class Найденная сущность
     *     'id' ?=> ID# сущности (кроме файлов)
     *     'vis' ?=> Видима ли страница или материал, существует ли файл,
     * ]</pre>
     */
    public function getCanonicalURL($url, $domainId = null)
    {
        $initUrl = $url;
        // Отработаем внутреннюю ссылку
        $url = preg_replace_callback(
            '/raas:\\/\\/((domain\\/)?((page|material)\\/)(\\d+)(\\/?))/umis',
            function ($matches) {
                $oldUrl = $matches[0];
                $newUrl = Redirect::getInternalLink($oldUrl);
                return $newUrl ?: $oldUrl;
            },
            $url
        );
        // Отработаем редиректы
        if (preg_match('/^\\/[^\\/]/umis', $url) && $domainId) {
            $url = ('http' . ($_SERVER['HTTPS'] ? 's' : ''))
                 . '://'
                 . $this->domainsMainURLs[$domainId]
                 . $url;
        }
        $beforeRedirect = $url;
        $url = $afterRedirect = Redirect::processAll($url);

        $urlArr = parse_url($url);
        $linkDomainId = null;
        if ($urlArr['host']) {
            $linkHost = preg_replace('/^www\\./umis', '', $urlArr['host']);
            if (isset($this->domainsData[$linkHost])) {
                $linkDomainId = $this->domainsData[$linkHost];
            } else {
                // Внешние ссылки
                return [
                    'url' => $initUrl,
                    'beforeRedirect' => $beforeRedirect,
                    'afterRedirect' => $afterRedirect,
                    'external' => true,
                    'changed' => false,
                ];
            }
        }
        if (!$linkDomainId && $domainId) {
            $linkDomainId = $domainId;
        }

        // Файловые ссылки
        if (preg_match('/^\\/files\\//umis', $urlArr['path'])) {
            $vis = file_exists(Application::i()->baseDir . urldecode($urlArr['path']));
            $url = $urlArr['path']
                 . ($urlArr['query'] ? ('?' . $urlArr['query']) : '')
                 . ($urlArr['fragment'] ? ('#' . $urlArr['fragment']) : '');
            return [
                'url' => $url,
                'beforeRedirect' => $beforeRedirect,
                'afterRedirect' => $afterRedirect,
                'external' => false,
                'changed' => ($url != $initUrl),
                'domainId' => $linkDomainId,
                'classname' => Attachment::class,
                'vis' => $vis
            ];
        }

        if ($linkDomainId &&
            $domainId &&
            ($found = $this->urlsAssoc[$urlArr['path']][$linkDomainId])
        ) {
            $url = 'raas://'
                    . (($linkDomainId != $domainId) ? 'domain/' : '');
            if ($found['classname'] == Page::class) {
                $url .= 'page/';
            } elseif ($found['classname'] == Material::class) {
                $url .= 'material/';
            }
            $url .= $found['id'] . '/'
                 .  ($urlArr['query'] ? ('?' . $urlArr['query']) : '')
                 .  ($urlArr['fragment'] ? ('#' . $urlArr['fragment']) : '');
            return [
                'url' => $url,
                'beforeRedirect' => $beforeRedirect,
                'afterRedirect' => $afterRedirect,
                'external' => false,
                'changed' => ($url != $initUrl),
                'domainId' => $linkDomainId,
                'classname' => $found['classname'],
                'id' => $found['id'],
                'vis' => $found['vis']
            ];
        } else {
            if ($linkDomainId &&
                $domainId &&
                ($linkDomainId == $domainId)
            ) {
                $url = $urlArr['path']
                     . ($urlArr['query'] ? ('?' . $urlArr['query']) : '')
                     . ($urlArr['fragment'] ? ('#' . $urlArr['fragment']) : '');
            }
            return [
                'url' => $url,
                'beforeRedirect' => $beforeRedirect,
                'afterRedirect' => $afterRedirect,
                'external' => false,
                'changed' => ($url != $initUrl),
                'domainId' => $linkDomainId,
            ];
        }
    }


    /**
     * Получает данные по редиректам
     */
    public function getRedirects()
    {
        $result = [];
        $c = count($this->linksData);
        $i = 0;
        foreach ($this->linksData as $href => $linkData) {
            foreach ($linkData['domainsIds'] as $domainId) {
                $redirectData = $this->getCanonicalURL($href, $domainId);
                if ($redirectData['external']) {
                    continue;
                } else {
                    if ($redirectData['classname']) {
                        if ($redirectData['changed']) {
                            if ($redirectData['vis'] || (
                                ($redirectData['classname'] == Attachment::class) &&
                                preg_match('/^\\/files\\//umis', $redirectData['url'])
                            )) {
                                $status = STATUS_OK;
                            } else {
                                $status = STATUS_INVISIBLE;
                            }
                            $this->redirectData[$href][$domainId] = [
                                'url' => $redirectData['url'],
                                'status' => $status,
                            ];
                        }
                    } else {
                        $this->redirectData[$href][$domainId] = [
                            'url' => $redirectData['url'],
                            'status' => STATUS_NOT_FOUND,
                        ];
                    }
                }
            }
            $i++;
            if (!($i % 10)) {
                $this->controller->doLog('Processed link ' . $i . '/' . $c);
            }
        }
        $this->controller->doLog('Processed link ' . $i . '/' . $c);
    }


    /**
     * Получает данные по редиректам для конкретных ссылок
     */
    public function getLinksRedirects()
    {
        foreach ($this->linksData as $href => $linkData) {
            foreach ($linkData as $datatype => $entries) {
                foreach ($entries as $i => $entry) {
                    $redirects = array_intersect_key(
                        (array)$this->redirectData[$href],
                        (array)$entry['domainsIds']
                    );
                    if ($notFound = array_map(
                        function ($x) {
                            return $x['url'];
                        },
                        array_filter($redirects, function ($x) {
                            return $x['status'] == STATUS_NOT_FOUND;
                        })
                    )) {
                        $notFound = array_keys($notFound);
                        $this->linksData[$href][$datatype][$i]['notFound'] = $notFound;
                    } elseif ($invisible = array_map(
                        function ($x) {
                            return $x['url'];
                        },
                        array_filter($redirects, function ($x) {
                            return $x['status'] == STATUS_INVISIBLE;
                        })
                    )) {
                        $this->linksData[$href][$datatype][$i]['invisible'] = $invisible;
                    } elseif ($okRedirects = array_map(
                        function ($x) {
                            return $x['url'];
                        },
                        array_filter($redirects, function ($x) {
                            return $x['status'] == STATUS_OK;
                        })
                    )) {
                        $uniqueRedirects = array_values(array_unique($okRedirects));
                        if (count($uniqueRedirects) == 1) {
                            $this->linksData[$href][$datatype][$i]['href'] = $uniqueRedirects[0];
                        } else {
                            $this->linksData[$href][$datatype][$i]['ambiguous'] = $okRedirects;
                        }
                    }
                }
            }
        }
    }
}
