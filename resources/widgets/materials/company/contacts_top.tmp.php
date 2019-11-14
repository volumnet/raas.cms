<?php
/**
 * Виджет блока "{{WIDGET_NAME}}"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Text;

$company = $Set[0];

?>
<div class="{{WIDGET_CSS_CLASSNAME}}">
  <?php if ($phones = $company->fields['phone']->getValues(true)) {
      $phonesText = array_map(function ($phone) {
          return '<span class="{{WIDGET_CSS_CLASSNAME}}-phones-list__item">' .
                   '<span class="{{WIDGET_CSS_CLASSNAME}}-phones-item">' .
                     '<a href="tel:%2B7' . Text::beautifyPhone($phone) . '">' .
                        htmlspecialchars($phone) .
                     '</a>' .
                   '</span>' .
                 '</span>';
      }, $phones);?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__phones">
        <span class="{{WIDGET_CSS_CLASSNAME}}__phones-list">
          <span class="{{WIDGET_CSS_CLASSNAME}}-phones-list">
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
          $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-' . $suffix . '">'
                        .    htmlspecialchars($fieldVal)
                        . '</span>';
      }
  }
  if ($addressArr) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__address">
        <?php echo implode(', ', $addressArr)?>
      </div>
  <?php } ?>
</div>
