<?php
/**
 * Страница "Разработка / Диагностика"
 */
namespace RAAS\CMS;

use RAAS\FormTab;

?>
<form action="" method="get">
  <?php foreach ($VIEW->nav as $key => $val) { ?>
      <?php if (!in_array($key, ['from', 'to'])) { ?>
          <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($val)?>">
      <?php } ?>
  <?php } ?>
  <?php echo \CMS\SHOW_STAT?>:<br />
  <raas-field-date name="from" model-value="<?php echo htmlspecialchars($Form->meta['from'])?>"></raas-field-date> —
  <raas-field-date name="to" model-value="<?php echo htmlspecialchars($Form->meta['to'])?>"></raas-field-date><br />
  <button type="submit" class="btn btn-primary">
    <?php echo \CMS\REFRESH?>
  </button>
</form>
<?php
echo $Form->children->render();
