<?php
/**
 * Рендерер HTML-полей уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера HTML-полей уведомления для сайта
 */
class HtmlAreaNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        if ($sms) {
            return strip_tags((string)$value);
        } else {
            return '<div>' . $value . '</div>';
        }
    }
}
