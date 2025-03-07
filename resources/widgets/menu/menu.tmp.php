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
use RAAS\AssetManager;

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
$showMenu = function ($node, Page $current) use (&$showMenu, $ajax) {
    static $level = 0;
    $text = '';
    if ($node instanceof Menu) {
        $children = $node->visSubMenu;
    } else {
        $children = (isset($node['children']) && is_array($node['children'])) ? $node['children'] : array();
    }
    for ($i = 0; $i < count($children); $i++) {
        $row = $children[$i];
        if ($node instanceof Menu) {
            $url = $row->url;
            $name = $row->name;
        } else {
            $url = $row['url'];
            $name = $row['name'];
        }
        if ($url == '#') {
            $url = '';
        }
        $active = $semiactive = false;
        // 2021-02-23, AVS: заменил HTTP::queryString('', true) на $current->url,
        // чтобы была возможность использовать через AJAX
        // 2021-06-16, AVS: Заменил ($url == $current->url) на (!$ajax && ($url == $_SERVER['REQUEST_URI'])),
        // чтобы при активном материале ссылка не была активной
        if (!$ajax && ($url == $_SERVER['REQUEST_URI'])) {
            $active = true;
        } elseif (preg_match('/^' . preg_quote($url, '/') . '/umi', $current->url) && $url && ($url != '/')) {
            $semiactive = true;
        }
        $ch = '';
        if (1 || $active || $semiactive || $ajax || !stristr($url, '/catalog/')) { // Для подгрузки AJAX'ом
            $level++;
            $ch = $showMenu($row, $current);
            $level--;
        }
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
              .  '  <' . ((!$active && $url) ? 'a' : 'span') . '
                      class="' . implode(' ', $aClasses) . '"
                      ' . (($active || !$url) ? '' : ' href="' . htmlspecialchars($url) . '"') . '
                    >'
              .       htmlspecialchars($name)
              .       ($ch ? '<span class="{{MENU_CSS_CLASSNAME}}__children-trigger"></span>' : '')
              .  '  </' . ((!$active && $url) ? 'a' : 'span') . '>'
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
