<?php
/**
 * Перенос типа материалов
 */
namespace RAAS\CMS;

use SOME\HTTP;

/**
 * Отображает дерево типов материалов
 * @param Material_Type $node Текущий узел
 * @param array<int> $ids Список ID# переносимых узлов
 * @param array<int> $pids Список ID# родительских узлов к переносимым
 * @return string
 */
function showMoveMaterialType(Material_Type $node, array $ids)
{
    static $level = 0;
    foreach ($node->children as $row) {
        $active = in_array($row->id, $ids);
        $text .= '<li class="' . ($active ? ' active' : '') . '">';
        if ($active) {
            $text .= '<span>' . htmlspecialchars($row->name) . '</span>';
        } else {
            $text .= '<a href="' . HTTP::queryString('new_pid=' . (int)$row->id) . '">'
                  .     htmlspecialchars($row->name)
                  .  '</a>';
        }
        if (!in_array($row->id, $ids)) {
            $level++;
            $text .= showMoveMaterialType($row, $ids);
            $level--;
        }
        $text .= '</li>';
    }

    if ($text) {
        $text = '<ul' . (!$level ? ' class="tree" data-role="move-material-type" style="margin-bottom: 20px"' : '') . '>'
              .    $text
              . '</ul>';
    }
    return $text;
}
?>
<p><?php echo \CMS\CHOOSE_NEW_PARENT?>:</p>
<?php echo showMoveMaterialType(new Material_Type(), $ids)?>
<script>
jQuery(document).ready(function($) {
    $('[data-role="move-material-type"]').RAAS_menuTree();
});
</script>
