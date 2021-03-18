<?php
/**
 * Файл мока для проверки поля формы
 */
namespace RAAS\CMS;

/**
 * Класс мока для проверки поля формы
 */
class FormFieldMock extends Form_Field
{
    public function getValues($forceArray = false)
    {
        if ($this->isEmpty) {
            return [];
        } else {
            return ['aaa', 'bbb', '"ccc'];
        }
    }
}
