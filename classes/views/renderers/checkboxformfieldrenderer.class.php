<?php
/**
 * Рендерер флажков формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера флажков формы для сайта
 */
class CheckboxFormFieldRenderer extends FormFieldRenderer
{
    public function getAttributes()
    {
        $attrs = $this->mergeAttributes(
            ['type' => $this->field->datatype],
            parent::getAttributes()
        );

        if ($this->field->multiple) {
            unset(
                $attrs['required'],
                $attrs['multiple'],
                $attrs['data-multiple']
            );
        } else {
            $attrs['value'] = $this->field->defval ?: 1;
            if ($this->data == $attrs['value']) {
                $attrs['checked'] = 'checked';
            }
        }

        return $attrs;
    }


    /**
     * Получает дерево флажков
     * @param array $source <pre>array<string[] Значение опции => [
     *     'name' => string Текст опции,
     *     'children' => <рекурсивно>
     * ]></pre> Источник опций
     * @param int $level Уровень вложенности
     * @todo
     */
    public function getOptionsTree(array $source = [], $level = 0)
    {
        $result = '';
        $stdAttrs = $this->getAttributes();
        foreach ($source as $key => $val) {
            $attrs = $this->mergeAttributes($stdAttrs, ['value' => $key]);
            if (in_array($key, (array)$this->data)) {
                $attrs['checked'] = 'checked';
            }
            $checkboxHtml = $this->getElement('input', $attrs);
            $labelHtml = $this->getElement(
                'label',
                [],
                $checkboxHtml . ' ' . htmlspecialchars($val['name'])
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


    public function render($additionalData = [])
    {
        if ($this->field->multiple) {
            $optionsTree = $this->getOptionsTree($this->field->stdSource);
            $attrs = $this->mergeAttributes([
                'class' => ['checkbox-tree' => true],
                'data-role' => 'checkbox-tree',
            ], $additionalData);
            return $this->getElement('ul', $attrs, $optionsTree);
        } else {
            $attrs = $this->mergeAttributes(
                $this->getAttributes(),
                $additionalData
            );
            return $this->getElement('input', $attrs);
        }
    }
}
