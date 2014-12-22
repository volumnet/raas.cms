<form action="" method="get">
  <?php foreach ($VIEW->nav as $key => $val) { ?>
      <?php if (!in_array($key, array('from', 'to'))) { ?>
          <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($val)?>">
      <?php } ?>
  <?php } ?>
  <?php echo CMS\SHOW_STAT?>:<br />
  <input type="date" name="from" value="<?php echo htmlspecialchars($Form->meta['from'])?>" /> —
  <input type="date" name="to" value="<?php echo htmlspecialchars($Form->meta['to'])?>" /><br />
  <button type="submit" class="btn btn-primary"><?php echo CMS\REFRESH?></button>
</form>
<?php include $VIEW->tmp('/form.inc.php')?>
<?php 
if (array_filter((array)$Form->children, function($x) { return $x instanceof \RAAS\FormTab; })) { 
    $_RAASForm_Form_Tabbed($Form->children);
} else {
    $_RAASForm_Form_Plain($Form->children);
}
?>