<?php
namespace RAAS\CMS;
use \RAAS\Field as RAASField;

class EditBlockMenuForm extends EditBlockForm
{
    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        $snippet = Snippet::importByURN('__RAAS_menu_interface');
        $field->default = $snippet->id;
        return $field;
    }


    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        $tmp_menu = new Menu();
        $this->meta['CONTENT']['menus'] = array('Set' => $tmp_menu->visChildren, 'level' => 0);
        $this->meta['CONTENT']['menu_appearances'][] = array('value' => 1, 'caption' => $this->_view->_('FULL_MENU'));
        $this->meta['CONTENT']['menu_appearances'][] = array('value' => 0, 'caption' => $this->_view->_('PAGE_SUBSECTIONS'));
        $tab->children[] = new RAASField(array(
            'type' => 'select', 'name' => 'menu', 'caption' => $this->_view->_('MENU'), 'children' => $this->meta['CONTENT']['menus']
        ));
        $tab->children[] = new RAASField(array(
            'type' => 'select', 'name' => 'full_menu', 'caption' => $this->_view->_('MENU_APPEARANCE'), 'children' => $this->meta['CONTENT']['menu_appearances'], 'default' => 1)
        );
        $tab->children[] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = parent::getServiceTab();
        $tab->children[] = $this->getInterfaceField();
        return $tab;
    }
}