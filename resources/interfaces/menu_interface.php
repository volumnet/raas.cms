<?php
/**
 * Стандартный интерфейс меню
 * @param Block_Menu $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

$interface = new MenuInterface(
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
