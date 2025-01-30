<?php
/**
 * Тест для класса MaterialDatatypeStrategy
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\DatatypeStrategy;

/**
 * Тест для класса MaterialDatatypeStrategy
 */
#[CoversClass(MaterialDatatypeStrategy::class)]
class MaterialDatatypeStrategyTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_materials_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_pages_assoc',
        'cms_data',
        'cms_fields',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_pages',
    ];

    /**
     * Провайдер данных для метода testExport
     * @return array <pre><code>array<[
     *     mixed Входное значение,
     *     mixed Ожидаемое значение
     *     string? Ожидается исключение класса
     * ]></code></pre>
     */
    public static function exportDataProvider(): array
    {
        static::installTables();
        return [
            [
                new Material(['id' => 10, 'vis' => true, 'name' => 'aaa', 'description' => 'AAA description']),
                10,
            ],
            [
                ' 11 ',
                11,
            ],
            ['abc', null],
        ];
    }


    /**
     * Проверка метода export()
     * @param mixed $inputValue Входное значение
     * @param mixed $expected Ожидаемое значение
     * @param string $expectedException Ожидается исключение класса
     */
    #[DataProvider('exportDataProvider')]
    public function testExport($inputValue, $expected, $expectedException = null)
    {
        $strategy = DatatypeStrategy::spawn('cms.material');

        if ($expectedException) {
            $this->expectException($expectedException);
        }

        $result = $strategy->export($inputValue);

        if (!$expectedException) {
            $this->assertEquals($expected, $result);
        }
    }


    /**
     * Проверка метода import()
     */
    public function testImport()
    {
        $material = new Material();
        $material->commit();
        $materialId = (int)$material->id;

        $strategy = DatatypeStrategy::spawn('cms.material');

        $result = $strategy->import($materialId);

        $this->assertInstanceOf(Material::class, $result);
        $this->assertEquals($materialId, $result->id);

        Material::delete($material);
    }

    /**
     * Проверка метода import()
     */
    public function testBatchImport()
    {
        $material1 = new Material();
        $material1->commit();
        $material1Id = (int)$material1->id;
        $material2 = new Material();
        $material2->commit();
        $material2Id = (int)$material2->id;
        $material3 = new Material();
        $material3->commit();
        $material3Id = (int)$material3->id;
        $values = [
            $material1Id,
            $material2Id,
            $material3Id,
        ];

        $strategy = DatatypeStrategy::spawn('cms.material');

        $result = $strategy->batchImport($values);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Material::class, $result[0]);
        $this->assertEquals($material1Id, $result[0]->id);
        $this->assertInstanceOf(Material::class, $result[1]);
        $this->assertEquals($material2Id, $result[1]->id);
        $this->assertInstanceOf(Material::class, $result[2]);
        $this->assertEquals($material3Id, $result[2]->id);

        Material::delete($material1);
        Material::delete($material2);
        Material::delete($material3);
    }


    /**
     * Проверка метода import() с пустым значением
     */
    public function testImportWithEmpty()
    {
        $strategy = DatatypeStrategy::spawn('cms.material');

        $result = $strategy->import('');

        $this->assertNull($result);
    }
}
