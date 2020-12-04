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
}
