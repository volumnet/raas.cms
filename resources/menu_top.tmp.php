<?php
$showMenu = function(\RAAS\CMS\Menu $node, \RAAS\CMS\Page $current) use (&$showMenu) {
    static $level = 0;
    $children = $node->visSubMenu;
    for ($i = 0; $i < count($children); $i++) {
        $row = $children[$i];
        if (($row->page_id == $current->id) || ($row->url == $current->url)) {
            $text .= '<li class="active"><a href="' . htmlspecialchars($row->url) . '">' . htmlspecialchars($row->name) . '</a>';
        } elseif ($row->findPage($current)) {
            $text .= '<li class="active"><a href="' . htmlspecialchars($row->url) . '">' . htmlspecialchars($row->name) . '</a>';
        } else {
            $text .= '<li><a href="' . htmlspecialchars($row->url) . '">' . htmlspecialchars($row->name) . '</a>';
        }
        if (1 || $row->findPage($current)) {
            $level++;
            $text .= $showMenu($row, $current);
            $level--;
        }
        $text .= '</li>';
    }
    return $text && $level ? '<ul>' . $text . '</ul>' : $text;
};

echo '<nav class="">' . $showMenu($Item, $Page) . '</nav>';