<?php
namespace RAAS\CMS;
use \RAAS\FormTab;
use \RAAS\FieldSet;

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


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $Item = isset($params['Item']) ? $params['Item'] : null;
        
        $defaultParams = array(
            'caption' => $view->_('DIAGNOSTICS'), 
            'meta' => array('Item' => $Item, 'from' => $params['from'], 'to' => $params['to']), 
            'children' => array(), 
            'commit' => 'is_null',
            'template' => 'dev_diag.tmp.php'
        );
        foreach (array('queries', 'blocks', 'pages') as $key) {
            $row = new FormTab(array(
                'name' => $key, 
                'caption' => $this->view->_('DIAGNOSTICS_TAB_' . strtoupper($key)), 
                'children' => array(),
                'template' => 'dev_diag_tab.inc.php'
            ));
            foreach (array('main', 'long', 'freq') as $key2) {
                $row->children[$key2] = new FieldSet(array(
                    'name' => $key2,
                    'caption' => $this->view->_('DIAGNOSTICS_TOP10') . ' ' 
                            . $this->view->_('DIAGNOSTICS_SET_' . strtoupper($key2)) . ' ' 
                            . $this->view->_('DIAGNOSTICS_SET_' . strtoupper($key)),
                    'meta' => array('Table' => new DiagTable(array('Set' => isset($Item->stat[$key][$key2]) ? $Item->stat[$key][$key2] : array()))),
                    'template' => 'dev_diag_set.inc.php'
                ));
            }
            $defaultParams['children'][$key] = $row;
        }
        $arr = $defaultParams;
        parent::__construct($arr);
    }
}