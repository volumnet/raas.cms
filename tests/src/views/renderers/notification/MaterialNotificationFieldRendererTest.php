<?php
/**
 * Файл теста рендерера материального поля уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера материального поля уведомления
 */
#[CoversClass(MaterialNotificationFieldRenderer::class)]
class MaterialNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = MaterialNotificationFieldRenderer::class;

    const DATATYPE = 'material';

    public static function getValueHTMLDataProvider()
    {
        static::installTables();
        $material = new Material([
            'id' => 123,
            'name' => 'aaa',
            'cache_url' => '/aaa/bbb/ccc/',
        ]);
        $material2 = new Material([
            'id' => 231,
            'name' => 'bbb',
        ]);
        return [
            [
                $material,
                false,
                false,
                '<a href="http://localhost/aaa/bbb/ccc/">aaa</a>',
            ],
            [
                $material2,
                false,
                false,
                'bbb',
            ],
            [
                $material,
                true,
                false,
                '<a href="http://localhost/admin/?p=cms&action=edit_material&id=123">aaa</a>',
            ],
            [
                $material,
                false,
                true,
                'aaa',
            ],
        ];
    }
}
