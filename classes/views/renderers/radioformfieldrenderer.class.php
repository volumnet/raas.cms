<?php
/**
 * Рендерер переключателей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера переключателей формы для сайта
 */
class RadioFormFieldRenderer extends CheckboxFormFieldRenderer
{
    public function getAttributes()
    {
        $attrs = $this->mergeAttributes(
            ['type' => $this->field->datatype],
            parent::getAttributes()
        );
        unset(
            $attrs['multiple'],
            $attrs['data-multiple'],
            $attrs['value'],
            $attrs['id'],
            $attrs['checked']
        );
        return $attrs;
    }


    public function render($additionalData = [])
    {
        $optionsTree = $this->getOptionsTree($this->field->stdSource);
        $attrs = $this->mergeAttributes([
            'data-raas-field' => '',
            'data-type' => $this->field->datatype,
            'class' => ['checkbox-tree' => true, 'checkbox-tree_radio' => true],
            'data-role' => 'checkbox-tree',
        ], $additionalData);
        return $this->getElement('ul', $attrs, $optionsTree);
    }
}
