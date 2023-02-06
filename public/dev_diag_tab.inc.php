<?php
/**
 * Вкладка для сводки диагностики
 */
namespace RAAS\CMS;

use RAAS\FormTab;

/**
 * Отображает вкладку
 * @param FormTab $formTab Вкладка для отображения
 */
$_RAASForm_FormTab = function(FormTab $formTab) use (
    &$_RAASForm_Form_Tabbed,
    &$_RAASForm_Form_Plain,
    &$_RAASForm_Attrs
) {
    $Item = $formTab->Form->meta['Item'];
    $cVar = $formTab->name . 'Counter';
    $tVar = $formTab->name . 'Time';
    ?>
    <p style="font-weight: bold">
      <?php echo \CMS\DIAGNOSTICS_COUNTER?>:
      <?php echo (int)($Item->$cVar ?? 0)?><br />
      <?php echo \CMS\DIAGNOSTICS_TOTAL_TIME?>:
      <?php echo number_format($Item->$tVar ?? 0, 3, '.', ' ')?>
    </p>
    <?php
    if (array_filter(
        (array)$formTab->children,
        function ($x) {
            return $x instanceof FormTab;
        }
    )) {
        $_RAASForm_Form_Tabbed($formTab->children);
    } else {
        $_RAASForm_Form_Plain($formTab->children);
    }

};
