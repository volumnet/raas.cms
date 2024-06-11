<?php
/**
 * Поле формы
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс поля формы
 * @property-read Form $parent Родительская форма
 * @property-read Snippet $Preprocessor Препроцессор поля
 * @property-read Snippet $Postprocessor Постпроцессор поля
 * @property Feedback $Owner Владелец поля
 */
class Form_Field extends Field
{
    protected static $references = [
        // 2024-05-02, AVS: заменил каскадирование на true, что в совокупности с $objectCascadeDelete позволяет избежать
        // удаления полей n-ой формы при удалении n-го типа материала
        'parent' => [
            'FK' => 'pid',
            'classname' => Form::class,
            'cascade' => true
        ],
        'Preprocessor' => [
            'FK' => 'preprocessor_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
        'Postprocessor' => [
            'FK' => 'postprocessor_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
    ];

    public function __set($var, $val)
    {
        switch ($var) {
            case 'Owner':
                if ($val instanceof Feedback) {
                    $this->Owner = $val;
                }
                break;
            default:
                return parent::__set($var, $val);
                break;
        }
    }


    /**
     * Получает HTML-атрибут id поля
     * @param Block|null $Block Блок, для которого получаем
     * @param int|string|null $index Индекс поля в множественном списке
     * @return string
     */
    public function getHTMLId(Block $block = null, $index = null)
    {
        $result = $this->urn . $this->id;
        if ($block->id) {
            $result .= '_' . $block->id;
        }
        if ($index !== null) {
            $result .= '@' . $index;
        }
        return $result;
    }
}
