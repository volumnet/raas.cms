<?php
/**
 * Форма просмотра меню
 */
namespace RAAS\CMS;

use RAAS\Form as RAASForm;
use RAAS\FormTab;

/**
 * Класс формы просмотра меню
 * @property-read ViewSub_Dev $view Представление
 */
class ViewMenuForm extends RAASForm
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
            'caption' => $item->name,
            'children' => [
                'common' => new FormTab([
                    'name' => 'common',
                    'caption' => $this->view->_('SUBSECTIONS'),
                    'meta' => ['Table' => $params['Table']],
                    'template' => 'entity_users.inc.php'
                ])
            ]
        ];
        if ($item->id) {
            if ($usingBlocks = $item->usingBlocks) {
                $defaultParams['children']['blocks'] = new FormTab([
                    'name' => 'blocks',
                    'caption' => $this->view->_('BLOCKS'),
                    'meta' => ['Table' => new EntityUsersTable([
                        'Set' => $usingBlocks,
                    ])],
                    'template' => 'entity_users.inc.php'
                ]);
            }
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
