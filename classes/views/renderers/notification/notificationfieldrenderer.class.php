<?php
/**
 * Рендерер полей уведомления для сайта
 */
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\HTMLRenderer;

class NotificationFieldRenderer extends HTMLRenderer
{
    /**
     * Поле для отображения (с Owner'ом)
     * @var Form_Field
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
     * @param Form_Field $field Поле для отображения (с Owner'ом)
     * @param SOME|null $owner Переопределенный владелец поля
     */
    public function __construct(Form_Field $field, SOME $owner = null)
    {
        $this->field = $field;
        $this->owner = $owner;
    }


    /**
     * Получение конкретного рендерера для поля
     * @param Form_Field $field Поле для отображения
     * @param SOME|null $owner Переопределенный владелец поля
     */
    public static function spawn(Form_Field $field, SOME $owner = null)
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
    public function filterValue($value)
    {
        if (is_scalar($value)) {
            return (bool)trim($value);
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
     */
    public function getValueHTML($value, $admin = false, $sms = false)
    {
        $richValue = $this->field->doRich($value);
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
     */
    public function getValuesHTMLArray($admin = false, $sms = false)
    {
        if ($this->owner) {
            if ($ownerField = $this->owner->fields[$this->field->urn]) {
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
        }, $values);
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
     */
    public function render($additionalData = [])
    {
        $values = $this->getValuesHTMLArray(
            (bool)$additionalData['admin'],
            (bool)$additionalData['sms']
        );
        $values = array_filter($values);
        if (!$values) {
            return '';
        }
        if ($additionalData['sms']) {
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
