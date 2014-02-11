<?php
namespace RAAS\CMS;

class ViewBlockMenu extends ViewBlock
{
    const blockListItemClass = 'cms-block-menu';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_MENU'));
    }


    public function locationContextMenu(Page $Page, Location $Location)
    {
        return parent::locationContextMenu($Page, $Location, $this->view->_('ADD_MENU_BLOCK'), 'Block_Menu');
    }
}