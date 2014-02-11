<?php
namespace RAAS\CMS;

class ViewBlockSearch extends ViewBlock
{
    const blockListItemClass = 'cms-block-search';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_SEARCH'));
    }


    public function locationContextMenu(Page $Page, Location $Location)
    {
        return parent::locationContextMenu($Page, $Location, $this->view->_('ADD_SEARCH_BLOCK'), 'Block_Search');
    }
}