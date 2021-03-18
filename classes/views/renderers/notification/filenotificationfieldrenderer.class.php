<?php
/**
 * Рендерер файловых полей уведомления для сайта
 */
namespace RAAS\CMS;

use RAAS\Controller_Frontend as ControllerFrontend;

/**
 * Класс рендерера файловых полей уведомления для сайта
 */
class FileNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        if ($sms) {
            return $value->filename;
        } else {
            $cf = ControllerFrontend::i();
            $url = $cf->scheme . '://' . $cf->host . '/' . $value->fileURL;
            return '<a href="' . htmlspecialchars($url) . '">' .
                      htmlspecialchars($value->filename) .
                   '</a>';
        }
    }
}
