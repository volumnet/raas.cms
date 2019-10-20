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
  <?php
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
        <div class="{{WIDGET_CSS_CLASSNAME}}__address-title">
          <?php echo View_Web::i()->_('ADDRESS')?>:
        </div>
        <div class="{{WIDGET_CSS_CLASSNAME}}__address-value">
          <?php echo implode(', ', $addressArr)?>
        </div>
      </div>
  <?php } ?>
  <?php if ($phones = $company->fields['phone']->getValues(true)) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__phones">
        <div class="{{WIDGET_CSS_CLASSNAME}}__phones-title">
          <?php echo htmlspecialchars($company->field['phone']->name)?>:
        </div>
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
  <?php } ?>
  <?php if ($emails = $company->fields['email']->getValues(true)) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__emails">
        <div class="{{WIDGET_CSS_CLASSNAME}}__emails-title">
          <?php echo htmlspecialchars($company->field['email']->name)?>:
        </div>
        <div class="{{WIDGET_CSS_CLASSNAME}}__emails-list">
          <div class="{{WIDGET_CSS_CLASSNAME}}-emails-list">
            <?php foreach ($emails as $email) { ?>
                <span class="{{WIDGET_CSS_CLASSNAME}}-emails-list__item">
                  <span class="{{WIDGET_CSS_CLASSNAME}}-emails-item">
                    <a href="mailto:<?php echo htmlspecialchars($email)?>">
                      <?php echo htmlspecialchars($email)?>
                    </a>
                  </span>
                </span>
            <?php } ?>
          </div>
        </div>
      </div>
  <?php } ?>
  <?php if ($schedule = $company->schedule) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__schedule">
        <div class="{{WIDGET_CSS_CLASSNAME}}__schedule-title">
          <?php echo View_Web::i()->_('SCHEDULE')?>:
        </div>
        <div class="{{WIDGET_CSS_CLASSNAME}}__schedule-value">
          <?php echo htmlspecialchars($schedule)?>
        </div>
      </div>
  <?php } ?>
  <?php if ($transport = $company->transport) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__transport">
        <div class="{{WIDGET_CSS_CLASSNAME}}__transport-title">
          <?php echo View_Web::i()->_('TRANSPORT')?>:
        </div>
        <div class="{{WIDGET_CSS_CLASSNAME}}__transport-value">
          <?php echo htmlspecialchars($transport)?>
        </div>
      </div>
  <?php } ?>
</div>
