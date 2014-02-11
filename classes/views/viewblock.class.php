<?php
namespace RAAS\CMS;

abstract class ViewBlock
{
    const blockListItemClass = 'cms-block';

    public function __construct()
    {}

    
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return Package::i()->view;
                break;
        }
    }


    public function renderBlock(Block $Item, Page $Page, Location $Location)
    {
        $text .= '<div class="well well-small cms-block ' . static::blockListItemClass . '">';
        if (!$Location->horizontal) {
            $text .= '<a class="cms-block-name" href="' . $this->view->url . '&action=edit_block&id=' . (int)$Item->id . '&pid=' . (int)$Page->id . '" title="' . htmlspecialchars($Item->title) . '">
                        <span' . (!$Item->vis ? ' class="muted"' : '') . '>' . htmlspecialchars($Item->title) . '</span>
                      </a>';
        }
        if ($temp = $this->view->context->getBlockContextMenu($Item, $Page, $i, count($Page->blocksByLocations[$Location->urn]))) {
            $text .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="' . htmlspecialchars($Item->title) . '">
                        <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">' . showMenu($temp) . '</ul>';
        }
        $text .= '<input type="hidden" value="' . (int)$Item->id . '" />
                </div>';
        return $text;
    }

    
    public function renderLegend()
    {
        $name = func_get_arg(0);
        return '<div class="well well-small cms-block ' . static::blockListItemClass . '"><span class="cms-block-name">' . $name . '</span></div>';
    }

    
    public function locationContextMenu(Page $Page, Location $Location)
    {
        $name = func_get_arg(2);
        $type = func_get_arg(3);
        return array(
            array(
                'name' => $name, 
                'href' => \SOME\HTTP::queryString('type=' . $type, false, $this->view->url . '&action=edit_block&pid=' . (int)$Page->id . '&loc=' . urlencode($Location->urn))
            )
        );
    }
}