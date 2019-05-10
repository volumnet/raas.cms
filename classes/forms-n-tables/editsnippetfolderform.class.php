<?php
/**
 * Форма редактирования папки сниппетов
 */
namespace RAAS\CMS;

use RAAS\Form as RAASForm;

/**
 * Класс формы редактирования папки сниппетов
 * @property-read ViewSub_Dev $view Представление
 */
class EditSnippetFolderForm extends RAASForm
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
        $defaultParams = [
            'caption' => $view->_('EDIT_SNIPPET_FOLDER'),
            'parentUrl' => Sub_Dev::i()->url . '&action=snippets',
            'children' => [
                [
                    'name' => 'name',
                    'caption' => $view->_('NAME'),
                    'required' => 'required'
                ],
                [
                    'name' => 'urn',
                    'caption' => $view->_('URN')
                ],
                [
                    'type' => 'select',
                    'name' => 'pid',
                    'caption' => $view->_('PARENT_FOLDER'),
                    'children' => [
                        'Set' => [
                            new Snippet_Folder([
                                'name' => $view->_('ROOT_FOLDER'),
                                'id' => 0
                            ])
                        ],
                        'filter' => function ($x) use ($item) {
                            return $x->id != $item->id;
                        }
                    ]
                ]
            ]
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
