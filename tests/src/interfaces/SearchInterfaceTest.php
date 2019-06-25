<?php
/**
 * Файл теста стандартного интерфейса поиска
 */
namespace RAAS\CMS;

use SOME\Pages;

/**
 * Класс теста стандартного интерфейса поиска
 */
class SearchInterfaceTest extends BaseDBTest
{
    // Независимые методы

    /**
     * Тест получения результатов поиска по рейтингам
     */
    public function testGetSearchResults()
    {
        $interface = new SearchInterface();
        $ratios = [
            'm7' => 23, // Закрыт доступ
            'p2' => 31,
            'm9' => 45,
            'p1' => 56,
            'm8' => 75,
            'p3' => 76,
        ];
        $pages = new Pages(1, 3);

        $result = $interface->getSearchResults($ratios, $pages, 100);

        $this->assertInstanceOf(Page::class, $result[0]);
        $this->assertEquals(2, $result[0]->id);
        $this->assertInstanceOf(Material::class, $result[1]);
        $this->assertEquals(9, $result[1]->id);
        $this->assertEquals(3, $result[1]->pid);
        $this->assertInstanceOf(Page::class, $result[2]);
        $this->assertEquals(1, $result[2]->id);
        $this->assertEquals(5, $pages->count);
        $this->assertEquals(2, $pages->pages);
    }


    /**
     * Тест получения ограничения по страницам поиска
     * (случай с явно указанными страницами)
     */
    public function testGetSearchPagesIdsWithPages()
    {
        $interface = new SearchInterface();
        $block = new Block_Search();
        $block->search_pages_ids = [3,7,8];
        $block->languages = ['ru'];
        $block->commit();
        $page = new Page(15);

        $result = $interface->getSearchPagesIds($block, $page);
        sort($result);
        $result = array_map('intval', $result);

        $this->assertEquals([3, 7, 8], $result);

        Block_Search::delete($block);
    }


    /**
     * Тест получения ограничения по страницам поиска
     * (случай с неподходящим языком)
     */
    public function testGetSearchPagesIdsWithInvalidLang()
    {
        $interface = new SearchInterface();
        $block = new Block_Search();
        $block->languages = ['en'];
        $block->commit();
        $page = new Page(15);

        $result = $interface->getSearchPagesIds($block, $page);

        $this->assertEmpty($result);

        Block_Search::delete($block);
    }


    /**
     * Тест получения ограничения по страницам поиска
     * (случай с не указанными страницами)
     */
    public function testGetSearchPagesIdsWithDomain()
    {
        $interface = new SearchInterface();
        $block = new Block_Search();
        $block->languages = ['ru'];
        $block->commit();
        $page = new Page(15);

        $result = $interface->getSearchPagesIds($block, $page);
        sort($result);
        $result = array_map('intval', $result);

        $this->assertContains(1, $result);
        $this->assertContains(2, $result);
        $this->assertContains(21, $result);
        $this->assertNotContains(6, $result);
        $this->assertNotContains(9, $result);
        $this->assertNotContains(26, $result);

        Block_Search::delete($block);
    }


    /**
     * Тест получения набора значимых слов в поисковой строке
     */
    public function testGetSearchArray()
    {
        $interface = new SearchInterface();

        $result = $interface->getSearchArray('абвгд, еж зик, лм нопр', 3);

        $this->assertEquals(['абвгд', 'зик', 'нопр'], $result);
    }


    /**
     * Тест получения рейтинга строки по поиску
     * (случай с вхождением поисковой строки)
     */
    public function testGetRatioWithSentence()
    {
        $interface = new SearchInterface();

        $result = $interface->getRatio(
            '12345 абвгд еж зик лм нопр 65784',
            'АБВГД ЕЖ ЗИК ЛМ НОПР',
            ['АБВГД', 'ЗИК', 'НОПР'],
            10,
            1
        );

        $this->assertEquals(10, $result);
    }


    /**
     * Тест получения рейтинга строки по поиску
     * (случай с вхождением слов)
     */
    public function testGetRatioWithWords()
    {
        $interface = new SearchInterface();

        $result = $interface->getRatio(
            '12345 абвгд еж зик лм нопр 65784',
            'ЗИК НОПР',
            ['ЗИК', 'НОПР'],
            10,
            1
        );

        $this->assertEquals(2, $result);
    }


    /**
     * Тест получения ID# всех возможных полей
     * (случай с полями страниц)
     */
    public function testGetFieldsIdsWithPages()
    {
        $interface = new SearchInterface();

        $result = $interface->getFieldsIds(false);

        $this->assertContains(1, $result);
        $this->assertNotContains(2, $result);
        $this->assertNotContains(25, $result);
    }


    /**
     * Тест получения ID# всех возможных полей
     * (случай с полями материалов)
     */
    public function testGetFieldsIdsWithMaterials()
    {
        $interface = new SearchInterface();

        $result = $interface->getFieldsIds(true);

        $this->assertContains(25, $result);
        $this->assertContains(18, $result);
        $this->assertNotContains(17, $result);
        $this->assertNotContains(29, $result);
        $this->assertNotContains(1, $result);
    }


    /**
     * Тест получения рейтингов страниц списков материалов и страниц материалов
     */
    public function testGetMaterialPageRatings()
    {
        $interface = new SearchInterface();

        $result = $interface->getMaterialPageRatings([
            '7' => 11,
            '8' => 12,
            '9' => 13,
        ], [7], 2);

        $this->assertEquals(['7' => 6], $result['pages']);
        $this->assertEquals([
            '7' => 11,
            '8' => 12,
            '9' => 13,
        ], $result['materials']);
    }


    // Зависимые методы

    /**
     * Тест поиска страниц по наименованию
     */
    public function testSearchPagesByName()
    {
        $interface = new SearchInterface();

        $result = $interface->searchPagesByName(
            'каталог продукции',
            ['каталог', 'продукции'],
            [2, 3, 7, 8, 15, 21],
            100,
            10
        );

        $this->assertEquals(['15' => 100], $result);
    }


    /**
     * Тест поиска страниц по данным
     */
    public function testSearchPagesByData()
    {
        $interface = new SearchInterface();
        $page = new Page(1);
        $page->fields['_description_']->addValue('ааа ббб ввв');

        $result = $interface->searchPagesByData(
            'АА ВВ',
            ['АА', 'ВВ'],
            [1, 2, 3, 7, 8, 15, 21],
            5,
            1,
            100
        );

        $this->assertEquals(['1' => 2], $result);

        $page->fields['_description_']->deleteValues();
    }


    /**
     * Тест поиска материалов по названию и описанию
     * (случай поиска по фразе в названии)
     */
    public function testSearchMaterialsByNameAndDescriptionWithNameSentence()
    {
        $interface = new SearchInterface();

        $result = $interface->searchMaterialsByNameAndDescription(
            'Эмпирический кредитор в XXI веке',
            ['Эмпирический', 'кредитор', 'XXI', 'веке'],
            [3],
            100,
            10,
            5,
            1,
            100
        );

        $this->assertEquals(['7' => 100, '8' => 100], $result);
    }


    /**
     * Тест поиска материалов по названию и описанию
     * (случай поиска по словам в названии)
     */
    public function testSearchMaterialsByNameAndDescriptionWithNameWords()
    {
        $interface = new SearchInterface();

        $result = $interface->searchMaterialsByNameAndDescription(
            'Эмпирический веке',
            ['Эмпирический', 'веке'],
            [3],
            100,
            10,
            5,
            1,
            100
        );

        $this->assertEquals(['7' => 20, '8' => 20], $result);
    }


    /**
     * Тест поиска материалов по названию и описанию
     * (случай поиска по фразе в описании)
     */
    public function testSearchMaterialsByNameAndDescriptionWithDescriptionSentence()
    {
        $interface = new SearchInterface();

        $result = $interface->searchMaterialsByNameAndDescription(
            'Психологический параллелизм',
            ['Психологический', 'параллелизм'],
            [3],
            100,
            10,
            5,
            1,
            100
        );

        $this->assertEquals(['9' => 5], $result);
    }


    /**
     * Тест поиска материалов по названию и описанию
     * (случай поиска по словам в описании)
     */
    public function testSearchMaterialsByNameAndDescriptionWithDescriptionWords()
    {
        $interface = new SearchInterface();

        $result = $interface->searchMaterialsByNameAndDescription(
            'Психологический Афинах',
            ['Психологический', 'Афинах'],
            [3],
            100,
            10,
            5,
            1,
            100
        );

        $this->assertEquals(['9' => 2], $result);
    }


    /**
     * Тест поиска материалов по данным
     * (случай поиска по фразе)
     */
    public function testSearchMaterialsByDataWithSentence()
    {
        $interface = new SearchInterface();

        $result = $interface->searchMaterialsByData(
            'Преамбула, согласно',
            ['Преамбула', 'согласно'],
            [3],
            5,
            1,
            100
        );

        $this->assertEquals(['7' => 5, '8' => 5], $result);
    }


    /**
     * Тест поиска материалов по данным
     * (случай поиска по словам)
     */
    public function testSearchMaterialsByDataWithWords()
    {
        $interface = new SearchInterface();

        $result = $interface->searchMaterialsByData(
            'согласно, преамбула',
            ['согласно', 'преамбула'],
            [3],
            5,
            1,
            100
        );

        $this->assertEquals(['7' => 2, '8' => 2], $result);
    }


    /**
     * Тест поиска страниц по HTML-блокам
     * (случай поиска по фразе)
     */
    public function testSearchPagesByHTMLBlocksWithSentence()
    {
        $interface = new SearchInterface();

        $result = $interface->searchPagesByHTMLBlocks(
            'Добро пожаловать',
            ['Добро', 'пожаловать'],
            [1, 2, 3],
            5,
            1,
            100
        );

        $this->assertEquals(['1' => 5], $result);
    }


    /**
     * Тест поиска страниц по HTML-блокам
     * (случай поиска по словам)
     */
    public function testSearchPagesByHTMLBlocksWithWords()
    {
        $interface = new SearchInterface();

        $result = $interface->searchPagesByHTMLBlocks(
            'пожаловать добро',
            ['пожаловать', 'добро'],
            [1, 2, 3],
            5,
            1,
            100
        );

        $this->assertEquals(['1' => 2], $result);
    }


    /**
     * Тест получения рейтингов по страницам и страницам материалов
     * (случай с подстановками)
     */
    public function testGetPagesMaterialsRatiosWithMock()
    {
        $interface = $this->getMockBuilder(SearchInterface::class)
            ->setMethods([
                'getSearchPagesIds',
                'searchPagesByName',
                'searchPagesByData',
                'searchMaterialsByNameAndDescription',
                'searchMaterialsByData',
                'getMaterialPageRatings',
                'searchPagesByHTMLBlocks'
            ])->getMock();
        $block = new Block_Search();
        $page = new Page(15);

        $interface->expects($this->once())
            ->method('getSearchPagesIds')
            ->with($block, $page)
            ->willReturn([1, 2, 3, 4]);

        $interface->expects($this->once())
            ->method('searchPagesByName')
            ->with(
                'Поисковая строка',
                ['Поисковая', 'строка'],
                [1, 2, 3, 4],
                100,
                10
            )->willReturn(['1' => 11]);

        $interface->expects($this->once())
            ->method('searchPagesByData')
            ->with(
                'Поисковая строка',
                ['Поисковая', 'строка'],
                [1, 2, 3, 4],
                5,
                1,
                100
            )->willReturn(['2' => 22]);

        $interface->expects($this->once())
            ->method('searchMaterialsByNameAndDescription')
            ->with(
                'Поисковая строка',
                ['Поисковая', 'строка'],
                [],
                0,
                0,
                0,
                1,
                100
            )->willReturn(['101' => 111]);

        $interface->expects($this->once())
            ->method('searchMaterialsByData')
            ->with(
                'Поисковая строка',
                ['Поисковая', 'строка'],
                [],
                5,
                1,
                100
            )->willReturn(['102' => 222]);

        $interface->expects($this->once())
            ->method('getMaterialPageRatings')
            ->with(
                ['101' => 111, '102' => 222],
                [1, 2, 3, 4],
                1
            )->willReturn([
                'materials' => ['101' => 111, '102' => 222],
                'pages' => ['3' => 33]
            ]);


        $interface->expects($this->once())
            ->method('searchPagesByHTMLBlocks')
            ->with(
                'Поисковая строка',
                ['Поисковая', 'строка'],
                [1, 2, 3, 4],
                5,
                1,
                100
            )->willReturn(['4' => 44]);

        $result = $interface->getPagesMaterialsRatios(
            $block,
            $page,
            'Поисковая строка',
            ['Поисковая', 'строка'],
            $pageNameSentenceRatio = 100,
            $pageNameWordRatio = 10,
            $pageDataSentenceRatio = 5,
            $pageDataWordRatio = 1,
            $pageHTMLSentenceRatio = 5,
            $pageHTMLWordRatio = 1,
            $materialNameSentenceRatio = 0,
            $materialNameWordRatio = 0,
            $materialDescriptionSentenceRatio = 0,
            $materialDescriptionWordRatio = 1,
            $materialDataSentenceRatio = 5,
            $materialDataWordRatio = 1,
            $pageMaterialsRatio = 1,
            $searchLimit = 100
        );

        $this->assertEquals([
            'm102' => 222,
            'm101' => 111,
            'p4' => 44,
            'p3' => 33,
            'p2' => 22,
            'p1' => 11,
        ], $result);
    }


    /**
     * Тест получения рейтингов по страницам и страницам материалов
     * (случай с реальным поиском)
     */
    public function testGetPagesMaterialsRatiosWithRealSearch()
    {
        $interface = new SearchInterface();

        $result = $interface->getPagesMaterialsRatios(
            $block,
            $page,
            'моменты',
            ['моменты'],
            100,
            10,
            5,
            1,
            5,
            1,
            100,
            10,
            5,
            1,
            5,
            1,
            1,
            100
        );

        $this->assertEquals(100, $result['m9']);
        $this->assertEquals(1, $result['p20']);
        $this->assertEquals(1, $result['p19']);
        $this->assertEmpty($result['p14']);
    }


    /**
     * Тест отработки результата поиска
     * (случай с пустой поисковой строкой)
     */
    public function testProcessWithEmptyString()
    {
        $block = new Block_Search();
        $block->search_var_name = 'search_string';
        $interface = new SearchInterface(
            $block,
            new Page(1),
            ['search_string' => '   ']
        );

        $result = $interface->process();

        $this->assertEquals('NO_SEARCH_QUERY', $result['localError']);
        $this->assertEquals('', $result['search_string']);
    }


    /**
     * Тест отработки результата поиска
     * (случай с короткими словами)
     */
    public function testProcessWithShortWords()
    {
        $block = new Block_Search();
        $block->search_var_name = 'search_string';
        $block->min_length = 3;
        $interface = new SearchInterface(
            $block,
            new Page(1),
            ['search_string' => 'а б в г']
        );

        $result = $interface->process();

        $this->assertEquals('SEARCH_QUERY_TOO_SHORT', $result['localError']);
        $this->assertEquals('а б в г', $result['search_string']);
    }


    /**
     * Тест отработки результата поиска
     * (случай с отсутствием результатов)
     */
    public function testProcessWithNotFound()
    {
        $block = new Block_Search();
        $block->search_var_name = 'search_string';
        $block->pages_var_name = 'page';
        $block->rows_per_page = 3;
        $interface = new SearchInterface(
            $block,
            new Page(1),
            ['search_string' => 'абвгдеёжзийклмн']
        );

        $result = $interface->process();
        $this->assertEquals('NO_RESULTS_FOUND', $result['localError']);
        $this->assertEquals(0, count($result['Set']));
        $this->assertEquals(0, $result['Pages']->count);
    }


    /**
     * Тест отработки результата поиска
     * (случай с реальным поиском)
     */
    public function testProcessWithRealSearch()
    {
        $block = new Block_Search();
        $block->search_var_name = 'search_string';
        $block->pages_var_name = 'page';
        $block->rows_per_page = 3;
        $interface = new SearchInterface(
            $block,
            new Page(1),
            ['search_string' => 'моменты']
        );

        $result = $interface->process();
        $this->assertEquals(3, count($result['Set']));
        $this->assertEquals(17, $result['Pages']->count);
        $this->assertInstanceOf(Material::class, $result['Set'][0]);
        $this->assertEquals(9, $result['Set'][0]->id);
        $this->assertInstanceOf(Page::class, $result['Set'][1]);
        $this->assertEquals(20, $result['Set'][1]->id);
        $this->assertInstanceOf(Page::class, $result['Set'][2]);
        $this->assertEquals(19, $result['Set'][2]->id);
    }
}
