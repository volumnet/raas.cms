<?php
/**
 * Рендерер флажков уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\View_Web as RAASViewWeb;

/**
 * Класс рендерера флажков уведомления для сайта
 */
class CheckboxNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        if (!$this->field->multiple) {
            return RAASViewWeb::i()->_($value ? '_YES' : '_NO');
        } else {
            $richValue = $this->field->doRich((string)$value);
            if ($sms) {
                return $richValue;
            } else {
                return nl2br(htmlspecialchars($richValue));
            }
        }
    }
}
