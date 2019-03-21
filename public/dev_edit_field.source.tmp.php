<?php
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Field as RAASField;

$_RAASForm_Field = function(RAASField $Field) use (&$_RAASForm_Attrs, &$_RAASForm_Options, &$_RAASForm_Checkbox, &$_RAASForm_Control) {
    ?>
    <div class="control-group">
      <label class="control-label"><?php echo htmlspecialchars($Field->caption)?>:</label>
      <div class="controls">
        <?php
        include Application::i()->view->context->tmp('/field.inc.php');
        $Field->type = 'textarea';
        $Field->id = 'source_textarea';
        $Field->class = 'code';
        $_RAASForm_Control($Field);
        $Field->type = 'select';
        $Field->id = 'source_dictionary';
        $Field->class = '';
        $_RAASForm_Control($Field);
        $Field->id = 'source_materials';
        $row = new Material_Type();
        $Field->children = array('Set' => $row->children);
        $Field->placeholder = Application::i()->view->context->_('ALL_MATERIAL_TYPES');
        $_RAASForm_Control($Field);
        ?>
      </div>
    </div>
    <?php
    $Field->type = 'text';
    $Field->caption = Application::i()->view->context->_('ALLOWED_FILE_EXTENSIONS');
    $Field->children = [];
    $Field->placeholder = Application::i()->view->context->_('ALLOWED_FILE_EXTENSIONS_HINT');
    $Field->id = 'source_file';
    ?>
    <div class="control-group">
      <label class="control-label"><?php echo htmlspecialchars($Field->caption)?>:</label>
      <div class="controls">
        <?php
        $_RAASForm_Control($Field);
        ?>
      </div>
    </div>
    <?php
};
