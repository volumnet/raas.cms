<?php
/**
 * Рендерер полей URL уведомления для сайта
 */
namespace RAAS\CMS;

use RAAS\Controller_Frontend as ControllerFrontend;

/**
 * Класс рендерера полей URL уведомления для сайта
 */
class URLNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        if ($sms) {
            return parent::getValueHTML($value, $admin, $sms);
        } else {
            $cf = ControllerFrontend::i();
            $url = '';
            if (!preg_match('/\\/\\//umi', trim($value))) {
                $url .= $cf->scheme . '://' . $cf->host;
            }
            $url .= $value;
            return '<a href="' . htmlspecialchars($url) . '">' .
                      htmlspecialchars($value) .
                   '</a>';
        }
    }
}
