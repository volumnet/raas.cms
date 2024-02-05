<?php
/**
 * Рендерер многострочных полей формы для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера многострочных полей формы для сайта
 */
class TextAreaFormFieldRenderer extends TextFormFieldRenderer
{
    public function render(array $additionalData = []): string
    {
        $attrs = $this->mergeAttributes(
            $this->getAttributes(),
            $additionalData
        );
        $content = '';
        if ($this->field->multiple) {
            $attrs['data-value'] = $attrs['value'];
        } else {
            $content = htmlspecialchars((string)$attrs['value']);
        }
        unset($attrs['value'], $attrs['type']);
        return $this->getElement('textarea', $attrs, $content);
    }
}
