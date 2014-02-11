<?php
function showMoveMenu(\RAAS\CMS\Dictionary $node, \RAAS\CMS\Dictionary $current)
{
    static $level = 0;
    foreach ($node->children as $row) {
        $text .= '<li class="' . ((!$row->vis || !$row->pvis) ? ' cms-invis' : '') . (!$row->pvis ? ' cms-inpvis' : '') . '">';
        if (($current->pid == $row->id)) {
            $text .= '<span>' . htmlspecialchars($row->name) . '</span>';
        } elseif (($current->id == $row->id)) {
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
<ul class="tree" data-raas-role="tree">
  <li>
    <?php if (!$Item->pid) { ?>
        <span><?php echo CMS\ROOT_SECTION?></span>
    <?php } else { ?>
        <a href="<?php echo \SOME\HTTP::queryString('pid=0')?>"><?php echo CMS\ROOT_SECTION?></a>
    <?php } ?>
    <?php echo showMoveMenu(new \RAAS\CMS\Dictionary(), $Item)?>
  </li>
</ul>