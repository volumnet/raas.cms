<?php
namespace RAAS\CMS;

class ViewBlockMaterial extends ViewBlock
{
    const blockListItemClass = 'cms-block-material';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_MATERIAL'));
    }


    public function locationContextMenu(Page $Page, Location $Location)
    {
        return parent::locationContextMenu($Page, $Location, $this->view->_('ADD_MATERIAL_BLOCK'), 'Block_Material');
    }
}