<?php
/**
 * Рендерер форм для сайта
 */
namespace RAAS\CMS;

use RAAS\HTMLRenderer;

/**
 * Класс рендерера форм для сайта
 */
class FormRenderer extends HTMLRenderer
{
    /**
     * Форма для отображения
     * @var Form
     */
    public $form;

    /**
     * Данные формы
     * @var array <pre>array<
     *     string[] URN поля => string|array<
     *         string[] Индекс множественного поля => string
     *     >
     * ></pre>
     */
    public $data = [];

    /**
     * Ошибки формы
     * @var array <pre>array<
     *     string[] URN поля => string Текст ошибки
     * </pre>
     */
    public $errors = [];

    /**
     * Блок для связки атрибутов
     * @var Block|null
     */
    public $block;

    /**
     * Конструктор класса
     * @param Form $form Форма для отображения
     * @param Block|null $block Блок для связки атрибутов
     * @param array $data <pre>array<
     *     string[] URN поля => string|array<
     *         string[] Индекс множественного поля => string
     *     >
     * ></pre> Данные формы
     * @param array $errors <pre>array<
     *     string[] URN поля => string Текст ошибки
     * </pre> Ошибки формы
     */
    public function __construct(
        Form $form,
        Block $block = null,
        array $data = [],
        array $errors = []
    ) {
        $this->form = $form;
        $this->block = $block;
        $this->data = $data;
        $this->errors = $errors;
    }


    /**
     * Возвращает код поля подписи формы
     * @param array|callable $additionalData Дополнительные данные,
     *     либо callback, их возвращающий
     * @return string
     */
    public function renderSignatureField($additionalData = [])
    {
        if ($this->form->signature) {
            $attrs = $this->mergeAttributes([
                'type' => 'hidden',
                'name' => 'form_signature',
                'value' => $this->form->getSignature($this->block),
            ], $additionalData);
            return $this->getElement('input', $attrs);
        }
        return '';
    }


    /**
     * Возвращает код скрытого антиспам-поля формы
     * @param array|callable $additionalData Дополнительные данные,
     *     либо callback, их возвращающий
     * @return string
     */
    public function renderHiddenAntispamField($additionalData = [])
    {
        if ((in_array($this->form->antispam, ['hidden', 'smart'])) &&
            ($fieldURN = $this->form->antispam_field_name)
        ) {
            $attrs = $this->mergeAttributes([
                'autocomplete' => 'off',
                'name' => $fieldURN,
                'style' => 'position: absolute; left: -9999px',
            ], $additionalData);
            return $this->getElement(
                'textarea',
                $attrs,
                htmlspecialchars(trim($this->data[$fieldURN]))
            );
        }
        return '';
    }
}
