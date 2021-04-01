<?php
/**
 * Файл мока для кастомного владельца поля
 */
namespace RAAS\CMS;

/**
 * Класс мока для кастомного владельца поля
 * @property-read FormFieldMock[] $fields Поля пользователя
 */
class OwnerMock extends User
{
    public function __get($var)
    {
        switch ($var) {
            case 'fields':
                return [
                    'testfield' => new FormFieldMock([
                        'urn' => 'testfield',
                        'datatype' => 'text',
                        'name' => 'Название',
                    ])
                ];
                break;
            case 'login':
                return 'testuser';
                break;
        }
    }
}
