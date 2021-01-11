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
    public function getAttributes()
    {
        $attrs = $this->mergeAttributes(
            [
                'type' => $this->field->datatype,
                'class' => ['form-control' => true]
            ],
            parent::getAttributes()
        );
        foreach (['maxlength'] as $key) {
            if ($val = $this->field->$key) {
                $attrs[$key] = (int)$val;
            }
        }
        foreach (['placeholder', 'pattern'] as $key) {
            if ($val = $this->field->$key) {
                $attrs[$key] = trim($val);
            }
        }
        if ($this->field->multiple) {
            $attrs['value'] = json_encode($this->data);
        } elseif (is_scalar($this->data)) {
            $attrs['value'] = trim($this->data);
        }
        return $attrs;
    }


    public function render($additionalData = [])
    {
        $attrs = $this->mergeAttributes(
            $this->getAttributes(),
            $additionalData
        );
        if ($this->field->multiple) {
            $attrs['data-value'] = $attrs['value'];
            unset($attrs['value']);
        }
        return $this->getElement('input', $attrs);
    }
}
