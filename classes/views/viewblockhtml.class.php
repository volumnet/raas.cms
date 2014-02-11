<?php
namespace RAAS\CMS;

class ViewBlockHTML extends ViewBlock
{
    const blockListItemClass = 'cms-block-html';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_HTML'));
    }


    public function locationContextMenu(Page $Page, Location $Location)
    {
        return parent::locationContextMenu($Page, $Location, $this->view->_('ADD_HTML_BLOCK'), '');
    }
}