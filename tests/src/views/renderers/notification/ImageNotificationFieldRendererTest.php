<?php
/**
 * Файл теста рендерера поля изображения уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use RAAS\Attachment;
use RAAS\User as RAASUser;

/**
 * Класс теста рендерера поля изображения уведомления
 */
#[CoversClass(ImageNotificationFieldRenderer::class)]
class ImageNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = ImageNotificationFieldRenderer::class;

    const DATATYPE = 'image';

    public static function getValueHTMLDataProvider()
    {
        static::installTables();
        $att = new Attachment([
            'classname' => RAASUser::class,
            'filename' => 'dummy.jpg',
            'realname' => 'dummy.jpg',
            'image' => 1,
        ]);
        return [
            [
                $att,
                false,
                false,
                '<a href="http://localhost/files/common/dummy.jpg"><img src="http://localhost/files/common/dummy_tn.jpg" alt="dummy.jpg" /></a>'
            ],
            [
                $att,
                false,
                true,
                'dummy.jpg'
            ],
        ];
    }
}
