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
    const blockListItemClass = 'cms-block-form';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_FORM'));
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
