<?php
include \RAAS\Application::i()->context->view->tmp('field.inc.php');
$_RAASForm_FieldSet = function(\RAAS\FieldSet $FieldSet) use (&$_RAASForm_Form_Tabbed, &$_RAASForm_Form_Plain, &$_RAASForm_Control, &$_RAASForm_Options) {
    $Field = $FieldSet->children[0];
    $inheritField = $FieldSet->children[1];
    $err = (bool)array_filter((array)$Field->Form->localError, function($x) use ($Field) { return $x['value'] == $Field->name; });
    if (in_array($Field->type, array('htmlarea', 'codearea'))) {
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <?php if ($Field->caption) { ?>
              <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>"><?php echo htmlspecialchars($Field->caption)?>:</label> 
              <div class="controls clearfix"><label class="checkbox"><?php echo $_RAASForm_Control($inheritField, false) . htmlspecialchars($inheritField->caption)?></label></div>
          <?php } ?>
          <div class="clearfix"><?php echo $_RAASForm_Control($Field)?></div>
        </div>
        <?php
    } elseif (($Field->type == 'password') && $Field->confirm) {
        $err2 = (bool)array_filter((array)$Field->Form->localError, function($x) use ($Field) { return $x['value'] == $Field->name . '@confirm'; });
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>"><?php echo htmlspecialchars($Field->caption)?>:</label> 
          <div class="row">
            <div class="span3" style="margin-left: 20px"><?php echo $_RAASForm_Control($Field, false)?></div> <div class="span2"><label class="checkbox"><?php echo $_RAASForm_Control($inheritField, false) . htmlspecialchars($inheritField->caption)?></label></div>
          </div>
        </div>
        <div class="control-group<?php echo $err2 ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>@confirm"><?php echo PASSWORD_CONFIRM?>:</label> 
          <div class="controls"><?php echo $_RAASForm_Control($Field, true)?></div>
        </div>
        <?php
    } elseif ($Field->type == 'checkbox' && !$Field->multiple) {
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <div class="controls">
            <div class="row">
              <div class="span3">
                <label class="checkbox"<?php echo $Field->{'data-hint'} ? ' style="width: 174px;"' : ''?>><?php echo $_RAASForm_Control($Field, false)?> <?php echo htmlspecialchars($Field->caption)?></label>
              </div>
              <div class="span2"><label class="checkbox"><?php echo $_RAASForm_Control($inheritField, false) . htmlspecialchars($inheritField->caption)?></label></div>
            </div>
          </div>
        </div>
        <?php
    } else {
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>"><?php echo htmlspecialchars($Field->caption ? $Field->caption . ':' : '')?></label> 
          <div class="controls">
            <div class="row">
              <div class="span3" style="margin-left: 20px"><?php echo $_RAASForm_Control($Field, false)?></div> 
              <div class="span2"><label class="checkbox"><?php echo $_RAASForm_Control($inheritField, false) . htmlspecialchars($inheritField->caption)?></label></div>
            </div>
          </div>
        </div>
        <?php
    }
};