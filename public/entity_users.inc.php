<?php
/**
 * Вкладка сущностей, использующих текущую сущность
 */
namespace RAAS\CMS;

use RAAS\FormTab;

/**
 * Отображает вкладку сущности
 * @param FormTab $formTab Вкладка для отображения
 */
$_RAASForm_FormTab = function (FormTab $formTab) {
    echo $formTab->meta['Table']->render();
};
