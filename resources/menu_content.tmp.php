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
        $level++;
        $ch = $showMenu($row, $current);
        $level--;
        if ($node instanceof \RAAS\CMS\Menu) {
            $url = $row->url;
            $name = $row->name;
            $active = (($row->page_id == $current->id) || ($row->url == $current->url));
            $semiactive = $row->findPage($current);
        } else {
            $url = $row['url'];
            $name = $row['name'];
            $active = ($row['url'] == $current->url);
            $semiactive = false;
        }
        if (stristr($ch, 'class="active"')) {
            $semiactive = true;
        }
        $text .= '<li' . ($active || $semiactive ? ' class="active"' : '') . '>'
              .  '  <a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($name) . '</a>'
              .     $ch
              .  '</li>';
    }
    return $text ? '<ul>' . $text . '</ul>' : $text;
};

echo '<nav class="menu_content">' . $showMenu($menuArr ?: $Item, $Page) . '</nav>';