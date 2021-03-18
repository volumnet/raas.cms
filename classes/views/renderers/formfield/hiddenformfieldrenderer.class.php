<?php
/**
 * Рендерер материальных полей формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера материальных полей формы для сайта
 */
class HiddenFormFieldRenderer extends FormFieldRenderer
{
    public function getAttributes()
    {
        $attrs = $this->mergeAttributes(
            ['type' => 'hidden'],
            parent::getAttributes()
        );
        unset($attrs['id']);
        return $attrs;
    }


    public function render($additionalData = [])
    {
        $attrs = $this->mergeAttributes(
            $this->getAttributes(),
            $additionalData
        );
        unset($attrs['id']);
        if ($this->field->multiple) {
            $result = '';
            foreach ((array)$this->data as $val) {
                if (is_scalar($val)) {
                    $result .= $this->getElement(
                        'input',
                        $this->mergeAttributes($attrs, ['value' => $val])
                    );
                }
            }
        } else {
            if (is_scalar($this->data)) {
                $attrs['value'] = trim($this->data);
            }
            $result = $this->getElement('input', $attrs);
        }
        return $result;
    }
}
