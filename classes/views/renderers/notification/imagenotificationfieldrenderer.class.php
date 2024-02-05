<?php
/**
 * Рендерер полей изображений уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Controller_Frontend as ControllerFrontend;

/**
 * Класс рендерера полей изображений уведомления для сайта
 */
class ImageNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        if ($sms) {
            return $value->filename;
        } else {
            $cf = ControllerFrontend::i();
            $host = $cf->scheme . '://' . $cf->host;
            $url = $host . '/' . $value->fileURL;
            $tnURL = $host . '/' . $value->tnURL;
            return '<a href="' . htmlspecialchars($url) . '">' .
                     '<img src="' . htmlspecialchars($tnURL) . '" alt="' . htmlspecialchars($value->filename) . '" />' .
                   '</a>';
        }
    }
}
