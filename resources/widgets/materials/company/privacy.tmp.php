<?php
/**
 * Виджет страницы "Политика обработки персональных данных"
 * @param Block_HTML $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

use Twig_Environment;
use Twig_Loader_String;
use RAAS\AssetManager;

$companyMaterialType = Material_Type::importByURN('company');
$companies = Material::getSet([
    'where' => "pid = " . (int)$companyMaterialType->id,
    'orderBy' => "NOT priority, priority",
]);
if ($companies) {
    $company = $companies[0];
} else {
    $company = new Material();
}

$templateData = ['name' => $company->name];
foreach ($company->fields as $fieldURN => $field) {
    $val = $field->getValue();
    $templateData[$fieldURN] = $val;
}

$twig = new Twig_Environment(new Twig_Loader_String());
$text = $twig->render($Block->description, $templateData);

echo $text;

AssetManager::requestCSS('/css/privacy.css');
AssetManager::requestJS('/js/privacy.js');
