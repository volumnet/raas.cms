<?php
/**
 * Форма редактирования редиректов
 */
namespace RAAS\CMS;

use RAAS\FieldSet;
use RAAS\Form as RAASForm;

/**
 * Класс формы редактирования редиректов
 * @property-read ViewSub_Dev $view Представление
 */
class RedirectsForm extends RAASForm
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
            'caption' => $this->view->_('REDIRECTS'),
            'import' => function ($formtab) use ($params) {
                $data = [];
                foreach ((array)$params['Set'] as $redirect) {
                    $data['redirect_id'][] = (int)$redirect->id;
                    $data['redirect_rx'][] = (int)$redirect->rx;
                    $data['redirect_url_from'][] = trim($redirect->url_from);
                    $data['redirect_url_to'][] = trim($redirect->url_to);
                }
                return $data;
            },
            'export' => 'is_null',
            'children' => [
                'redirectsTable' => new FieldSet([
                    'template' => fn($fieldSet) => $fieldSet->children->renderCompound(),
                    'children' => [
                        'redirect_id' => [
                            'type' => 'hidden',
                            'name' => 'redirect_id',
                            'multiple' => true,
                        ],
                        'redirect_rx' => [
                            'type' => 'checkbox',
                            'name' => 'redirect_rx',
                            'caption' => $this->view->_('REDIRECTS_RX'),
                            'multiple' => true,
                            'meta' => [
                                'hint' => $this->view->_('REGULAR_EXPRESSION'),
                            ],
                        ],
                        'redirect_url_from' => [
                            'name' => 'redirect_url_from',
                            'caption' => $this->view->_('URL_FROM'),
                            'multiple' => true,
                            'classname' => 'span4',
                        ],
                        'redirect_url_to' => [
                            'name' => 'redirect_url_to',
                            'caption' => $this->view->_('URL_TO'),
                            'multiple' => true,
                            'classname' => 'span4',
                        ],
                    ]
                ]),
            ],
            'oncommit' => function ($form) {
                $affectedIds = [];
                foreach ((array)$_POST['redirect_id'] as $key => $redirectId) {
                    $redirect = new Redirect($redirectId);
                    $redirect->rx = (int)(bool)$_POST['redirect_rx'][$key];
                    $redirect->url_from = trim($_POST['redirect_url_from'][$key]);
                    $redirect->url_to = trim($_POST['redirect_url_to'][$key]);
                    $redirect->priority = (int)$key;
                    $redirect->commit();
                    $affectedIds[] = $redirect->id;
                }
                $sqlQuery = "DELETE FROM " . Redirect::_tablename();
                if ($affectedIds) {
                    $sqlQuery .= " WHERE id NOT IN (" . implode(", ", $affectedIds) . ")";
                }
                Redirect::_SQL()->query($sqlQuery);
            }
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
        $redirectsFieldSet = $this->children['redirectsTable'];
    }
}
