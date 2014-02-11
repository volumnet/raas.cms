<form class="form-search" action="" method="get">
  <?php foreach ($VIEW->nav as $key => $val) { ?>
      <?php if (!in_array($key, array('page', 'search_string'))) { ?>
          <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($val)?>" />
      <?php } ?>
  <?php } ?>
  <div class="input-append">
    <input type="search" class="span2 search-query" name="search_string" value="<?php echo htmlspecialchars($VIEW->nav['search_string'])?>" />
    <button type="submit" class="btn"><i class="icon-search"></i></button>
  </div>
</form>
<?php include \RAAS\Application::i()->view->context->tmp('/table.tmp.php'); ?>