<?php
/**
 * Стандартный интерфейс водяных знаков
 * @param Field $field Обрабатываемое поле
 * @param bool $postProcess Пост-обработка
 * @param Attachment[]|null $attachmentsToProcess Добавленные вложения (только в случае пост-обработки)
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
    $interface = new WatermarkInterface(
        $field,
        $watermark,
        (bool)$postProcess,
        $_GET,
        $_POST,
        $_COOKIE,
        $_SESSION,
        $_SERVER,
        $_FILES
    );
    $interface->process(($postProcess ?? false) ? (array)$attachmentsToProcess : []);
}
