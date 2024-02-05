<?php
/**
 * Вопрос-ответ для главной страницы
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Text;

$nat = true;

if ($Set) { ?>
    <div class="faq-main">
      <a class="faq-main__title"<?php echo $nat ? ' href="/faq/"' : ''?>>
        <?php echo htmlspecialchars($Block->name)?>
      </a>
      <div class="faq-main__list faq-main-list">
        <?php foreach ($Set as $item) { ?>
            <div class="faq-main-list__item faq-main-item">
              <div class="faq-main-item__text faq-main-item__text_question">
                <?php if ($item->image->id) { ?>
                    <a class="faq-main-item__image"<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                      <img loading="lazy" src="/<?php echo htmlspecialchars($item->image->tnURL)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" /></a>
                <?php } ?>
                <div class="faq-main-item__title">
                  <a class="faq-main-item__name"<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                    <?php echo htmlspecialchars($item->full_name)?></a>
                  <?php
                  $time = strtotime($item->date);
                  if ($time <= 0) {
                      $time = strtotime($item->post_date);
                  }
                  if ($time > 0) { ?>
                      <span class="faq-main-item__date">
                        <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
                      </span>
                  <?php } ?>
                </div>
                <div class="faq-main-item__description">
                  <?php echo htmlspecialchars($item->name)?>
                </div>
              </div>
              <?php if ($item->answer) { ?>
                  <div class="faq-main-item__text faq-main-item__text_answer<?php echo !$item->url ? ' faq-main-item__text_slider' : ''?>">
                    <?php if ($item->answer_image->id) { ?>
                        <a class="faq-main-item__image"<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                          <img loading="lazy" src="/<?php echo htmlspecialchars($item->answer_image->tnURL)?>" alt="<?php echo htmlspecialchars($item->answer_image->name ?: $item->answer_name)?>" /></a>
                    <?php } ?>
                    <div class="faq-main-item__title">
                      <a class="faq-main-item__name"<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                        <?php if ($item->answer_name) { ?>
                            <?php echo (((string)$item->answer_gender === '') ? ANSWERED_UNDEFINED : ($item->answer_gender ? ANSWERED_MALE : ANSWERED_FEMALE)) . ' ' . htmlspecialchars($item->answer_name)?>
                        <?php } else { ?>
                            <?php echo ANSWER?>
                        <?php } ?>
                      </a>
                      <?php
                      $time = strtotime($item->answer_date);
                      if ($time <= 0) {
                          $time = strtotime($item->modify_date);
                      }
                      if ($time > 0) {
                          ?>
                          <span class="faq-main-item__date">
                            <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
                          </span>
                      <?php } ?>
                    </div>
                    <div class="faq-main-item__description">
                      <div class="faq-main-item__brief-description">
                        <?php echo Text::cuttext(html_entity_decode(strip_tags($item->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...')?>
                      </div>
                      <?php if (!$item->url) { ?>
                          <div class="faq-main-item__full-description">
                            <?php echo $item->answer?>
                          </div>
                      <?php } ?>
                    </div>
                    <div class="faq-main-item__more">
                      <?php if ($item->url && (mb_strlen(html_entity_decode(strip_tags($item->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8')) > 256)) { ?>
                          <a<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ' class="faq-main-item__more-trigger" data-show="' . READ_ANSWER . '" data-hide="' . HIDE . '"'?>>
                            <?php echo READ_ANSWER?>
                          </a>
                      <?php } ?>
                    </div>
                  </div>
              <?php } ?>
            </div>
        <?php } ?>
      </div>
    </div>
<?php } ?>
