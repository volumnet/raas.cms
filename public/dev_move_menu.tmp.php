<?php
function showMoveMenu(\RAAS\CMS\Menu $node, array $ids, array $pids, array $actives)
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
            $text .= '<a href="' . \SOME\HTTP::queryString('new_pid=' . (int)$row->id) . '">' . htmlspecialchars($row->name) . '</a>';
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
<p><?php echo CMS\CHOOSE_NEW_PARENT?>:</p>
<ul class="tree" data-role="move-menu" style="margin-bottom: 20px">
  <li class="active">
    <?php if (!$Item->pid) { ?>
        <span><?php echo CMS\ROOT_SECTION?></span>
    <?php } else { ?>
        <a href="<?php echo \SOME\HTTP::queryString('new_pid=0')?>"><?php echo CMS\ROOT_SECTION?></a>
    <?php } ?>
    <?php echo showMoveMenu(new \RAAS\CMS\Menu(), $ids, $pids, $actives)?>
  </li>
</ul>
<script>
jQuery(document).ready(function($) {
    $('[data-role="move-menu"]').RAAS_menuTree();
});
</script>