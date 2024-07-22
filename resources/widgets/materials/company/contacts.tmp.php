<?php
/**
 * Контакты
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Text;
use RAAS\AssetManager;

$company = $Page->company;
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
    $Page->headData .= ' <meta
                           property="og:image"
                           content="' . htmlspecialchars($host . '/' . $company->logo->fileURL) . '"
                         />';
}
$Page->headData .= ' <meta property="og:url" content="' . htmlspecialchars($host) . '" />
                     <meta property="business:contact_data:country_name" content="Russian Federation" />
                     <meta property="business:contact_data:website" content="' . htmlspecialchars($host) . '" />';
?>
<div class="contacts vcard" itemscope itemtype="http://schema.org/Organization">
  <span itemprop="name" class="fn org" style="display: none">
    <?php echo htmlspecialchars($company->name)?>
  </span>
  <?php
  $addressArr = $mapAddressArr = [];
  if ($postalCode = $company->postal_code) {
      $jsonLd['address']['postalCode'] = $postalCode;
      $addressArr[] = '<span class="postal-code" itemprop="postalCode">'
                    .    htmlspecialchars($postalCode)
                    . '</span>';
      $mapAddressArr[] = $postalCode;
  }
  if ($city = $company->city) {
      $jsonLd['address']['addressLocality'] = $city;
      $addressArr[] = '<span class="locality" itemprop="addressLocality">'
                    .    htmlspecialchars($city)
                    . '</span>';
      $mapAddressArr[] = 'г. ' . $city;
  }
  if ($streetAddress = $company->street_address) {
      $jsonLd['address']['streetAddress'] = $streetAddress;
      $addressArr[] = '<span class="street-address" itemprop="streetAddress">'
                    .    htmlspecialchars($streetAddress)
                    . '</span>';
      $mapAddressArr[] = $streetAddress;
  }
  if ($office = $company->office) {
      $addressArr[] = htmlspecialchars($office);
      $mapAddressArr[] = $office;
  }
  if (($lat = $company->lat) && ($lon = $company->lon)) { ?>
      <div class="contacts__map">
        <div
          data-v-once
          data-vue-role="yandex-map"
          data-v-bind_coords="[<?php echo (float)$lat?>, <?php echo (float)$lon?>]"
          data-v-bind_zoom="15"
          data-v-on_map-was-initialized="$event.controls.remove('searchControl')"
          data-v-bind_scroll-zoom="false"
        >
          <div
            data-vue-role="ymap-marker"
            data-v-bind_coords="[<?php echo (float)$lat?>, <?php echo (float)$lon?>]"
            data-vue-marker-id="company"
            data-vue-hint-content="<?php echo htmlspecialchars(implode(', ', $mapAddressArr))?>"
          ></div>
        </div>
      </div>
  <?php }
  if ($addressArr) {
      $jsonLd['address']['@type'] = 'PostalAddress'; ?>
      <div class="contacts__item contacts__item_address">
        <span class="contacts__item-title">
          <?php echo View_Web::i()->_('ADDRESS')?>:
        </span>
        <span class="contacts__item-value adr" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
          <?php echo implode(', ', $addressArr)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($phones = $company->fields['phone']->getValues(true)) {
      $jsonLd['telephone'] = (count($phones) > 1) ? $phones : $phones[0];
      $phonesText = array_map(function ($phone) {
          return '<a href="tel:%2B7' . Text::beautifyPhone($phone) . '" class="tel" itemprop="telephone">' .
                    htmlspecialchars($phone) .
                 '</a>';
      }, $phones);?>
      <div class="contacts__item contacts__item_phones">
        <span class="contacts__item-title">
          <?php echo htmlspecialchars($company->fields['phone']->name)?>:
        </span>
        <span class="contacts__item-value">
          <?php echo implode(', ', $phonesText)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($emails = $company->fields['email']->getValues(true)) {
      $jsonLd['email'] = (count($emails) > 1) ? $emails : $emails[0];
      $emailsText = array_map(function ($email) {
          return '<a href="mailto:' . htmlspecialchars($email) . '" class="email" itemprop="email">' .
                    htmlspecialchars($email) .
                 '</a';
      }, $emails);?>
      <div class="contacts__item contacts__item_emails">
        <span class="contacts__item-title">
          <?php echo htmlspecialchars($company->fields['email']->name)?>:
        </span>
        <span class="contacts__item-value">
          <?php echo implode(', ', $emailsText)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($schedule = $company->schedule) { ?>
      <div class="contacts__item contacts__item_schedule">
        <span class="contacts__item-title">
          <?php echo View_Web::i()->_('SCHEDULE')?>:
        </span>
        <span class="contacts__item-value">
          <?php echo htmlspecialchars($schedule)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($transport = $company->transport) { ?>
      <div class="contacts__item contacts__item_transport">
        <span class="contacts__item-title">
          <?php echo View_Web::i()->_('TRANSPORT')?>:
        </span>
        <span class="contacts__item-value">
          <?php echo nl2br(htmlspecialchars($transport))?>
        </span>
      </div>
  <?php } ?>
</div>
<script type="application/ld+json"><?php echo json_encode($jsonLd)?></script>
<?php
AssetManager::requestCSS('/css/contacts.css');
AssetManager::requestJS('/js/contacts.js');
