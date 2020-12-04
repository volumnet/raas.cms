<?php
/**
 * Рендерер выпадающих списков формы для сайта
 */
namespace RAAS\CMS;

/**
 * Класс рендерера выпадающих списков формы для сайта
 */
class SelectFormFieldRenderer extends FormFieldRenderer
{
    public function renderSingle($index = null)
    {
        $attrs = $this->getAttributes($index);
        $content = $this->getOptionsTree($this->field->stdSource);
        return $this->getElement('select', $attrs, $content);
    }


    public function render()
    {
        return $this->renderSingle();
    }


    public function getAttributes($index = null)
    {
        $attrs = [
            'class' => 'form-control',
            'name' => $this->field->urn . ($this->field->multiple ? '[]' : ''),
        ];
        if ($this->field->multiple) {
            $attr['multiple'] = 'multiple';
        } else {
            $attrs['id'] = $this->field->getHTMLId($this->block);
        }
        if ($this->field->required) {
            $attrs['required'] = 'required';
        }
        return $attrs;
    }


    /**
     * Получает набор опций
     * @param array $source <pre>array<string[] Значение опции => [
     *     'name' => string Текст опции,
     *     'children' => <рекурсивно>
     * ]></pre> Источник опций
     * @param int $level Уровень вложенности
     */
    public function getOptionsTree(array $source = [], $level = 0)
    {
        $result = '';
        if (!$level && (!$this->field->required || $this->field->placeholder)) {
            $attrs = ['value' => ''];
            if (!$this->data) {
                $attrs['selected'] = $selected;
            }
            $content = ($this->field->placeholder ?: '--');
            $result .= $this->getElement('option', $attrs, $content);
        }
        foreach ($source as $key => $val) {
            $attrs = ['value' => $key];
            if (in_array($key, (array)$this->data)) {
                $attrs['selected'] = $selected;
            }
            $content = str_repeat('&nbsp;', $level * 5)
                     . htmlspecialchars($val['name']);
            $result .= $this->getElement('option', $attrs, $content);
            if (isset($val['children']) && is_array($val['children'])) {
                $result .= $this->getOptionsTree($val['children'], $level + 1);
            }
        }
        return $result;
    }
}
