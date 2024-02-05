<?php
/**
 * Рендерер цветовых полей уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера цветовых полей уведомления для сайта
 */
class ColorNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        if ($sms) {
            return parent::getValueHTML($value, $admin, $sms);
        } else {
            return '<span style="display: inline-block; height: 16px; width: 16px; background-color: ' . htmlspecialchars((string)$value) . '"></span>';
        }
    }
}
