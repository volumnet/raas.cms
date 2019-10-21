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
<div class="{{WIDGET_CSS_CLASSNAME}}" itemscope itemtype="http://schema.org/Organization">
  <meta itemprop="name" content="<?php echo htmlspecialchars($company->name)?>" />
  <?php if ($map = $company->map) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__map">
        <?php echo $map?>
      </div>
  <?php }
  $addressArr = [];
  if ($postalCode = $company->postal_code) {
      $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-postal-code" itemprop="postalCode">'
                    .    htmlspecialchars($postalCode)
                    . '</span>';
  }
  if ($city = $company->city) {
      $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-city" itemprop="addressLocality">'
                    .    htmlspecialchars($city)
                    . '</span>';
  }
  if ($streetAddress = $company->street_address) {
      $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-address" itemprop="streetAddress">'
                    .    htmlspecialchars($streetAddress)
                    . '</span>';
  }
  if ($office = $company->office) {
      $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-office">'
                    .    htmlspecialchars($office)
                    . '</span>';
  }
  if ($addressArr) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__address">
        <span class="{{WIDGET_CSS_CLASSNAME}}__address-title">
          <?php echo View_Web::i()->_('ADDRESS')?>:
        </span>
        <span class="{{WIDGET_CSS_CLASSNAME}}__address-value" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
          <?php echo implode(', ', $addressArr)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($phones = $company->fields['phone']->getValues(true)) {
      $phonesText = array_map(function ($phone) {
          return '<span class="{{WIDGET_CSS_CLASSNAME}}-phones-list__item">' .
                   '<span class="{{WIDGET_CSS_CLASSNAME}}-phones-item">' .
                     '<a href="tel:%2B7' . Text::beautifyPhone($phone) . '" itemprop="telephone">' .
                        htmlspecialchars($phone) .
                     '</a>' .
                   '</span>' .
                 '</span>';
      }, $phones);?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__phones">
        <span class="{{WIDGET_CSS_CLASSNAME}}__phones-title">
          <?php echo htmlspecialchars($company->fields['phone']->name)?>:
        </span>
        <span class="{{WIDGET_CSS_CLASSNAME}}__phones-list">
          <span class="{{WIDGET_CSS_CLASSNAME}}-phones-list">
            <?php echo implode(', ', $phonesText)?>
          </span>
        </span>
      </div>
  <?php } ?>
  <?php if ($emails = $company->fields['email']->getValues(true)) {
      $emailsText = array_map(function ($email) {
          return '<span class="{{WIDGET_CSS_CLASSNAME}}-emails-list__item">' .
                   '<span class="{{WIDGET_CSS_CLASSNAME}}-emails-item">' .
                     '<a href="mailto:' . htmlspecialchars($email) . '" itemprop="telephone">' .
                        htmlspecialchars($email) .
                     '</a>' .
                   '</span>' .
                 '</span>';
      }, $emails);?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__emails">
        <span class="{{WIDGET_CSS_CLASSNAME}}__emails-title">
          <?php echo htmlspecialchars($company->fields['email']->name)?>:
        </span>
        <span class="{{WIDGET_CSS_CLASSNAME}}__emails-list">
          <span class="{{WIDGET_CSS_CLASSNAME}}-emails-list">
            <?php echo implode(', ', $emailsText)?>
          </span>
        </span>
      </div>
  <?php } ?>
  <?php if ($schedule = $company->schedule) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__schedule">
        <span class="{{WIDGET_CSS_CLASSNAME}}__schedule-title">
          <?php echo View_Web::i()->_('SCHEDULE')?>:
        </span>
        <span class="{{WIDGET_CSS_CLASSNAME}}__schedule-value">
          <?php echo htmlspecialchars($schedule)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($transport = $company->transport) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__transport">
        <span class="{{WIDGET_CSS_CLASSNAME}}__transport-title">
          <?php echo View_Web::i()->_('TRANSPORT')?>:
        </span>
        <span class="{{WIDGET_CSS_CLASSNAME}}__transport-value">
          <?php echo htmlspecialchars($transport)?>
        </span>
      </div>
  <?php } ?>
</div>
