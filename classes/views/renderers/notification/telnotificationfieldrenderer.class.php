<?php
/**
 * Рендерер полей телефона уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\Text;

/**
 * Класс рендерера полей телефона уведомления для сайта
 */
class TelNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        if ($sms) {
            return parent::getValueHTML($value, $admin, $sms);
        } else {
            return '<a href="tel:%2B' . Text::beautifyPhone((string)$value, 11) . '">' .
                      htmlspecialchars((string)$value) .
                   '</a>';
        }
    }
}
