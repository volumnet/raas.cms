<?php
/**
 * Файл теста рендерера файлового поля уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use RAAS\Attachment;
use RAAS\User as RAASUser;

/**
 * Класс теста рендерера файлового поля уведомления
 */
#[CoversClass(FileNotificationFieldRenderer::class)]
class FileNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = FileNotificationFieldRenderer::class;

    const DATATYPE = 'file';

    public static function getValueHTMLDataProvider()
    {
        static::installTables();
        $att = new Attachment([
            'classname' => RAASUser::class,
            'filename' => 'dummy.txt',
            'realname' => 'dummy.txt',
        ]);
        return [
            [$att, false, false, '<a href="http://localhost/files/common/dummy.txt">dummy.txt</a>'],
            [$att, false, true, 'dummy.txt'],
        ];
    }
}
