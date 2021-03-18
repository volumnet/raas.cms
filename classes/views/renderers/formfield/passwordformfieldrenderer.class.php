<?php
/**
 * Рендерер парольных полей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера парольных полей формы для сайта
 */
class PasswordFormFieldRenderer extends TextFormFieldRenderer
{
    public function render($additionalData = [])
    {
        $attrs = $this->mergeAttributes(
            $this->getAttributes(),
            $additionalData
        );
        unset($attrs['value'], $attrs['data-value']);
        return $this->getElement('input', $attrs);
    }
}
