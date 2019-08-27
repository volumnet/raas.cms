<?php
/**
 * Стандартный интерфейс формы
 * @param Block_Form $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

$interface = new FormInterface(
    $Block,
    $Page,
    $_GET,
    $_POST,
    $_COOKIE,
    $_SESSION,
    $_SERVER,
    $_FILES
);
return $interface->process();
