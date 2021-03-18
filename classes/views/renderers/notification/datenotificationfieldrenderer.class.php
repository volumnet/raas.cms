<?php
/**
 * Рендерер полей даты уведомления для сайта
 */
namespace RAAS\CMS;

use RAAS\View_Web as RAASViewWeb;

/**
 * Класс рендерера полей даты уведомления для сайта
 */
class DateNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        $t = strtotime($value);
        if ($t > 0) {
            return date(RAASViewWeb::i()->_('DATEFORMAT'), $t);
        }
        return '';
    }
}
