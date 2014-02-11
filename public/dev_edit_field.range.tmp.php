<?php
$_RAASForm_FieldSet = function(\RAAS\FieldSet $FieldSet) use (&$_RAASForm_Attrs, &$_RAASForm_Form_Tabbed, &$_RAASForm_Form_Plain) {
    $DATA = $FieldSet->Form->DATA;
    ?>
    <div class="control-group">
      <label class="control-label"><?php echo htmlspecialchars($FieldSet->caption)?>:</label> 
      <div class="controls">
        <?php 
        include \RAAS\Application::i()->view->context->tmp('/field.inc.php');
        $_RAASForm_Control($FieldSet->children[0]);
        echo ' — ';
        $_RAASForm_Control($FieldSet->children[1]);
        ?>
      </div>
    </div>
    <?php
};