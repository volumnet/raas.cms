<?php
/**
 * Виджет блока "Контакты в шапке"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Text;
use RAAS\AssetManager;

$company = $Set[0];

?>
<div class="contacts-top">
  <?php if ($phones = $company->fields['phone']->getValues(true)) {
      $phonesText = array_map(function ($phone) {
          return '<a class="contacts-top-phones-list__item contacts-top-phones-item" href="tel:%2B7' . Text::beautifyPhone($phone) . '">' .
                    htmlspecialchars($phone) .
                 '</a>';
      }, $phones);?>
      <div class="contacts-top__phones contacts-top__phones-list contacts-top-phones-list">
        <?php echo implode(', ', $phonesText)?>
      </div>
  <?php }
  $addressArr = [];
  foreach ([
      'city' => 'city',
      'address' => 'street_address',
      'office' => 'office'
  ] as $suffix => $fieldURN) {
      if ($fieldVal = $company->$fieldURN) {
          $addressArr[] = '<span class="contacts-top__address-' . $suffix . '">'
                        .    htmlspecialchars($fieldVal)
                        . '</span>';
      }
  }
  if ($addressArr) { ?>
      <div class="contacts-top__address">
        <?php echo implode(', ', $addressArr)?>
      </div>
  <?php } ?>
</div>
<?php
AssetManager::requestCSS('/css/contacts-top.css');
AssetManager::requestJS('/js/contacts-top.js');
