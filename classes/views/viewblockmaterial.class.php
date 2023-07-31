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
    const BLOCK_LIST_ITEM_CLASS = 'cms-block_material';

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
