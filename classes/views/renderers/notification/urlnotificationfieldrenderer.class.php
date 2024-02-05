<?php
/**
 * Рендерер полей URL уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Controller_Frontend as ControllerFrontend;

/**
 * Класс рендерера полей URL уведомления для сайта
 */
class URLNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        if ($sms) {
            return parent::getValueHTML($value, $admin, $sms);
        } else {
            $cf = ControllerFrontend::i();
            $url = '';
            if (!preg_match('/\\/\\//umi', trim((string)$value))) {
                $url .= $cf->scheme . '://' . $cf->host;
            }
            $url .= $value;
            return '<a href="' . htmlspecialchars($url) . '">' .
                      htmlspecialchars((string)$value) .
                   '</a>';
        }
    }
}
