<?php
/**
 * Рендерер переключателей формы для сайта
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс рендерера переключателей формы для сайта
 */
class RadioFormFieldRenderer extends CheckboxFormFieldRenderer
{
    public function getAttributes(): array
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


    public function getOptionsTree(array $source = [], int $level = 0): string
    {
        $result = '';
        $stdAttrs = $this->getAttributes();
        unset(
            $stdAttrs['data-raas-field'],
            $stdAttrs['data-type'],
            $stdAttrs['data-placeholder'],
            $stdAttrs['id']
        );
        if (!$level && (
            !$this->field->required ||
            $this->field->placeholder
        )) {
            $attrs = $this->mergeAttributes($stdAttrs, ['value' => '']);
            if (!$this->data) {
                $attrs['checked'] = 'checked';
            }
            $radioHtml = $this->getElement('input', $attrs);
            $labelHtml = $this->getElement(
                'label',
                [],
                $radioHtml . ' ' . htmlspecialchars($this->field->placeholder ?: '--')
            );
            $result .= $this->getElement('li', [], $labelHtml);
        }
        foreach ($source as $key => $val) {
            $attrs = $this->mergeAttributes($stdAttrs, ['value' => $key]);
            if ($key == $this->data) {
                $attrs['checked'] = 'checked';
            }
            $radioHtml = $this->getElement('input', $attrs);
            $labelHtml = $this->getElement(
                'label',
                [],
                $radioHtml . ' ' . htmlspecialchars($val['name'])
            );
            if (isset($val['children']) && is_array($val['children'])) {
                $labelHtml .= $this->getOptionsTree($val['children'], $level + 1);
            }
            $result .= $this->getElement('li', [], $labelHtml);
        }
        if ($level) {
            $result = $this->getElement('ul', [], $result);
        }
        return $result;
    }


    public function render(array $additionalData = []): string
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
