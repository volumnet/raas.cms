<?php
/**
 * Меню подразделов
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
$showMenu = function ($node, Page $current) use (&$showMenu) {
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
            $page = $row->page;
        } else {
            $url = $row['url'];
            $name = $row['name'];
            $page = new Page($row['page_id']);
        }
        // 2021-02-23, AVS: заменил HTTP::queryString('', true) на $current->url,
        // чтобы была возможность использовать через AJAX
        // 2021-06-16, AVS: Заменили так, чтобы при активном материале ссылка не была активной
        $active = (!$ajax && ($url == $_SERVER['REQUEST_URI']));
        $semiactive = preg_match('/^' . preg_quote($url, '/') . '/umi', $current->url) && ($url != '/') && !$active;
        if (preg_match('/class="[\\w\\- ]*?active[\\w\\- ]*?"/umi', $ch)) {
            $semiactive = true;
        }
        $liClasses = array(
            'menu-sections__item',
            'menu-sections__item_' . (!$level ? 'main' : 'inner'),
            'menu-sections__item_level_' . $level
        );
        $aClasses = array(
            'menu-sections__link',
            'menu-sections__link_' . (!$level ? 'main' : 'inner'),
            'menu-sections__link_level_' . $level
        );
        if ($active) {
            $liClasses[] = 'menu-sections__item_active';
            $liClasses[] = 'menu-sections__item_focused';
            $aClasses[] = 'menu-sections__link_active';
        } elseif ($semiactive) {
            $liClasses[] = 'menu-sections__item_semiactive';
            $liClasses[] = 'menu-sections__item_focused';
            $aClasses[] = 'menu-sections__link_semiactive';
        }
        if ($ch) {
            $liClasses[] = 'menu-sections__item_has-children';
            $aClasses[] = 'menu-sections__link_has-children';
        }
        $text .= '<li class="' . implode(' ', $liClasses) . '">';
        ob_start();
        Snippet::importByURN('category')->process(['page' => $page]);
        $text .=    ob_get_clean()
              .  '</li>';
    }
    $ulClasses = array(
        'menu-sections__list',
        'menu-sections__list_' . (!$level ? 'main' : 'inner'),
        'menu-sections__list_level_' . $level
    );
    return $text ? '<ul class="' . implode(' ', $ulClasses) . '">' . $text . '</ul>' : $text;
};
?>

<nav class="menu-sections">
  <?php echo $showMenu($menuArr ?: $Item, $Page)?>
</nav>
<?php Package::i()->requestCSS('/css/menu-sections.css');
