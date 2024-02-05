<?php
/**
 * Контакты в подвале
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
          $addressArr[] = htmlspecialchars($fieldVal);
      }
  }
  if ($addressArr) { ?>
      <div class="contacts-bottom__item contacts-bottom__item_address">
        <span class="contacts-bottom__item-title">
          <?php echo View_Web::i()->_('ADDRESS')?>:
        </span>
        <span class="contacts-bottom__item-value">
          <?php echo implode(', ', $addressArr)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($phones = $company->fields['phone']->getValues(true)) {
      $phonesText = array_map(function ($phone) {
          return '<a href="tel:%2B7' . Text::beautifyPhone($phone) . '">' .
                    htmlspecialchars($phone) .
                 '</a>';
      }, $phones);?>
      <div class="contacts-bottom__item contacts-bottom__item_phones">
        <span class="contacts-bottom__item-title">
          <?php echo htmlspecialchars($company->fields['phone']->name)?>:
        </span>
        <span class="contacts-bottom__item-value">
          <?php echo implode(', ', $phonesText)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($emails = $company->fields['email']->getValues(true)) {
      $emailsText = array_map(function ($email) {
          return '<a href="mailto:' . htmlspecialchars($email) . '">' .
                    htmlspecialchars($email) .
                 '</a>';
      }, $emails);?>
      <div class="contacts-bottom__item contacts-bottom__item_emails">
        <span class="contacts-bottom__item-title">
          <?php echo htmlspecialchars($company->fields['email']->name)?>:
        </span>
        <span class="contacts-bottom__item-value">
          <?php echo implode(', ', $emailsText)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($schedule = $company->schedule) { ?>
      <div class="contacts-bottom__item contacts-bottom__item_schedule">
        <span class="contacts-bottom__item-title">
          <?php echo View_Web::i()->_('SCHEDULE')?>:
        </span>
        <span class="contacts-bottom__item-value">
          <?php echo htmlspecialchars($schedule)?>
        </span>
      </div>
  <?php } ?>
  <?php if ($transport = $company->transport) { ?>
      <div class="contacts-bottom__item contacts-bottom__item_transport">
        <span class="contacts-bottom__item-title">
          <?php echo View_Web::i()->_('TRANSPORT')?>:
        </span>
        <span class="contacts-bottom__item-value">
          <?php echo htmlspecialchars($transport)?>
        </span>
      </div>
  <?php } ?>
</div>
<?php
AssetManager::requestCSS('/css/contacts-bottom.css');
AssetManager::requestJS('/js/contacts-bottom.js');
