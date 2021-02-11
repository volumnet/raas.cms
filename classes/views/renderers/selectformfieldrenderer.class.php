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
    const HTML_VALID_MULTIPLE = true;

    public function getAttributes()
    {
        $attrs = parent::getAttributes();
        $attrs['class'] = ['form-control' => true];
        return $attrs;
    }


    /**
     * Получает набор опций
     * @param array $source <pre>array<string[] Значение опции => [
     *     'name' => string Текст опции,
     *     'children' => <рекурсивно>
     * ]></pre> Стандартный источник опций
     * @param int $level Уровень вложенности
     */
    public function getOptionsTree(array $source = [], $level = 0)
    {
        $result = '';
        if (!$level && (
            (!$this->field->multiple && $this->field->required) ||
            $this->field->placeholder
        )) {
            $attrs = ['value' => ''];
            if (!$this->data) {
                $attrs['selected'] = 'selected';
            }
            $content = ($this->field->placeholder ?: '--');
            $result .= $this->getElement('option', $attrs, $content);
        }
        foreach ($source as $key => $val) {
            $attrs = ['value' => $key];
            if (in_array($key, (array)$this->data)) {
                $attrs['selected'] = 'selected';
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


    public function render($additionalData = [])
    {
        $attrs = $this->mergeAttributes(
            $this->getAttributes(),
            $additionalData
        );
        $content = $this->getOptionsTree($this->field->stdSource);
        return $this->getElement('select', $attrs, $content);
    }
}
