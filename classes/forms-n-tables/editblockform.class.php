<?php
namespace RAAS\CMS;
use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\Field as RAASField;
use \RAAS\Option;
use \RAAS\FieldSet;

class EditBlockForm extends \RAAS\Form
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Main::i();
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
        $Parent = $params['meta']['Parent'];
        $loc = $Item->location ? $Item->location : (isset($_GET['loc']) ? $_GET['loc'] : '');
        $defaultParams = array(
            'caption' => $Item->id ? $this->view->_('EDITING_BLOCK') : $this->view->_('CREATING_BLOCK'),
            'data-block-type' => str_replace('RAAS\\CMS\\', '', $Item->block_type),
            'parentUrl' => Package::i()->url . '&id=' . (int)$Parent->id,
            'newUrl' => Package::i()->url . '&pid=%s&action=edit_block&pid=' . (int)$Parent->id . '&type=' . str_replace('\\', '.', str_replace('RAAS\\CMS\\', '', $Item->block_type)) . '&loc=' . $loc,
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
        $this->meta['CONTENT']['cats'] = array('Set' => $temp->children, 'additional' => function($row) { return array('data-group' => $row->template); });
        foreach ($this->meta['Parent']->Template->locations as $key => $val) {
            $this->meta['CONTENT']['locations'][] = array('value' => $key, 'caption' => $key);
        }
        $this->children['commonTab'] = $this->getCommonTab();
        if (isset(Application::i()->packages['cms']->modules['users'])) {
            $this->children['accessTab'] = new CMSAccessFormTab($params);
        }
        $this->children['serviceTab'] = $this->getServiceTab();
        $this->children['pagesTab'] = $this->getPagesTab();
        if ($this->Item->id) {
            $this->children['serviceTab']->children['post_date'] = new RAASField(array(
                'name' => 'post_date', 'caption' => $this->view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php'
            ));
            $this->children['serviceTab']->children['modify_date'] = new RAASField(array(
                'name' => 'modify_date', 'caption' => $this->view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php'
            ));
        }
        

        $interfaceField = $this->getInterfaceField();
        $interfaceField->name = 'cache_interface_id';
        $interfaceField->caption = $this->view->_('CACHE_INTERFACE');
        $interfaceField->placeholder = null;
        $s = Snippet::importByURN('__raas_cache_interface');
        $interfaceField->default = $s->id;
        $interfaceField->required = false;

        $this->children['serviceTab']->children['cache_type'] = array(
            'type' => 'select', 
            'name' => 'cache_type', 
            'caption' => $this->view->_('CACHE_TYPE'), 
            'children' => array(
                array('value' => Block::CACHE_NONE, 'caption' => $this->view->_('_NONE')),
                array('value' => Block::CACHE_DATA, 'caption' => $this->view->_('CACHE_DATA')),
                array('value' => Block::CACHE_HTML, 'caption' => $this->view->_('CACHE_HTML')),
            ),
            'default' => Block::CACHE_NONE
        );
        $this->children['serviceTab']->children['cache_single_page'] = array(
            'type' => 'checkbox', 'name' => 'cache_single_page', 'caption' => $this->view->_('CACHE_BY_SINGLE_PAGES')
        );
        $this->children['serviceTab']->children['cache_interface_id'] = $interfaceField;
    }


    protected function getInterfaceField()
    {
        $wf = function(Snippet_Folder $x) use (&$wf) {
            $temp = array();
            foreach ($x->children as $row) {
                if (strtolower($row->urn) != '__raas_views') {
                    $o = new Option(array('value' => '', 'caption' => $row->name, 'disabled' => 'disabled'));
                    $o->__set('children', $wf($row));
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
            'required' => true,
            'caption' => $this->view->_('INTERFACE'), 
            'placeholder' => $this->view->_('_NONE'), 
            'children' => $wf(new Snippet_Folder())
        ));
        return $field;
    }


    protected function getWidgetField()
    {
        $wf = function(Snippet_Folder $x) use (&$wf) {
            $temp = array();
            foreach ($x->children as $row) {
                if (strtolower($row->urn) != '__raas_interfaces') {
                    $o = new Option(array('value' => '', 'caption' => $row->name, 'disabled' => 'disabled'));
                    $o->__set('children', $wf($row));
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
            'required' => true,
            'caption' => $this->view->_('WIDGET'), 
            'placeholder' => $this->view->_('_NONE'), 
            'children' => $wf(new Snippet_Folder())
        ));
        return $field;
    }


    protected function getPagesVarField()
    {
        $field = new RAASField(array('name' => 'pages_var_name', 'caption' => $this->view->_('PAGES_VAR_NAME'), 'default' => 'page'));
        return $field;
    }


    protected function getRowsPerPageField()
    {
        $field = new RAASField(array(
            'name' => 'rows_per_page', 'caption' => $this->view->_('ITEMS_PER_PAGE'), 'default' => Application::i()->registryGet('rowsPerPage')
        ));
        return $field;
    }


    protected function getCommonTab()
    {
        $tab = new FormTab(array(
            'name' => 'common', 
            'caption' => $this->view->_('GENERAL'),
            'children' => array(
                array('name' => 'name', 'caption' => $this->view->_('NAME'))
            )
        ));
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = new FormTab(array(
            'name' => 'service', 
            'caption' => $this->view->_('SERVICE'),
            'children' => array(
                array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_('VISIBLE'), 'default' => 1),
                array(
                    'type' => 'select',
                    'name' => 'vis_material',
                    'caption' => $this->view->_('VISIBILITY_WITH_ACTIVE_MATERIAL'),
                    'children' => array(
                        array('value' => Block::BYMATERIAL_BOTH, 'caption' => $this->view->_('BYMATERIAL_BOTH')),
                        array('value' => Block::BYMATERIAL_WITH, 'caption' => $this->view->_('BYMATERIAL_WITH')),
                        array('value' => Block::BYMATERIAL_WITHOUT, 'caption' => $this->view->_('BYMATERIAL_WITHOUT')),
                    ),
                )
            )
        ));
        return $tab;
    }


    protected function getPagesTab()
    {
        $tab = new FormTab(array('name' => 'pages', 'caption' => $this->view->_('PAGES')));
        $loc = $Item->location ? $Item->location : (isset($_GET['loc']) ? $_GET['loc'] : '');
        $tab->children[] = new RAASField(array('type' => 'checkbox', 'name' => 'inherit', 'caption' => $this->view->_('INHERIT')));
        $tab->children[] = new RAASField(array(
            'type' => 'select', 
            'name' => 'location', 
            'caption' => $this->view->_('LOCATION'), 
            'default' => $loc, 
            'placeholder' => '--', 
            'children' => $this->meta['CONTENT']['locations']
        ));
        $tab->children[] = new RAASField(array(
            'type' => 'checkbox', 
            'name' => 'cats', 
            'caption' => $this->view->_('PAGES'), 
            'multiple' => 'multiple', 
            'children' => $this->meta['CONTENT']['cats'],
            'check' => function($Field) {
                if (!isset($_POST['cats']) || !$_POST['cats']) {
                    return array('name' => 'MISSED', 'value' => $Field->name, 'description' => 'ERR_NO_PAGES');
                }
            },
            'import' => function($Field) { return $Field->Form->Item->pages_ids; },
            'default' => array((int)$this->meta['Parent']->id),
        ));
        return $tab;
    }
}