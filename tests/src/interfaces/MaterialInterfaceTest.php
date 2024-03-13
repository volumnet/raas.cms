<?php
/**
 * Файл теста стандартного интерфейса материалов
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use SOME\Pages;

/**
 * Класс теста стандартного интерфейса материалов
 * @covers \RAAS\CMS\MaterialInterface
 */
class MaterialInterfaceTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_materials_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_material_filter',
        'cms_blocks_material_sort',
        'cms_data',
        'cms_fields',
        'cms_material_types',
        'cms_materials',
        'cms_pages',
    ];

    /**
     * Тест получения полного списка параметров (включая GET и дополнительные из блока)
     */
    public function testGetAllParams()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $get = ['aaa' => 'bbb', 'id' => 123];
        $block->params = 'ccc=ddd&eee=fff';
        $block->nat = false;
        $interface = new MaterialInterface();

        $result = $interface->getAllParams($block, $get);

        $this->assertEquals(['aaa' => 'bbb', 'ccc' => 'ddd', 'eee' => 'fff'], $result);
    }


    /**
     * Тест проверки старых адресов материалов
     */
    public function testCheckLegacyAddress()
    {
        $block = Block::spawn(22);
        $block->legacy = true;
        $page = new Page(7);
        $get = ['id' => 8];
        $server = ['HTTPS' => 'on', 'HTTP_HOST' => 'localhost'];
        $interface = new MaterialInterface();

        $result = $interface->checkLegacyAddress($block, $page, $get, $server, true);

        $this->assertEquals([
            'HTTP/1.1 301 Moved Permanently',
            'Location: https://localhost/news/empiricheskiy_kreditor_v_xxi_veke-8/'
        ], $result);
    }


    /**
     * Тест проверки старых адресов материалов - случай с отсутствием материала
     */
    public function testCheckLegacyAddressWithNoMaterial()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $get = ['id' => 123];
        $interface = new MaterialInterface();

        $result = $interface->checkLegacyAddress($block, $page, $get, [], true);

        $this->assertTrue($result);
    }


    /**
     * Тест проверки старых адресов материалов - случай без указанного $_GET['id']
     */
    public function testCheckLegacyAddressWithoutId()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $interface = new MaterialInterface();

        $result = $interface->checkLegacyAddress($block, $page, [], [], true);

        $this->assertFalse($result);
    }


    /**
     * Тест проверки материала по произвольному адресу с URN материала
     */
    public function testCheckLegacyArbitraryMaterialAddress()
    {
        $block = Block::spawn(22);
        $block->legacy = true;
        $page = new Page(1);
        $page->initialURL = '/empiricheskiy_kreditor_v_xxi_veke-8/';
        $item = new Material(8);
        $server = ['HTTPS' => 'on', 'HTTP_HOST' => 'localhost'];
        $interface = new MaterialInterface();

        $result = $interface->checkLegacyArbitraryMaterialAddress($block, $page, $item, $server, true);

        $this->assertEquals([
            'HTTP/1.1 301 Moved Permanently',
            'Location: https://localhost/news/empiricheskiy_kreditor_v_xxi_veke-8/'
        ], $result);
    }


    /**
     * Тест проверки материала по произвольному адресу с URN материала - случай с нормальным URL
     */
    public function testCheckLegacyArbitraryMaterialAddressWithMatchingURL()
    {
        $block = Block::spawn(22);
        $block->legacy = true;
        $page = new Page(7);
        $page->initialURL = '/news/empiricheskiy_kreditor_v_xxi_veke-8/';
        $item = new Material(8);
        $interface = new MaterialInterface();

        $result = $interface->checkLegacyArbitraryMaterialAddress($block, $page, $item, [], true);

        $this->assertFalse($result);
    }


    /**
     * Тест проверки материала по произвольному адресу с URN материала - случай с отключенным параметром $block->legacy
     */
    public function testCheckLegacyArbitraryMaterialAddressWithNoLegacyBlock()
    {
        $block = Block::spawn(22);
        $page = new Page(1);
        $page->initialURL = '/empiricheskiy_kreditor_v_xxi_veke-8/';
        $item = new Material(8);
        $interface = new MaterialInterface();

        $result = $interface->checkLegacyArbitraryMaterialAddress($block, $page, $item, [], true);

        $this->assertTrue($result);
    }


    /**
     * Тест установки тегов страницы
     */
    public function testSetPageMetatags()
    {
        $page = new Page(7);
        $page->breadcrumbs_name = 'Новости для хлебных крошек';
        $item = new Material(8);
        $interface = new MaterialInterface();

        $interface->setPageMetatags($page, $item);

        $this->assertEquals('Эмпирический кредитор в XXI веке', $page->name);
        $this->assertEquals('Новости', $page->oldName);
        $this->assertEquals('Новости для хлебных крошек', $page->oldBreadcrumbs_name);
    }


    /**
     * Тест получения SQL-инструкций по правам доступа
     */
    public function testGetListAccessSQL()
    {
        $sqlFrom = $sqlFromBind = $sqlWhere = [];
        $interface = new MaterialInterface();

        $interface->getListAccessSQL($sqlFrom, $sqlFromBind, $sqlWhere);
        $sqlFrom = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlFrom);
        $sqlWhere = array_map('trim', $sqlWhere);

        $this->assertEquals(
            ['tA' => "LEFT JOIN cms_access_materials_cache AS tA ON tA.material_id = tM.id AND tA.uid = ?"],
            $sqlFrom
        );
        $this->assertEquals([0], $sqlFromBind);
        $this->assertEquals(["(tA.allow OR (tA.allow IS NULL))"], $sqlWhere);
    }


    /**
     * Тест получения SQL-инструкций по материалам
     */
    public function testGetMaterialsSQL()
    {
        $block = Block::spawn(34);
        $page = new Page(15);
        $sqlFrom = $sqlWhere = $sqlWhereBind = [];
        $interface = new MaterialInterface();

        $interface->getMaterialsSQL($block, $page, $sqlFrom, $sqlWhere, $sqlWhereBind);
        $sqlFrom = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlFrom);
        $sqlWhere = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlWhere);
        $sqlWhereBind = array_map(function ($x) {
            return (int)$x;
        }, $sqlWhereBind);

        $this->assertEquals([
            'tMPA' => "JOIN cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id",
        ], $sqlFrom);
        $this->assertEquals([
            "tM.vis",
            "(NOT tM.show_from OR tM.show_from <= NOW())",
            "(NOT tM.show_to OR tM.show_to >= NOW())",
            "tM.pid IN (?, ?)",
            "tMPA.pid = ?",
        ], $sqlWhere);
        $this->assertEquals([4, 5, 15], $sqlWhereBind);
    }


    /**
     * Тест получения SQL-представления поля для запроса
     */
    public function testGetField()
    {
        $sqlFrom = $sqlBind = [];
        $interface = new MaterialInterface();

        $result = $interface->getField(25, 'tArticle', $sqlFrom, $sqlBind);
        $sqlFrom = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlFrom);
        $sqlBind = array_map(function ($x) {
            return (int)$x;
        }, $sqlBind);

        $this->assertEquals('tArticle.value', trim($result));
        $this->assertEquals([
            'tArticle' => "LEFT JOIN cms_data AS `tArticle` ON `tArticle`.pid = tM.id AND `tArticle`.fid = ?"
        ], $sqlFrom);
        $this->assertEquals([25], $sqlBind);
    }


    /**
     * Тест получения SQL-представления поля для запроса - случай нативного поля
     */
    public function testGetFieldWithNative()
    {
        $sqlFrom = $sqlBind = [];
        $interface = new MaterialInterface();

        $result = $interface->getField('name', 'somefield', $sqlFrom, $sqlBind);

        $this->assertEquals('tM.name', $result);
        $this->assertEquals([], $sqlFrom);
        $this->assertEquals([], $sqlBind);
    }


    /**
     * Тест получения SQL-представления поля для запроса - случай числового кастомного поля
     */
    public function testGetFieldWithNumeric()
    {
        $sqlFrom = $sqlBind = [];
        $interface = new MaterialInterface();

        $result = $interface->getField(26, 'tPrice', $sqlFrom, $sqlBind);
        $sqlFrom = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlFrom);
        $sqlBind = array_map(function ($x) {
            return (int)$x;
        }, $sqlBind);

        $this->assertEquals('CAST(tPrice.value AS SIGNED)', trim($result));
        $this->assertEquals([
            'tPrice' => "LEFT JOIN cms_data AS `tPrice` ON `tPrice`.pid = tM.id AND `tPrice`.fid = ?"
        ], $sqlFrom);
        $this->assertEquals([26], $sqlBind);
    }


    /**
     * Тест получения SQL-представления поля для запроса - случайный порядок
     */
    public function testGetFieldWithRandom()
    {
        $sqlFrom = $sqlBind = [];
        $interface = new MaterialInterface();

        $result = $interface->getField('random', 'rnd', $sqlFrom, $sqlBind);

        $this->assertEquals('RAND()', trim($result));
        $this->assertEquals([], $sqlFrom);
        $this->assertEquals([], $sqlBind);
    }


    /**
     * Тест получения SQL-инструкций по фильтрации для одной записи фильтрации
     */
    public function testGetFilteringItemSQL()
    {
        $interface = new MaterialInterface();

        $result = $interface->getFilteringItemSQL('tM.name', '=', 'aaa');

        $this->assertEquals(["(tM.name = ?)", 'aaa'], $result);
    }


    /**
     * Тест получения SQL-инструкций по фильтрации для одной записи фильтрации - случай с LIKE
     */
    public function testGetFilteringItemSQLWithLike()
    {
        $interface = new MaterialInterface();

        $result = $interface->getFilteringItemSQL('tM.name', 'LIKE', 'aaa');

        $this->assertEquals(["(tM.name LIKE ?)", '%aaa%'], $result);
    }


    /**
     * Тест получения SQL-инструкций по фильтрации для одной записи фильтрации - случай с CONTAINED
     */
    public function testGetFilteringItemSQLWithContained()
    {
        $interface = new MaterialInterface();

        $result = $interface->getFilteringItemSQL('tM.name', 'CONTAINED', 'aaa');

        $this->assertEquals(["(? LIKE CONCAT('%', tM.name, '%'))", 'aaa'], $result);
    }


    /**
     * Тест получения SQL-инструкций по фильтрации для одной записи фильтрации - случай с FULLTEXT
     */
    public function testGetFilteringItemSQLWithFulltext()
    {
        $interface = new MaterialInterface();

        $result = $interface->getFilteringItemSQL('tM.name', 'FULLTEXT', 'aaa');

        $this->assertEquals(["(MATCH (tM.name) AGAINST(? IN NATURAL LANGUAGE MODE))", 'aaa'], $result);
    }


    /**
     * Тест получения SQL-инструкций по фильтрации
     */
    public function testGetFilteringSQL()
    {
        $sqlFrom = $sqlFromBind = $sqlWhere = $sqlWhereBind = [];
        $filter = [
            ['var' => 'name', 'relation' => 'LIKE', 'field' => 'name'],
            ['var' => 'article', 'relation' => 'LIKE', 'field' => 25],
            ['var' => 'price_from', 'relation' => '>=', 'field' => 26]
        ];
        $get = [
            'name' => 'Товар',
            'article' => 'AAA',
            'price_from' => '1000'
        ];
        $interface = new MaterialInterface();

        $interface->getFilteringSQL($sqlFrom, $sqlFromBind, $sqlWhere, $sqlWhereBind, $filter, $get);
        $sqlFrom = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlFrom);
        $sqlFromBind = array_map(function ($x) {
            return (int)$x;
        }, $sqlFromBind);
        $sqlWhere = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlWhere);

        $this->assertEquals([
            't25' => "LEFT JOIN cms_data AS `t25` ON `t25`.pid = tM.id AND `t25`.fid = ?",
            't26' => "LEFT JOIN cms_data AS `t26` ON `t26`.pid = tM.id AND `t26`.fid = ?",
        ], $sqlFrom);
        $this->assertEquals([25, 26], $sqlFromBind);
        $this->assertEquals([
            'name' => "((tM.name LIKE ?))",
            'article' => "((t25.value LIKE ?))",
            'price_from' => "((CAST(t26.value AS SIGNED) >= ?))",
        ], $sqlWhere);
        $this->assertEquals(['%Товар%', '%AAA%', '1000'], $sqlWhereBind);
    }


    /**
     * Тест получения порядка для SQL-сортировки - случай с опциональным asc и пустой переменной
     */
    public function testGetOrderWithAscOptionalNoneInVariable()
    {
        $interface = new MaterialInterface();

        $result = $interface->getOrder('order', 'asc', []);

        $this->assertEquals('asc', $result);
    }


    /**
     * Тест получения порядка для SQL-сортировки - случай с опциональным asc и desc в переменной
     */
    public function testGetOrderWithAscOptionalDescInVariable()
    {
        $interface = new MaterialInterface();

        $result = $interface->getOrder('order', 'asc', ['order' => 'desc']);

        $this->assertEquals('desc', $result);
    }


    /**
     * Тест получения порядка для SQL-сортировки - случай с опциональным desc и пустой переменной
     */
    public function testGetOrderWithDescOptionalNoneInVariable()
    {
        $interface = new MaterialInterface();

        $result = $interface->getOrder('order', 'desc', []);

        $this->assertEquals('desc', $result);
    }


    /**
     * Тест получения порядка для SQL-сортировки - случай с опциональным desc и asc в переменной
     */
    public function testGetOrderWithDescOptionalAscInVariable()
    {
        $interface = new MaterialInterface();

        $result = $interface->getOrder('order', 'desc', ['order' => 'asc']);

        $this->assertEquals('asc', $result);
    }


    /**
     * Тест получения порядка для SQL-сортировки - случай с принудительным asc!
     */
    public function testGetOrderWithAscImportant()
    {
        $interface = new MaterialInterface();

        $result = $interface->getOrder('order', 'asc!', ['order' => 'desc']);

        $this->assertEquals('asc', $result);
    }


    /**
     * Тест получения порядка для SQL-сортировки - случай с принудительным desc!
     */
    public function testGetOrderWithDescImportant()
    {
        $interface = new MaterialInterface();

        $result = $interface->getOrder('order', 'desc!', ['order' => 'asc']);

        $this->assertEquals('desc', $result);
    }


    /**
     * Тест получения порядка для SQL-сортировки - случай с не установленным значением
     */
    public function testGetOrderWithNone()
    {
        $interface = new MaterialInterface();

        $result = $interface->getOrder('order', '', ['order' => 'asc']);

        $this->assertEquals('', $result);
    }


    /**
     * Тест поиска параметра сортировки, соответствующего значению переменной сортировки
     */
    public function testGetMatchingSortParam()
    {
        $interface = new MaterialInterface();
        $sortVal = 'name';
        $sortParams = [
            ['var' => 'aaa', 'relation' => 'asc!', 'field' => 111],
            ['var' => 'name', 'relation' => 'desc', 'field' => 'name'],
            ['var' => 'bbb', 'relation' => 'desc!', 'field' => 222],
        ];

        $result = $interface->getMatchingSortParam($sortVal, $sortParams, 'var');

        $this->assertEquals([
            'var' => 'name',
            'relation' => 'desc',
            'field' => 'name'
        ], $result);
    }


    /**
     * Тест поиска параметра сортировки, соответствующего значению переменной сортировки
     * случай, когда соответствующий параметр не найден
     */
    public function testGetMatchingSortParamWithNotFound()
    {
        $interface = new MaterialInterface();
        $sortVal = 'post_date';
        $sortParams = [
            ['var' => 'aaa', 'relation' => 'asc!', 'field' => 111],
            ['var' => 'name', 'relation' => 'desc', 'field' => 'name'],
            ['var' => 'bbb', 'relation' => 'desc!', 'field' => 222],
        ];

        $result = $interface->getMatchingSortParam($sortVal, $sortParams, 'var');

        $this->assertNull($result);
    }


    /**
     * Тест поиска параметра сортировки, соответствующего значению переменной сортировки
     * случай с переменной field
     */
    public function testGetMatchingSortParamWithFieldVar()
    {
        $interface = new MaterialInterface();
        $sortVal = 111;
        $sortParams = [
            ['var' => 'aaa', 'relation' => 'asc!', 'field' => 111],
            ['var' => 'name', 'relation' => 'desc', 'field' => 'name'],
            ['var' => 'bbb', 'relation' => 'desc!', 'field' => 222],
        ];

        $result = $interface->getMatchingSortParam($sortVal, $sortParams, 'field');

        $this->assertEquals([
            'var' => 'aaa',
            'relation' => 'asc!',
            'field' => 111
        ], $result);
    }


    /**
     * Тест получения значения сортировки и порядка для вывода в виджет
     */
    public function testGetOrderVar()
    {
        $block = Block::spawn(22);
        $block->sort_var_name = 'sort';
        $block->order_var_name = 'order';
        $block->sort = [
            ['var' => 'byname', 'relation' => 'desc!', 'field' => 'name'],
            ['var' => 'bydate', 'relation' => 'desc', 'field' => 16],
        ];
        $get = ['sort' => 'bydate', 'order' => 'asc'];
        $sort = $order = '';
        $interface = new MaterialInterface();

        $result = $interface->getOrderVar($block, $get, $sort, $order);

        $this->assertEquals('bydate', trim($sort));
        $this->assertEquals('asc', trim($order));
    }


    /**
     * Тест получения значения сортировки и порядка для вывода в виджет
     * случай, когда сортировка по умолчанию найдена среди настроек сортировки
     */
    public function testGetOrderVarWithMatchingDefaultSorting()
    {
        $block = Block::spawn(22);
        $block->sort_var_name = 'sort';
        $block->order_var_name = 'order';
        $block->sort = [
            ['var' => 'byname', 'relation' => 'desc!', 'field' => 'name'],
            ['var' => 'bydate', 'relation' => 'desc', 'field' => 16],
        ];
        $get = [];
        $sort = $order = '';
        $interface = new MaterialInterface();

        $result = $interface->getOrderVar($block, $get, $sort, $order);

        $this->assertEquals('bydate', trim($sort));
        $this->assertEquals('desc', trim($order));
    }


    /**
     * Тест получения значения сортировки и порядка для вывода в виджет
     * случай с сортировкой по умолчанию
     */
    public function testGetOrderVarWithDefaultSorting()
    {
        $block = Block::spawn(22);
        $block->sort_var_name = 'sort';
        $block->sort = [
            ['var' => 'byname', 'relation' => 'desc!', 'field' => 'name'],
        ];
        $get = ['sort' => 'bydate'];
        $sort = $order = '';
        $interface = new MaterialInterface();

        $result = $interface->getOrderVar($block, $get, $sort, $order);

        $this->assertEquals('date', trim($sort));
        $this->assertEquals('desc', trim($order));
    }


    /**
     * Тест получения значения сортировки и порядка для вывода в виджет
     * случай с сортировкой по умолчанию и нативным полем
     */
    public function testGetOrderVarWithDefaultSortingAndNativeField()
    {
        $block = Block::spawn(22);
        $block->sort_field_default = 'name';
        $block->sort_order_default = 'asc';
        $get = [];
        $sort = $order = '';
        $interface = new MaterialInterface();

        $result = $interface->getOrderVar($block, $get, $sort, $order);

        $this->assertEquals('name', trim($sort));
        $this->assertEquals('asc', trim($order));
    }


    /**
     * Тест получения SQL-инструкций по сортировке
     */
    public function testGetOrderSQL()
    {
        $block = Block::spawn(22);
        $block->sort_var_name = 'sort';
        $block->order_var_name = 'order';
        $block->sort = [
            ['var' => 'byname', 'relation' => 'desc!', 'field' => 'name'],
            ['var' => 'bydate', 'relation' => 'desc', 'field' => 16],
        ];
        $get = ['sort' => 'bydate', 'order' => 'asc'];
        $sqlFrom = $sqlFromBind = [];
        $sqlSort = $sqlOrder = '';
        $interface = new MaterialInterface();

        $result = $interface->getOrderSQL($block, $get, $sqlFrom, $sqlFromBind, $sqlSort, $sqlOrder);
        $sqlFrom = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlFrom);
        $sqlFromBind = array_map(function ($x) {
            return (int)$x;
        }, $sqlFromBind);

        $this->assertEquals([
            'tOr' => "LEFT JOIN cms_data AS `tOr` ON `tOr`.pid = tM.id AND `tOr`.fid = ?"
        ], $sqlFrom);
        $this->assertEquals([16], $sqlFromBind);
        $this->assertEquals("tOr.value", trim($sqlSort));
        $this->assertEquals("ASC", trim($sqlOrder));
    }


    /**
     * Тест получения SQL-инструкций по сортировке - случай с сортировкой по умолчанию
     */
    public function testGetOrderSQLWithDefaultSorting()
    {
        $block = Block::spawn(22);
        $block->sort_var_name = 'sort';
        $block->sort = [
            ['var' => 'byname', 'relation' => 'desc!', 'field' => 'name'],
        ];
        $get = ['sort' => 'bydate'];
        $sqlFrom = $sqlFromBind = [];
        $sqlSort = $sqlOrder = '';
        $interface = new MaterialInterface();

        $result = $interface->getOrderSQL($block, $get, $sqlFrom, $sqlFromBind, $sqlSort, $sqlOrder);
        $sqlFrom = array_map(function ($x) {
            return trim(preg_replace('/\\s+/umis', ' ', $x));
        }, $sqlFrom);
        $sqlFromBind = array_map(function ($x) {
            return (int)$x;
        }, $sqlFromBind);

        $this->assertEquals([
            'tOr' => "LEFT JOIN cms_data AS `tOr` ON `tOr`.pid = tM.id AND `tOr`.fid = ?"
        ], $sqlFrom);
        $this->assertEquals([16], $sqlFromBind);
        $this->assertEquals("tOr.value", trim($sqlSort));
        $this->assertEquals("DESC", trim($sqlOrder));
    }


    /**
     * Тест получения частей SQL-выражения
     */
    public function testGetSQLParts()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $interface = new MaterialInterface();

        $result = $interface->getSQLParts($block, $page, []);
        foreach (['from', 'where'] as $key) {
            $result[$key] = array_map(function ($x) {
                return trim(preg_replace('/\\s+/umis', ' ', $x));
            }, $result[$key]);
        }
        foreach (['sort', 'order'] as $key) {
            $result[$key] = trim($result[$key]);
        }
        $result['bind'] = array_map(function ($x) {
            return (int)$x;
        }, $result['bind']);

        $this->assertEquals([
            'tA' => "LEFT JOIN cms_access_materials_cache AS tA ON tA.material_id = tM.id AND tA.uid = ?",
            'tOr' => "LEFT JOIN cms_data AS `tOr` ON `tOr`.pid = tM.id AND `tOr`.fid = ?",
        ], $result['from']);
        $this->assertEquals([
             "(tA.allow OR (tA.allow IS NULL))",
             "tM.vis",
             "(NOT tM.show_from OR tM.show_from <= NOW())",
             "(NOT tM.show_to OR tM.show_to >= NOW())",
             "tM.pid IN (?)",
        ], $result['where']);
        $this->assertEquals("tOr.value", $result['sort']);
        $this->assertEquals("DESC", $result['order']);
        $this->assertEquals([0, 16, 3], $result['bind']);
    }


    /**
     * Тест получения запроса на получение списка материалов
     */
    public function testGetSQLQuery()
    {
        $sqlFrom = [
            'tA' => "LEFT JOIN cms_access_materials_cache AS tA ON tA.material_id = tM.id AND tA.uid = ?",
            'tOr' => "LEFT JOIN cms_data AS `tOr` ON `tOr`.pid = tM.id AND `tOr`.fid = ?",
        ];
        $sqlWhere = [
             "(tA.allow OR (tA.allow IS NULL))",
             "tM.vis",
             "(NOT tM.show_from OR tM.show_from <= NOW())",
             "(NOT tM.show_to OR tM.show_to >= NOW())",
             "tM.pid IN (?)",
        ];
        $sqlSort = "tOr.value";
        $sqlOrder = "DESC";
        $interface = new MaterialInterface();

        $result = $interface->getSQLQuery($sqlFrom, $sqlWhere, $sqlSort, $sqlOrder, false);
        $result = trim(preg_replace('/\\s+/umis', ' ', $result));

        $this->assertEquals(
            "SELECT SQL_CALC_FOUND_ROWS tM.* " .
              "FROM cms_materials AS tM " .
              "LEFT JOIN cms_access_materials_cache AS tA ON tA.material_id = tM.id AND tA.uid = ? " .
              "LEFT JOIN cms_data AS `tOr` ON `tOr`.pid = tM.id AND `tOr`.fid = ? " .
             "WHERE (tA.allow OR (tA.allow IS NULL)) " .
               "AND tM.vis " .
               "AND (NOT tM.show_from OR tM.show_from <= NOW()) " .
               "AND (NOT tM.show_to OR tM.show_to >= NOW()) " .
               "AND tM.pid IN (?) " .
             "GROUP BY tM.id " .
             "ORDER BY NOT tM.priority, tM.priority ASC, tOr.value DESC",
            $result
        );
    }


    /**
     * Тест получения запроса на получение списка материалов - случай возврата ID# материалов
     */
    public function testGetSQLQueryWithIdsOnly()
    {
        $sqlFrom = [];
        $sqlWhere = [];
        $sqlSort = $sqlOrder = "";
        $interface = new MaterialInterface();

        $result = $interface->getSQLQuery($sqlFrom, $sqlWhere, $sqlSort, $sqlOrder, true);
        $result = trim(preg_replace('/\\s+/umis', ' ', $result));

        $this->assertEquals(
            "SELECT tM.id " .
              "FROM cms_materials AS tM " .
             "GROUP BY tM.id " .
             "ORDER BY NOT tM.priority, tM.priority ASC",
            $result
        );
    }


    /**
     * Тест получения запроса на получение списка материалов - случай со случайной сортировкой
     */
    public function testGetSQLQueryWithRandom()
    {
        $sqlFrom = [];
        $sqlWhere = [];
        $sqlSort = "RAND()";
        $sqlOrder = "DESC";
        $interface = new MaterialInterface();

        $result = $interface->getSQLQuery($sqlFrom, $sqlWhere, $sqlSort, $sqlOrder, true);
        $result = trim(preg_replace('/\\s+/umis', ' ', $result));

        $this->assertEquals(
            "SELECT tM.id " .
              "FROM cms_materials AS tM " .
             "GROUP BY tM.id " .
             "ORDER BY RAND()",
            $result
        );
    }


    /**
     * Тест получения списка ID# всех материалов
     */
    public function testGetIdsList()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $interface = new MaterialInterface();

        $sqlQuery = "DELETE FROM cms_access_materials_cache WHERE 1";
        Material::_SQL()->query($sqlQuery);
        $sqlQuery = "DELETE FROM cms_access WHERE material_id";
        Material::_SQL()->query($sqlQuery);
        $result = $interface->getIdsList($block, $page, []);

        $this->assertEquals([7, 8, 9], $result);
    }


    /**
     * Тест поиска предыдущего и следующего материалов по списку для заданного
     */
    public function testGetPrevNext()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $item = new Material(8);
        $interface = new MaterialInterface();

        $result = $interface->getPrevNext($block, $page, $item, []);

        $this->assertEquals(7, $result['prev']->id);
        $this->assertEquals(9, $result['next']->id);
    }


    /**
     * Тест обработки одного материала
     */
    public function testProcessMaterial()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $page->initialURL = '/news/empiricheskiy_kreditor_v_xxi_veke-8/';
        $item = new Material(8);
        $interface = new MaterialInterface();

        $result = $interface->processMaterial($block, $page, $item, [], []);

        $this->assertEquals('Эмпирический кредитор в XXI веке', $page->name);
        $this->assertTrue($item->proceed);
        $this->assertEquals($item, $result['Item']);
        $this->assertEquals(7, $result['prev']->id);
        $this->assertEquals(9, $result['next']->id);
    }


    /**
     * Тест обработки одного материала - случай с неправильным legacy-адресом
     */
    public function testProcessMaterialWithInvalidLegacyAddress()
    {
        $block = Block::spawn(22);
        $page = new Page(1);
        $page->initialURL = '/empiricheskiy_kreditor_v_xxi_veke-8/';
        $item = new Material(8);
        $interface = new MaterialInterface();

        $result = $interface->processMaterial($block, $page, $item, [], []);

        $this->assertEmpty($result);
    }


    /**
     * Тест получения списка материалов
     */
    public function testGetList()
    {
        $block = Block::spawn(22);
        $page = new Page(1);
        $interface = new MaterialInterface();

        $result = $interface->getList($block, $page, [], null);

        $this->assertCount(3, $result);
        $this->assertEquals(7, $result[0]->id);
        $this->assertEquals(8, $result[1]->id);
        $this->assertEquals(9, $result[2]->id);
    }


    /**
     * Тест обработки списка материалов
     */
    public function testProcessList()
    {
        $block = Block::spawn(22);
        $block->rows_per_page = 2;
        $page = new Page(1);
        $interface = new MaterialInterface();

        $result = $interface->processList($block, $page, ['page' => 1]);

        $this->assertCount(2, $result['Set']);
        $this->assertEquals(7, $result['Set'][0]->id);
        $this->assertEquals(8, $result['Set'][1]->id);
        $this->assertEquals(1, $result['Pages']->page);
        $this->assertEquals(2, $result['Pages']->rows_per_page);
        $this->assertEquals(3, $result['Pages']->count);
        $this->assertEquals('date', $result['sort']);
        $this->assertEquals('desc', $result['order']);
        $this->assertEquals(3, $result['MType']->id);
    }


    /**
     * Тест обработки интерфейса - случай со списком
     */
    public function testProcessWithList()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $interface = new MaterialInterface($block, $page);

        $result = $interface->process();

        $this->assertCount(3, $result['Set']);
        $this->assertEquals(7, $result['Set'][0]->id);
        $this->assertEquals(8, $result['Set'][1]->id);
        $this->assertEquals(9, $result['Set'][2]->id);
        $this->assertEquals(1, $result['Pages']->page);
        $this->assertEquals(20, $result['Pages']->rows_per_page);
        $this->assertEquals(3, $result['Pages']->count);
        $this->assertEquals('date', $result['sort']);
        $this->assertEquals('desc', $result['order']);
        $this->assertEquals(3, $result['MType']->id);
    }


    /**
     * Тест обработки интерфейса - случай с одним материалом
     */
    public function testProcessWithMaterial()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $page->initialURL = '/news/empiricheskiy_kreditor_v_xxi_veke-8/';
        $page->Material = new Material(8);
        $interface = new MaterialInterface($block, $page);

        $result = $interface->process();

        $this->assertEquals('Эмпирический кредитор в XXI веке', $page->name);
        $this->assertTrue($page->Material->proceed);
        $this->assertEquals(8, $result['Item']->id);
        $this->assertEquals(7, $result['prev']->id);
        $this->assertEquals(9, $result['next']->id);
    }


    /**
     * Тест обработки интерфейса - случай необработанным legacy-адресом
     */
    public function testProcessWithUnprocessedLegacyAddress()
    {
        $block = Block::spawn(22);
        $page = new Page(7);
        $interface = new MaterialInterface($block, $page, ['id' => 8]);

        $result = $interface->process();

        $this->assertNull($result);
        $this->assertEquals('Новости', $page->name);
        $this->assertEmpty($page->Material);
    }
}
