<?php
/**
 * Контакты в шапке
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Text;
use RAAS\AssetManager;

$company = $Page->company;

?>
<div class="contacts-top">
  <?php if ($phones = $company->fields['phone']->getValues(true)) { ?>
      <div class="contacts-top__phones">
        <?php foreach ($phones as $phone) { ?>
            <a href="tel:%2B7<?php echo Text::beautifyPhone($phone)?>">
              <?php echo htmlspecialchars($phone)?>
            </a>
        <?php } ?>
      </div>
  <?php }
  $addressArr = [];
  foreach ([
      'city' => 'city',
      'address' => 'street_address',
      'office' => 'office'
  ] as $suffix => $fieldURN) {
      if ($fieldVal = $company->$fieldURN) {
          $addressArr[] = htmlspecialchars($fieldVal);
      }
  }
  if ($addressArr) { ?>
      <div class="contacts-top__address">
        <?php echo implode(', ', $addressArr)?>
      </div>
  <?php } ?>
</div>
