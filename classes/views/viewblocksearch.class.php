<?php
/**
 * Представление блока поиска
 */
namespace RAAS\CMS;

/**
 * Класс представления блока поиска
 */
class ViewBlockSearch extends ViewBlock
{
    const BLOCK_LIST_ITEM_CLASS = 'cms-block_search';

    public function renderBlockTypeName()
    {
        return $this->view->_('BLOCK_LEGEND_SEARCH');
    }


    public function locationContextMenu(Page $page, Location $location)
    {
        return parent::locationContextMenu(
            $page,
            $location,
            $this->view->_('ADD_SEARCH_BLOCK'),
            'Block_Search'
        );
    }
}
