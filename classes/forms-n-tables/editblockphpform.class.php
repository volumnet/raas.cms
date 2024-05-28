<?php
/**
 * Форма редактирования PHP-блока
 */
namespace RAAS\CMS;

use RAAS\FormTab;

/**
 * Класс формы редактирования PHP-блока
 */
class EditBlockPHPForm extends EditBlockForm
{
    protected function getCommonTab(): FormTab
    {
        $tab = parent::getCommonTab();
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
