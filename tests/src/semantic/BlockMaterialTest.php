<?php
/**
 * Тест класса Block_Material
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса Block_Material
 * @covers RAAS\CMS\Block_Material
 */
class BlockMaterialTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_material_filter',
        'cms_blocks_material_sort',
        'cms_blocks_pages_assoc',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_pages',
    ];

    /**
     * Тест метода commit и конструктора
     */
    public function testCommitAndConstruct()
    {
        $block = new Block_Material([
            'material_type' => 1, // Преимущества
            'cats' => [3], // Услуги
            'pages_var_name' => 'page',
            'rows_per_page' => 20,
            'sort_var_name' => 'sort',
            'order_var_name' => 'order',
            'sort_field_default' => 'post_date',
            'sort_order_default' => 'desc!',
            'filter' => [
                ['var' => 'name', 'relation' => '=', 'field' => 'name'],
            ],
            'sort' => [
                ['var' => '13', 'field' => '13', 'relation' => 'asc'], // 13 - значок
            ],
        ]);
        $this->assertEquals(
            [['var' => 'name', 'relation' => '=', 'field' => 'name']],
            $block->filter
        );
        $this->assertEquals(
            [['var' => '13', 'field' => '13', 'relation' => 'asc']],
            $block->sort
        );

        $materialType = new Material_Type(1); // Преимущества
        $affectedPagesIds = array_map(function ($x) {
            return $x->id;
        }, $materialType->affectedPages);
        $this->assertNotContains(3, $affectedPagesIds); // пока их нет на странице 3

        $block->commit();
        $blockId = $block->id;

        $block = new Block_Material($blockId);
        $this->assertEquals('Наши преимущества', $block->name);
        $this->assertEquals(
            [['var' => 'name', 'relation' => '=', 'field' => 'name']],
            $block->filter
        );
        $this->assertEquals(
            [['var' => '13', 'field' => '13', 'relation' => 'asc']],
            $block->sort
        );

        $sqlQuery = "SELECT * FROM cms_blocks WHERE id = " . $blockId;
        $sqlRow = Block::_SQL()->getline($sqlQuery);
        $block = new Block_Material($sqlRow);
        $this->assertEquals('Наши преимущества', $block->name);
        $this->assertEquals(
            [['var' => 'name', 'relation' => '=', 'field' => 'name']],
            $block->filter
        );
        $this->assertEquals(
            [['var' => '13', 'field' => '13', 'relation' => 'asc']],
            $block->sort
        );

        $materialType = new Material_Type(1); // Преимущества
        $affectedPagesIds = array_map(function ($x) {
            return $x->id;
        }, $materialType->affectedPages);
        $this->assertContains(3, $affectedPagesIds); // страница 3 появилась в списке задействованных у материала заданного типа

        // Проверим пересохранение с другим типом материалов
        $block->material_type = 2; // Баннеры
        $block->commit();

        $materialType = new Material_Type(1); // Преимущества
        $affectedPagesIds = array_map(function ($x) {
            return $x->id;
        }, $materialType->affectedPages);
        $this->assertNotContains(3, $affectedPagesIds); // преимущества больше не присутствуют на странице 3

        $materialType = new Material_Type(2); // Баннеры
        $affectedPagesIds = array_map(function ($x) {
            return $x->id;
        }, $materialType->affectedPages);
        $this->assertContains(3, $affectedPagesIds); // зато баннеры появились

        Block_Material::delete($block);
    }


    /**
     * Тест метода getAddData()
     */
    public function testGetAddData()
    {
        $block = new Block_Material([
            'material_type' => 1, // Преимущества
            'cats' => [3], // Услуги
            'pages_var_name' => 'page',
            'rows_per_page' => 20,
            'sort_var_name' => 'sort',
            'order_var_name' => 'order',
            'sort_field_default' => 'post_date',
            'sort_order_default' => 'desc!',
            'filter' => [
                ['var' => 'name', 'relation' => '=', 'field' => 'name'],
            ],
            'sort' => [
                ['var' => '13', 'field' => '13', 'relation' => 'asc'], // 13 - значок
            ],
        ]);

        $result = $block->getAddData();

        $this->assertEquals(0, $result['id']);
        $this->assertEquals(1, $result['material_type']);
        $this->assertEquals('page', $result['pages_var_name']);
        $this->assertEquals(20, $result['rows_per_page']);
        $this->assertEquals('sort', $result['sort_var_name']);
        $this->assertEquals('order', $result['order_var_name']);
        $this->assertEquals('post_date', $result['sort_field_default']);
        $this->assertEquals('desc!', $result['sort_order_default']);
        $this->assertEquals(0, $result['legacy']);

        $block->commit();
        $blockId = $block->id;

        $result = $block->getAddData();

        $this->assertEquals($blockId, $result['id']);
        $this->assertEquals(1, $result['material_type']);
        $this->assertEquals('page', $result['pages_var_name']);
        $this->assertEquals(20, $result['rows_per_page']);
        $this->assertEquals('sort', $result['sort_var_name']);
        $this->assertEquals('order', $result['order_var_name']);
        $this->assertEquals('post_date', $result['sort_field_default']);
        $this->assertEquals('desc!', $result['sort_order_default']);
        $this->assertEquals(0, $result['legacy']);

        Block_Material::delete($block);
    }
}
