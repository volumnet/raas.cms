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
      <<?php echo $nat ? 'a href="/faq/"' : 'span'?> class="faq-main__title">
        <?php echo htmlspecialchars($Block->name)?>
      </<?php echo $nat ? 'a' : 'span'?>>
      <div class="faq-main__list faq-main-list">
        <?php foreach ($Set as $item) { ?>
            <div class="faq-main-list__item faq-main-item">
              <div class="faq-main-item__text faq-main-item__text_question">
                <?php if ($item->image->id) { ?>
                    <<?php echo $item->url ? 'a href="' . htmlspecialchars($item->url) . '"' : 'span'?>
                      class="faq-main-item__image"
                    >
                      <img
                        loading="lazy"
                        src="/<?php echo htmlspecialchars($item->image->tnURL)?>"
                        alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>"
                      />
                    </<?php echo $item->url ? 'a' : 'span'?>>
                <?php } ?>
                <div class="faq-main-item__title">
                  <<?php echo $item->url ? 'a href="' . htmlspecialchars($item->url) . '"' : 'span'?>
                    class="faq-main-item__name"
                  >
                    <?php echo htmlspecialchars($item->full_name)?>
                  </<?php echo $item->url ? 'a' : 'span'?>>
                  <?php
                  $time = strtotime($item->date);
                  if ($time <= 0) {
                      $time = strtotime($item->post_date);
                  }
                  if ($time > 0) { ?>
                      <span class="faq-main-item__date">
                        <?php
                        echo date('d', $time) . ' ' .
                            Text::$months[(int)date('m', $time)] . ' ' .
                            date('Y', $time);
                        ?>
                      </span>
                  <?php } ?>
                </div>
                <div class="faq-main-item__description">
                  <?php echo htmlspecialchars($item->name)?>
                </div>
              </div>
              <?php if ($item->answer) {
                  $htmlAnswer = html_entity_decode(strip_tags($item->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                  <div class="
                    faq-main-item__text
                    faq-main-item__text_answer
                    <?php echo !$item->url ? 'faq-main-item__text_slider' : ''?>
                  ">
                    <?php if ($item->answer_image->id) { ?>
                        <<?php echo $item->url ? 'a href="' . htmlspecialchars($item->url) . '"' : 'span'?>
                          class="faq-main-item__image"
                        >
                          <img
                            loading="lazy"
                            src="/<?php echo htmlspecialchars($item->answer_image->tnURL)?>"
                            alt="<?php echo htmlspecialchars($item->answer_image->name ?: $item->answer_name)?>"
                          />
                        </<?php echo $item->url ? 'a' : 'span'?>>
                    <?php } ?>
                    <div class="faq-main-item__title">
                      <<?php echo $item->url ? 'a href="' . htmlspecialchars($item->url) . '"' : 'span'?>
                        class="faq-main-item__name"
                      >
                        <?php if ($item->answer_name) {
                            if ((string)$item->answer_gender === '') {
                                echo ANSWERED_UNDEFINED;
                            } elseif ($item->answer_gender) {
                                echo ANSWERED_MALE;
                            } else {
                                echo ANSWERED_FEMALE;
                            }
                            echo ' ' . htmlspecialchars($item->answer_name);
                        } else {
                            echo ANSWER;
                        } ?>
                      </<?php echo $item->url ? 'a' : 'span'?>>
                      <?php
                      $time = strtotime($item->answer_date);
                      if ($time <= 0) {
                          $time = strtotime($item->modify_date);
                      }
                      if ($time > 0) {
                          ?>
                          <span class="faq-main-item__date">
                            <?php
                            echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time);
                            ?>
                          </span>
                      <?php } ?>
                    </div>
                    <div class="faq-main-item__description">
                      <div class="faq-main-item__brief-description">
                        <?php echo Text::cuttext($htmlAnswer, 256, '...')?>
                      </div>
                      <?php if (!$item->url) { ?>
                          <div class="faq-main-item__full-description">
                            <?php echo $item->answer?>
                          </div>
                      <?php } ?>
                    </div>
                    <div class="faq-main-item__more">
                      <?php if ($item->url || (mb_strlen($htmlAnswer) > 256)) { ?>
                          <<?php echo $item->url ?
                                ('a href="' . htmlspecialchars($item->url) . '"') :
                                ('span class="faq-main-item__more-trigger" data-show="' . READ_ANSWER . '" data-hide="' . HIDE . '"');
                            ?>
                          >
                            <?php echo READ_ANSWER?>
                          </<?php echo $item->url ? 'a' : 'span'?>>
                      <?php } ?>
                    </div>
                  </div>
              <?php } ?>
            </div>
        <?php } ?>
      </div>
    </div>
<?php } ?>
