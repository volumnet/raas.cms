<?php
/**
 * Файл теста рендерера цветового поля уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера цветового поля уведомления
 * @covers RAAS\CMS\ColorNotificationFieldRenderer
 */
class ColorNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = ColorNotificationFieldRenderer::class;

    const DATATYPE = 'color';

    public function getValueHTMLDataProvider()
    {
        return [
            ['#ff0000', false, false, '<span style="display: inline-block; height: 16px; width: 16px; background-color: #ff0000"></span>'],
            ['#ff0000', false, true, '#ff0000'],
        ];
    }
}
