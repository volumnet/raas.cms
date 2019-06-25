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
$_RAASForm_FieldSet = function(FieldSet $fieldSet) use (&$_RAASForm_Options) {
    $Table = $fieldSet->meta['Table'];
    $VIEW = $fieldSet->Form->view;
    ?>
    <fieldset>
      <legend><?php echo htmlspecialchars($fieldSet->caption)?></legend>
      <?php include $VIEW->tmp($Table->template);?>
    </fieldset>
    <br />
    <?php
};
