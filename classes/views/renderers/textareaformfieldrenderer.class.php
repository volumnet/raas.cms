<?php
/**
 * Рендерер многострочных полей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера многострочных полей формы для сайта
 */
class TextAreaFormFieldRenderer extends TextFormFieldRenderer
{
    public function render($additionalData = [])
    {
        $attrs = $this->mergeAttributes(
            $this->getAttributes(),
            $additionalData
        );
        $content = '';
        if ($this->field->multiple) {
            $attrs['data-value'] = $attrs['value'];
        } else {
            $content = htmlspecialchars($attrs['value']);
        }
        unset($attrs['value'], $attrs['type']);
        return $this->getElement('textarea', $attrs, $content);
    }
}
