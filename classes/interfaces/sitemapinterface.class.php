<?php
/**
 * Файл класса интерфейса sitemap.xml
 */
namespace RAAS\CMS;

use RAAS\Attachment;
use RAAS\Timer;
use SOME\EventProcessor;

/**
 * Класс интерфейса sitemap.xml
 */
class SitemapInterface extends AbstractInterface
{
    /**
     * Данные по изображениям
     * @var [
     *          'images' => array<string[] ID изображения (MD5 от URL) => [
     *              'url' => string абсолютный URL картинки,
     *              'title' =>? string alt картинки
     *          ]>,
     *          'pagesImages' => array<string[] ID# страницы => array<
     *              string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                             (MD5 от URL)
     *          >>,
     *          'materialsImages' => array<string[] ID# материала => array<
     *              string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                             (MD5 от URL)
     *          >>
     *      ]
     */
    protected $imagesData = [];

    /**
     * Данные по уже использованным изображениям
     * для предотвращения повторного использования
     * @var array<string[] ID изображения => string ID изображения>
     */
    protected $affectedImages = [];


    /**
     * Количество страниц
     * @var int
     */
    protected $pagesCounter = 0;

    public function process()
    {
        Timer::add('sitemap.xml');
        $pageCache = PageRecursiveCache::i();
        $this->prepareMetaData();
        $domainId = array_shift($pageCache->getParentsIds($this->page->id));
        $domainPageData = $pageCache->cache[$domainId];
        if ($this->server['HTTP_HOST']) {
            $domainURL = 'http' . ($this->server['HTTPS'] ? 's' : '') . '://'
                . $this->server['HTTP_HOST'];
        } else {
            $domainPage = new Page($domainPageData);
            $domainURL = $domainPage->domain;
        }
        $domainPageData['url'] = $domainURL . '/';

        $pages = array_merge(
            [trim($domainId) => $domainPageData],
            $this->getPages([$domainId])
        );
        $content = $this->showMenu($pages) . $this->showMaterials($pages);
        $text = $this->getUrlSet($content)
              . '<!-- ' . Timer::get('sitemap.xml')->time . ' -->';
        return $text;
    }


    /**
     * Подготавливает метаданные для обработки
     */
    public function prepareMetaData()
    {
        $this->imagesData = $this->getImagesData();
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . Page::_tablename()
                  . " WHERE vis
                        AND pvis
                        AND NOT response_code";
        $this->pagesCounter = Page::_SQL()->getvalue($sqlQuery);
    }


    /**
     * Получает список данных страниц, пригодных для отображения
     * @param array<int> $parentsIds ID# родительских страниц
     * @param array<int> $ignoredIds ID# игнорируемых страниц
     * @param array $pagesData Данные уже полученных страниц
     * @return array<string[] ID# страницы => array<string[] => mixed>>
     */
    public function getPages(
        array $parentsIds = [],
        array $ignoredIds = [],
        array &$pagesData = []
    ) {
        $pageCache = PageRecursiveCache::i();
        if ($parentsIds) {
            $pagesIds = $pageCache->getAllChildrenIds($parentsIds);
        } else {
            $pagesIds = array_keys($pageCache->cache);
        }
        if ($ignoredIds) {
            $pagesIds = array_diff($pagesIds, $ignoredIds);
        }
        $sqlResult = array_intersect_key(
            $pageCache->cache,
            array_flip($pagesIds)
        );
        $sqlResult = array_filter($sqlResult, function ($x) {
            return $x['vis'] && $x['pvis'] && !$x['response_code'];
        });
        $result = [];
        $domainsURLs = [];
        foreach ($sqlResult as $sqlRow) {
            $domainId = array_shift($pageCache->getParentsIds($sqlRow['id']));
            if (!isset($domainsURLs[$domainId])) {
                if ($this->server['HTTP_HOST']) {
                    $domainURL = 'http' . ($this->server['HTTPS'] ? 's' : '') . '://'
                        . $this->server['HTTP_HOST'];
                } else {
                    $domainPage = new Page($domainId);
                    $domainURL = $domainPage->domain;
                }
                $domainsURLs[trim($domainId)] = $domainURL;
            }
            $sqlRow['url'] = $domainsURLs[$domainId] . $sqlRow['cache_url'];
            $sqlRow['entry_type'] = 'page';
            $result[$sqlRow['id']] = $sqlRow;
        }
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
                  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
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
     * @param array $itemData Данные по странице или материалу,
     *                        для которого получаем
     * @return string
     */
    public function getUrl(array $itemData)
    {
        $text = '<url>'
              // .   '<!-- ' . (isset($itemData['page_id']) ? Material::class : Page::class) . ' #' . (int)$itemData['id'] . ' -->'
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
        $imagesData = [];
        if ($itemData['entry_type'] == 'page') {
            $imagesData = (array)$this->imagesData['pagesImages'][$itemData['id']];
        } elseif ($itemData['entry_type'] == 'material') {
            $imagesData = (array)$this->imagesData['materialsImages'][$itemData['id']];
        }
        if ($imagesData) {
            foreach ($imagesData as $imgId) {
                if (!$this->affectedImages[$imgId]) {
                    $imageData = $this->imagesData['images'][$imgId];
                    if ($imageData) {
                        $text .= '<image:image>'
                              .    '<image:loc>'
                              .       htmlspecialchars($imageData['url'])
                              .    '</image:loc>';
                        if ($imageData['name']) {
                            $text .= '<image:title>'
                                  .     htmlspecialchars($imageData['name'])
                                  .  '</image:title>';
                        }
                        $text .= '</image:image>';
                    }
                    $this->affectedImages[$imgId] = $imgId;
                }
            }
        }
        $text .= '</url>';
        return $text;
    }


    /**
     * Получить список блоков <url> для дерева страниц (включая материалы)
     * @param array<
     *            array<string[] => mixed>
     *        > $pagesData Данные страниц для отображения
     * @return string
     */
    public function showMenu(array $pagesData)
    {
        $i = 0;
        foreach ($pagesData as $pageRow) {
            EventProcessor::emit(
                'startpage',
                $pageRow['id'],
                ['index' => ++$i, 'size' => $this->pagesCounter]
            );
            $text .= $this->getUrl($pageRow);
        }
        return $text;
    }


    /**
     * Получить список блоков <url> для материалов
     * @param array<
     *            array<string[] => mixed>
     *        > $pagesData Данные страниц для отображения
     * @param array<int> $materialTypesIds ID# типов материалов (ограничение)
     * @return string
     */
    public function showMaterials(
        array $pagesData = [],
        array $materialTypesIds = []
    ) {
        $pagesIds = array_map(function ($x) {
            return $x['id'];
        }, $pagesData);
        if (!$pagesIds) {
            return '';
        }
        $sqlQuery = "SELECT *
                       FROM " . Material::_tablename()
                  . " WHERE vis
                        AND cache_url_parent_id IN (" . implode(", ", $pagesIds) . ")";
        if ($materialTypesIds) {
            $sqlQuery .= " AND pid IN (" . implode(", ", $materialTypesIds) . ") ";
        }
        $sqlQuery .= " ORDER BY priority";
        $sqlResult = Material::_SQL()->query($sqlQuery);
        $text = '';
        $i = 0;
        $c = $sqlResult->rowCount();
        $domainsURLs = [];
        foreach ($sqlResult as $sqlRow) {
            $domainId = array_shift(
                PageRecursiveCache::i()->getParentsIds(
                    $sqlRow['cache_url_parent_id']
                )
            );
            if (!isset($domainsURLs[$domainId])) {
                if ($this->server['HTTP_HOST']) {
                    $domainURL = 'http' . ($this->server['HTTPS'] ? 's' : '') . '://'
                        . $this->server['HTTP_HOST'];
                } else {
                    $domainPage = new Page($domainId);
                    $domainURL = $domainPage->domain;
                }
                $domainsURLs[trim($domainId)] = $domainURL;
            }
            EventProcessor::emit(
                'startmaterial',
                $sqlRow['id'],
                ['index' => ++$i, 'size' => $c]
            );
            $sqlRow['url'] = $domainsURLs[$domainId] . $sqlRow['cache_url'];
            $sqlRow['entry_type'] = 'material';
            $text .= $this->getUrl($sqlRow);
        }
        return $text;
    }


    /**
     * Получает данные по картинкам
     * @return [
     *             'images' => array<
     *                 string[] ID изображения (MD5 от URL) => [
     *                     'url' => string абсолютный URL картинки,
     *                     'title' =>? string alt картинки
     *                 ]
     *             >,
     *             'pagesImages' => array<string[] ID# страницы => array<
     *                 string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                                (MD5 от URL)
     *             >>,
     *             'materialsImages' => array<string[] ID# материала => array<
     *                 string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                                (MD5 от URL)
     *             >>
     *         ]
     */
    public function getImagesData()
    {
        $textBlocksImages = $this->getTextBlocksImagesData();
        $materialsImages = $this->getMaterialsImagesData();
        return [
            'images' => array_merge(
                $textBlocksImages['images'],
                $materialsImages['images']
            ),
            'pagesImages' => $textBlocksImages['pagesImages'],
            'materialsImages' => $materialsImages['materialsImages'],
        ];
    }


    /**
     * Получает данные по картинкам текстовых блоков
     * @return [
     *             'images' => array<string[] ID изображения (MD5 от URL) => [
     *                 'url' => string абсолютный URL картинки,
     *                 'title' =>? string alt картинки
     *             ]>,
     *             'pagesImages' => array<string[] ID# страницы => array<
     *                 string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                                (MD5 от URL)
     *             >>
     *         ]
     */
    public function getTextBlocksImagesData()
    {
        $pageCache = PageRecursiveCache::i();
        $domainId = array_shift($pageCache->getParentsIds($this->page->id));
        $pagesIds = $pageCache->getSelfAndChildrenIds($domainId);
        $sqlQuery = "SELECT tB.name,
                            tBH.description,
                            GROUP_CONCAT(tBPA.page_id SEPARATOR ',') AS pages_ids
                       FROM " . Block::_tablename() . " AS tB
                       JOIN " . Block_HTML::_tablename2() . "
                         AS tBH
                         ON tBH.id = tB.id
                       JOIN cms_blocks_pages_assoc
                         AS tBPA
                         ON tBPA.block_id = tB.id
                       JOIN " . Page::_tablename() . "
                         AS tP
                         ON tP.id = tBPA.page_id
                      WHERE tB.vis
                        AND tP.response_code = ''
                        AND tBH.description LIKE ?
                        AND tBPA.page_id IN (" . implode(", ", $pagesIds) . ")
                   GROUP BY tB.id";
        $sqlResult = Block::_SQL()->get([$sqlQuery, '%<img%']);
        $images = [];
        $pagesImages = [];
        foreach ($sqlResult as $sqlRow) {
            $imagesData = $this->parseTextImages($sqlRow['description']);
            $pagesIds = explode(',', $sqlRow['pages_ids']);
            foreach ($imagesData as $imgId => $imageData) {
                $images[$imgId] = $imageData;
                foreach ($pagesIds as $pageId) {
                    $pagesImages[(string)$pageId][(string)$imgId] = $imgId;
                }
            }
        }
        return ['images' => $images, 'pagesImages' => $pagesImages];
    }


    /**
     * Выбирает картинки из текста
     * @return array<string[] ID изображения (MD5 от URL) => [
     *             'url' => string абсолютный URL картинки,
     *             'title' =>? string alt картинки
     *         ]>
     */
    public function parseTextImages($text)
    {
        $imagesData = [];
        if (preg_match_all('/\\<img.*?>/umis', $text, $regs)) {
            for ($i = 0; $i < count($regs[0]); $i++) {
                $imgData = $this->parseImgTag($regs[0][$i]);
                if ($imgData) {
                    $imagesData[md5($imgData['url'])] = $imgData;
                }
            }
        }
        return $imagesData;
    }


    /**
     * Разбирает тег картинки
     * @param string $text Текст тега <img>
     * @return [
     *             'url' => string абсолютный URL картинки,
     *             'title' =>? string alt картинки
     *         ]|null Данные по картинке или null, если не удалось разобрать
     */
    public function parseImgTag($text)
    {
        $imageData = [];
        if (preg_match('/src="(.*?)"/umis', $text, $regs)) {
            $url = $this->formatImageURL($regs[1]);
            if (!$url) {
                return false;
            }
            $imageData['url'] = $url;
        }
        if (preg_match('/alt="(.*?)"/umis', $text, $regs)) {
            $imageData['name'] = $regs[1];
        }
        return $imageData ?: null;
    }


    /**
     * Форматирует URL картинки
     * @param string $url URL картинки
     * @return string|false URL картинки или false, если картинка с другого
     *                          сайта
     */
    public function formatImageURL($url)
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl['path'] &&
            ($parsedUrl['scheme'] != 'data') &&
            (
                !$parsedUrl['host'] ||
                ($parsedUrl['host'] == $this->getCurrentHostName())
            )
        ) {
            return $this->getCurrentHostURL() . $parsedUrl['path'];
        }
        return false;
    }


    /**
     * Получает данные по картинкам материалов
     * @return [
     *             'images' => array<string[] ID изображения (MD5 от URL) => [
     *                 'url' => string абсолютный URL картинки,
     *                 'title' =>? string alt картинки
     *             ]>,
     *             'materialsImages' => array<string[] ID# материала => array<
     *                 string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                                (MD5 от URL)
     *             >>
     *         ]
     */
    public function getMaterialsImagesData()
    {
        $fieldsNames = $this->getImagesFieldsNames();

        $fieldsIds = array_map('intval', array_keys($fieldsNames));
        $temp = $this->getMaterialsAttachmentsData($fieldsIds);

        $attachmentsUrls = $this->getAttachmentsURLs($temp['affectedAttachmentsIds']);
        $materialsNames = $this->getMaterialsNames($temp['affectedMaterialsIds']);

        // Сформируем выдачу
        $imagesData = $this->getMaterialsImagesRawData(
            $temp['materialsAttachmentsData'],
            $attachmentsUrls,
            $materialsNames
        );

        // Применим счетчики
        $imagesData = $this->applyMaterialImagesCounters(
            $imagesData,
            $fieldsNames
        );

        // Схлопнем привязку относительно полей
        $imagesData['materialsImages'] = array_map(function ($materialImages) {
            return array_reduce($materialImages, 'array_merge', []);
        }, $imagesData['materialsImages']);

        return $imagesData;
    }


    /**
     * Получает данные полей с картинками
     * @return array<string[] ID# поля => string наименование поля>
     */
    public function getImagesFieldsNames()
    {
        $sqlQuery = "SELECT id, name
                       FROM " . Field::_tablename() . "
                      WHERE classname = ?
                        AND datatype = ?";
        $sqlBind = [Material_Type::class, 'image'];
        $sqlResult = Field::_SQL()->get([$sqlQuery, $sqlBind]);
        $fieldsNames = [];
        foreach ($sqlResult as $sqlRow) {
            $fieldsNames[trim((int)$sqlRow['id'])] = $sqlRow['name'];
        }
        return $fieldsNames;
    }


    /**
     * Получает данные по значениям полей изображений у материалов
     * @param array<int> $fieldsIds ID# полей изображений
     * @return [
     *             'materialsAttachmentsData' => array<[
     *                 'attachment' => int|string ID# вложения,
     *                 'fid' => int|string ID# поля,
     *                 'pid' => int|string ID# материала,
     *                 'name' => string заголовок изображения
     *             ]>,
     *             'affectedAttachmentsIds' => array<int> ID# задействованных
     *                                                    вложений,
     *             'affectedMaterialsIds' => array<int> ID# задействованных
     *                                                  материалов
     *         ]
     */
    public function getMaterialsAttachmentsData(array $fieldsIds = [])
    {
        $pageCache = PageRecursiveCache::i();
        $domainId = array_shift($pageCache->getParentsIds($this->page->id));
        $pagesIds = $pageCache->getSelfAndChildrenIds($domainId);

        $materialsAttachmentsData = [];
        $affectedAttachmentsIds = [];
        $affectedMaterialsIds = [];
        if ($fieldsIds) {
            $sqlQuery = "SELECT tD.pid, tD.fid, tD.value
                           FROM cms_data AS tD
                           JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                          WHERE tD.fid IN (" . implode(", ", $fieldsIds) . ")
                            AND IF (
                                    tF.pid,
                                    (
                                        SELECT COUNT(tM.id)
                                          FROM " . Material::_tablename() . " AS tM
                                          JOIN " . Material_Type::_tablename() . " AS tMT ON tMT.id = tM.pid
                                     LEFT JOIN cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id
                                         WHERE tM.id = tD.pid
                                           AND (
                                                   tMT.global_type
                                                OR tMPA.pid IN (" . implode(", ", $pagesIds) . ")
                                            )
                                    ) > 0, -- Материальное поле
                                    (
                                        tF.pid IN (" . implode(", ", $pagesIds) . ")
                                    ) -- Страничное поле
                              )";
            $sqlResult = Field::_SQL()->get($sqlQuery);
            foreach ($sqlResult as $sqlRow) {
                $json = json_decode($sqlRow['value'], true);
                if ($json['vis']) {
                    $affectedMaterialsIds[trim($sqlRow['pid'])] = (int)$sqlRow['pid'];
                    $affectedAttachmentsIds[trim($json['attachment'])] = (int)$json['attachment'];
                    $json['fid'] = $sqlRow['fid'];
                    $json['pid'] = $sqlRow['pid'];
                    $materialsAttachmentsData[] = $json;
                }
            }
        }
        return [
            'materialsAttachmentsData' => $materialsAttachmentsData,
            'affectedAttachmentsIds' => $affectedAttachmentsIds,
            'affectedMaterialsIds' => $affectedMaterialsIds
        ];
    }


    /**
     * Получает URL вложений
     * @param array<int> $attachmentsIds ID# вложений
     * @return array<string[] ID# вложения => URL вложения>
     */
    public function getAttachmentsURLs(array $attachmentsIds = [])
    {
        if (!$attachmentsIds) {
            return [];
        }
        $attachmentsUrls = [];
        $sqlQuery = "SELECT *
                       FROM " . Attachment::_tablename()
                  . " WHERE id IN (" . implode(", ", $attachmentsIds) . ")";
        $sqlResult = Attachment::getSQLSet($sqlQuery);
        foreach ($sqlResult as $attachment) {
            $attachmentsUrls[trim($attachment->id)] = '/' . $attachment->fileURL;
            $attachment->rollback();
        }
        return $attachmentsUrls;
    }


    /**
     * Получает наименования материалов
     * @param array<int> $materialsIds ID# материалов
     * @return array<string[] ID# материала => string наименование материала>
     */
    public function getMaterialsNames(array $materialsIds = [])
    {
        if (!$materialsIds) {
            return [];
        }
        $materialsNames = [];
        $sqlQuery = "SELECT id, name
                       FROM " . Material::_tablename()
                  . " WHERE id IN (" . implode(", ", $materialsIds) . ")";
        $sqlResult = Attachment::_SQL()->get($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            $materialsNames[trim($sqlRow['id'])] = $sqlRow['name'];
        }
        return $materialsNames;
    }


    /**
     * Получает "сырые" данные по изображениям материалов
     * (без учета счетчиков картинок)
     * @param array<[
     *            'attachment' => int|string ID# вложения,
     *            'fid' => int|string ID# поля,
     *            'pid' => int|string ID# материала,
     *            'name' => string заголовок изображения
     *        ]> $materialsAttachmentsData Данные по вложениям материалов
     * @param array<
     *            string[] ID# вложения => URL вложения
     *        > $attachmentsUrls Данные по вложениям
     * @param array<
     *            string[] ID# материала => string наименование материала
     *        > $materialsNames Наименования материалов
     * @return [
     *             'images' => array<string[] ID изображения (MD5 от URL) => [
     *                 'url' => string абсолютный URL картинки,
     *                 'title' =>? string alt картинки
     *             ]>,
     *             'materialsImages' => array<string[] ID# материала => array<
     *                 string[] ID# поля => array<
     *                     string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                                    (MD5 от URL)
     *                 >
     *             >>
     *         ]
     */
    public function getMaterialsImagesRawData(
        array $materialsAttachmentsData = [],
        array $attachmentsUrls = [],
        array $materialsNames = []
    ) {
        $images = [];
        $materialsImages = [];
        foreach ($materialsAttachmentsData as $materialAttachmentData) {
            $materialId = $materialAttachmentData['pid'];
            $fieldId = $materialAttachmentData['fid'];
            $fileURL = $attachmentsUrls[$materialAttachmentData['attachment']];
            $fileURL = $this->formatImageURL($fileURL);
            $materialName = $materialsNames[$materialId];
            if ($fileURL && $materialName) {
                $title = $materialAttachmentData['name'];
                if (!$title) {
                    $title = $materialName;
                }
                $imgId = md5($fileURL);
                $imageData = ['url' => $fileURL];
                if ($title) {
                    $imageData['name'] = $title;
                }
                $images[$imgId] = $imageData;
                $materialsImages[trim($materialId)][trim($fieldId)][trim($imgId)] = $imgId;
            }
        }
        return ['images' => $images, 'materialsImages' => $materialsImages];
    }


    /**
     * Применяет счетчики изображений материалов по полям
     * @param [
     *             'images' => array<string[] ID изображения (MD5 от URL) => [
     *                 'url' => string абсолютный URL картинки,
     *                 'title' =>? string alt картинки
     *             ]>,
     *             'materialsImages' => array<string[] ID# материала => array<
     *                 string[] ID# поля => array<
     *                     string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                                    (MD5 от URL)
     *                 >
     *             >>
     *         ] $data Данные по привязке картинок к материалам
     * @param array<string[] ID# поля => string наименование поля> $fieldsNames Наименования полей
     * @return [
     *             'images' => array<string[] ID изображения (MD5 от URL) => [
     *                 'url' => string абсолютный URL картинки,
     *                 'title' =>? string alt картинки
     *             ]>,
     *             'materialsImages' => array<string[] ID# материала => array<
     *                 string[] ID# поля => array<
     *                     string[] ID изображения (MD5 от URL) => string ID изображения
     *                                                                    (MD5 от URL)
     *                 >
     *             >>
     *         ]
     */
    public function applyMaterialImagesCounters(
        array $data = ['images' => [], 'materialsImages' => []],
        array $fieldsNames = []
    ) {
        $affectedNumberedImages = [];
        foreach ($data['materialsImages'] as $materialId => $materialImages) {
            foreach ($materialImages as $fieldId => $materialFieldImages) {
                if (count($materialFieldImages) > 1) {
                    $fieldName = $fieldsNames[$fieldId];
                    $i = 0;
                    foreach ($materialFieldImages as $imgId) {
                        if (!$affectedNumberedImages[$imgId]) {
                            $data['images'][$imgId]['name'] .= ' — '
                                                            . $fieldName
                                                            .  ' ' . (++$i);
                            $affectedNumberedImages[$imgId] = $imgId;
                        }
                    }
                }
            }
        }
        return $data;
    }
}
