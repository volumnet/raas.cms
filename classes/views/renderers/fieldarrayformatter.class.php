<?php
/**
 * Форматтер массива для поля
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс форматтера массива для поля
 */
class FieldArrayFormatter
{
    /**
     * Поле для форматирования
     * @var Field
     */
    public $field;

    /**
     * Получать поля для админки (
     *     preprocessor_classname,
     *     preprocessor_id,
     *     postprocessor_classname,
     *     postprocessor_id,
     *     show_in_table
     * )
     * @var bool
     */
    public $getAdminFields = false;

    /**
     * Конструктор класса
     * @param Field $field Поле для форматирования
     * @param bool $getAdminFields Получать поля для администратора
     */
    public function __construct(Field $field, bool $getAdminFields = false)
    {
        $this->field = $field;
        $this->getAdminFields = $getAdminFields;
    }

    /**
     * Форматирует данные
     * @param array $with <pre>array<(
     *     string[] URN атрибута => function (Field $field Поле): mixed Обработчик данных
     * )|(
     *     int[] Индекс атрибута => string URN поля
     * )> Массив дополнительных полей для отображения
     * @return array <pre>array<string[] Свойство поля => mixed></pre>
     */
    public function format(array $with = []): array
    {
        $result = (array)$this->field->getArrayCopy();
        foreach ([
            'id',
            'pid',
            'maxlength',
            'preprocessor_id',
            'postprocessor_id',
            'priority',
        ] as $key) {
            if (($result[$key] ?? null) !== null) {
                $result[$key] = (int)$result[$key];
            }
        }
        foreach (['min_val', 'max_val', 'step'] as $key) {
            if (($result[$key] ?? null) !== null) {
                $result[$key] = (float)$result[$key];
            }
        }
        foreach (['vis', 'required', 'multiple', 'show_in_table'] as $key) {
            if (($result[$key] ?? null) !== null) {
                $result[$key] = (bool)(int)$result[$key];
            }
        }
        if (is_numeric($result['source'] ?? null)) {
            $result['source'] = (int)$result['source'];
        }
        if (in_array($result['datatype'], ['file', 'image']) && ($result['source'] ?? null)) {
            $allowedExtensions = preg_split('/\\W+/umis', $this->field->source);
            $allowedExtensions = array_map(function ($x) {
                return '.' . mb_strtolower($x);
            }, $allowedExtensions);
            if ($allowedExtensions) {
                $result['accept'] = implode(',', $allowedExtensions);
            }
        }
        if (($result['datatype'] == 'image') && !$result['accept']) {
            $result['accept'] = 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml';
        }
        if (!$this->getAdminFields) {
            unset(
                $result['classname'],
                $result['preprocessor_classname'],
                $result['preprocessor_id'],
                $result['postprocessor_classname'],
                $result['postprocessor_id'],
                $result['show_in_table'],
                $result['source'],
                $result['source_type']
            );
        }
        if ($stdSource = $this->field->stdSource) {
            $result['stdSource'] = $this->formatStdSource($stdSource);
        }

        foreach ($with as $key => $val) {
            $value = null;
            if (is_numeric($key) && is_string($val)) {
                $urn = $val;
                if (is_scalar($val) || is_array($val)) {
                    $value = $this->field->$val;
                }
            } elseif (is_string($key)) {
                $urn = $key;
                if (is_callable($val)) {
                    $value = $val($this->field);
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


    /**
     * Форматирует стандартный источник поля
     * @param array $source <pre><code>array<string[] Значение => [
     *     'name' => string Текст,
     *     'children' =>? array Рекурсивно
     * ]]></code></pre> Входной источник
     * @return <pre><code>array<[
     *     'value' => string Значение
     *     'name' => string Текст,
     *     'children' =>? array Рекурсивно
     * ]]></code></pre>
     */
    public function formatStdSource(array $source): array
    {
        $result = [];
        foreach ($source as $val => $sourceData) {
            $entry = ['value' => $val, 'name' => $sourceData['name'] ?? ''];
            if ($sourceData['children'] ?? []) {
                $entry['children'] = $this->formatStdSource($sourceData['children']);
            }
            $result[] = $entry;
        }
        return $result;
    }
}
