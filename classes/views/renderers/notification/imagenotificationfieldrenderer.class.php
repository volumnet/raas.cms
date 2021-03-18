<?php
/**
 * Рендерер полей изображений уведомления для сайта
 */
namespace RAAS\CMS;

use RAAS\Controller_Frontend as ControllerFrontend;

/**
 * Класс рендерера полей изображений уведомления для сайта
 */
class ImageNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
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
