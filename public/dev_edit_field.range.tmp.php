<?php
/**
 * Поле "от / до" в редактировании поля
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FieldSet;

/**
 * Отображает группу полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function(FieldSet $fieldSet) use (
    &$_RAASForm_Attrs,
    &$_RAASForm_Form_Tabbed,
    &$_RAASForm_Form_Plain
) {
    $DATA = $fieldSet->Form->DATA;
    ?>
    <div class="control-group">
      <label class="control-label">
        <?php echo htmlspecialchars($fieldSet->caption)?>:
      </label>
      <div class="controls">
        <?php
        include Application::i()->view->context->tmp('/field.inc.php');
        $_RAASForm_Control($fieldSet->children[0]);
        echo ' — ';
        $_RAASForm_Control($fieldSet->children[1]);
        echo ' ' . \CMS\WITH_STEP . ' ';
        $_RAASForm_Control($fieldSet->children[2]);?>
      </div>
    </div>
    <?php
};
