<?php
/**
 * Представление блока формы
 */
namespace RAAS\CMS;

/**
 * Класс представления блока формы
 */
class ViewBlockForm extends ViewBlock
{
    const BLOCK_LIST_ITEM_CLASS = 'cms-block_form';

    public function renderBlockTypeName()
    {
        return $this->view->_('BLOCK_LEGEND_FORM');
    }


    public function locationContextMenu(Page $page, Location $location)
    {
        return parent::locationContextMenu(
            $page,
            $location,
            $this->view->_('ADD_FORM_BLOCK'),
            'Block_Form'
        );
    }
}
