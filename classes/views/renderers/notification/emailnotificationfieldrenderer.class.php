<?php
/**
 * Рендерер полей e-mail уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера полей e-mail уведомления для сайта
 */
class EmailNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        if ($sms) {
            return parent::getValueHTML($value, $admin, $sms);
        } else {
            return '<a href="mailto:' . htmlspecialchars((string)$value) . '">' .
                      htmlspecialchars((string)$value) .
                   '</a>';
        }
    }
}
