<?php
/**
 * Страница "Разработка / Управление кэшированием"
 */
namespace RAAS\CMS;

?>
<div class="cms-cache">
  <div style="margin-bottom: 20px">
    <a href="<?php echo Sub_Dev::i()->url?>&action=clear_cache" class="btn btn-danger btn-large">
      <?php echo \CMS\CLEAR_CACHE?>
    </a>
  </div>
</div>
