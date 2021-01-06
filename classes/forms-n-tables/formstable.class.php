<?php
/**
 * Таблица форм
 */
namespace RAAS\CMS;

use RAAS\Table;
use RAAS\Row;

/**
 * Класс таблицы форм
 * @property-read ViewSub_Dev $view Представление
 */
class FormsTable extends Table
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Dev::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $defaultParams = [
            'columns' => [
                'id' => [
                    'caption' => $this->view->_('ID'),
                    'callback' => function (Form $form) use ($view) {
                        return '<a href="' . $this->getEditURL($form) . '">
                                  ' . (int)$form->id . '
                                </a>';
                    }
                ],
                'name' => [
                    'caption' => $this->view->_('NAME'),
                    'callback' => function (Form $form) use ($view) {
                        return '<a href="' . $this->getEditURL($form) . '">
                                  ' . htmlspecialchars($form->name) . '
                                </a>';
                    }
                ],
                'urn' => [
                    'caption' => $this->view->_('URN'),
                    'callback' => function (Form $form) use ($view, $Item) {
                        return '<a href="' . $this->getEditURL($form) . '">
                                  ' . htmlspecialchars($form->urn) . '
                                </a>';
                    }
                ],
                ' ' => [
                    'callback' => function (
                        Form $form,
                        $i
                    ) use (
                        $view,
                        $contextMenuName,
                        $params
                    ) {
                        return rowContextMenu($view->getFormContextMenu(
                            $form,
                            $i,
                            count((array)$params['Set'])
                        ));
                    }
                ]

            ],
            'emptyString' => $this->view->_('NO_FORMS_FOUND'),
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает URL редактирования формы
     * @param Form $form Форма для редактирования
     * @return string
     */
    public function getEditURL(Form $form)
    {
        $url = $this->view->url . '&action=edit_form&id=' . (int)$form->id;
        return $url;
    }
}
