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
  <?php if ($phones = $company->fields['phone']->getValues(true)) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__phones">
        <div class="{{WIDGET_CSS_CLASSNAME}}__phones-list">
          <div class="{{WIDGET_CSS_CLASSNAME}}-phones-list">
            <?php foreach ($phones as $phone) { ?>
                <span class="{{WIDGET_CSS_CLASSNAME}}-phones-list__item">
                  <span class="{{WIDGET_CSS_CLASSNAME}}-phones-item">
                    <a href="tel:%2B7<?php echo Text::beautifyPhone($phone)?>">
                      <?php echo htmlspecialchars($phone)?>
                    </a>
                  </span>
                </span>
            <?php } ?>
          </div>
        </div>
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
