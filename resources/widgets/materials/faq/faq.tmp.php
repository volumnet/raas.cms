<?php
/**
 * Вопрос-ответ
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Pages $Pages Постраничная разбивка
 * @param Material[]|null $Set Список материалов
 * @param Material|null $Item Активный материал
 */
namespace RAAS\CMS;

use SOME\Text;
use SOME\HTTP;
use RAAS\AssetManager;

if ($Item) { ?>
    <div class="faq faq__article faq-article">
      <div class="faq-article__text faq-article__text_question">
        <?php if ($Item->image->id) { ?>
            <img class="faq-article__image" loading="lazy" src="/<?php echo $Item->image->tnURL?>" alt="<?php echo htmlspecialchars($Item->image->name ?: $Item->name)?>" />
        <?php } ?>
        <div class="faq-article__title">
          <span class="faq-article__name">
            <?php echo htmlspecialchars($Item->full_name)?>
          </span>
          <?php
          $time = strtotime($Item->date);
          if ($time <= 0) {
              $time = strtotime($Item->post_date);
          }
          if ($time > 0) { ?>
              <span class="faq-article__date">
                <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
              </span>
          <?php } ?>
        </div>
        <div class="faq-article__description">
          <?php echo htmlspecialchars($Item->name)?>
        </div>
      </div>
      <?php if ($Item->answer) { ?>
          <div class="faq-article__text faq-article__text_answer">
            <?php if ($Item->answer_image->id) { ?>
                <div class="faq-article__image">
                  <img loading="lazy" src="/<?php echo $Item->answer_image->tnURL?>" alt="<?php echo htmlspecialchars($Item->answer_image->name ?: $Item->answer_name)?>" />
                </div>
            <?php } ?>
            <div class="faq-article__title">
              <span class="faq-article__name">
                <?php if ($Item->answer_name) { ?>
                    <?php echo (((string)$Item->answer_gender === '') ? ANSWERED_UNDEFINED : ($Item->answer_gender ? ANSWERED_MALE : ANSWERED_FEMALE)) . ' ' . htmlspecialchars($Item->answer_name)?>
                <?php } else { ?>
                    <?php echo ANSWER?>
                <?php } ?>
              </span>
              <?php
              $time = strtotime($Item->answer_date);
              if ($time <= 0) {
                  $time = strtotime($Item->modify_date);
              }
              if ($time > 0) { ?>
                  <span class="faq-article__date">
                    <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
                  </span>
              <?php } ?>
            </div>
            <div class="faq-article__description">
              <?php echo $Item->answer?>
            </div>
          </div>
      <?php } ?>
    </div>
    <?php
    AssetManager::requestCSS('/css/faq-article.css');
    AssetManager::requestJS('/js/faq-article.js');
} elseif ($Set) { ?>
    <div class="faq">
      <div class="faq__list faq-list">
        <?php foreach ($Set as $item) { ?>
            <div class="faq-list__item">
              <div class="faq-item">
                <div class="faq-item__text faq-item__text_question">
                  <?php if ($item->image->id) { ?>
                      <a class="faq-item__image"<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                        <img loading="lazy" src="/<?php echo htmlspecialchars($item->image->tnURL)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" /></a>
                  <?php } ?>
                  <div class="faq-item__title">
                    <a class="faq-item__name"<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                      <?php echo htmlspecialchars($item->full_name)?></a>
                    <?php
                    $time = strtotime($item->date);
                    if ($time <= 0) {
                        $time = strtotime($item->post_date);
                    }
                    if ($time > 0) { ?>
                        <span class="faq-item__date">
                          <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
                        </span>
                    <?php } ?>
                  </div>
                  <div class="faq-item__description">
                    <?php echo htmlspecialchars($item->name)?>
                  </div>
                </div>
                <?php if ($item->answer) { ?>
                    <div class="faq-item__text faq-item__text_answer<?php echo !$item->url ? ' faq-item__text_slider' : ''?>">
                      <?php if ($item->answer_image->id) { ?>
                          <div class="faq-item__image">
                            <a<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                              <img loading="lazy" src="/<?php echo htmlspecialchars($item->answer_image->tnURL)?>" alt="<?php echo htmlspecialchars($item->answer_image->name ?: $item->answer_name)?>" /></a>
                          </div>
                      <?php } ?>
                      <div class="faq-item__title">
                        <a class="faq-item__name"<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
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
                            <span class="faq-item__date">
                              <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
                            </span>
                        <?php } ?>
                      </div>
                      <div class="faq-item__description">
                        <div class="faq-item__brief-description">
                          <?php echo Text::cuttext(html_entity_decode(strip_tags($item->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...')?>
                        </div>
                        <?php if (!$item->url) { ?>
                            <div class="faq-item__full-description">
                              <?php echo $item->answer?>
                            </div>
                        <?php } ?>
                      </div>
                      <div class="faq-item__more">
                        <?php if ($item->url && (mb_strlen(html_entity_decode(strip_tags($item->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8')) > 256)) { ?>
                            <a<?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ' class="faq-item__more-trigger" data-show="' . READ_ANSWER . '" data-hide="' . HIDE . '"'?>>
                              <?php echo READ_ANSWER?>
                            </a>
                        <?php } ?>
                      </div>
                    </div>
                <?php } ?>
              </div>
            </div>
        <?php } ?>
      </div>
      <?php if ($Pages->pages > 1) { ?>
          <div class="faq__pagination">
            <?php Snippet::importByURN('pagination')->process(['pages' => $Pages]); ?>
          </div>
      <?php } ?>
    </div>
    <?php
    AssetManager::requestCSS('/css/faq.css');
    AssetManager::requestJS('/js/faq.js');
} ?>
