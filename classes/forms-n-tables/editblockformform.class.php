<?php
namespace RAAS\CMS;
use \RAAS\Field as RAASField;
use \RAAS\CMS\Form as CMSForm;

class EditBlockFormForm extends EditBlockForm
{
    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        $snippet = Snippet::importByURN('__raas_form_interface');
        $field->default = $snippet->id;
        return $field;
    }


    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        $tab->children[] = new RAASField(array(
            'type' => 'select', 'name' => 'form', 'caption' => $this->view->_('FORM'), 'children' => array('Set' => CMSForm::getSet())
        ));
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