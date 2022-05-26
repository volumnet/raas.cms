<?php
/**
 * Перемещение страницы
 */
namespace RAAS\CMS;

use SOME\HTTP;

/**
 * Возвращает дерево узлов для перемещения
 * @param Page $node Текущий узел
 * @param array<int> $ids Список ID# переносимых узлов
 * @param array<int> $pids Список ID# родительских узлов к переносимым
 * @param array<int> $actives Список ID# переносимых узлов и всех их
 *                            родительских всех уровней
 * @return string
 */
function showMoveMenu(Page $node, array $ids, array $pids, array $actives)
{
    static $level = 0;
    foreach ($node->children as $row) {
        $active = in_array($row->id, $actives);
        $text .= '<li class="' . ((!$row->vis || !$row->pvis) ? ' cms-invis' : '') . (!$row->pvis ? ' cms-inpvis' : '') . ($active ? ' active' : '') . '">';
        if (in_array($row->id, $pids)) {
            $text .= '<span>' . htmlspecialchars($row->name) . '</span>';
        } elseif (in_array($row->id, $ids)) {
            $text .= '<b>' . htmlspecialchars($row->name) . '</b>';
        } else {
            $text .= '<a href="' . HTTP::queryString('new_pid=' . (int)$row->id) . '">'
                  .     htmlspecialchars($row->name)
                  .  '</a>';
        }
        if (!in_array($row->id, $ids)) {
            $level++;
            $text .= showMoveMenu($row, $ids, $pids, $actives);
            $level--;
        }
        $text .= '</li>';
    }

    if ($text) {
        // if ($level) {
            $text = '<ul>' . $text . '</ul>';
        // } else {
        //     $text = '<ul class="tree" data-role="move-menu" style="margin-bottom: 20px">'
        //           .    $text
        //           . '</ul>';
        // }
    }
    return $text;
}
?>
<p><?php echo \CMS\CHOOSE_NEW_PARENT?>:</p>
<ul class="tree" data-role="move-menu" style="margin-bottom: 20px">
  <li class="active">
    <a href="<?php echo HTTP::queryString('new_pid=0')?>">
      <?php echo \CMS\ROOT_SECTION?>
    </a>
  </li>
  <?php echo showMoveMenu(new Page(), $ids, $pids, $actives)?>
</ul>
<script>
jQuery(document).ready(function($) {
    $('[data-role="move-menu"]').RAAS_menuTree();
});
</script>
