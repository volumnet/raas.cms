<?php
/**
 * Политика обработки персональных данных
 * @param Block_HTML $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

use SOME\Text;
use RAAS\AssetManager;

$company = $Page->company;

$templateData = ['name' => $company->name];
foreach ($company->fields as $fieldURN => $field) {
    $val = $field->getValue();
    $templateData[$fieldURN] = $val;
}

$text = Text::renderTemplate($Block->description, $templateData);

echo $text;
