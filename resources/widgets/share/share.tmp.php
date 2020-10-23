<?php
/**
 * Виджет блока "{{WIDGET_NAME}}"
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

?>
<div class="{{WIDGET_CSS_CLASSNAME}}">
  <div class="ya-share2" data-services="vkontakte,facebook,twitter,whatsapp"></div>
</div>
<?php Package::i()->requestJS([
    '//yastatic.net/es5-shims/0.0.2/es5-shims.min.js',
    '//yastatic.net/share2/share.js',
]) ?>
