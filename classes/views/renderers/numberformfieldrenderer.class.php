<?php
/**
 * Рендерер числовых полей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера числовых полей формы для сайта
 */
class NumberFormFieldRenderer extends TextFormFieldRenderer
{
    public function getAttributes()
    {
        $attrs = parent::getAttributes();
        foreach (['min_val', 'max_val', 'step'] as $key) {
            if ($val = $this->field->$key) {
                $attrs[str_replace('_val', '', $key)] = (float)$val;
            }
        }
        return $attrs;
    }
}
