<?php
$_RAASForm_Field = function(\RAAS\Field $Field) use (&$_RAASForm_Attrs, &$_RAASForm_Options, &$_RAASForm_Checkbox, &$_RAASForm_Control) {
    ?>
    <div class="control-group">
      <label class="control-label"><?php echo htmlspecialchars($Field->caption)?>:</label> 
      <div class="controls">
        <?php
        include \RAAS\Application::i()->view->context->tmp('/field.inc.php');
        $Field->type = 'textarea';
        $Field->id = 'source_textarea';
        $Field->class = 'code';
        $_RAASForm_Control($Field);
        $Field->type = 'select';
        $Field->id = 'source_dictionary';
        $_RAASForm_Control($Field);
        $Field->id = 'source_materials';
        $row = new \RAAS\CMS\Material_Type();
        $Field->children = array('Set' => $row->children);
        $Field->placeholder = \RAAS\Application::i()->view->context->_('ALL_MATERIAL_TYPES');
        $_RAASForm_Control($Field);
        ?>
      </div>
    </div>
    <?php
};