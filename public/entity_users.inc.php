<?php
/**
 * Вкладка сущностей, использующих текущую сущность
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FormTab;

/**
 * Отображает вкладку сущности
 * @param FormTab $formTab Вкладка для отображения
 */
$_RAASForm_FormTab = function (FormTab $formTab) {
    $Table = $formTab->meta['Table'];
    $VIEW = ViewSub_Dev::i();
    include ViewSub_Dev::i()->tmp($Table->template);
};
