<?php
/**
 * Раздел "Разработка / Справочники"
 */
namespace RAAS\CMS;

use RAAS\Application;

if ($Item->id) { ?>
    <form class="form-inline" action="" method="post" enctype="multipart/form-data">
      <label>
        <?php echo \CMS\LOAD_FROM_FILE?>
        <input type="file" name="file" required="required" />
      </label>
      <input type="submit" class="btn btn-primary" value="<?php echo SUBMIT?>" />
      <raas-hint>
        <?php echo sprintf(\CMS\AVAILABLE_DICTIONARIES_FORMATS, strtoupper(implode(', ', Dictionary::$availableExtensions)))?>
      </raas-hint>
    </form>
<?php }
echo $Table->renderFull();
