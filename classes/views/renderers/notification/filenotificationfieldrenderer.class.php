<?php
/**
 * Рендерер файловых полей уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Controller_Frontend as ControllerFrontend;

/**
 * Класс рендерера файловых полей уведомления для сайта
 */
class FileNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
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
