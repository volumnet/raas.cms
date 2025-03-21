<?php
/**
 * Вкладка "Связанные материалы" в редактировании материалов
 */
namespace RAAS\CMS;

use RAAS\FormTab;

/**
 * Отображает вкладку типа материалов
 * @param FormTab $formTab Вкладка для отображения
 */
$_RAASForm_FormTab = function (FormTab $formTab) {
    $pagesHash = '_' . $formTab->meta['mtype']->urn;
    echo $formTab->meta['Table']->render(false, $pagesHash);
};
