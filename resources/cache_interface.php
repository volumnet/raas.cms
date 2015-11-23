<?php
namespace RAAS\CMS;

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
    // 2015-11-23, AVS: заменил, т.к. в кэше меню <?php так же заменяется и глючит
    if ($Block->cache_type == Block::CACHE_HTML) {
        $cacheText = preg_replace('/\\<\\?xml (.*?)\\?\\>/umi', '<?php echo \'<\' . \'?xml $1?\' . ">\\n"?' . '>', $cacheText);
    }
    file_put_contents($Block->getCacheFile($_SERVER['REQUEST_URI']), $cacheText);
}
return $OUT;