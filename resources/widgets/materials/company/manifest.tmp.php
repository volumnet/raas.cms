<?php
/**
 * manifest.json
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

$company = $Page->company;

$json = [
   'version' => '1.0',
   'api_version' => 3,
   'name' => $company->name,
   'short_name' => $company->name,
   'display' => 'standalone',
   'start_url' => '/',
   'icons' => [],
   'layout' => [
       'show_title' => 'none'
   ]
];

if ($company->apple_touch_icon && $company->apple_touch_icon->id) {
    $sizes = getimagesize($company->apple_touch_icon->file);
    $json['icons'][] = [
       'src' => '/apple-touch-icon.png',
       'type' => 'image/png',
       'sizes' => $sizes[0] . 'x' . $sizes[1],
    ];
}
if ($company->manifest_logo && $company->manifest_logo->id) {
    $json['layout']['logo'] = '/manifest-logo.png';
}
if ($company->primary_color) {
    $json['layout']['color'] = $company->primary_color;
}

echo json_encode($json, JSON_UNESCAPED_UNICODE);
