<?php
/**
 * Уведомление о cookies
 * @param Page $Page Текущая страница
 * @param Block_HTML $Block Текущий блок
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

?>
<!--noindex-->
<div class="cookies-notification" data-vue-role="cookies-notification">
  <?php echo $Block->description?>
</div>
<!--/noindex-->
<?php
AssetManager::requestCSS('/css/cookies-notification.css');
AssetManager::requestJS('/js/cookies-notification.js');
