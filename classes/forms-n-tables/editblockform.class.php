<?php
namespace RAAS\CMS;
use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\Field as RAASField;
use \RAAS\Option;

class EditBlockForm extends \RAAS\Form
{
    protected $_view;

    public function __construct(array $params = array())
    {
        $this->_view = isset($params['view']) ? $params['view'] : null;
        unset($params['view']);
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $loc = $Item->location ? $Item->location : (isset($_GET['loc']) ? $_GET['loc'] : '');
        $defaultParams = array(
            'caption' => $Item->id ? $this->_view->_('EDITING_BLOCK') : $this->_view->_('CREATING_BLOCK'),
            'data-block-type' => str_replace('RAAS\\CMS\\', '', $Item->block_type),
            'parentUrl' => $this->url . '&id=' . (int)$Parent->id,
            'newUrl' => $this->url . '&pid=%s&action=edit_block&pid=' . (int)$Parent->id . '&type=' . str_replace('\\', '.', str_replace('RAAS\\CMS\\', '', $Item->block_type)) . '&loc=' . $loc,
            'export' => function($Form) use ($t) {
                $Form->exportDefault();
                $Form->Item->editor_id = Application::i()->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            }
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
        $this->meta['CONTENT'] = array();
        $temp = new Page();
        $this->meta['CONTENT']['cats'] = array('Set' => $temp->children);
        foreach ($this->meta['Parent']->Template->locations as $key => $val) {
            $this->meta['CONTENT']['locations'][] = array('value' => $key, 'caption' => $key);
        }
        $this->children['commonTab'] = $this->getCommonTab();
        $this->children['serviceTab'] = $this->getServiceTab();
        $this->children['pagesTab'] = $this->getPagesTab();
        if ($this->Item->id) {
            $this->children['serviceTab']->children[] = new RAASField(array(
                'name' => 'post_date', 'caption' => $this->_view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php'
            ));
            $this->children['serviceTab']->children[] = new RAASField(array(
                'name' => 'modify_date', 'caption' => $this->_view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php'
            ));
        }
        
    }


    protected function getInterfaceField()
    {
        $wf = function(Snippet_Folder $x) use (&$wf) {
            $temp = array();
            foreach ($x->children as $row) {
                if ($row->urn != '__RAAS_views') {
                    $o = new Option(array('value' => '', 'caption' => $row->name, 'disabled' => 'disabled'));
                    $o->children = $wf($row);
                    $temp[] = $o;
                }
            }
            foreach ($x->snippets as $row) {
                $temp[] = new Option(array('value' => $row->id, 'caption' => $row->name));
            }
            return $temp;
        };
        $field = new RAASField(array(
            'type' => 'select',
            'class' => 'input-xxlarge',
            'name' => 'interface_id', 
            'caption' => $this->_view->_('INTERFACE'), 
            'placeholder' => $this->_view->_('_NONE'), 
            'children' => $wf(new Snippet_Folder())
        ));
        return $field;
    }


    protected function getInterfaceCodeField()
    {
        $field = new RAASField(array(
            'type' => 'codearea', 
            'name' => 'interface', 
            'export' => function($Field) {
                $Field->Form->Item->interface = '';
                if (!(isset($_POST['interface_id']) && (int)$_POST['interface_id']) && isset($_POST['interface'])) {
                    $Field->Form->Item->interface = (string)$_POST['interface'];
                }
            }, 
            'import' => function($Field) use ($t) { 
                return $Field->Form->Item->Interface->id ? $Field->Form->Item->Interface->description : $Field->Form->Item->interface; 
            }
        ));
        return $field;
    }


    protected function getWidgetField()
    {
        $wf = function(Snippet_Folder $x) use (&$wf) {
            $temp = array();
            foreach ($x->children as $row) {
                if ($row->urn != '__RAAS_interfaces') {
                    $o = new Option(array('value' => '', 'caption' => $row->name, 'disabled' => 'disabled'));
                    $o->children = $wf($row);
                    $temp[] = $o;
                }
            }
            foreach ($x->snippets as $row) {
                $temp[] = new Option(array('value' => $row->id, 'caption' => $row->name));
            }
            return $temp;
        };
        $field = new RAASField(array(
            'type' => 'select',
            'class' => 'input-xxlarge',  
            'name' => 'widget_id', 
            'caption' => $this->_view->_('WIDGET'), 
            'placeholder' => $this->_view->_('_NONE'), 
            'children' => $wf(new Snippet_Folder())
        ));
        return $field;
    }


    protected function getWidgetCodeField()
    {
        $field = new RAASField(array(
            'type' => 'codearea', 
            'name' => 'widget', 
            'export' => function($Field) {
                $Field->Form->Item->widget = '';
                if (!(isset($_POST['widget_id']) && (int)$_POST['widget_id']) && isset($_POST['widget'])) {
                    $Field->Form->Item->widget = (string)$_POST['widget'];
                }
            }, 
            'import' => function($Field) use ($t) { 
                return $Field->Form->Item->Widget->id ? $Field->Form->Item->Widget->description : $Field->Form->Item->widget; 
            }
        ));
        return $field;
    }


    protected function getPagesVarField()
    {
        $field = new RAASField(array('name' => 'pages_var_name', 'caption' => $this->_view->_('PAGES_VAR_NAME'), 'default' => 'page'));
        return $field;
    }


    protected function getRowsPerPageField()
    {
        $field = new RAASField(array(
            'name' => 'rows_per_page', 'caption' => $this->_view->_('ITEMS_PER_PAGE'), 'default' => Application::i()->registryGet('rowsPerPage')
        ));
        return $field;
    }


    protected function getCommonTab()
    {
        $tab = new FormTab(array(
            'name' => 'common', 
            'caption' => $this->_view->_('GENERAL'),
            'children' => array(
                array('name' => 'name', 'caption' => $this->_view->_('NAME'))
            )
        ));
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = new FormTab(array(
            'name' => 'service', 
            'caption' => $this->_view->_('SERVICE'),
            'children' => array(
                array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->_view->_('VISIBLE'), 'default' => 1)
            )
        ));
        return $tab;
    }


    protected function getPagesTab()
    {
        $tab = new FormTab(array('name' => 'pages', 'caption' => $this->_view->_('PAGES')));
        $loc = $Item->location ? $Item->location : (isset($_GET['loc']) ? $_GET['loc'] : '');
        $tab->children[] = new RAASField(array('type' => 'checkbox', 'name' => 'inherit', 'caption' => $this->_view->_('INHERIT')));
        $tab->children[] = new RAASField(array(
            'type' => 'select', 
            'name' => 'location', 
            'caption' => $this->_view->_('LOCATION'), 
            'default' => $loc, 
            'placeholder' => '--', 
            'children' => $this->meta['CONTENT']['locations']
        ));
        $tab->children[] = new RAASField(array(
            'type' => 'checkbox', 
            'name' => 'cats', 
            'caption' => $this->_view->_('PAGES'), 
            'multiple' => 'multiple', 
            'children' => $this->meta['CONTENT']['cats'],
            'check' =>function($Field) {
                if (!isset($_POST['cats']) || !$_POST['cats']) {
                    return array('name' => 'MISSED', 'value' => $Field->name, 'description' => 'ERR_NO_PAGES');
                }
            },
            'import' => function($Field) { return $Field->Form->Item->pages_ids; },
            'default' => array((int)$this->meta['Parent']->id)
        ));
        return $tab;
    }
}