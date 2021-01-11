<?php
/**
 * Рендерер полей формы для сайта
 */
namespace RAAS\CMS;

use RAAS\HTMLRenderer;

abstract class FormFieldRenderer extends HTMLRenderer
{
    /**
     * Валидный ли атрибут multiple для данного типа поля
     */
    const HTML_VALID_MULTIPLE = false;

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
     * Блок для связки атрибутов
     * @var Block|null
     */
    public $block;

    /**
     * Конструктор класса
     * @param Form_Field $field Поле для отображения
     * @param Block|null $block Блок для связки атрибутов
     * @param string|string[] $data <pre>string|array<
     *     string[] Индекс множественного поля => string
     * ></pre> Данные поля
     */
    public function __construct(
        Form_Field $field,
        Block $block = null,
        $data = null
    ) {
        $this->field = $field;
        $this->block = $block;
        $this->data = $data;
    }


    /**
     * Получение конкретного рендерера для поля
     * @param Form_Field $field Поле для отображения
     * @param Block|null $block Блок для связки атрибутов
     * @param string|string[] $data <pre>string|array<
     *     string[] Индекс множественного поля => string
     * ></pre> Данные поля
     * @param string|null $error Ошибка поля
     */
    public static function spawn(
        Form_Field $field,
        Block $block = null,
        $data = [],
        $error = null
    ) {
        switch ($field->datatype) {
            case 'number':
            case 'range':
                $classname = NumberFormFieldRenderer::class;
                break;
            case 'password':
                $classname = PasswordFormFieldRenderer::class;
                break;
            case 'checkbox':
            case 'radio':
                $classname = CheckboxFormFieldRenderer::class;
                break;
            case 'file':
                $classname = FileFormFieldRenderer::class;
                break;
            case 'image':
                $classname = ImageFormFieldRenderer::class;
                break;
            case 'select':
                $classname = SelectFormFieldRenderer::class;
                break;
            case 'textarea':
                $classname = TextAreaFormFieldRenderer::class;
                break;
            case 'htmlarea':
                $classname = HtmlAreaFormFieldRenderer::class;
                break;
            case 'material':
                $classname = HiddenFormFieldRenderer::class;
                break;
            default:
                $classname = TextFormFieldRenderer::class;
                break;
        }
        return new $classname($field, $block, $data);
    }


    /**
     * Получает собственные атрибуты поля
     * @return array <pre>array<
     *     string[] URN атрибута => string|string[] Значение атрибута
     * ></pre>
     */
    public function getAttributes()
    {
        $attrs = [
            'name' => $this->field->urn . ($this->field->multiple ? '[]' : ''),
        ];
        if ($this->field->multiple) {
            if (static::HTML_VALID_MULTIPLE) {
                $attrs['multiple'] = 'multiple';
            } else {
                $attrs['data-multiple'] = 'multiple';
            }
        } else {
            $attrs['id'] = $this->field->getHTMLId($this->block);
        }
        if ($this->field->required) {
            $attrs['required'] = 'required';
        }
        return $attrs;
    }


    public function render($additionalData = [])
    {
        $attrs = $this->mergeAttributes(
            $this->getAttributes(),
            $additionalData
        );
        return $this->getElement('input', $attrs);
    }
}
