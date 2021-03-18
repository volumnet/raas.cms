<?php
/**
 * Рендерер полей e-mail уведомления для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера полей e-mail уведомления для сайта
 */
class EmailNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        if ($sms) {
            return parent::getValueHTML($value, $admin, $sms);
        } else {
            return '<a href="mailto:' . htmlspecialchars($value) . '">' .
                      htmlspecialchars($value) .
                   '</a>';
        }
    }
}
