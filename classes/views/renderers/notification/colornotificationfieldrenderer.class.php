<?php
/**
 * Рендерер цветовых полей уведомления для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера цветовых полей уведомления для сайта
 */
class ColorNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        if ($sms) {
            return parent::getValueHTML($value, $admin, $sms);
        } else {
            return '<span style="display: inline-block; height: 16px; width: 16px; background-color: ' . htmlspecialchars($value) . '"></span>';
        }
    }
}
