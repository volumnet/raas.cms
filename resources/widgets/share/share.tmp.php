<?php
/**
 * Виджет блока "Поделиться"
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

?>
<div class="share">
  <div class="ya-share2" data-services="vkontakte,facebook,twitter,whatsapp,telegram"></div>
</div>
<?php Package::i()->requestJS([
    '//yastatic.net/es5-shims/0.0.2/es5-shims.min.js',
    '//yastatic.net/share2/share.js',
]) ?>
