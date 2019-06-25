<?php
/**
 * Форма редактирования справочника
 */
namespace RAAS\CMS;

use RAAS\Form as RAASForm;

/**
 * Класс формы редактирования справочника
 * @property-read ViewSub_Dev $view Представление
 */
class EditDictionaryForm extends RAASForm
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
        $parent = isset($params['Parent']) ? $params['Parent'] : null;
        $CONTENT = [];
        foreach (Dictionary::$ordersBy as $key => $val) {
            $CONTENT['orderBy'][] = [
                'value' => $key,
                'caption' => $this->view->_($val)
            ];
        }
        $defaultParams = [
            'caption' => $item->id
                      ?  $item->name
                      :  $this->view->_('CREATING_' . ($parent->id ? 'NOTE' : 'DICTIONARY')),
            'export' => function ($form) use ($parent) {
                $form->exportDefault();
                $form->Item->pid = (int)$parent->id;
            },
            'parentUrl' => $this->view->url . '&action=dictionaries&id='
                        .  (int)$parent->id,
            'newUrl' => $this->view->url . '&action=edit_dictionary&pid=%s'
        ];
        $defaultParams['children'][] = [
            'type' => 'checkbox',
            'name' => 'vis',
            'caption' => $this->view->_('VISIBLE'),
            'default' => 1
        ];
        $defaultParams['children'][] = [
            'name' => 'name',
            'caption' => $this->view->_('NAME'),
            'required' => 'required'
        ];
        $defaultParams['children'][] = [
            'name' => 'urn',
            'caption' => $this->view->_($parent->id ? 'VALUE' : 'URN')
        ];
        $defaultParams['children'][] = [
            'type' => 'radio',
            'name' => 'orderby',
            'children' => $CONTENT['orderBy'],
            'default' => 'priority'
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
