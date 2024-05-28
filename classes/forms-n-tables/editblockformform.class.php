<?php
/**
 * Форма редактирования блока с формой
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Field as RAASField;
use RAAS\CMS\Form as CMSForm;
use RAAS\FormTab;

/**
 * Класс формы редактирования блока с формой
 */
class EditBlockFormForm extends EditBlockForm
{
    protected function getInterfaceField(): RAASField
    {
        $field = parent::getInterfaceField();
        $snippet = Snippet::importByURN('__raas_form_interface');
        $field->default = $snippet->id;
        return $field;
    }


    protected function getCommonTab(): FormTab
    {
        $tab = parent::getCommonTab();
        $tab->children['form'] = new RAASField([
            'type' => 'select',
            'name' => 'form',
            'caption' => $this->view->_('FORM'),
            'children' => ['Set' => CMSForm::getSet()],
            'required' => true,
            'placeholder' => '--',
        ]);
        $tab->children['widget_id'] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab(): FormTab
    {
        $tab = parent::getServiceTab();
        $tab->children['interface_id'] = $this->getInterfaceField();
        return $tab;
    }
}
