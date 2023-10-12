<?php
/**
 * Форма редактирования сниппета
 */
namespace RAAS\CMS;

use RAAS\Form as RAASForm;
use RAAS\FormTab;

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
                'common' => $this->getCommonTab(),
            ]
        ];
        if ($item->id) {
            $defaultParams['children']['service'] = $this->getServiceTab();
            if ($usingBlocks = $item->usingBlocks) {
                $defaultParams['children']['blocks'] = $this->getSnippetUsersTab(
                    $this->view->_('BLOCKS'),
                    'blocks',
                    $usingBlocks
                );
            }
            if ($usingSnippets = $item->usingSnippets) {
                $defaultParams['children']['snippets'] = $this->getSnippetUsersTab(
                    $this->view->_('SNIPPETS'),
                    'snippets',
                    $usingSnippets
                );
            }
            if ($usingForms = $item->usingForms) {
                $defaultParams['children']['forms'] = $this->getSnippetUsersTab(
                    $this->view->_('FORMS'),
                    'forms',
                    $usingForms
                );
            }
            if ($usingFields = $item->usingFields) {
                $defaultParams['children']['fields'] = $this->getSnippetUsersTab(
                    $this->view->_('FIELDS'),
                    'fields',
                    $usingFields
                );
            }
            if ($usingPriceloaders = $item->usingPriceloaders) {
                $defaultParams['children']['priceloaders'] = $this->getSnippetUsersTab(
                    $this->view->_('PRICELOADERS'),
                    'priceloaders',
                    $usingPriceloaders
                );
            }
            if ($usingImageloaders = $item->usingImageloaders) {
                $defaultParams['children']['imageloaders'] = $this->getSnippetUsersTab(
                    $this->view->_('IMAGELOADERS'),
                    'imageloaders',
                    $usingImageloaders
                );
            }
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает основную вкладку редактирования сниппета
     * @return FormTab
     */
    public function getCommonTab()
    {
        $tab = new FormTab([
            'name' => 'common',
            'caption' => $this->view->_('MAIN'),
            'children' => [
                'urn' => [
                    'name' => 'urn',
                    'caption' => $this->view->_('URN'),
                ],
                'pid' => [
                    'type' => 'select',
                    'name' => 'pid',
                    'caption' => $this->view->_('PARENT_FOLDER'),
                    'children' => [
                        'Set' => [
                            new Snippet_Folder([
                                'name' => $this->view->_('ROOT_FOLDER'),
                                'id' => 0,
                            ])
                        ]
                    ],
                ],
                'description' => [
                    'type' => 'codearea',
                    'name' => 'description',
                    'caption' => $this->view->_('SOURCE_CODE'),
                ],
            ]
        ]);
        return $tab;
    }


    /**
     * Получает вкладку "Служебные"
     * @return FormTab
     */
    protected function getServiceTab()
    {
        $arr = [
            'name' => 'service',
            'caption' => $this->view->_('SERVICE'),
            'children' => [
                'post_date' => [
                    'name' => 'post_date',
                    'caption' => $this->view->_('CREATED_BY'),
                    'export' => 'is_null',
                    'import' => 'is_null',
                    'template' => 'stat.inc.php'
                ],
                'modify_date' => [
                    'name' => 'modify_date',
                    'caption' => $this->view->_('EDITED_BY'),
                    'export' => 'is_null',
                    'import' => 'is_null',
                    'template' => 'stat.inc.php'
                ],
            ],
        ];
        $tab = new FormTab($arr);
        return $tab;
    }


    public function getSnippetUsersTab($name, $urn, array $snippetUsers)
    {
        $table = new EntityUsersTable([
            'Set' => $snippetUsers,
        ]);
        $tab = new FormTab([
            'name' => $urn,
            'caption' => $name,
            'meta' => ['Table' => $table],
            'template' => 'entity_users.inc.php'
        ]);
        return $tab;
    }
}
