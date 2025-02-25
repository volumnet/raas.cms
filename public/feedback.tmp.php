<?php
/**
 * Раздел "Обратная связь"
 */
namespace RAAS\CMS;

use RAAS\Application;

?>
<form class="form-search" action="" method="get">
  <?php foreach ($VIEW->nav as $key => $val) { ?>
      <?php if (!in_array($key, ['page', 'search_string', 'from', 'to'])) { ?>
          <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($val)?>" />
      <?php } ?>
  <?php } ?>
  <raas-field-datetime
    type="datetime-local"
    name="from"
    class="span2"
    placeholder="<?php echo \CMS\SHOW_FROM?>"
    model-value="<?php echo $VIEW->nav['from'] ?? ''?>"
  ></raas-field-datetime>
  <raas-field-datetime
    type="datetime"
    name="to"
    class="span2"
    placeholder="<?php echo \CMS\SHOW_TO?>"
    model-value="<?php echo $VIEW->nav['to'] ?? ''?>"
  ></raas-field-datetime>
  <div class="input-append">
    <raas-field-text
      type="search"
      class="span2 search-query"
      name="search_string"
      model-value="<?php echo htmlspecialchars($VIEW->nav['search_string'] ?? '')?>"
    ></raas-field-text>
    <button type="submit" class="btn"><i class="icon-search"></i></button>
  </div>
</form>
<?php include Application::i()->view->context->tmp('multitable.tmp.php'); ?>
