<?php
/**
 * Стандартный интерфейс материалов
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

$interface = new MaterialInterface($Block, $Page, $_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES);
return $interface->process();
