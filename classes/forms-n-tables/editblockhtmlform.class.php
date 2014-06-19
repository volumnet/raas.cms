<?php
namespace RAAS\CMS;

class EditBlockHTMLForm extends EditBlockForm
{
    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        $tab->children[] = array('type' => 'htmlarea', 'name' => 'description');
        return $tab;
    }
}