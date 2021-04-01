<?php
/**
 * Виджет блока "Контакты в шапке"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Text;

$company = $Set[0];

?>
<div class="contacts-top">
  <?php if ($phones = $company->fields['phone']->getValues(true)) {
      $phonesText = array_map(function ($phone) {
          return '<span class="contacts-top-phones-list__item">' .
                   '<span class="contacts-top-phones-item">' .
                     '<a href="tel:%2B7' . Text::beautifyPhone($phone) . '">' .
                        htmlspecialchars($phone) .
                     '</a>' .
                   '</span>' .
                 '</span>';
      }, $phones);?>
      <div class="contacts-top__phones">
        <span class="contacts-top__phones-list">
          <span class="contacts-top-phones-list">
            <?php echo implode(', ', $phonesText)?>
          </span>
        </span>
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
Package::i()->requestCSS('/css/contacts-top.css');
Package::i()->requestJS('/js/contacts-top.js');
