<?php
namespace RAAS\CMS;
$OUT = array();
$Item = new Menu(isset($config['menu']) ? (int)$config['menu'] : 0);
if (!isset($config['full_menu']) || !(int)$config['full_menu']) {
    $Item = $Item->findPage($Page);
}
if ($Item->id || $Item->page_id) {
    $OUT['Item'] = $Item;
}
return $OUT;