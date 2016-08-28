<?php
function showMoveMenu(\RAAS\CMS\Page $node, array $ids, array $actives)
{
    static $level = 0;
    foreach ($node->children as $row) {
        $active = in_array($row->id, $actives);
        $text .= '<li class="' . ((!$row->vis || !$row->pvis) ? ' cms-invis' : '') . (!$row->pvis ? ' cms-inpvis' : '') . ($active ? ' active' : '') . '">';
        if (in_array($row->id, $ids)) {
            $text .= '<b>' . htmlspecialchars($row->name) . '</b>';
        } else {
            $text .= '<a href="' . \SOME\HTTP::queryString('new_pid=' . (int)$row->id) . '">' . htmlspecialchars($row->name) . '</a>';
        }
        $level++;
        $text .= showMoveMenu($row, $ids, $actives);
        $level--;
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
<?php echo showMoveMenu(new \RAAS\CMS\Page(), $ids, $actives)?>
<script>
jQuery(document).ready(function($) {
    $('[data-role="move-menu"]').RAAS_menuTree();
});
</script>
