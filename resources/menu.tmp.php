<?php
namespace RAAS\CMS;

use SOME\HTTP;

$showMenu = function($node, Page $current) use (&$showMenu) {
    static $level = 0;
    if ($node instanceof Menu) {
        $children = $node->visSubMenu;
    } else {
        $children = (isset($node['children']) && is_array($node['children'])) ? $node['children'] : array();
    }
    for ($i = 0; $i < count($children); $i++) {
        $row = $children[$i];
        $level++;
        $ch = $showMenu($row, $current);
        $level--;
        if ($node instanceof Menu) {
            $url = $row->url;
            $name = $row->name;
        } else {
            $url = $row['url'];
            $name = $row['name'];
        }
        $active = ($url == HTTP::queryString('', true));
        $semiactive = preg_match('/^' . preg_quote($url, '/') . '/umi', HTTP::queryString('', true)) && ($url != '/') && !$active;
        if (preg_match('/class="[\\w\\- ]*?active[\\w\\- ]*?"/umi', $ch)) {
            $semiactive = true;
        }
        $liClasses = array(
            '{MENU_NAME}__item',
            '{MENU_NAME}__item_' . (!$level ? 'main' : 'inner'),
            '{MENU_NAME}__item_level_' . $level
        );
        $aClasses = array(
            '{MENU_NAME}__link',
            '{MENU_NAME}__link_' . (!$level ? 'main' : 'inner'),
            '{MENU_NAME}__link_level_' . $level
        );
        if ($active) {
            $liClasses[] = '{MENU_NAME}__item_active';
            $aClasses[] = '{MENU_NAME}__link_active';
        } elseif ($semiactive) {
            $liClasses[] = '{MENU_NAME}__item_semiactive';
            $aClasses[] = '{MENU_NAME}__link_semiactive';
        }
        $text .= '<li class="' . implode(' ', $liClasses) . '">'
              .  '  <a class="' . implode(' ', $aClasses) . '" ' . ($active ? '' : ' href="' . htmlspecialchars($url) . '"') . '>' . htmlspecialchars($name) . '</a>'
              .     $ch
              .  '</li>';
    }
    $ulClasses = array(
        '{MENU_NAME}__list',
        '{MENU_NAME}__list_' . (!$level ? 'main' : 'inner'),
        '{MENU_NAME}__list_level_' . $level
    );
    return $text ? '<ul class="' . implode(' ', $ulClasses) . '">' . $text . '</ul>' : $text;
};

echo '<nav class="{MENU_NAME}">' . $showMenu($menuArr ?: $Item, $Page) . '</nav>';
