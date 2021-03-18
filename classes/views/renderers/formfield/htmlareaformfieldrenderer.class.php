<?php
/**
 * Рендерер HTML-полей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера HTML-полей формы для сайта
 */
class HtmlAreaFormFieldRenderer extends TextAreaFormFieldRenderer
{
    public function getAttributes()
    {
        $attrs = $this->mergeAttributes(
            parent::getAttributes(),
            ['data-type' => 'htmlarea']
        );
        return $attrs;
    }
}
