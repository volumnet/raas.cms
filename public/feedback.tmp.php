<form class="form-search" action="" method="get">
  <?php foreach ($VIEW->nav as $key => $val) { ?>
      <?php if (!in_array($key, array('page', 'search_string', 'from', 'to'))) { ?>
          <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($val)?>" />
      <?php } ?>
  <?php } ?>
  <input type="datetime" name="from" class="span2" placeholder="<?php echo CMS\SHOW_FROM?>" value="<?php echo $VIEW->nav['from']?>" />
  <input type="datetime" name="to" class="span2" placeholder="<?php echo CMS\SHOW_TO?>" value="<?php echo $VIEW->nav['to']?>" />
  <div class="input-append">
    <input type="search" class="span2 search-query" name="search_string" value="<?php echo htmlspecialchars($VIEW->nav['search_string'])?>" />
    <button type="submit" class="btn"><i class="icon-search"></i></button>
  </div>
</form>
<?php include \RAAS\Application::i()->view->context->tmp('multitable.tmp.php'); ?>
