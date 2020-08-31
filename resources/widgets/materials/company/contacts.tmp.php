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
$jsonLd = [
    '@context' => 'http://schema.org',
    '@type' => 'Organization',
    'name' => $company->name,
];
$host = 'http' . (mb_strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '') . '://'
      . $_SERVER['HTTP_HOST'];
$Page->headPrefix = 'business: http://ogp.me/ns/business#';
$Page->headData = ' <meta property="og:title" content="' . htmlspecialchars($company->name) . '" />
                    <meta property="og:type" content="business.business" />';
if ($company->logo->id) {
    $Page->headData .= ' <meta property="og:image" content="' . htmlspecialchars($host . '/' . $company->logo->fileURL) . '" />';
}
$Page->headData .= ' <meta property="og:url" content="' . htmlspecialchars($host) . '" />
                     <meta property="business:contact_data:country_name" content="Russian Federation" />
                     <meta property="business:contact_data:website" content="' . htmlspecialchars($host) . '" />';
?>
<div class="{{WIDGET_CSS_CLASSNAME}} vcard" itemscope itemtype="http://schema.org/Organization">
  <span itemprop="name" class="fn org" style="display: none">
    <?php echo htmlspecialchars($company->name)?>
  </span>
  <?php if ($map = $company->map) { ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__map">
        <?php
        $map = preg_replace('/width=\\d+("|&)/i', 'width=100%$1', $map);
        $map = preg_replace('/type=".*?"/i', 'type="application/javascript"', $map);
        echo $map;
        ?>
      </div>
  <?php }
  $addressArr = [];
  if ($postalCode = $company->postal_code) {
      $jsonLd['address']['postalCode'] = $postalCode;
      $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-postal-code postal-code" itemprop="postalCode">'
                    .    htmlspecialchars($postalCode)
                    . '</span>';
  }
  if ($city = $company->city) {
      $jsonLd['address']['addressLocality'] = $city;
      $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-city locality" itemprop="addressLocality">'
                    .    htmlspecialchars($city)
                    . '</span>';
  }
  if ($streetAddress = $company->street_address) {
      $jsonLd['address']['streetAddress'] = $streetAddress;
      $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-address street-address" itemprop="streetAddress">'
                    .    htmlspecialchars($streetAddress)
                    . '</span>';
  }
  if ($office = $company->office) {
      $addressArr[] = '<span class="{{WIDGET_CSS_CLASSNAME}}__address-office">'
                    .    htmlspecialchars($office)
                    . '</span>';
  }
  if ($addressArr) {
      $jsonLd['address']['@type'] = 'PostalAddress'; ?>
      <div class="{{WIDGET_CSS_CLASSNAME}}__address">
        <span class="{{WIDGET_CSS_CLASSNAME}}__address-title">
          <?php echo View_Web::i()->_('ADDRESS')?>:
        </span>
        <span class="{{WIDGET_CSS_CLASSNAME}}__address-value adr" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
          <?php echo implode(', ', $addressArr)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($phones = $company->fields['phone']->getValues(true)) {
      $jsonLd['telephone'] = (count($phones) > 1) ? $phones : $phones[0];
      $phonesText = array_map(function ($phone) {
          return '<span class="{{WIDGET_CSS_CLASSNAME}}-phones-list__item">' .
                   '<span class="{{WIDGET_CSS_CLASSNAME}}-phones-item">' .
                     '<a href="tel:%2B7' . Text::beautifyPhone($phone) . '" class="tel" itemprop="telephone">' .
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
      $jsonLd['email'] = (count($emails) > 1) ? $emails : $emails[0];
      $emailsText = array_map(function ($email) {
          return '<span class="{{WIDGET_CSS_CLASSNAME}}-emails-list__item">' .
                   '<span class="{{WIDGET_CSS_CLASSNAME}}-emails-item">' .
                     '<a href="mailto:' . htmlspecialchars($email) . '" class="email" itemprop="email">' .
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
<script type="application/ld+json"><?php echo json_encode($jsonLd)?></script>
