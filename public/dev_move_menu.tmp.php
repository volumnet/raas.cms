<?php
function showMoveMenu(\RAAS\CMS\Menu $node, \RAAS\CMS\Menu $current)
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
        <a href="<?php echo \SOME\HTTP::queryString('pid=0')?>"><?php echo CMS\ROOT_SECTION?></a>
    <?php } ?>
    <?php echo showMoveMenu(new \RAAS\CMS\Menu(), $Item)?>
  </li>
</ul>
<script>
jQuery(document).ready(function($) {
    $('[data-role="move-menu"]').RAAS_menuTree();
});
</script>