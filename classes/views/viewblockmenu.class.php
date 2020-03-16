<?php
/**
 * Представление блока меню
 */
namespace RAAS\CMS;

/**
 * Класс представления блока меню
 */
class ViewBlockMenu extends ViewBlock
{
    const blockListItemClass = 'cms-block-menu';

    public function renderBlockTypeName()
    {
        return $this->view->_('BLOCK_LEGEND_MENU');
    }


    public function locationContextMenu(Page $page, Location $location)
    {
        return parent::locationContextMenu(
            $page,
            $location,
            $this->view->_('ADD_MENU_BLOCK'),
            'Block_Menu'
        );
    }
}
