<?php
/**
 * Рендерер материальных полей уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Controller_Frontend as ControllerFrontend;

/**
 * Класс рендерера материальных полей уведомления для сайта
 */
class MaterialNotificationFieldRenderer extends NotificationFieldRenderer
{
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        if ($sms) {
            return $value->name;
        } else {
            $cf = ControllerFrontend::i();
            $host = $cf->scheme . '://' . $cf->host;
            if ($admin) {
                $url = $host . '/admin/?p=cms&action=edit_material&id='
                     . (int)$value->id;
            } elseif ($value->url) {
                $url = $host . $value->url;
            } else {
                $url = '';
            }
            $result = htmlspecialchars($value->name);
            if ($url) {
                $result = '<a href="' . $url . '">'
                        .    $result
                        . '</a>';
            }
            return $result;
        }
    }
}
