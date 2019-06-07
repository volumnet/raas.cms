<?php
/**
 * Стандартный интерфейс поиска
 * @param Block_Search $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

$interface = new SearchInterface($Block, $Page, $_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES);
return $interface->process();
