<?php
/**
 * Рендерер флажков уведомления для сайта
 */
namespace RAAS\CMS;

use RAAS\View_Web as RAASViewWeb;

/**
 * Класс рендерера флажков уведомления для сайта
 */
class CheckboxNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        if (!$this->field->multiple) {
            return RAASViewWeb::i()->_($value ? '_YES' : '_NO');
        } elseif ($sms) {
            return $value;
        } else {
            return nl2br(htmlspecialchars($value));
        }
    }
}
