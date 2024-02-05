<?php
/**
 * Рендерер файловых полей формы для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера файловых полей формы для сайта
 */
class FileFormFieldRenderer extends FormFieldRenderer
{
    const HTML_VALID_MULTIPLE = true;

    public function getAttributes(): array
    {
        $attrs = parent::getAttributes();
        $attrs['type'] = 'file';
        $allowedExtensions = preg_split('/\\W+/umis', (string)$this->field->source);
        $allowedExtensions = array_map(function ($x) {
            return '.' . mb_strtolower($x);
        }, $allowedExtensions);
        if ($allowedExtensions) {
            $attrs['accept'] = implode(',', $allowedExtensions);
        }
        return $attrs;
    }
}
