<?php
/**
 * Рендерер полей даты уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\View_Web as RAASViewWeb;

/**
 * Класс рендерера полей даты уведомления для сайта
 */
class DateNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        $t = strtotime((string)$value);
        if ($t > 0) {
            return date(RAASViewWeb::i()->_('DATEFORMAT'), $t);
        }
        return '';
    }
}
