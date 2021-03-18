<?php
/**
 * Файл теста рендерера материального поля уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера материального поля уведомления
 * @covers RAAS\CMS\MaterialNotificationFieldRenderer
 */
class MaterialNotificationFieldRendererTest extends CustomFormFieldRendererTest
{
    const CLASSNAME = MaterialNotificationFieldRenderer::class;

    const DATATYPE = 'material';

    public function getValueHTMLDataProvider()
    {
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
