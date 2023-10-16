<?php
/**
 * Стандартный интерфейс водяных знаков
 * @param string[]|null $files Пути файлов для обработки
 */
namespace RAAS\CMS;

use SOME\Graphics;
use RAAS\Application;
use RAAS\Attachment;

$watermark = null;
foreach (['design/watermark.png', 'watermark.png'] as $tmpWatermark) {
    if (is_file($tmpWatermark = Application::i()->baseDir . '/files/cms/common/image/' . $tmpWatermark)) {
        $watermark = $tmpWatermark;
        break;
    }
}
if ($watermark) {
    $interface = new WatermarkInterface($watermark, $_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES);
    $interface->process((array)$files);
}
