<?php
/**
 * Рендерер полей уведомления для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\SOME;
use RAAS\HTMLRenderer;

class NotificationFieldRenderer extends HTMLRenderer
{
    /**
     * Поле для отображения (с Owner'ом)
     * @var Field
     */
    public $field;

    /**
     * Владелец поля (используется для переопределения владельца по URN поля,
     * например в случае пользователя, когда ID# поля пользователя не совпадает
     * с ID# поля формы)
     * @var SOME|null
     */
    public $owner = null;

    /**
     * Конструктор класса
     * @param Field $field Поле для отображения (с Owner'ом)
     * @param ?SOME $owner Переопределенный владелец поля
     */
    public function __construct(Field $field, ?SOME $owner = null)
    {
        $this->field = $field;
        $this->owner = $owner;
    }


    /**
     * Получение конкретного рендерера для поля
     * @param Field $field Поле для отображения
     * @param ?SOME $owner Переопределенный владелец поля
     * @return self
     */
    public static function spawn(Field $field, ?SOME $owner = null): self
    {
        switch ($field->datatype) {
            case 'date':
                $classname = DateNotificationFieldRenderer::class;
                break;
            case 'datetime-local':
                $classname = DateTimeNotificationFieldRenderer::class;
                break;
            case 'color':
                $classname = ColorNotificationFieldRenderer::class;
                break;
            case 'email':
                $classname = EmailNotificationFieldRenderer::class;
                break;
            case 'tel':
                $classname = TelNotificationFieldRenderer::class;
                break;
            case 'url':
                $classname = URLNotificationFieldRenderer::class;
                break;
            case 'file':
                $classname = FileNotificationFieldRenderer::class;
                break;
            case 'image':
                $classname = ImageNotificationFieldRenderer::class;
                break;
            case 'htmlarea':
                $classname = HtmlAreaNotificationFieldRenderer::class;
                break;
            case 'material':
                $classname = MaterialNotificationFieldRenderer::class;
                break;
            case 'checkbox':
                $classname = CheckboxNotificationFieldRenderer::class;
                break;
            default:
                $classname = static::class;
                break;
        }
        return new $classname($field, $owner);
    }


    /**
     * Фильтрует значение
     * @param mixed $value Значение для фильтрации
     * @return bool
     */
    public function filterValue($value): bool
    {
        if (is_scalar($value)) {
            return (bool)trim((string)$value);
        } elseif ($value instanceof SOME) {
            return (bool)$value->id;
        }
        return (bool)$value;
    }


    /**
     * Получает HTML для значения
     * @param mixed $value Значение
     * @param bool $admin Рендеринг для администратора
     * @param bool $sms Рендеринг для SMS
     * @return string
     */
    public function getValueHTML($value, bool $admin = false, bool $sms = false): string
    {
        $richValue = (string)$this->field->doRich((string)$value);
        if ($sms) {
            $result = $richValue;
        } else {
            $result = nl2br(htmlspecialchars($richValue));
        }
        return $result;
    }


    /**
     * Получает массив HTML-значений
     * @param mixed $value Значение
     * @param bool $admin Рендеринг для администратора
     * @param bool $sms Рендеринг для SMS
     * @return array
     */
    public function getValuesHTMLArray(bool $admin = false, bool $sms = false): array
    {
        $values = [];
        if ($this->owner) {
            if ($ownerField = ($this->owner->fields[$this->field->urn] ?? null)) {
                $values = $ownerField->getValues(true);
            } elseif (($value = $this->owner->{$this->field->urn}) !== null) {
                $values = [$value];
            } elseif (isset($_POST[$this->field->urn])) {
                $values = (array)$_POST[$this->field->urn];
            }
        } else {
            $values = $this->field->getValues(true);
        }
        $valuesHTML = array_map(function ($x) use ($admin, $sms) {
            return $this->getValueHTML($x, $admin, $sms);
        }, (array)$values);
        $valuesHTML = array_filter($valuesHTML, function ($x) {
            return $this->filterValue($x);
        });
        return $valuesHTML;
    }


    /**
     * Рендер поля
     * @param array $additionalData <pre><code>[
     *     'admin' => bool Рендер для администратора
     *     'sms' => bool Рендер для SMS
     * ]</code></pre>
     * @return string
     */
    public function render(array $additionalData = []): string
    {
        $values = $this->getValuesHTMLArray(
            (bool)($additionalData['admin'] ?? false),
            (bool)($additionalData['sms'] ?? false)
        );
        $values = array_filter($values);
        if (!$values) {
            return '';
        }
        if ($additionalData['sms'] ?? null) {
            $result = $this->field->name . ': ' . implode(', ', $values) . "\n";
        } else {
            $result = '<div>'
                    .    htmlspecialchars($this->field->name) . ': '
                    .    implode(', ', $values)
                    . '</div>';
        }
        return $result;
    }
}
