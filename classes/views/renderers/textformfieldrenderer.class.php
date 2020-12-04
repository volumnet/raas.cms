<?php
/**
 * Рендерер текстовых полей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера текстовых полей формы для сайта
 */
class TextFormFieldRenderer extends FormFieldRenderer
{
    public function getAttributes($index = null)
    {
        $attrs = parent::getAttributes($index);
        foreach (['min_val', 'max_val'] as $key) {
            if ($val = $this->field->$key) {
                $attrs[$key] = (float)$val;
            }
        }
        $attrs['type'] = $this->field->datatype;
        if (($val = $this->getValue($index)) !== null) {
            $attrs['value'] = $val;
        }
        return $attrs;
    }
}
