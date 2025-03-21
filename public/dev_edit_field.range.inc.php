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
$_RAASForm_FieldSet = function(FieldSet $fieldSet) {
    $DATA = $fieldSet->Form->DATA;
    $children = $fieldSet->children;
    ?>
    <div class="control-group">
      <label class="control-label">
        <?php echo htmlspecialchars($fieldSet->caption)?>:
      </label>
      <div class="controls">
        <?php
        echo $children[0]->render() . ' — ' . $children[1]->render() . ' ' . \CMS\WITH_STEP . ' ' . $children[2]->render();
        ?>
      </div>
    </div>
    <?php
};
