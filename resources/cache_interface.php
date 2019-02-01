<?php
/**
 * Стандартный интерфейс кэширования
 * @param Block $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param mixed $OUT Данные, полученные от интерфейса блока
 */
namespace RAAS\CMS;

$interface = new CacheInterface($Block, $Page, $_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES, $OUT);
return $interface->process();
