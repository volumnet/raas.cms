<?php
namespace RAAS\CMS;
use \RAAS\Field as RAASField;

class EditBlockSearchForm extends EditBlockForm
{
    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        $snippet = Snippet::importByURN('__RAAS_search_interface');
        $field->default = $snippet->id;
        return $field;
    }


    protected function getInterfaceCodeField()
    {
        $field = parent::getInterfaceCodeField();
        $snippet = Snippet::importByURN('__RAAS_search_interface');
        $field->default = $snippet->description;
        return $field;
    }


    protected function getWidgetCodeField()
    {
        $field = parent::getWidgetCodeField();
        $field->default = Package::i()->stdSearchView;
        return $field;
    }


    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        $tmp_page = new Page();
        $this->meta['CONTENT']['pages'] = array('Set' => $tmp_page->children);
        $this->meta['CONTENT']['material_types'] = array(
            'Set' => array_merge(array(new Material_Type(array('id' => 0, 'name' => $this->_view->_('PAGES')))), Material_Type::getSet())
        );
        foreach ($this->_view->availableLanguages as $key => $val) {
            $this->meta['CONTENT']['languages'][] = array('value' => $key, 'caption' => $val);
        }
        $tab->children[] = new RAASField(array('name' => 'search_var_name', 'caption' => $this->_view->_('SEARCH_VAR_NAME'), 'default' => 'search_string'));
        $tab->children[] = new RAASField(array('name' => 'min_length', 'caption' => $this->_view->_('MIN_SEARCH_QUERY_LENGTH'), 'default' => 3));
        $tab->children[] = new RAASField(array(
            'type' => 'checkbox', 
            'name' => 'mtypes', 
            'caption' => $this->_view->_('LIMIT_TO_MATERIAL_TYPES'), 
            'multiple' => 'multiple', 
            'children' => $this->meta['CONTENT']['material_types']
        ));
        $tab->children[] = new RAASField(array(
            'type' => 'checkbox', 
            'name' => 'languages', 
            'caption' => $this->_view->_('LIMIT_TO_LANGUAGE'), 
            'multiple' => 'multiple', 
            'children' => $this->meta['CONTENT']['languages']
        ));
        $tab->children[] = new RAASField(array(
            'type' => 'checkbox', 
            'name' => 'search_pages_ids', 
            'caption' => $this->_view->_('LIMIT_TO_PAGES'), 
            'multiple' => 'multiple', 
            'children' => $this->meta['CONTENT']['pages']
        ));
        $tab->children[] = $this->getWidgetField();
        $tab->children[] = $this->getWidgetCodeField();
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = parent::getServiceTab();
        $tab->children[] = $this->getPagesVarField();
        $tab->children[] = $this->getRowsPerPageField();
        $tab->children[] = $this->getInterfaceField();
        $tab->children[] = $this->getInterfaceCodeField();
        return $tab;
    }
}