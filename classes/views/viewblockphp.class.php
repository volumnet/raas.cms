<?php
namespace RAAS\CMS;

class ViewBlockPHP extends ViewBlock
{
    const blockListItemClass = 'cms-block-php';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_PHP'));
    }


    public function locationContextMenu(Page $Page, Location $Location)
    {
        return parent::locationContextMenu($Page, $Location, $this->view->_('ADD_PHP_BLOCK'), 'Block_PHP');
    }
}