<?php
/**
 * Форма диагностики (вкладки с таблицами)
 */
namespace RAAS\CMS;

use RAAS\FormTab;
use RAAS\FieldSet;

/**
 * Класс формы диагностики (вкладки с таблицами)
 * @property-read ViewSub_Dev $view Представление
 */
class DiagForm extends \RAAS\Form
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
        $Item = isset($params['Item']) ? $params['Item'] : null;

        $defaultParams = [
            'caption' => $view->_('DIAGNOSTICS'),
            'meta' => [
                'Item' => $Item,
                'from' => $params['from'],
                'to' => $params['to']
            ],
            'children' => [],
            'commit' => 'is_null',
            'template' => 'dev_diag.tmp.php'
        ];
        foreach ([
            'queries',
            'timers',
            'snippets',
            'blocks',
            'templates',
            'pages'
        ] as $key) {
            $row = new FormTab([
                'name' => $key,
                'caption' => $this->view->_('DIAGNOSTICS_TAB_' . strtoupper($key)),
                'children' => [],
                'template' => 'dev_diag_tab.inc.php'
            ]);
            foreach (['main', 'long', 'freq'] as $key2) {
                $row->children[$key2] = new FieldSet([
                    'name' => $key2,
                    'caption' => $this->view->_('DIAGNOSTICS_TOP10') . ' '
                              . $this->view->_('DIAGNOSTICS_SET_' . strtoupper($key2))
                              . ' '
                              . $this->view->_('DIAGNOSTICS_SET_' . strtoupper($key)),
                    'meta' => [
                        'Table' => new DiagTable([
                            'Set' => isset($Item->stat[$key][$key2])
                                  ?  $Item->stat[$key][$key2]
                                  : [],
                            'meta' => [
                                'type' => $key,
                            ],
                        ])
                    ],
                    'template' => 'dev_diag_set.inc.php'
                ]);
            }
            $defaultParams['children'][$key] = $row;
        }
        $arr = $defaultParams;
        parent::__construct($arr);
    }
}
