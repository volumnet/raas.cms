<?php
/**
 * Файл класса интерфейса поиска
 */
namespace RAAS\CMS;

use SOME\Pages;
use SOME\SOME;

/**
 * Класс интерфейса поиска
 */
class SearchInterface extends AbstractInterface
{
    /**
     * Вес совпадения фразы в названии страницы
     * @var int
     */
    public $pageNameSentenceRatio = 100;

    /**
     * Вес совпадения слова в названии страницы
     * @var int
     */
    public $pageNameWordRatio = 10;

    /**
     * Вес совпадения фразы в данных страницы
     * @var int
     */
    public $pageDataSentenceRatio = 5;

    /**
     * Вес совпадения слова в данных страницы
     * @var int
     */
    public $pageDataWordRatio = 1;

    /**
     * Вес совпадения фразы в HTML-блоках страницы
     * @var int
     */
    public $pageHTMLSentenceRatio = 5;

    /**
     * Вес совпадения слова в HTML-блоках страницы
     * @var int
     */
    public $pageHTMLWordRatio = 1;

    /**
     * Вес совпадения фразы в названии материала
     * @var int
     */
    public $materialNameSentenceRatio = 100;

    /**
     * Вес совпадения слова в названии материала
     * @var int
     */
    public $materialNameWordRatio = 10;

    /**
     * Вес совпадения фразы в описании материала
     * @var int
     */
    public $materialDescriptionSentenceRatio = 5;

    /**
     * Вес совпадения слова в описании материала
     * @var int
     */
    public $materialDescriptionWordRatio = 1;

    /**
     * Вес совпадения фразы в данных материала
     * @var int
     */
    public $materialDataSentenceRatio = 5;

    /**
     * Вес совпадения слова в данных материала
     * @var int
     */
    public $materialDataWordRatio = 1;

    /**
     * Вес попадания подходящих материалов в странице
     * @var int
     */
    public $pageMaterialsRatio = 1;

    /**
     * Конструктор класса
     * @param Block_Search|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        Block_Search $block = null,
        Page $page = null,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        parent::__construct(
            $block,
            $page,
            $get,
            $post,
            $cookie,
            $session,
            $server,
            $files
        );
    }


    /**
     * Отрабатывает результат поиска
     * @param int $searchLimit Лимит поиска
     * @return [
     *             'search_string' => string Поисковая строка,
     *             'localError' => array<string> Токены переводов ошибок поиска,
     *             'Set' => array<Page|Material> Результаты поиска,
     *             'Pages' => Pages Постраничная разбивка
     *         ]
     */
    public function process($searchLimit = 100)
    {
        $out = [];

        $searchString = trim(
            isset($this->get[$this->block->search_var_name]) ?
            $this->get[$this->block->search_var_name] :
            ''
        );
        $out['search_string'] = $searchString;

        if (!$searchString) {
            $out['localError'] = 'NO_SEARCH_QUERY';
            return $out;
        }

        $searchArray = $this->getSearchArray($searchString, $this->block->min_length);
        if (!$searchArray) {
            $out['localError'] = 'SEARCH_QUERY_TOO_SHORT';
            return $out;
        }

        $result = $this->getPagesMaterialsRatios(
            $this->block,
            $this->page,
            $searchString,
            $searchArray,
            $this->pageNameSentenceRatio,
            $this->pageNameWordRatio,
            $this->pageDataSentenceRatio,
            $this->pageDataWordRatio,
            $this->pageHTMLSentenceRatio,
            $this->pageHTMLWordRatio,
            $this->materialNameSentenceRatio,
            $this->materialNameWordRatio,
            $this->materialDescriptionSentenceRatio,
            $this->materialDescriptionWordRatio,
            $this->materialDataSentenceRatio,
            $this->materialDataWordRatio,
            $this->pageMaterialsRatio,
            $searchLimit
        );
        $pages = null;
        if (isset($this->block->pages_var_name, $this->block->rows_per_page) &&
            (int)$this->block->rows_per_page
        ) {
            $pagesVarName = $this->block->pages_var_name;
            $currentPage = isset($this->get[$pagesVarName])
                         ? (int)$this->get[$pagesVarName]
                         : 1;
            $pages = new Pages($currentPage, (int)$this->block->rows_per_page);
        }
        $set = $this->getSearchResults($result, $pages, $searchLimit);
        if (!$set) {
            $out['localError'] = 'NO_RESULTS_FOUND';
        }
        $out['Pages'] = $pages;
        $out['Set'] = $set;
        return $out;
    }


    /**
     * Получает рейтинги по страницам и страницам материалов
     * @param Block_Search $block Поисковый блок
     * @param Page $page Страница поиска
     * @param string $searchString Поисковая строка
     * @param array<string> $searchArray Набор поисковых слов
     * @param int $pageNameSentenceRatio Вес совпадения фразы
     *                                   в названии страницы
     * @param int $pageNameWordRatio Вес совпадения слова в названии страницы
     * @param int $pageDataSentenceRatio Вес совпадения фразы в данных страницы
     * @param int $pageDataWordRatio Вес совпадения слова в данных страницы
     * @param int $pageHTMLSentenceRatio Вес совпадения фразы
     *                                   в HTML-блоках страницы
     * @param int $pageHTMLWordRatio Вес совпадения слова в HTML-блоках страницы
     * @param int $materialNameSentenceRatio Вес совпадения фразы
     *                                       в названии материала
     * @param int $materialNameWordRatio Вес совпадения слова
     *                                   в названии материала
     * @param int $materialDescriptionSentenceRatio Вес совпадения фразы
     *                                              в описании материала
     * @param int $materialDescriptionWordRatio Вес совпадения слова
     *                                          в описании материала
     * @param int $materialDataSentenceRatio Вес совпадения фразы
     *                                       в данных материала
     * @param int $materialDataWordRatio Вес совпадения слова в данных материала
     * @param int $pageMaterialsRatio Вес попадания подходящих материалов
     *                                в странице
     * @param int $searchLimit Лимит поиска
     * @return array<
     *             (
     *                 ('p' страница |'m' материал) .
     *                 (int ID# страницы или материала)
     *             ) => int Рейтинг
     *         >
     */
    public function getPagesMaterialsRatios(
        Block_Search $block,
        Page $page,
        $searchString,
        array $searchArray,
        $pageNameSentenceRatio = 100,
        $pageNameWordRatio = 10,
        $pageDataSentenceRatio = 5,
        $pageDataWordRatio = 1,
        $pageHTMLSentenceRatio = 5,
        $pageHTMLWordRatio = 1,
        $materialNameSentenceRatio = 100,
        $materialNameWordRatio = 10,
        $materialDescriptionSentenceRatio = 5,
        $materialDescriptionWordRatio = 1,
        $materialDataSentenceRatio = 5,
        $materialDataWordRatio = 1,
        $pageMaterialsRatio = 1,
        $searchLimit = 100
    ) {
        $result = $materials = [];

        // Получим начальные условия для страниц и материалов
        $searchPagesIds = $this->getSearchPagesIds($block, $page);

        // 1. Ищем страницы по имени
        if ($pageNameSentenceRatio || $pageNameWordRatio) {
            $pagesNameResult = $this->searchPagesByName(
                $searchString,
                $searchArray,
                $searchPagesIds,
                $pageNameSentenceRatio,
                $pageNameWordRatio
            );
            foreach ($pagesNameResult as $pageId => $pageWeight) {
                $result['p' . $pageId] += $pageWeight;
            }
        }

        // 2. Ищем страницы по данным
        if ($pageDataSentenceRatio || $pageDataWordRatio) {
            $pagesDataResult = $this->searchPagesByData(
                $searchString,
                $searchArray,
                $searchPagesIds,
                $pageDataSentenceRatio,
                $pageDataWordRatio,
                $searchLimit
            );
            foreach ($pagesDataResult as $pageId => $pageWeight) {
                $result['p' . $pageId] += $pageWeight;
            }
        }

        // 3. Ищем все материалы по имени и описанию
        if ($materialNameSentenceRatio ||
            $materialNameWordRatio ||
            $materialDescriptionSentenceRatio ||
            $materialDescriptionWordRatio
        ) {
            $materialsNameDescriptionResult = $this->searchMaterialsByNameAndDescription(
                $searchString,
                $searchArray,
                (array)$block->material_types_ids,
                $materialNameSentenceRatio,
                $materialNameWordRatio,
                $materialDescriptionSentenceRatio,
                $materialDescriptionWordRatio,
                $searchLimit
            );
            foreach ($materialsNameDescriptionResult as $materialId => $materialWeight) {
                $materials[$materialId] = $materialWeight;
            }
        }

        // 4. Ищем все материалы по данным
        if ($materialDataSentenceRatio || $materialDataWordRatio) {
            $materialsDataResult = $this->searchMaterialsByData(
                $searchString,
                $searchArray,
                (array)$block->material_types_ids,
                $materialDataSentenceRatio,
                $materialDataWordRatio,
                $searchLimit
            );
            foreach ($materialsDataResult as $materialId => $materialWeight) {
                $materials[$materialId] = $materialWeight;
            }
        }

        // 5. Выбираем блоки по типам данных
        if ($materials) {
            $materialsPagesResult = $this->getMaterialPageRatings(
                $materials,
                $searchPagesIds,
                $pageMaterialsRatio
            );
            foreach ((array)$materialsPagesResult['pages'] as $pageId => $pageWeight) {
                $result['p' . $pageId] += $pageWeight;
            }
            foreach ((array)$materialsPagesResult['materials'] as $materialId => $materialWeight) {
                $result['m' . $materialId] += $materialWeight;
            }
        }

        // 6. Выбираем блоки по HTML-коду
        if ($pageHTMLSentenceRatio || $pageHTMLWordRatio) {
            $pagesHTMLResult = $this->searchPagesByHTMLBlocks(
                $searchString,
                $searchArray,
                $searchPagesIds,
                $pageHTMLSentenceRatio,
                $pageHTMLWordRatio,
                $searchLimit
            );
            foreach ($pagesHTMLResult as $pageId => $pageWeight) {
                $result['p' . $pageId] += $pageWeight;
            }
        }

        arsort($result);
        return $result;
    }


    /**
     * Получает результаты поиска по рейтингам
     * @param array<
     *            (
     *                ('p' страница |'m' материал) .
     *                (int ID# страницы или материала)
     *            ) => int Рейтинг
     *        > $ratios Рейтинги
     * @param Pages $pages Постраничная разбивка
     * @param int $searchLimit Лимит поиска
     * @return array<Page|Material>
     */
    public function getSearchResults(
        array $ratios,
        Pages $pages = null,
        $searchLimit = 100
    ) {
        $resultIds = array_slice(array_keys($ratios), 0, $searchLimit);
        $set = array_values(array_filter(array_map(function ($x) {
            if ($x[0] == 'm') {
                $row = new Material(substr($x, 1));
                if ($row->currentUserHasAccess() &&
                    $row->parent->currentUserHasAccess()
                ) {
                    return $row;
                }
            } else {
                $row = new Page(PageRecursiveCache::i()->cache[substr($x, 1)]);
                if ($row->currentUserHasAccess()) {
                    return $row;
                }
            }
            $row->rollback();
        }, $resultIds)));
        $set = SOME::getArraySet($set, $pages);
        return $set;
    }


    /**
     * Получает ограничение по страницам поиска
     * @param Block_Search $block Поисковый блок
     * @param Page $page Страница поиска
     * @return array<string|int ID# страницы>
     */
    public function getSearchPagesIds(Block_Search $block, Page $page)
    {
        if (!($searchPagesIds = (array)$block->search_pages_ids)) {
            $pageId = (int)$page->id;
            if ($pageParentsIds = PageRecursiveCache::i()->getParentsIds($pageId)) {
                $domainId = $pageParentsIds[0];
            } else {
                $domainId = $pageId;
            }
            $allDomainPagesIds = PageRecursiveCache::i()->getAllChildrenIds($domainId);
            $searchPagesIds = array_merge([$domainId], $allDomainPagesIds);
        }

        $pageCache = array_intersect_key(
            PageRecursiveCache::i()->cache,
            array_flip($searchPagesIds)
        );
        $pageCache = array_filter($pageCache, function ($x) {
            return $x['vis'] && !$x['response_code'];
        });
        if ($languages = array_filter((array)$block->languages)) {
            $pageCache = array_filter(
                $pageCache,
                function ($x) use ($languages) {
                    return in_array($x['lang'], $languages);
                }
            );
        }
        return array_keys($pageCache);
    }


    /**
     * Получает набор значимых слов в поисковой строке
     * @param string $searchString Поисковая строка
     * @param int $minLength Минимальная длина слова
     * @return array<string>
     */
    public function getSearchArray($searchString, $minLength = 3)
    {
        $searchArray = preg_split('/\\s|,/umi', $searchString);
        $searchArray = array_map('trim', $searchArray);
        $searchArray = array_filter($searchArray);
        if ((int)$minLength) {
            $searchArray = array_values(array_filter(
                $searchArray,
                function ($x) use ($minLength) {
                    return (mb_strlen($x) >= (int)$minLength);
                }
            ));
        }
        return $searchArray;
    }


    /**
     * Получает рейтинг строки по поиску
     * @param string $haystack Актуальные данные
     * @param string $searchString Поисковая строка
     * @param array<string> $searchArray Набор поисковых слов
     * @param int $sentenceRatio Вес вхождения поисковой строки
     * @param int $wordRatio Вес вхождения слова
     * @return int
     */
    public function getRatio(
        $haystack,
        $searchString,
        array $searchArray,
        $sentenceRatio = 100,
        $wordRatio = 10
    ) {
        $result = 0;
        $haystack = preg_replace('/\\s+/umi', ' ', $haystack);
        $searchString = preg_replace('/\\s+/umi', ' ', $searchString);
        if (mb_stristr($haystack, $searchString)) {
            $result += $sentenceRatio;
        } else {
            foreach ($searchArray as $searchWord) {
                $searchWord = preg_replace('/\\s+/umi', ' ', $searchWord);
                if (mb_stristr($haystack, $searchWord)) {
                    $result += $wordRatio;
                }
            }
        }
        return $result;
    }


    /**
     * Получает ID# всех возможных полей
     * @param bool $isMaterial Для материалов (если false, то для страниц)
     * @return array<int>
     */
    public function getFieldsIds($isMaterial = false)
    {
        $sqlQuery = "SELECT id
                       FROM " . Material_Field::_tablename()
                  . " WHERE " . ($isMaterial ? "" : "NOT") . " pid
                        AND classname = ?
                        AND datatype IN (?, ?, ?, ?, ?, ?) ";
        $fieldsIds = Material_Field::_SQL()->getcol([
            $sqlQuery,
            Material_Type::class,
            'text',
            'tel',
            'email',
            'url',
            'textarea',
            'htmlarea'
        ]);
        return $fieldsIds;
    }


    /**
     * Поиск страниц по наименованию
     * @param string $searchString Поисковая строка
     * @param array<string> $searchArray Набор поисковых слов
     * @param array<int> $searchPagesIds ID# страниц для поиска
     * @param int $sentenceRatio Вес вхождения поисковой строки
     * @param int $wordRatio Вес вхождения слова
     * @return array<string ID# страницы => int Вес страницы по наименованию>
     */
    public function searchPagesByName(
        $searchString,
        array $searchArray,
        array $searchPagesIds,
        $sentenceRatio = 100,
        $wordRatio = 10
    ) {
        $result = [];
        foreach ($searchPagesIds as $pageId) {
            $pageData = PageRecursiveCache::i()->cache[$pageId];
            $result[trim($pageId)] = $this->getRatio(
                $pageData['name'],
                $searchString,
                $searchArray,
                $sentenceRatio,
                $wordRatio
            );
        }
        return array_filter($result);
    }


    /**
     * Поиск страниц по данным
     * @param string $searchString Поисковая строка
     * @param array<string> $searchArray Набор поисковых слов
     * @param array<int> $searchPagesIds ID# страниц для поиска
     * @param int $sentenceRatio Вес вхождения поисковой строки
     * @param int $wordRatio Вес вхождения слова
     * @param int $searchLimit Лимит поиска
     * @return array<string ID# страницы => int Вес страницы по данным>
     */
    public function searchPagesByData(
        $searchString,
        array $searchArray,
        array $searchPagesIds,
        $sentenceRatio = 5,
        $wordRatio = 1,
        $searchLimit = 100
    ) {
        $result = [];
        $pagesFieldsIds = $this->getFieldsIds(false);

        if ($pagesFieldsIds && $searchArray && $searchPagesIds) {
            $sqlBind = array_map(function ($x) {
                return '%' . $x . '%';
            }, $searchArray);
            $sqlQuery = "SELECT *
                           FROM " . Page::_dbprefix() . "cms_data
                          WHERE fid IN (" . implode(", ", $pagesFieldsIds) . ")
                            AND pid IN (" . implode(", ", array_map('intval', (array)$searchPagesIds)) . ")
                            AND (" . implode(" OR ", array_fill(0, count($searchArray), " value LIKE ?")) . ")
                          LIMIT ?";
            $sqlBind[] = (int)$searchLimit;
            $sqlResult = Page::_SQL()->get([$sqlQuery, $sqlBind]);
            foreach ($sqlResult as $sqlRow) {
                $result[trim($sqlRow['pid'])] += $this->getRatio(
                    $sqlRow['value'],
                    $searchString,
                    $searchArray,
                    $sentenceRatio,
                    $wordRatio
                );
            }
        }
        return array_filter($result);
    }


    /**
     * Поиск материалов по названию и описанию
     * @param string $searchString Поисковая строка
     * @param array<string> $searchArray Набор поисковых слов
     * @param array<int> $searchMaterialTypesIds ID# типов материалов для поиска
     * @param int $sentenceRatio Вес вхождения поисковой строки
     * @param int $wordRatio Вес вхождения слова
     * @param int $searchLimit Лимит поиска
     * @return array<
     *             string ID# материала => int Вес материала по наименованию
     *                                         или описанию
     *         >
     */
    public function searchMaterialsByNameAndDescription(
        $searchString,
        array $searchArray,
        array $searchMaterialTypesIds = [],
        $nameSentenceRatio = 100,
        $nameWordRatio = 10,
        $descriptionSentenceRatio = 5,
        $descriptionWordRatio = 1,
        $searchLimit = 100
    ) {
        $result = [];

        if ($searchArray) {
            $sqlBind = $sqlWhereArr = [];
            foreach ($searchArray as $searchWord) {
                if ($nameSentenceRatio || $nameWordRatio) {
                    $sqlWhereArr[] = "name LIKE ?";
                    $sqlBind[] = '%' . $searchWord . '%';
                }
                if ($descriptionSentenceRatio || $descriptionWordRatio) {
                    $sqlWhereArr[] = "description LIKE ?";
                    $sqlBind[] = '%' . $searchWord . '%';
                }
            }

            $sqlQuery = "SELECT id, name, description
                           FROM " . Material::_tablename() . "
                          WHERE vis
                            AND (" . implode(" OR ", $sqlWhereArr) . ")";
            if ($searchMaterialTypesIds) {
                $sqlQuery .= " AND pid IN (" . implode(", ", array_map('intval', (array)$searchMaterialTypesIds)) . ")";
            }
            $sqlQuery .= " ORDER BY (name LIKE ?) DESC LIMIT ?";
            $sqlBind[] = '%' . $searchWord . '%';
            $sqlBind[] = (int)$searchLimit;
            $sqlResult = Material::_SQL()->get([$sqlQuery, $sqlBind]);

            foreach ($sqlResult as $sqlRow) {
                $result[trim($sqlRow['id'])] += $this->getRatio(
                    $sqlRow['name'],
                    $searchString,
                    $searchArray,
                    $nameSentenceRatio,
                    $nameWordRatio
                ) + $this->getRatio(
                    $sqlRow['description'],
                    $searchString,
                    $searchArray,
                    $descriptionSentenceRatio,
                    $descriptionWordRatio
                );
            }
        }
        return array_filter($result);
    }


    /**
     * Поиск материалов по данным
     * @param string $searchString Поисковая строка
     * @param array<string> $searchArray Набор поисковых слов
     * @param array<int> $searchMaterialTypesIds ID# типов материалов для поиска
     * @param int $sentenceRatio Вес вхождения поисковой строки
     * @param int $wordRatio Вес вхождения слова
     * @param int $searchLimit Лимит поиска
     * @return array<string ID# материала => int Вес материала по данным>
     */
    public function searchMaterialsByData(
        $searchString,
        array $searchArray,
        array $searchMaterialTypesIds = [],
        $sentenceRatio = 5,
        $wordRatio = 1,
        $searchLimit = 100
    ) {
        $result = [];

        $materialFieldsIds = $this->getFieldsIds(true);

        if ($materialFieldsIds && $searchArray) {
            $sqlBind = array_map(function ($x) {
                return '%' . $x . '%';
            }, $searchArray);
            $sqlQuery = "SELECT tD.*
                           FROM " . Material::_dbprefix() . "cms_data AS tD ";
            if ($searchMaterialTypesIds) {
                $sqlQuery .= " JOIN " . Material::_tablename() . " AS tM ON tM.id = tD.pid ";
            }
            $sqlQuery .= " WHERE tD.fid IN (" . implode(", ", $materialFieldsIds) . ") ";
            if ($searchMaterialTypesIds) {
                $sqlQuery .= " AND tM.pid IN (" . implode(", ", array_map('intval', (array)$searchMaterialTypesIds)) . ") ";
            }
            $sqlQuery .= "   AND (" . implode(" OR ", array_fill(0, count($searchArray), " tD.value LIKE ?")) . ")
                           LIMIT ?";
            $sqlBind[] = (int)$searchLimit;
            $sqlResult = Material::_SQL()->get([$sqlQuery, $sqlBind]);
            foreach ($sqlResult as $sqlRow) {
                $result[trim($sqlRow['pid'])] += $this->getRatio(
                    $sqlRow['value'],
                    $searchString,
                    $searchArray,
                    $sentenceRatio,
                    $wordRatio
                );
            }
        }
        return array_filter($result);
    }


    /**
     * Получает рейтинги страниц списков материалов и страниц материалов
     * @param array<
     *            string ID# материала => int Вес материала
     *        > $materialRatings Рейтинги материалов
     * @param array<int> $searchPagesIds ID# страниц для поиска
     * @param int $pageMaterialsRatio Вес попадания подходящих материалов
     *                                в странице
     * @return [
     *             'pages' => array<
     *                 string ID# страницы => int Вес страницы списка
     *                                            по материалам
     *             >,
     *             'materials' => array<
     *                 string ID# материала => int Вес страницы материала
     *             >
     *         ]
     */
    public function getMaterialPageRatings(
        $materialRatings,
        array $searchPagesIds,
        $pageMaterialsRatio = 1
    ) {
        $result = [
            'pages' => [],
            'materials' => [],
        ];
        if ($materialRatings && $searchPagesIds) {
            $sqlQuery = "SELECT id
                           FROM " . Material::_tablename()
                      . " WHERE cache_url_parent_id
                            AND id IN (" . implode(", ", array_keys($materialRatings)) . ")
                            AND cache_url_parent_id IN (" . implode(", ", array_map('intval', (array)$searchPagesIds)) . ")";
            $sqlResult = Material::_SQL()->getcol($sqlQuery);
            $result['materials'] = array_intersect_key(
                $materialRatings,
                array_flip($sqlResult)
            );

            if ($pageMaterialsRatio) {
                $sqlQuery = "SELECT tM.id AS material_id, tMTAPM.page_id
                               FROM " . Material::_tablename() . " AS tM
                               JOIN " . Material::_dbprefix() . "cms_material_types_affected_pages_for_materials_cache AS tMTAPM ON tMTAPM.material_type_id = tM.pid
                          LEFT JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id
                              WHERE tM.id IN (" . implode(", ", array_keys($materialRatings)) . ")
                                AND tMTAPM.page_id IN (" . implode(", ", array_map('intval', (array)$searchPagesIds)) . ")
                                AND ((tMPA.pid = tMTAPM.page_id) OR (tMPA.pid IS NULL))
                           GROUP BY tM.id, tMTAPM.page_id";
                $sqlResult = Material::_SQL()->get($sqlQuery);

                foreach ($sqlResult as $sqlRow) {
                    $result['pages'][$sqlRow['page_id']] += $pageMaterialsRatio;
                }
            }
        }

        return array_filter($result);
    }


    /**
     * Поиск страниц по HTML-блокам
     * @param string $searchString Поисковая строка
     * @param array<string> $searchArray Набор поисковых слов
     * @param array<int> $searchPagesIds ID# страниц для поиска
     * @param int $sentenceRatio Вес вхождения поисковой строки
     * @param int $wordRatio Вес вхождения слова
     * @param int $searchLimit Лимит поиска
     * @return array<string ID# страницы => int Вес страницы по данным>
     */
    public function searchPagesByHTMLBlocks(
        $searchString,
        array $searchArray,
        array $searchPagesIds,
        $sentenceRatio = 5,
        $wordRatio = 1,
        $searchLimit = 100
    ) {
        $result = [];

        if ($searchArray && $searchPagesIds) {
            $sqlBind = array_map(function ($x) {
                return '%' . $x . '%';
            }, $searchArray);
            $sqlQuery = "SELECT tP.id AS page_id, tBH.description
                            FROM " . Page::_tablename() . " AS tP
                            JOIN " . Block::_dbprefix() . "cms_blocks_pages_assoc
                              AS tBPA
                              ON tBPA.page_id = tP.id
                            JOIN " . Block::_tablename() . "
                              AS tB
                              ON tB.id = tBPA.block_id
                             AND tB.vis
                            JOIN " . Block::_dbprefix() . "cms_blocks_html
                              AS tBH
                              ON tBH.id = tB.id
                           WHERE tP.id IN (" . implode(", ", array_map('intval', (array)$searchPagesIds)) . ")
                             AND (" . implode(" OR ", array_fill(0, count($searchArray), " tBH.description LIKE ?")) . ")
                        GROUP BY tP.id
                           LIMIT ?";
            $sqlBind[] = (int)$searchLimit;
            // echo $sqlQuery; exit;
            $sqlResult = Page::_SQL()->get([$sqlQuery, $sqlBind]);
            foreach ($sqlResult as $sqlRow) {
                $result[trim($sqlRow['page_id'])] += $this->getRatio(
                    $sqlRow['description'],
                    $searchString,
                    $searchArray,
                    $sentenceRatio,
                    $wordRatio
                );
            }
        }
        return array_filter($result);
    }
}
