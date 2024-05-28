<?php
/**
 * Форма редактирования блока поиска
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\FormTab;

/**
 * Класс формы редактирования блока поиска
 */
class EditBlockSearchForm extends EditBlockForm
{
    protected function getInterfaceField(): RAASField
    {
        $field = parent::getInterfaceField();
        $snippet = Snippet::importByURN('__raas_search_interface');
        $field->default = $snippet->id;
        return $field;
    }


    protected function getCommonTab(): FormTab
    {
        $tab = parent::getCommonTab();
        $tmp_page = new Page();
        $this->meta['CONTENT']['pages'] = ['Set' => $tmp_page->children];
        $m = new Material_Type();
        foreach ($this->view->availableLanguages as $key => $val) {
            $this->meta['CONTENT']['languages'][] = [
                'value' => $key,
                'caption' => $val
            ];
        }
        $tab->children[] = new RAASField([
            'name' => 'search_var_name',
            'caption' => $this->view->_('SEARCH_VAR_NAME'),
            'default' => 'search_string'
        ]);
        $tab->children[] = new RAASField([
            'name' => 'min_length',
            'caption' => $this->view->_('MIN_SEARCH_QUERY_LENGTH'),
            'default' => 3
        ]);
        $tab->children[] = new RAASField([
            'type' => 'checkbox',
            'name' => 'mtypes',
            'caption' => $this->view->_('LIMIT_TO_MATERIAL_TYPES'),
            'multiple' => 'multiple',
            'children' => ['Set' => $m->children]
        ]);
        $tab->children[] = new RAASField([
            'type' => 'checkbox',
            'name' => 'languages',
            'caption' => $this->view->_('LIMIT_TO_LANGUAGE'),
            'multiple' => 'multiple',
            'children' => $this->meta['CONTENT']['languages']
        ]);
        $tab->children[] = new RAASField([
            'type' => 'checkbox',
            'name' => 'search_pages_ids',
            'caption' => $this->view->_('LIMIT_TO_PAGES'),
            'multiple' => 'multiple',
            'children' => $this->meta['CONTENT']['pages']
        ]);
        $tab->children[] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab(): FormTab
    {
        $tab = parent::getServiceTab();
        $tab->children[] = $this->getPagesVarField();
        $tab->children[] = $this->getRowsPerPageField();
        $tab->children[] = $this->getInterfaceField();
        return $tab;
    }


    protected function getPagesVarField(): RAASField
    {
        $field = parent::getPagesVarField();
        $field->default = 'page';
        return $field;
    }


    /**
     * Получает поле "Количество записей на странице (0 — все)"
     * @return RAASField
     */
    protected function getRowsPerPageField(): RAASField
    {
        $field = parent::getRowsPerPageField();
        $field->default = Application::i()->registryGet('rowsPerPage');
        return $field;
    }
}
