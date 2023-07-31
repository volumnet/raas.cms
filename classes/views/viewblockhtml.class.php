<?php
/**
 * Представление HTML-блока
 */
namespace RAAS\CMS;

/**
 * Класс представления HTML-блока
 */
class ViewBlockHTML extends ViewBlock
{
    const BLOCK_LIST_ITEM_CLASS = 'cms-block_html';

    public function renderBlockTypeName()
    {
        return $this->view->_('BLOCK_LEGEND_HTML');
    }


    public function locationContextMenu(Page $page, Location $location)
    {
        return parent::locationContextMenu(
            $page,
            $location,
            $this->view->_('ADD_HTML_BLOCK'),
            ''
        );
    }
}
