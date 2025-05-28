<?php
/**
 * Форматтер массива для формы
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс форматтера массива для формы
 */
class FormArrayFormatter
{
    /**
     * Форма для форматирования
     * @var Form
     */
    public $form;

    /**
     * Получать поля для админки (
     *     preprocessor_id,
     *     postprocessor_id,
     *     show_in_table
     * )
     * @var bool
     */
    public $getAdminFields = false;

    /**
     * Конструктор класса
     * @param Form $form Форма для форматирования
     * @param bool $getAdminFields Получать поля для администратора
     */
    public function __construct(Form $form, bool $getAdminFields = false)
    {
        $this->form = $form;
        $this->getAdminFields = $getAdminFields;
    }

    /**
     * Форматирует данные
     * @param array $with <pre>array<(
     *     string[] URN поля => function (Form $form Форма): mixed Обработчик данных
     * )|(
     *     int[] Индекс поля => string URN поля
     * )> Массив дополнительных полей формы для отображения
     * @param array $fieldWith <pre>array<(
     *     string[] URN поля => function (Form $form Форма): mixed Обработчик данных
     * )|(
     *     int[] Индекс поля => string URN поля
     * )> Массив дополнительных полей каждого поля формы для отображения
     * @return array <pre>array<string[] Свойство формы => mixed></pre>
     */
    public function format(array $with = [], array $fieldWith = []): array
    {
        $result = (array)$this->form->getArrayCopy();
        foreach (['id', 'material_type', 'interface_id'] as $key) {
            if ($result[$key] !== null) {
                $result[$key] = (int)$result[$key];
            }
        }

        foreach (['create_feedback', 'signature'] as $key) {
            if ($result[$key] !== null) {
                $result[$key] = (bool)(int)$result[$key];
            }
        }
        if (count($this->form->fieldGroups) > 1) {
            $result['fieldGroups'] = array_map(fn($group) => $group->getArrayCopy(), $this->form->fieldGroups);
        }
        if (!$this->getAdminFields) {
            unset(
                $result['material_type'],
                $result['create_feedback'],
                $result['email'],
                $result['interface_id']
            );
        }
        if ($this->getAdminFields) {
            $fields = $this->form->fields;
        } else {
            $fields = $this->form->visFields;
        }
        $result['fields'] = array_map(function ($field) use ($fieldWith) {
            $fieldArrayFormatter = new FieldArrayFormatter($field, $this->getAdminFields);
            return $fieldArrayFormatter->format($fieldWith);
        }, $fields);
        // if ($stdSource = $this->field->stdSource) {
        //     $result['stdSource'] = $stdSource;
        // }

        foreach ($with as $key => $val) {
            $value = null;
            if (is_numeric($key) && is_string($val)) {
                $urn = $val;
                if (is_scalar($val) || is_array($val)) {
                    $value = $this->form->$val;
                }
            } elseif (is_string($key)) {
                $urn = $key;
                if (is_callable($val)) {
                    $value = $val($this->form);
                } else {
                    $value = $val;
                }
            }
            if ($value !== null) {
                $result[$urn] = $value;
            }
        }
        return $result;
    }
}
