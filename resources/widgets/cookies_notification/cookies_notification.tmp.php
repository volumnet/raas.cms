<?php
/**
 * Виджет блока "уведомление о cookies"
 * @param Page $Page Текущая страница
 * @param Block_HTML $Block Текущий блок
 */
namespace RAAS\CMS;

?>
<!--noindex-->
<div class="cookies-notification" data-vue-role="cookies-notification">
  <?php echo $Block->description?>
</div>
<!--/noindex-->
<?php
Package::i()->requestCSS('/css/cookies-notification.css');
Package::i()->requestJS('/js/cookies-notification.js');
