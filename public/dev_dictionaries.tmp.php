<?php
/**
 * Раздел "Разработка / Справочники"
 */
namespace RAAS\CMS;

if ($Item->id) { ?>
    <form class="form-inline" action="" method="post" enctype="multipart/form-data">
      <label>
        <?php echo \CMS\LOAD_FROM_FILE?>
        <input type="file" name="file" required="required" />
      </label>
      <input type="submit" class="btn btn-primary" value="<?php echo SUBMIT?>" />
      <a class="btn" href="#" rel="popover" data-content="<?php echo sprintf(\CMS\AVAILABLE_DICTIONARIES_FORMATS, strtoupper(implode(', ', Dictionary::$availableExtensions)))?>">
        <raas-icon icon="circle-question"></raas-icon>
      </a>
    </form>
<?php
}
include \RAAS\Application::i()->view->context->tmp('prioritytable.tmp.php');
