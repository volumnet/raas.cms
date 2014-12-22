<?php
$showMenu = function($node, \RAAS\CMS\Page $current) use (&$showMenu) {
    static $level = 0;
    if ($node instanceof \RAAS\CMS\Menu) {
        $children = $node->visSubMenu;
    } else {
        $children = (isset($node['children']) && is_array($node['children'])) ? $node['children'] : array();
    }
    for ($i = 0; $i < count($children); $i++) {
        $row = $children[$i];
        if ($node instanceof \RAAS\CMS\Menu) {
            $active = (($row->page_id == $current->id) || ($row->url == $current->url));
            $semiactive = $row->findPage($current);
            $url = $row->url;
            $name = $row->name;
        } else {
            $active = ($row['url'] == $current->url);
            $semiactive = (stristr($current->url, $row['url']));
            $url = $row['url'];
            $name = $row['name'];
        }
        if ($active) {
            $text .= '<li class="active"><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($name) . '</a>';
        } elseif ($semiactive) {
            $text .= '<li class="active"><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($name) . '</a>';
        } else {
            $text .= '<li><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($name) . '</a>';
        }
        if (1 || $semiactive) {
            $level++;
            $text .= $showMenu($row, $current);
            $level--;
        }
        $text .= '</li>';
    }
    return $text ? '<ul>' . $text . '</ul>' : $text;
};

echo '<nav class="">' . $showMenu($menuArr ?: $Item, $Page) . '</nav>';