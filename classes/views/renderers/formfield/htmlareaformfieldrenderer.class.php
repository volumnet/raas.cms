<?php
/**
 * Рендерер HTML-полей формы для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера HTML-полей формы для сайта
 */
class HtmlAreaFormFieldRenderer extends TextAreaFormFieldRenderer
{
    public function getAttributes(): array
    {
        $attrs = $this->mergeAttributes(
            parent::getAttributes(),
            ['data-type' => 'htmlarea']
        );
        return $attrs;
    }
}
