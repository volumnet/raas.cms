<?php
namespace RAAS\CMS;

use \SOME\Text;
use \SOME\HTTP;

if ($Item) {
    ?>
    <div class="faq">
      <div class="article_opened">
        <div class="article__text article__question">
          <?php if ($Item->image->id) { ?>
              <div class="article__image">
                <img src="/<?php echo $Item->image->tnURL?>" alt="<?php echo htmlspecialchars($Item->image->name ?: $Item->name)?>" />
              </div>
          <?php } ?>
          <div class="article__title">
            <?php
            $t = strtotime($Item->date);
            if ($t <= 0) {
                $t = strtotime($Item->post_date);
            }
            if ($t > 0) {
                ?>
                <span class="article__date">
                  <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                </span>
            <?php } ?>
          </div>
          <div class="article__description">
            <?php echo $Item->description?>
          </div>
        </div>
        <?php if ($Item->answer) { ?>
            <div class="article__text article__answer">
              <?php if ($Item->answer_image->id) { ?>
                  <div class="article__image">
                    <img src="/<?php echo $Item->answer_image->tnURL?>" alt="<?php echo htmlspecialchars($Item->answer_image->name ?: $Item->answer_name)?>" />
                  </div>
              <?php } ?>
              <div class="article__title">
                <span class="article__name">
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
                    <span class="article__date">
                      <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                    </span>
                <?php } ?>
              </div>
              <div class="article__description">
                <?php echo $Item->answer?>
              </div>
            </div>
        <?php } ?>
      </div>
    </div>
<?php } elseif ($Set) { ?>
    <div class="faq">
      <?php foreach ($Set as $row) { ?>
          <div class="article">
            <div class="article__text article__question">
              <?php if ($row->image->id) { ?>
                  <div class="article__image">
                    <a<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                      <img src="/<?php echo htmlspecialchars($row->image->tnURL)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" /></a>
                  </div>
              <?php } ?>
              <div class="article__title">
                <a class="article__name"<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                  <?php echo htmlspecialchars($row->name)?></a>
                <?php
                $t = strtotime($row->date);
                if ($t <= 0) {
                    $t = strtotime($row->post_date);
                }
                if ($t > 0) {
                    ?>
                    <span class="article__date">
                      <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                    </span>
                <?php } ?>
              </div>
              <div class="article__description">
                <?php echo $row->description?>
              </div>
            </div>
            <?php if ($row->answer) { ?>
                <div class="article__text article__answer<?php echo !$Block->nat ? ' article__answer_slider' : ''?>">
                  <?php if ($row->answer_image->id) { ?>
                      <div class="article__image">
                        <a<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                          <img src="/<?php echo htmlspecialchars($row->answer_image->tnURL)?>" alt="<?php echo htmlspecialchars($row->answer_image->name ?: $row->answer_name)?>" /></a>
                      </div>
                  <?php } ?>
                  <div class="article__title">
                    <a class="article__name"<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
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
                        <span class="article__date">
                          <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                        </span>
                    <?php } ?>
                  </div>
                  <div class="article__description">
                    <div class="article__description__brief">
                      <?php echo Text::cuttext(html_entity_decode(strip_tags($row->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...')?>
                    </div>
                    <?php if (!$Block->nat) { ?>
                        <div class="article__description__full"><?php echo $row->answer?></div>
                    <?php } ?>
                  </div>
                  <div class="article__more">
                    <?php if ($Block->nat && (mb_strlen(html_entity_decode(strip_tags($row->answer), ENT_COMPAT | ENT_HTML5, 'UTF-8')) > 256)) { ?>
                        <a<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ' class="article__more__trigger" data-show="' . READ_ANSWER . '" data-hide="' . HIDE . '"'?>>
                          <?php echo READ_ANSWER?>
                        </a>
                    <?php } ?>
                  </div>
                </div>
            <?php } ?>
          </div>
      <?php } ?>
      <script src="/js/faq.js"></script>
    </div>
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
<?php } ?>
