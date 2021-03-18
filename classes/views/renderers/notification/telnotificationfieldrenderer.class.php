<?php
/**
 * Рендерер полей телефона уведомления для сайта
 */
namespace RAAS\CMS;

use SOME\Text;

/**
 * Класс рендерера полей телефона уведомления для сайта
 */
class TelNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        if ($sms) {
            return parent::getValueHTML($value, $admin, $sms);
        } else {
            return '<a href="tel:%2B' . Text::beautifyPhone($value, 11) . '">' .
                      htmlspecialchars($value) .
                   '</a>';
        }
    }
}
