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
                $NS = \SOME\Namespaces::getNS($this);
                if ($NS == __NAMESPACE__) {
                    return Package::i()->view;
                } else {
                    $classname = $NS . '\\Module';
                    return $classname::i()->view;
                }
                break;
        }
    }


    public function renderBlock(Block $Item, Page $Page, Location $Location, $i = 0)
    {
        // Заменил $this->view на Package::i()->view, т.к. блоки создаются из основного пакета
        $text .= '<div class="well well-small cms-block ' . static::blockListItemClass . '" id="block-' . (int)$Item->id . '" title="' . ($Item->title) . '">
                    <a class="cms-block-name" href="' . Package::i()->view->url . '&action=edit_block&id=' . (int)$Item->id . '&pid=' . (int)$Page->id . '">
                      <span' . (!$Item->vis ? ' class="muted"' : '') . '>' . ($Item->title) . '</span>
                    </a>';
        if ($temp = ViewSub_Main::i()->getBlockContextMenu($Item, $Page, $i, count($Page->blocksByLocations[$Location->urn]))) {
            $f = function($x) { return array('text' => '<i class="icon-' . $x['icon'] . '"></i>&nbsp;' . $x['name'], 'href' => $x['href'], 'onclick' => $x['onclick']); };
            $temp = array_map($f, $temp);
            $temp = json_encode($temp);
            $text .= '<script type="text/javascript">
            jQuery(document).ready(function($) { 
                var temp = ' . $temp . ';
                for (var i = 0; i < temp.length; i++) {
                    if (temp[i].onclick) {
                        temp[i].action = new Function("e", temp[i].onclick);
                    }
                }
                console.log(temp);
                context.attach("#block-' . (int)$Item->id . '", temp) 
            })
            </script>';
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
                // Заменил $this->view на Package::i()->view, т.к. блоки создаются из основного пакета
                'href' => \SOME\HTTP::queryString('type=' . $type, false, Package::i()->view->url . '&action=edit_block&pid=' . (int)$Page->id . '&loc=' . urlencode($Location->urn))
            )
        );
    }
}