<?php
/**
 * Рендерер парольных полей формы для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера парольных полей формы для сайта
 */
class PasswordFormFieldRenderer extends TextFormFieldRenderer
{
    public function render(array $additionalData = []): string
    {
        $attrs = $this->mergeAttributes(
            $this->getAttributes(),
            $additionalData
        );
        unset($attrs['value'], $attrs['data-value']);
        return $this->getElement('input', $attrs);
    }
}
