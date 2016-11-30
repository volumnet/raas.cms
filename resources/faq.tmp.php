<?php
namespace RAAS\CMS;

use \SOME\Text;
use \SOME\HTTP;

if ($Item) {
    ?>
    <div class="faq">
      <div class="faq__article">
        <div class="faq-article">
          <div class="faq-article__text faq-article__text_question">
            <?php if ($Item->image->id) { ?>
                <div class="faq-article__image">
                  <img src="/<?php echo $Item->image->tnURL?>" alt="<?php echo htmlspecialchars($Item->image->name ?: $Item->name)?>" />
                </div>
            <?php } ?>
            <div class="faq-article__title">
              <?php
              $t = strtotime($Item->date);
              if ($t <= 0) {
                  $t = strtotime($Item->post_date);
              }
              if ($t > 0) {
                  ?>
                  <span class="faq-article__date">
                    <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                  </span>
              <?php } ?>
            </div>
            <div class="faq-article__description">
              <?php echo $Item->description?>
            </div>
          </div>
          <?php if ($Item->answer) { ?>
              <div class="faq-article__text faq-article__text_answer">
                <?php if ($Item->answer_image->id) { ?>
                    <div class="faq-article__image">
                      <img src="/<?php echo $Item->answer_image->tnURL?>" alt="<?php echo htmlspecialchars($Item->answer_image->name ?: $Item->answer_name)?>" />
                    </div>
                <?php } ?>
                <div class="faq-article__title">
                  <span class="faq-article__name">
                    <?php if ($Item->answer_name) { ?>
                        <?php echo ((string)$Item->answer_gender === '') ? ANSWERED_UNDEFINED : ($Item->answer_gender ? ANSWERED_MALE : ANSWERED_FEMALE) . ' ' . htmlspecialchars($Item->answer_name)?>
                    <?php } else { ?>
                        <?php echo ANSWER?>
                    <?php } ?>
                  </span>
                  <?php
                  $t = strtotime($Item->answer_date);
                  if ($t <= 0) {
                      $t = strtotime($Item->modify_date);
                  }
                  if ($t > 0) {
                      ?>
                      <span class="faq-article__date">
                        <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                      </span>
                  <?php } ?>
                </div>
                <div class="faq-article__description">
                  <?php echo $Item->answer?>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
    </div>
<?php } elseif ($Set) { ?>
    <div class="faq">
      <div class="faq__list">
        <div class="faq-list">
          <?php foreach ($Set as $row) { ?>
              <div class="faq-list__item">
                <div class="faq-item">
                  <div class="faq-item__text faq-item__text_question">
                    <?php if ($row->image->id) { ?>
                        <a class="faq-item__image"<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                          <img src="/<?php echo htmlspecialchars($row->image->tnURL)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" /></a>
                    <?php } ?>
                    <div class="faq-item__title">
                      <a class="faq-item__name"<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                        <?php echo htmlspecialchars($row->name)?></a>
                      <?php
                      $t = strtotime($row->date);
                      if ($t <= 0) {
                          $t = strtotime($row->post_date);
                      }
                      if ($t > 0) {
                          ?>
                          <span class="faq-item__date">
                            <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                          </span>
                      <?php } ?>
                    </div>
                    <div class="faq-item__description">
                      <?php echo $row->description?>
                    </div>
                  </div>
                  <?php if ($row->answer) { ?>
                      <div class="faq-item__text faq-item__text_answer<?php echo !$Block->nat ? ' faq-item__text_slider' : ''?>">
                        <?php if ($row->answer_image->id) { ?>
                            <div class="faq-item__image">
                              <a<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                                <img src="/<?php echo htmlspecialchars($row->answer_image->tnURL)?>" alt="<?php echo htmlspecialchars($row->answer_image->name ?: $row->answer_name)?>" /></a>
                            </div>
                        <?php } ?>
                        <div class="faq-item__title">
                          <a class="faq-item__name"<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                            <?php if ($row->answer_name) { ?>
                                <?php echo ((string)$row->answer_gender === '') ? ANSWERED_UNDEFINED : ($row->answer_gender ? ANSWERED_MALE : ANSWERED_FEMALE) . ' ' . htmlspecialchars($row->answer_name)?>
                            <?php } else { ?>
                                <?php echo ANSWER?>
                            <?php } ?>
                          </a>
                          <?php
                          $t = strtotime($row->answer_date);
                          if ($t <= 0) {
                              $t = strtotime($row->modify_date);
                          }
                          if ($t > 0) {
                              ?>
                              <span class="faq-item__date">
                                <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                              </span>
                          <?php } ?>
                        </div>
                        <div class="faq-item__description">
                          <div class="faq-item__brief-description">
                            <?php echo Text::cuttext(html_entity_decode(strip_tags($row->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...')?>
                          </div>
                          <?php if (!$Block->nat) { ?>
                              <div class="faq-item__full-description"><?php echo $row->answer?></div>
                          <?php } ?>
                        </div>
                        <div class="faq-item__more">
                          <?php if ($Block->nat && (mb_strlen(html_entity_decode(strip_tags($row->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8')) > 256)) { ?>
                              <a<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ' class="faq-item__more-trigger" data-show="' . READ_ANSWER . '" data-hide="' . HIDE . '"'?>>
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
      </div>
      <script src="/js/faq.js"></script>
      <?php include Package::i()->resourcesDir . '/pages.inc.php'?>
      <?php if ($Pages->pages > 1) { ?>
          <ul class="pagination pull-right">
            <?php
            echo $outputNav(
                $Pages,
                array(
                    'pattern' => '<li><a href="' . HTTP::queryString('page={link}') . '">{text}</a></li>',
                    'pattern_active' => '<li class="active"><span>{text}</span></li>',
                    'ellipse' => '<li class="disabled"><a>...</a></li>'
                )
            );
            ?>
          </ul>
      <?php } ?>
    </div>
<?php } ?>
