<?php
/**
 * Представление блока материалов
 */
namespace RAAS\CMS;

/**
 * Класс представления блока материалов
 */
class ViewBlockMaterial extends ViewBlock
{
    const blockListItemClass = 'cms-block-material';

    public function renderBlockTypeName()
    {
        return $this->view->_('BLOCK_LEGEND_MATERIAL');
    }


    public function locationContextMenu(Page $page, Location $location)
    {
        return parent::locationContextMenu(
            $page,
            $location,
            $this->view->_('ADD_MATERIAL_BLOCK'),
            'Block_Material'
        );
    }
}
