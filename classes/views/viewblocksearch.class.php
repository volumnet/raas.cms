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
    const blockListItemClass = 'cms-block-search';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_SEARCH'));
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
