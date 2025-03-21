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
$_RAASForm_Field = function(RAASField $field) {
    ?>
    <div class="control-group">
      <label class="control-label">
        <?php echo htmlspecialchars($field->caption)?>:
      </label>
      <div class="controls">
        <?php
        $field1 = clone $field;
        $field1->Parent = $field->Parent;
        $field1->type = 'textarea';
        $field1->id = 'source_textarea';
        $field1->class = 'code';
        $field1->__set('children', []);
        echo $field1->render();
        $field1 = clone $field;
        $field1->Parent = $field->Parent;
        $field1->type = 'select';
        $field1->id = 'source_dictionary';
        $field1->class = '';
        echo $field1->render();
        $field1 = clone $field;
        $field1->Parent = $field->Parent;
        $field1->id = 'source_materials';
        $row = new Material_Type();
        $field1->__set('children', ['Set' => $row->children]);
        $field1->placeholder = Application::i()->view->context->_('ALL_MATERIAL_TYPES');
        echo $field1->render();
        ?>
      </div>
    </div>
    <?php
    $field->type = 'text';
    $field->caption = Application::i()->view->context->_('ALLOWED_FILE_EXTENSIONS');
    $field->__set('children', []);
    $field->placeholder = Application::i()->view->context->_('ALLOWED_FILE_EXTENSIONS_HINT');
    $field->id = 'source_file';
    ?>
    <div class="control-group">
      <label class="control-label">
        <?php echo htmlspecialchars($field->caption)?>:
      </label>
      <div class="controls">
        <?php
        echo $field->render();
        ?>
      </div>
    </div>
    <?php
    $field->type = 'text';
    $field->caption = Application::i()->view->context->_('MEASURE_UNIT');
    $field->placeholder = '';
    $field->__set('children', []);
    $field->id = 'source_unit';
    ?>
    <div class="control-group">
      <label class="control-label">
        <?php echo htmlspecialchars($field->caption)?>:
      </label>
      <div class="controls">
        <?php
        echo $field->render();
        ?>
      </div>
    </div>
    <?php
};
