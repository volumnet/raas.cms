<?php
/**
 * Рендерер текстовых полей формы для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера текстовых полей формы для сайта
 */
class TextFormFieldRenderer extends FormFieldRenderer
{
    public function getAttributes(): array
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
            $attrs['value'] = json_encode((array)$this->data);
        } else {
            if (is_array($this->data)) {
                $dataArr = (array)$this->data;
                $attrs['value'] = trim((string)array_shift($dataArr));
            } elseif (is_scalar($this->data)) {
                $attrs['value'] = trim((string)$this->data);
            }
        }
        return $attrs;
    }


    public function render(array $additionalData = []): string
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
