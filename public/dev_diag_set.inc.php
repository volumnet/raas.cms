<?php
$_RAASForm_FieldSet = function(\RAAS\FieldSet $FieldSet, $level = 0) use (&$_RAASForm_Options) {
    $Table = $FieldSet->meta['Table'];
    $VIEW = $FieldSet->Form->view;
    ?>
    <fieldset>
      <legend><?php echo htmlspecialchars($FieldSet->caption)?></legend>
      <?php include $VIEW->tmp($Table->template);?>
    </fieldset>
    <br />
    <?php
};