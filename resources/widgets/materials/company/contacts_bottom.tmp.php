<?php
/**
 * Виджет блока "Контакты в подвале"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Text;
use RAAS\AssetManager;

$company = $Set[0];

?>
<div class="contacts-bottom">
  <?php
  $addressArr = [];
  foreach ([
      'city' => 'city',
      'address' => 'street_address',
      'office' => 'office'
  ] as $suffix => $fieldURN) {
      if ($fieldVal = $company->$fieldURN) {
          $addressArr[] = '<span class="contacts-bottom__address-' . $suffix . '">'
                        .    htmlspecialchars($fieldVal)
                        . '</span>';
      }
  }
  if ($addressArr) { ?>
      <div class="contacts-bottom__address">
        <span class="contacts-bottom__address-title">
          <?php echo View_Web::i()->_('ADDRESS')?>:
        </span>
        <span class="contacts-bottom__address-value">
          <?php echo implode(', ', $addressArr)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($phones = $company->fields['phone']->getValues(true)) {
      $phonesText = array_map(function ($phone) {
          return '<a class="contacts-bottom-phones-list__item contacts-bottom-phones-item" href="tel:%2B7' . Text::beautifyPhone($phone) . '">' .
                    htmlspecialchars($phone) .
                 '</a>';
      }, $phones);?>
      <div class="contacts-bottom__phones">
        <span class="contacts-bottom__phones-title">
          <?php echo htmlspecialchars($company->fields['phone']->name)?>:
        </span>
        <span class="contacts-bottom__phones-list contacts-bottom-phones-list">
          <?php echo implode(', ', $phonesText)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($emails = $company->fields['email']->getValues(true)) {
      $emailsText = array_map(function ($email) {
          return '<a class="contacts-bottom-emails-list__item contacts-bottom-emails-item" href="mailto:' . htmlspecialchars($email) . '">' .
                    htmlspecialchars($email) .
                 '</a>';
      }, $emails);?>
      <div class="contacts-bottom__emails">
        <span class="contacts-bottom__emails-title">
          <?php echo htmlspecialchars($company->fields['email']->name)?>:
        </span>
        <span class="contacts-bottom__emails-list contacts-bottom-emails-list">
          <?php echo implode(', ', $emailsText)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($schedule = $company->schedule) { ?>
      <div class="contacts-bottom__schedule">
        <span class="contacts-bottom__schedule-title">
          <?php echo View_Web::i()->_('SCHEDULE')?>:
        </span>
        <span class="contacts-bottom__schedule-value">
          <?php echo htmlspecialchars($schedule)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($transport = $company->transport) { ?>
      <div class="contacts-bottom__transport">
        <span class="contacts-bottom__transport-title">
          <?php echo View_Web::i()->_('TRANSPORT')?>:
        </span>
        <span class="contacts-bottom__transport-value">
          <?php echo htmlspecialchars($transport)?>
        </span>
      </div>
  <?php } ?>
</div>
<?php
AssetManager::requestCSS('/css/contacts-bottom.css');
AssetManager::requestJS('/js/contacts-bottom.js');
