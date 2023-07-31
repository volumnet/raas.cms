<?php
/**
 * Представление PHP-блока
 */
namespace RAAS\CMS;

/**
 * Класс представления PHP-блока
 */
class ViewBlockPHP extends ViewBlock
{
    const BLOCK_LIST_ITEM_CLASS = 'cms-block_php';

    public function renderBlockTypeName()
    {
        return $this->view->_('BLOCK_LEGEND_PHP');
    }


    public function locationContextMenu(Page $page, Location $location)
    {
        return parent::locationContextMenu(
            $page,
            $location,
            $this->view->_('ADD_PHP_BLOCK'),
            'Block_PHP'
        );
    }
}
