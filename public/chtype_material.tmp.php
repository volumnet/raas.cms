<?php
/**
 * Перемещение или размещение материала
 */
namespace RAAS\CMS;

use SOME\HTTP;

/**
 * Отображает дерево типов материалов для размещения
 * @param Material_Type $node Текущий узел
 * @param Material_Type $current Текущий тип материалов
 * @return string
 */
function showMoveMenu(Material_Type $node, Material_Type $current)
{
    static $level = 0;
    foreach ($node->children as $row) {
        $active = ($row->id == $current->id);
        $text .= '<li' . ($active ? ' class="active"' : '') . '>';
        if ($active) {
            $text .= '<span>' . htmlspecialchars($row->name) . '</span>';
        } else {
            $text .= '<a href="' . HTTP::queryString('new_pid=' . (int)$row->id) . '">'
                  .     htmlspecialchars($row->name)
                  .  '</a>';
        }
        $level++;
        $text .= showMoveMenu($row, $current);
        $level--;
        $text .= '</li>';
    }

    if ($text) {
        if ($level) {
            $text = '<ul>' . $text . '</ul>';
        } else {
            $text = '<ul class="tree" data-role="move-menu" style="margin-bottom: 20px">'
                  .    $text
                  . '</ul>';
        }
    }
    return $text;
}
?>
<p><?php echo \CMS\CHOOSE_NEW_MATERIAL_TYPE?>:</p>
<?php echo showMoveMenu(new Material_Type(), $mtype)?>
<script>
jQuery(document).ready(function($) {
    $('[data-role="move-menu"]').RAAS_menuTree();
});
</script>
