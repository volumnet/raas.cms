<?php
/**
 * Рендерер полей даты/времени уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\View_Web as RAASViewWeb;

/**
 * Класс рендерера полей даты/времени уведомления для сайта
 */
class DateTimeNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        $t = strtotime((string)$value);
        if ($t > 0) {
            return date(RAASViewWeb::i()->_('DATETIMEFORMAT'), $t);
        }
        return '';
    }
}
