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
use SOME\Text;

$useAjax = true;

$ajax = (bool)stristr($Page->url, '/ajax/') || (($_GET['AJAX'] ?? '') == $Block->id);

/**
 * Возвращает код закрытия меню
 */
$showCloseButton = function () {
    $result = ' <button type="button" class="menu-mobile__close menu-mobile__close-link btn-close"></button>';
    return $result;
};

/**
 * Возвращает внутренний заголовок
 * @param string $url Ссылка
 * @param string $text Заголовок
 * @param bool $vueText Текст отображается через Vue.JS
 * @param bool $vueLink Ссылка отображается через Vue.JS
 * @return string
 */
$showInnerHeader = function (
    $url,
    $text,
    $vueText = false,
    $vueLink = false
) use (
    &$showCloseButton
) {
    $result = ' <button type="button" class="menu-mobile__back menu-mobile__back-link"></button>
                <a
                  class="menu-mobile__title"
                  ' . ($vueLink ? 'data-v-bind_' : '') . 'href="' . htmlspecialchars($url) . '"
                  ' . ($vueText ? ' data-v-html="' . htmlspecialchars($text) . '"' : '') . '
                >';
    if (!$vueText) {
        $result .= htmlspecialchars($text);
    }
    $result .=   '</a>';
    $result .= $showCloseButton();
    return $result;
};

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
$showMenu = function ($node, Page $current) use (
    &$showMenu,
    $useAjax,
    $ajax,
    &$phone,
    &$showInnerHeader,
    &$showCloseButton
) {
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
            $text .= '<div class="menu-mobile__logo">'
                  .     $current->location('logo')
                  .  '</div>'
                  .   $showCloseButton();
        } else {
            $text .= $showInnerHeader($nodeUrl, $nodeName);
        }
        $text .= '   </li>';
        if (!$level && $phone) {
            if ($phone) {
                $text .= '<li class="
                            menu-mobile__item
                            menu-mobile__item_main
                            menu-mobile__item_level_0
                            menu-mobile__item_phone
                          ">
                            <a
                              class="
                                menu-mobile__link
                                menu-mobile__link_main
                                menu-mobile__link_level_0
                                menu-mobile__link_phone
                              "
                              href="tel:%2B7' . Text::beautifyPhone($phone) . '"
                            >'
                      .       htmlspecialchars($phone)
                      .    '</a>
                          </li>';
            }
            if (class_exists('RAAS\CMS\Users\Module')) {
                $text .= '<li
                            data-v-if="user.id"
                            class="
                              menu-mobile__item
                              menu-mobile__item_level_0
                              menu-mobile__item_main
                              menu-mobile__item_user
                              menu-mobile__item_has-children
                            "
                          >
                            <a
                              href="/profile/"
                              class="
                                menu-mobile__link
                                menu-mobile__link_level_0
                                menu-mobile__link_main
                                menu-mobile__link_has-children
                                menu-mobile__link_user
                              "
                              data-v-html="user.first_name || user.full_name"
                            ></a>
                            <button
                              type="button"
                              class="
                                menu-mobile__children-trigger
                                menu-mobile__children-trigger_main
                                menu-mobile__children-trigger_level_0
                              "
                            ></button>
                            <ul class="menu-mobile__list menu-mobile__list_level_1 menu-mobile__list_inner">
                              <li class="menu-mobile__header">
                                ' . $showInnerHeader('/profile/', 'user.first_name || user.full_name', true) . '
                              </li>
                              <li class="menu-mobile__item menu-mobile__item_level_1 menu-mobile__item_inner">
                                <a
                                  href="/profile/"
                                  class="menu-mobile__link menu-mobile__link_level_1 menu-mobile__link_inner"
                                >
                                  ' . EDIT_PROFILE . '
                                </a>
                              </li>';
                if (class_exists('RAAS\CMS\Shop\Module')) {
                    $text .= '<li class="menu-mobile__item menu-mobile__item_level_1 menu-mobile__item_inner">
                                <a
                                  href="/my-orders/"
                                  class="menu-mobile__link menu-mobile__link_level_1 menu-mobile__link_inner"
                                >
                                  ' . MY_ORDERS . '
                                </a>
                              </li>';
                }
                $text .= '    <li class="menu-mobile__item menu-mobile__item_level_1 menu-mobile__item_inner">
                                <a
                                  href="/login/?logout=1"
                                  class="menu-mobile__link menu-mobile__link_level_1 menu-mobile__link_inner"
                                >
                                  ' . LOG_OUT . '
                                </a>
                              </li>
                            </ul>
                          </li>
                          <li
                            data-v-else
                            class="
                              menu-mobile__item
                              menu-mobile__item_level_0
                              menu-mobile__item_main
                              menu-mobile__item_user
                            "
                          >
                            <a
                              href="/login/"
                              class="
                                menu-mobile__link
                                menu-mobile__link_level_0
                                menu-mobile__link_main
                                menu-mobile__link_user
                              "
                            >
                              ' . LOG_IN . '
                            </a>
                          </li>';
            }
        }
    }
    for ($i = 0; $i < count($children); $i++) {
        $row = $children[$i];
        // $level++;
        // $ch = $showMenu($row, $current);
        // $level--;
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
        $urn = array_shift(array_reverse(explode('/', trim($url, '/'))));
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
        if (!$useAjax || $ajax || !stristr($url, '/catalog/')) { // Для подгрузки AJAX'ом
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
              .  '  <' . ((!$active && $url) ? 'a' : 'span') . '
                      class="' . implode(' ', $aClasses) . '"
                      ' . (($active || !$url) ? '' : ' href="' . htmlspecialchars($url) . '"') . '
                    >'
              .       htmlspecialchars($name)
              .  '  </' . ((!$active && $url) ? 'a' : 'span') . '>';
        if ($ch) {
            $text .= '<button
                        type="button"
                        class="
                          menu-mobile__children-trigger
                          menu-mobile__children-trigger_' . ($level ? 'inner' : 'main') . '
                          menu-mobile__children-trigger_level_' . (int)$level . '
                        "
                      ></button>'
                  .  $ch;

        }
        $text .= '</li>';
    }
    $ulClasses = array(
        'menu-mobile__list',
        'menu-mobile__list_' . (!$level ? 'main' : 'inner'),
        'menu-mobile__list_level_' . $level
    );
    if ($text) {
        return '<ul class="' . implode(' ', $ulClasses) . '">' . $text . '</ul>';
    }
};

$companyMaterialType = Material_Type::importByURN('company');
$company = Material::getSet([
    'where' => "pid = " . (int)$companyMaterialType->id,
    'orderBy' => "NOT priority, priority",
    'limit' => 1,
])[0];
$phone = (array)$company->phone;
$phone = $phone[0];
?>
<nav
  class="menu-mobile"
  data-vue-role="menu-mobile"
  data-v-bind_page-id="<?php echo (int)$Page->id?>"
  data-v-bind_block-id="<?php echo (int)$Block->id?>"
  data-v-bind_use-ajax="<?php echo htmlspecialchars(json_encode($useAjax))?>"
  data-v-slot="vm"
>
  <button type="button" class="menu-mobile__trigger" data-v-on_click.stop="jqEmit('raas.openmobilemenu')"></button>
  <?php echo $showMenu($menuArr ?: $Item, $Page)?>
</nav>
