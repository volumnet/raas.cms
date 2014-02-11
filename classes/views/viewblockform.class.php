<?php
namespace RAAS\CMS;

class ViewBlockForm extends ViewBlock
{
    const blockListItemClass = 'cms-block-form';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_FORM'));
    }


    public function locationContextMenu(Page $Page, Location $Location)
    {
        return parent::locationContextMenu($Page, $Location, $this->view->_('ADD_FORM_BLOCK'), 'Block_Form');
    }
}