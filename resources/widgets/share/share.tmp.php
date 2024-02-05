<?php
/**
 * Блок "Поделиться"
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

?>
<div class="share">
  <div class="ya-share2" data-services="vkontakte,twitter,whatsapp,telegram"></div>
</div>
<?php AssetManager::requestJS([
    '//yastatic.net/es5-shims/0.0.2/es5-shims.min.js',
    '//yastatic.net/share2/share.js',
]) ?>
