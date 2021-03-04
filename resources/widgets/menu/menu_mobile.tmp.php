<?php
/**
 * Мобильное меню
 * @param Page $Page Текущая страница
 * @param Block_Menu $Block Текущий блок
 * @param array<[
 *            'name' => string Наименование пункта,
 *            'url' => string URL пункта,
 *            'children' =>? array рекурсивно такой же массив
 *        ]> $menuArr Меню данных массива
 * @param Menu $Item Текущее меню
 */
namespace RAAS\CMS;

use SOME\HTTP;

$ajax = (bool)stristr($Page->url, '/ajax/');

/**
 * Получает код списка меню
 * @param array<[
 *            'name' => string Наименование пункта,
 *            'url' => string URL пункта,
 *            'children' =>? array рекурсивно такой же массив
 *        ]>|Menu $node Текущий узел для получения кода
 * @param Page $current Текущая страница
 * @return string
 */
$showMenu = function($node, Page $current) use (&$showMenu, $ajax) {
    static $level = 0;
    if ($node instanceof Menu) {
        $children = $node->visSubMenu;
        $nodeName = $node->name;
        $nodeUrl = $node->url;
    } else {
        $children = (isset($node['children']) && is_array($node['children'])) ? $node['children'] : [];
        $nodeName = $node['name'];
        $nodeUrl = $node['url'];
    }
    if (!$level || $children) {
        $text = '   <li class="menu-mobile__header">';
        if (!$level) {
            $text .= '<div class="menu-mobile__logo"></div>';
        } else {
            $text .= ' <div class="menu-mobile__back">
                         <a class="menu-mobile__back-link"></a>
                       </div>
                       <div class="menu-mobile__title">
                         <a href="' . htmlspecialchars($nodeUrl) . '">
                           ' . htmlspecialchars($nodeName) . '
                         </a>
                       </div>';
        }
        $text .= '     <div class="menu-mobile__close">
                         <a class="menu-mobile__close-link"></a>
                       </div>
                     </li>';
        if (!$level) {
            $text .= '<li class="menu-mobile__item menu-mobile__item_main menu-mobile__item_level_0 menu-mobile__item_phone"></li>';
        }
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
        $urn = array_shift(array_reverse(explode('/', trim($url, '/'))));
        $active = $semiactive = false;
        if ($url == $current->url) {
            $active = true;
        } elseif (preg_match('/^' . preg_quote($url, '/') . '/umi', $current->url) &&
            ($url != '/')
        ) {
            $semiactive = true;
        }
        // 2021-02-23, AVS: заменил HTTP::queryString('', true) на $current->url,
        // чтобы была возможность использовать через AJAX
        $ch = '';
        if (1 || $ajax || !stristr($url, '/catalog/')) { // Для подгрузки AJAX'ом
            $level++;
            $ch = $showMenu($row, $current);
            $level--;
        }
        if (preg_match('/class="[\\w\\- ]*?active[\\w\\- ]*?"/umi', $ch)) {
            $semiactive = true;
        }
        $liClasses = array(
            'menu-mobile__item',
            'menu-mobile__item_' . (!$level ? 'main' : 'inner'),
            'menu-mobile__item_level_' . $level,
            'menu-mobile__item_' . $urn
        );
        $aClasses = array(
            'menu-mobile__link',
            'menu-mobile__link_' . (!$level ? 'main' : 'inner'),
            'menu-mobile__link_level_' . $level,
            'menu-mobile__link_' . $urn
        );
        if ($active) {
            $liClasses[] = 'menu-mobile__item_active';
            $aClasses[] = 'menu-mobile__link_active';
        } elseif ($semiactive) {
            $liClasses[] = 'menu-mobile__item_semiactive';
            $aClasses[] = 'menu-mobile__link_semiactive';
        }
        if ($ch) {
            $liClasses[] = 'menu-mobile__item_has-children';
            $aClasses[] = 'menu-mobile__link_has-children';
        }
        $text .= '<li class="' . implode(' ', $liClasses) . '">'
              .  '  <a class="' . implode(' ', $aClasses) . '" ' . ($active ? '' : ' href="' . htmlspecialchars($url) . '"') . '>'
              .       htmlspecialchars($name)
              .  '  </a>';
        if ($ch) {
            $text .= '<a href="#" class="menu-mobile__children-trigger menu-mobile__children-trigger_' . ($level ? 'inner' : 'main') . ' menu-mobile__children-trigger_level_' . (int)$level . '"></a>'
                  .  $ch;

        }
        $text .= '</li>';
    }
    $ulClasses = array(
        'menu-mobile__list',
        'menu-mobile__list_' . (!$level ? 'main' : 'inner'),
        'menu-mobile__list_level_' . $level
    );
    return $text ? '<ul class="' . implode(' ', $ulClasses) . '">' . $text . '</ul>' : $text;
};
?>
<a class="menu-trigger"></a>
<div data-vue-role="menu-mobile" data-vue-inline-template>
  <nav class="menu-mobile">
    <?php echo $showMenu($menuArr ?: $Item, $Page)?>
  </nav>
</div>
