<?php
namespace RAAS\CMS;

function showMoveMaterialField(Material_Type $node, array $ids, array $pids, array $actives)
{
    static $level = 0;
    foreach ($node->children as $row) {
        $active = in_array($row->id, $actives);
        $text .= '<li class="' . ($active ? ' active' : '') . '">';
        if (in_array($row->id, $pids)) {
            $text .= '<span>' . htmlspecialchars($row->name) . '</span>';
        } else {
            $text .= '<a href="' . \SOME\HTTP::queryString('new_pid=' . (int)$row->id) . '">' . htmlspecialchars($row->name) . '</a>';
        }
        if (!in_array($row->id, $ids)) {
            $level++;
            $text .= showMoveMaterialField($row, $ids, $pids, $actives);
            $level--;
        }
        $text .= '</li>';
    }

    if ($text) {
        $text = '<ul' . (!$level ? ' class="tree" data-role="move-material-field" style="margin-bottom: 20px"' : '') . '>' . $text . '</ul>';
    }
    return $text;
}
?>
<p><?php echo \CMS\CHOOSE_NEW_PARENT?>:</p>
<?php echo showMoveMaterialField(new Material_Type(), $ids, $pids, $actives)?>
<script>
jQuery(document).ready(function($) {
    $('[data-role="move-material-field"]').RAAS_menuTree();
});
</script>
