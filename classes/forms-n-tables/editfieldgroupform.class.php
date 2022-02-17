<?php
/**
 * Форма редактирования группы полей
 */
namespace RAAS\CMS;

use RAAS\Option;
use RAAS\FieldSet;
use RAAS\Form as RAASForm;
use RAAS\FormTab;

/**
 * Класс формы редактирования группы полей
 * @property-read ViewSub_Dev $view Представление
 */
class EditFieldGroupForm extends RAASForm
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
        $item = isset($params['Item']) ? $params['Item'] : null;
        $parent = isset($params['meta']['Parent'])
                ? $params['meta']['Parent']
                : null;
        $parentUrl = $params['meta']['parentUrl'];

        $defaultParams = [
            'caption' => $item->id
                      ?  $item->name
                      :  $this->view->_('CREATING_FIELD_GROUP'),
            'parentUrl' => $parentUrl,
            'export' => function ($form) use ($item, $parent) {
                $form->exportDefault();
                if (!$form->Item->id &&
                    isset($parent) && $parent && $parent->id
                ) {
                    $form->Item->pid = (int)$parent->id;
                }
            },
            'children' => [
                'name' => [
                    'name' => 'name',
                    'caption' => $this->view->_('NAME'),
                    'required' => 'required'
                ],
                'urn' => [
                    'name' => 'urn',
                    'caption' => $this->view->_('URN')
                ],
            ]
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
