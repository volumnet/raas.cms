<?php if ($Item->id) { ?>
    <form class="form-inline" action="" method="post" enctype="multipart/form-data">
      <label><?php echo CMS\LOAD_FROM_FILE?> <input type="file" name="file" required="required" /></label> <input type="submit" class="btn btn-primary" value="<?php echo SUBMIT?>" />
      <a class="btn" href="#" rel="popover" data-content="<?php echo sprintf(CMS\AVAILABLE_DICTIONARIES_FORMATS, strtoupper(implode(', ', \RAAS\CMS\Dictionary::$availableExtensions)))?>">
        <i class="icon-question-sign"></i>
      </a>
    </form>
<?php 
}
include \RAAS\Application::i()->view->context->tmp('/table.tmp.php');
?>