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
    const blockListItemClass = 'cms-block-html';

    public function renderLegend()
    {
        return parent::renderLegend($this->view->_('BLOCK_LEGEND_HTML'));
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
