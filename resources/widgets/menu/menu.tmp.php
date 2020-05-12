<?php
/**
 * {{MENU_NAME}}
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
            '{{MENU_CSS_CLASSNAME}}__item',
            '{{MENU_CSS_CLASSNAME}}__item_' . (!$level ? 'main' : 'inner'),
            '{{MENU_CSS_CLASSNAME}}__item_level_' . $level
        );
        $aClasses = array(
            '{{MENU_CSS_CLASSNAME}}__link',
            '{{MENU_CSS_CLASSNAME}}__link_' . (!$level ? 'main' : 'inner'),
            '{{MENU_CSS_CLASSNAME}}__link_level_' . $level
        );
        if ($active) {
            $liClasses[] = '{{MENU_CSS_CLASSNAME}}__item_active';
            $liClasses[] = '{{MENU_CSS_CLASSNAME}}__item_focused';
            $aClasses[] = '{{MENU_CSS_CLASSNAME}}__link_active';
        } elseif ($semiactive) {
            $liClasses[] = '{{MENU_CSS_CLASSNAME}}__item_semiactive';
            $liClasses[] = '{{MENU_CSS_CLASSNAME}}__item_focused';
            $aClasses[] = '{{MENU_CSS_CLASSNAME}}__link_semiactive';
        }
        if ($ch) {
            $liClasses[] = '{{MENU_CSS_CLASSNAME}}__item_has-children';
            $aClasses[] = '{{MENU_CSS_CLASSNAME}}__link_has-children';
        }
        $text .= '<li class="' . implode(' ', $liClasses) . '">'
              .  '  <a class="' . implode(' ', $aClasses) . '" ' . ($active ? '' : ' href="' . htmlspecialchars($url) . '"') . '>'
              .       htmlspecialchars($name)
              .       ($ch ? '<span class="{{MENU_CSS_CLASSNAME}}__children-trigger"></span>' : '')
              .  '  </a>'
              .     $ch
              .  '</li>';
    }
    $ulClasses = array(
        '{{MENU_CSS_CLASSNAME}}__list',
        '{{MENU_CSS_CLASSNAME}}__list_' . (!$level ? 'main' : 'inner'),
        '{{MENU_CSS_CLASSNAME}}__list_level_' . $level
    );
    return $text ? '<ul class="' . implode(' ', $ulClasses) . '">' . $text . '</ul>' : $text;
};
?>

<nav class="{{MENU_CSS_CLASSNAME}}">
  <?php echo $showMenu($menuArr ?: $Item, $Page)?>
</nav>
<?php echo Package::i()->asset('/js/{{MENU_CSS_CLASSNAME}}.js')?>
