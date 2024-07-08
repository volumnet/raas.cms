<?php
/**
 * favicon
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

$company = $Page->company;

$image = null;
switch ($_GET['type'] ?? null) {
    case 'svg':
        $contentType = 'image/svg+xml';
        $image = $company->favicon_svg;
        break;
    case 'apple':
        $contentType = 'image/png';
        $image = $company->apple_touch_icon;
        break;
    case 'manifest':
        $contentType = 'image/png';
        $image = $company->manifest_logo;
        break;
    default:
        $contentType = 'image/x-icon';
        $image = $company->favicon_ico;
        break;
}
header('Content-Type: ' . $contentType);
if ($image && $image->id) {
    readfile($image->file);
} else {
    $Page->Material = new Material(); // Для генерации 404-й ошибки
}
