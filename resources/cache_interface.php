<?php
namespace RAAS\CMS;

$OUT = $IN;
switch ($Block->cache_type) {
    case Block::CACHE_HTML:
        $cacheText = ob_get_contents();
        break;
    case Block::CACHE_DATA:
        if ($Block instanceof Block_Menu) {
            unset($OUT['Item']);
        }
        $cacheText = '<' . '?php return unserialize("' . addslashes(serialize($OUT)) . '");';
        break;
}
if ($cacheText) {
    file_put_contents($Block->getCacheFile($_SERVER['REQUEST_URI']), $cacheText);
}
return $OUT;