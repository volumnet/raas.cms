<?php
/**
 * Рендерер полей изображений формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера полей изображений формы для сайта
 */
class ImageFormFieldRenderer extends FileFormFieldRenderer
{
    public function getAttributes()
    {
        $attrs = parent::getAttributes();
        $attrs['data-type'] = 'image';
        $allowedExtensions = preg_split('/\\W+/umis', $this->field->source);
        $allowedExtensions = array_map(function ($x) {
            return mb_strtolower($x);
        }, $allowedExtensions);
        if ($allowedExtensions) {
            $allowedExtensions = array_values(array_intersect(
                $allowedExtensions,
                ['jpg', 'jpeg', 'png', 'gif']
            ));
        }
        $allowedExtensions = array_map(function ($x) {
            return '.' . mb_strtolower($x);
        }, $allowedExtensions);
        if ($allowedExtensions) {
            $attrs['accept'] = implode(',', $allowedExtensions);
        } else {
            $attrs['accept'] = 'image/jpeg,image/png,image/gif';
        }
        return $attrs;
    }
}
