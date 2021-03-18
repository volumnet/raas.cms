<?php
/**
 * Рендерер полей даты/времени уведомления для сайта
 */
namespace RAAS\CMS;

use RAAS\View_Web as RAASViewWeb;

/**
 * Класс рендерера полей даты/времени уведомления для сайта
 */
class DateTimeNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        $t = strtotime($value);
        if ($t > 0) {
            return date(RAASViewWeb::i()->_('DATETIMEFORMAT'), $t);
        }
        return '';
    }
}
