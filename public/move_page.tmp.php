<?php
function showMoveMenu(\RAAS\CMS\Page $node, \RAAS\CMS\Page $current)
{
    static $level = 0;
    foreach ($node->children as $row) {
        $active = in_array($row->id, array_merge(array($current->id), (array)$current->parents_ids));
        $text .= '<li class="' . ((!$row->vis || !$row->pvis) ? ' cms-invis' : '') . (!$row->pvis ? ' cms-inpvis' : '') . ($active ? ' active' : '') . '">';
        if ($current->pid == $row->id) {
            $text .= '<span>' . htmlspecialchars($row->name) . '</span>';
        } elseif ($current->id == $row->id) {
            $text .= '<b>' . htmlspecialchars($row->name) . '</b>';
        } else {
            $text .= '<a href="' . \SOME\HTTP::queryString('pid=' . (int)$row->id) . '">' . htmlspecialchars($row->name) . '</a>';
        }
        if ($current->id != $row->id) {
            $level++;
            $text .= showMoveMenu($row, $current);
            $level--;
        }
        $text .= '</li>'; 
    }

    if ($text) {
        if ($level) {
            $text = '<ul>' . $text . '</ul>';
        } else {
            $text = '<ul class="tree" data-role="move-menu" style="margin-bottom: 20px">' . $text . '</ul>';
        }
    }
    return $text;
}
?>
<p><?php echo CMS\CHOOSE_NEW_PARENT?>:</p>
<?php echo showMoveMenu(new \RAAS\CMS\Page(), $Item)?>
<script>
jQuery(document).ready(function($) {
    $('[data-role="move-menu"]').RAAS_menuTree();
});
</script>
