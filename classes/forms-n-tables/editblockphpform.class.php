<?php
/**
 * Форма редактирования PHP-блока
 */
namespace RAAS\CMS;

/**
 * Класс формы редактирования PHP-блока
 */
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
}
