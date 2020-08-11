<?php
/**
 * Страница "Разработка / Управление кэшированием"
 */
namespace RAAS\CMS;

?>
<div class="cms-cache">
  <p>
    <?php echo \CMS\USED_BY_CACHE?>:
    <strong>
      <?php echo number_format($usedByCache, 2, '.', ' ') .
                 ' (' . mb_strtolower(\CMS\FILES_COUNTER) . ': ' . (int)$filesCounter . ')'?>
    </strong>
  </p>
  <p>
    <?php echo \CMS\DISK_FREE_SPACE?>:
    <strong><?php echo number_format($diskFreeSpace, 2, '.', ' ')?></strong>
  </p>
  <p>
    <?php echo \CMS\CACHE_LEAVE_FREE_SPACE?>:
    <strong><?php echo number_format($cacheLeaveFreeSpace, 2, '.', ' ')?></strong>
  </p>
  <p<?php echo ($availableForCache < 0 ? ' class="text-error"' : '')?>>
    <?php echo \CMS\AVAILABLE_FOR_CACHE?>:
    <strong><?php echo number_format($availableForCache, 2, '.', ' ')?></strong>
  </p>
  <div style="margin-bottom: 20px">
    <a href="<?php echo Sub_Dev::i()->url?>&action=clear_cache" class="btn btn-danger btn-large">
      <?php echo \CMS\CLEAR_CACHE?>
    </a>
  </div>
</div>
