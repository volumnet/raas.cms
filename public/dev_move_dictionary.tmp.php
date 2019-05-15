<?php
/**
 * Перенос справочника
 */
namespace RAAS\CMS;

use SOME\HTTP;

/**
 * Возвращает дерево узлов для перемещения
 * @param Dictionary $node Текущий узел
 * @param array<int> $ids Список ID# переносимых узлов
 * @param array<int> $pids Список ID# родительских узлов к переносимым
 * @param array<int> $actives Список ID# переносимых узлов и всех их
 *                            родительских всех уровней
 * @return string
 */
function showMoveMenu(Dictionary $node, array $ids, array $pids, array $actives)
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
        $text = '<ul>' . $text . '</ul>';
    }
    return $text;
}
?>
<p><?php echo \CMS\CHOOSE_NEW_PARENT?>:</p>
<ul class="tree" data-raas-role="tree" style="margin-bottom: 20px">
  <li>
    <?php if (!$Item->pid) { ?>
        <span><?php echo \CMS\ROOT_SECTION?></span>
    <?php } else { ?>
        <a href="<?php echo HTTP::queryString('new_pid=0')?>">
          <?php echo \CMS\ROOT_SECTION?>
        </a>
    <?php } ?>
    <?php echo showMoveMenu(new Dictionary(), $ids, $pids, $actives)?>
  </li>
</ul>
