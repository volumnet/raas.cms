<?php
/**
 * Группа полей для сводки диагностики
 */
namespace RAAS\CMS;

use RAAS\FieldSet;

/**
 * Отображает группу полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function(FieldSet $fieldSet) {
    $Table = $fieldSet->meta['Table'];
    $VIEW = $fieldSet->Form->view;
    ?>
    <fieldset>
      <legend><?php echo htmlspecialchars($fieldSet->caption)?></legend>
      <?php echo $Table->render()?>
    </fieldset>
    <br />
    <?php
};
