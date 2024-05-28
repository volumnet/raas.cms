<?php
/**
 * Поле "источник" в редактировании поля
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Field as RAASField;

/**
 * Отображает поле
 * @param RAASField $field Поле для отображения
 */
$_RAASForm_Field = function(RAASField $field) use (
    &$_RAASForm_Attrs,
    &$_RAASForm_Options,
    &$_RAASForm_Checkbox,
    &$_RAASForm_Control
) {
    ?>
    <div class="control-group">
      <label class="control-label">
        <?php echo htmlspecialchars($field->caption)?>:
      </label>
      <div class="controls">
        <?php
        include Application::i()->view->context->tmp('/field.inc.php');
        $field->type = 'textarea';
        $field->id = 'source_textarea';
        $field->class = 'code';
        $_RAASForm_Control($field);
        $field->type = 'select';
        $field->id = 'source_dictionary';
        $field->class = '';
        $_RAASForm_Control($field);
        $field->id = 'source_materials';
        $row = new Material_Type();
        $field->children = ['Set' => $row->children];
        $field->placeholder = Application::i()->view->context->_('ALL_MATERIAL_TYPES');
        $_RAASForm_Control($field);
        ?>
      </div>
    </div>
    <?php
    $field->type = 'text';
    $field->caption = Application::i()->view->context->_('ALLOWED_FILE_EXTENSIONS');
    $field->children = [];
    $field->placeholder = Application::i()->view->context->_('ALLOWED_FILE_EXTENSIONS_HINT');
    $field->id = 'source_file';
    ?>
    <div class="control-group">
      <label class="control-label">
        <?php echo htmlspecialchars($field->caption)?>:
      </label>
      <div class="controls">
        <?php
        $_RAASForm_Control($field);
        ?>
      </div>
    </div>
    <?php
    $field->type = 'text';
    $field->caption = Application::i()->view->context->_('MEASURE_UNIT');
    $field->placeholder = '';
    $field->children = [];
    $field->id = 'source_unit';
    ?>
    <div class="control-group">
      <label class="control-label">
        <?php echo htmlspecialchars($field->caption)?>:
      </label>
      <div class="controls">
        <?php
        $_RAASForm_Control($field);
        ?>
      </div>
    </div>
    <?php
};
