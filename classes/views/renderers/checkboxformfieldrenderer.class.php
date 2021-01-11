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
            unset($attrs['required']);
        } else {
            $attrs['value'] = $this->field->defval ?: 1;
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
        foreach ($source as $key => $val) {
            $attrs = ['value' => $key];
            if (in_array($key, (array)$this->data)) {
                $attrs['checked'] = 'checked';
            }
            $checkboxHtml = $this->getElement('input', $attrs);
            $labelHtml = $this->getElement(
                'label',
                [],
                $checkboxHtml . ' ' . htmlspecialchars($val['name'])
            );
            $result .= $this->getElement('li', [], $labelHtml);
            if (isset($val['children']) && is_array($val['children'])) {
                $result .= $this->getOptionsTree($val['children'], $level + 1);
            }
        }
        $ulAttrs = [];
        if (!$level) {
            $ulAttrs['class'] = ['checkbox-tree' => true];
            $ulAttrs['data-role'] = 'checkbox-tree';
        }
        $result = $this->getElement('ul', $ulAttrs, $result);
        return $result;
    }


    public function render($additionalData = [])
    {
        if ($this->field->multiple) {
            return $this->getOptionsTree($this->field->stdSource);
        } else {
            $attrs = $this->mergeAttributes(
                $this->getAttributes(),
                $additionalData
            );
            return $this->getElement('input', $attrs);
        }
    }
}
