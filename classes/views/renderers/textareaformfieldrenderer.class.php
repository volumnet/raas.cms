<?php
/**
 * Рендерер многострочных полей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера многострочных полей формы для сайта
 */
class TextareaFormFieldRenderer extends FormFieldRenderer
{
    public function renderSingle($index = null)
    {
        $attrs = $this->getAttributes($index);
        $content = '';
        if (($val = $this->getValue($index)) !== null) {
            $content = (string)$val;
        }
        return $this->getElement('textarea', $attrs, $content);
    }
}
