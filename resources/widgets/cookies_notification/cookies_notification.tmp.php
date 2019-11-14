<?php
/**
 * Виджет блока "уведомление о cookies"
 * @param Page $Page Текущая страница
 * @param Block_HTML $Block Текущий блок
 */
namespace RAAS\CMS;

?>
<div class="cookies-notification">
  <a href="#" class="cookies-notification__close"></a>
  <div class="cookies-notification__inner">
    <?php echo $Block->description?>
  </div>
</div>
<?php echo Package::i()->asset('cookies-notification.js')?>
