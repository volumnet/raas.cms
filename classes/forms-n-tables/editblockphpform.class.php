<?php
namespace RAAS\CMS;

class EditBlockPHPForm extends EditBlockForm
{
    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        $tab->children[] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = parent::getServiceTab();
        $tab->children[] = $this->getInterfaceField();
        return $tab;
    }


    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        $field->required = false;
        return $field;
    }
}