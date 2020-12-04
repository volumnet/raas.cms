<?php
/**
 * Рендерер полей формы для сайта
 */
namespace RAAS\CMS;

use RAAS\HTMLRenderer;

abstract class FormFieldRenderer extends HTMLRenderer
{
    /**
     * Поле для отображения
     * @var Form_Field
     */
    public $field;

    /**
     * Данные поля
     * @var string|string[] <pre>|array<
     *     string[] Индекс множественного поля => string
     * ></pre>
     */
    public $data;

    /**
     * Текст ошибки поля
     * @var string|null
     */
    public $error;

    /**
     * Блок для связки атрибутов
     * @var Block|null
     */
    public $block;

    /**
     * Конструктор класса
     * @param Form_Field $field Поле для отображения
     * @param Block|null $block Блок для связки атрибутов
     * @param string|string[] $data <pre>|array<
     *     string[] Индекс множественного поля => string
     * ></pre> Данные поля
     * @param string|null $error Ошибка поля
     */
    protected function __construct(
        Form_Field $field,
        Block $block = null,
        array $data = [],
        $error = null
    ) {
        $this->field = $field;
        $this->block = $block;
        $this->data = $data;
        $this->error = $error;
    }


    /**
     * Получение конкретного рендерера для поля
     * @param Form_Field $field Поле для отображения
     * @param Block|null $block Блок для связки атрибутов
     * @param string|string[] $data <pre>|array<
     *     string[] Индекс множественного поля => string
     * ></pre> Данные поля
     * @param string|null $error Ошибка поля
     */
    public static function spawn(
        Form_Field $field,
        Block $block = null,
        array $data = [],
        $error = null
    ) {
        switch ($field->datatype) {
            case 'textarea':
            case 'htmlarea':
                $classname = TextareaFormFieldRenderer::class;
                break;
            case 'select':
                $classname = SelectFormFieldRenderer::class;
                break;
            case 'checkbox':
                $classname = CheckboxFormFieldRenderer::class;
                break;
            default:
                $classname = TextFormFieldRenderer::class;
                break;
        }
        if ($classname) {
            return new $classname($field, $block, $data, $error);
        }
    }


    /**
     * Возвращает код поля, как если бы оно было единичным
     * @param string|null $index Индекс множественного поля
     * @return string
     */
    public function renderSingle($index = null)
    {
        $attrs = $this->getAttributes($index);
        return $this->getElement('input', $attrs);
    }


    /**
     * Получает атрибуты поля
     * @param string|null $index Индекс множественного поля
     * @return array <pre>array<
     *     string[] URN атрибута => string|string[] Значение атрибута
     * ></pre>
     */
    public function getAttributes($index = null)
    {
        $attrs = [
            'class' => 'form-control',
            'name' => $this->field->urn . ($this->field->multiple ? '[]' : ''),
        ];
        if (!$this->field->multiple) {
            $attrs['id'] = $this->field->getHTMLId($this->block);
        }
        if ($this->field->required) {
            $attrs['required'] = 'required';
        }
        foreach (['maxlength'] as $key) {
            if ($val = $this->field->$key) {
                $attrs[$key] = (int)$val;
            }
        }
        foreach (['placeholder', 'pattern'] as $key) {
            if ($val = $this->field->$key) {
                $attrs[$key] = (string)$val;
            }
        }
        return $attrs;
    }


    /**
     * Получает значение для вставки в HTML-элемент по индексу
     * @param string|null $index Индекс множественного поля
     * @return string|null;
     */
    public function getValue($index = null)
    {
        if ($this->data) {
            if ($index !== null) {
                $data = (array)$this->data;
                if (isset($data[$index])) {
                    return (string)$data[$index];
                }
            } else {
                return (string)$this->data;
            }
        }
        return null;
    }


    public function render()
    {
        if ($this->field->multiple) {
            // $result = '<div data-vue-role="repo" class="repo">';

            // $result .= '</div>';
            // return $result;
        } else {
            return $this->renderSingle();
        }
    }
}
