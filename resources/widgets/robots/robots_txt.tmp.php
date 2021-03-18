<?php
/**
 * Виджет раздела robots.txt
 * @param Page $Page Текущая страница
 * @param Block_HTML $Block Текущий блок
 */
namespace RAAS\CMS;

header('Content-Type: text/plain; charset=UTF-8');
$text = $Block->description;
$text = str_replace('{{HOST}}', $_SERVER['HTTP_HOST'], $text);
echo $text;
