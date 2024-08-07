<?php
/**
 * Социальные сети
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

$company = $Page->company;

$socialsData = [
    SocialProfile::SN_VK => ['urn' => 'vk', 'name' => 'VK'],
    SocialProfile::SN_FB => ['urn' => 'facebook', 'name' => 'FACEBOOK'],
    SocialProfile::SN_OK => ['urn' => 'odnoklassniki', 'name' => 'ODNOKLASSNIKI'],
    SocialProfile::SN_MR => ['urn' => 'mailru', 'name' => 'MAIL_RU_MY_WORLD'],
    SocialProfile::SN_TW => ['urn' => 'twitter', 'name' => 'TWITTER'],
    SocialProfile::SN_LJ => ['urn' => 'livejournal', 'name' => 'LIVEJOURNAL'],
    SocialProfile::SN_GO => ['urn' => 'google-plus', 'name' => 'GOOGLE_PLUS'],
    SocialProfile::SN_YA => ['urn' => 'yandex', 'name' => 'YANDEX'],
    SocialProfile::SN_WM => ['urn' => 'webmoney', 'name' => 'WEBMONEY'],
    SocialProfile::SN_YT => ['urn' => 'youtube', 'name' => 'YOUTUBE'],
    SocialProfile::SN_IN => ['urn' => 'instagram', 'name' => 'INSTAGRAM'],
    SocialProfile::SN_WA => ['urn' => 'whatsapp', 'name' => 'WHATSAPP'],
    SocialProfile::SN_TG => ['urn' => 'telegram', 'name' => 'TELEGRAM'],
];
if ($socials = $company->fields['socials']->getValues(true)) { ?>
    <div class="socials socials__list socials-list">
      <?php foreach ($socials as $social) {
          if ($snId = SocialProfile::getSocialNetwork($social)) {
              if ($socialData = $socialsData[$snId]) { ?>
                  <a
                    href="<?php echo htmlspecialchars($social)?>"
                    class="
                      socials-list__item
                      socials-item
                      socials-item_<?php echo htmlspecialchars($socialData['urn'])?>
                    "
                    title="<?php echo htmlspecialchars(View_Web::i()->_($socialData['name']))?>"
                    target="_blank"
                    rel="nofollow"
                  ></a>
              <?php }
          }
      } ?>
    </div>
<?php }
