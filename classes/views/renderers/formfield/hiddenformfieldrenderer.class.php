<?php
/**
 * Рендерер материальных полей формы для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера материальных полей формы для сайта
 */
class HiddenFormFieldRenderer extends FormFieldRenderer
{
    public function getAttributes(): array
    {
        $attrs = $this->mergeAttributes(
            ['type' => 'hidden'],
            parent::getAttributes()
        );
        unset($attrs['id']);
        return $attrs;
    }


    public function render(array $additionalData = []): string
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
                $attrs['value'] = trim((string)$this->data);
            }
            $result = $this->getElement('input', $attrs);
        }
        return $result;
    }
}
