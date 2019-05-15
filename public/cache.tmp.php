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
    <a href="#" class="btn btn-success btn-large" data-role="rebuild-cache">
      <?php echo \CMS\REBUILD_CACHE?> 
    </a>
  </div>

  <div class="cms-rebuild-cache">
    <div class="alert alert-info">
      <strong><?php echo \CMS\STATUS?>:</strong> 
      <span data-role="status-text"></span>
      <div class="cms-rebuild-cache__possible-statuses">
        <?php foreach ([
            'clear-cache', 
            'get-map', 
            'rebuild-block', 
            'rebuild-page', 
            'success'] as $key) { ?>
            <span data-role="status-text-<?php echo $key?>">
              <?php echo constant('CMS\\REBUILD_CACHE_STATUS_' . mb_strtoupper(str_replace('-', '_', $key)))?> 
            </span>
        <?php } ?>
      </div>
    </div>
    
    <div class="progress progress-striped active">
      <div class="bar" style="width: 0%;"><span data-role="progress"></span></div>
    </div>
  </div>
</div>