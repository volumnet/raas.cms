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


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $defaultParams = array(
            'columns' => array(
                'id' => array(
                    'caption' => $this->view->_('ID'),
                    'callback' => function (Form $form) use ($view) {
                        return '<a href="' . $this->getEditURL($form) . '">
                                  ' . (int)$form->id . '
                                </a>';
                    }
                ),
                'name' => array(
                    'caption' => $this->view->_('NAME'),
                    'callback' => function (Form $form) use ($view) {
                        return '<a href="' . $this->getEditURL($form) . '">
                                  ' . htmlspecialchars($form->name) . '
                                </a>';
                    }
                ),
                'urn' => array(
                    'caption' => $this->view->_('URN'),
                    'callback' => function (Form $form) use ($view, $Item) {
                        return '<a href="' . $this->getEditURL($form) . '">
                                  ' . htmlspecialchars($form->urn) . '
                                </a>';
                    }
                ),
                ' ' => array(
                    'callback' => function (Form $form, $i) use ($view, $contextMenuName, $IN) {
                        return rowContextMenu($view->getFormContextMenu(
                            $form,
                            $i,
                            count($IN['Set'])
                        ));
                    }
                )

            ),
            'emptyString' => $this->view->_('NO_FORMS_FOUND'),
        );
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
