<?php
namespace RAAS\CMS;
$OUT = array();
$f = function(\RAAS\CMS\Menu $node) use (&$f) {
    $temp = array();
    $children = $node->visSubMenu;
    $children = array_filter($children, function($x) { return !$x->page->id || $x->page->currentUserHasAccess(); });
    foreach ($children as $row) {
        $row2 = array('url' => $row->url, 'name' => $row->name, 'children' => $f($row));
        if ($row->page_id) {
            $row2['page_id'] = (int)$row->page_id;
        }
        $temp[] = $row2;
    }
    return $temp;
};
$Item = new Menu(isset($config['menu']) ? (int)$config['menu'] : 0);
if (!isset($config['full_menu']) || !(int)$config['full_menu']) {
    $Item = $Item->findPage($Page);
}
if ($Item->id || $Item->page_id) {
    $OUT['Item'] = $Item;
    $OUT['menuArr'] = array('children' => $f($Item));
}
return $OUT;
