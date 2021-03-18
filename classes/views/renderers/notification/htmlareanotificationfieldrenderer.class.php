<?php
/**
 * Рендерер HTML-полей уведомления для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера HTML-полей уведомления для сайта
 */
class HtmlAreaNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        if ($sms) {
            return strip_tags($value);
        } else {
            return '<div>' . $value . '</div>';
        }
    }
}
