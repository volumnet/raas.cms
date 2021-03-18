<?php
/**
 * Рендерер файловых полей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера файловых полей формы для сайта
 */
class FileFormFieldRenderer extends FormFieldRenderer
{
    const HTML_VALID_MULTIPLE = true;

    public function getAttributes()
    {
        $attrs = parent::getAttributes();
        $attrs['type'] = 'file';
        $allowedExtensions = preg_split('/\\W+/umis', $this->field->source);
        $allowedExtensions = array_map(function ($x) {
            return '.' . mb_strtolower($x);
        }, $allowedExtensions);
        if ($allowedExtensions) {
            $attrs['accept'] = implode(',', $allowedExtensions);
        }
        return $attrs;
    }
}
