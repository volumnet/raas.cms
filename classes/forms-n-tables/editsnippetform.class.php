<?php
/**
 * Форма редактирования сниппета
 */
namespace RAAS\CMS;

use RAAS\Form as RAASForm;

/**
 * Класс формы редактирования сниппета
 * @property-read ViewSub_Dev $view Представление
 */
class EditSnippetForm extends RAASForm
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
            'caption' => $view->_('EDIT_SNIPPET'),
            'parentUrl' => Sub_Dev::i()->url . '&action=snippets',
            'children' => [
                [
                    'name' => 'name',
                    'caption' => $view->_('NAME'),
                    'required' => 'required',
                ],
                [
                    'name' => 'urn',
                    'caption' => $view->_('URN'),
                ],
                [
                    'type' => 'select',
                    'name' => 'pid',
                    'caption' => $view->_('PARENT_FOLDER'),
                    'children' => [
                        'Set' => [
                            new Snippet_Folder([
                                'name' => $this->view->_('ROOT_FOLDER'),
                                'id' => 0,
                            ])
                        ]
                    ],
                ],
                [
                    'type' => 'codearea',
                    'name' => 'description',
                    'caption' => $view->_('SOURCE_CODE'),
                ],
            ]
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
