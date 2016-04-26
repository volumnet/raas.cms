<?php
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($Set) { 
    ?> 
    <div class="faq_main">
      <div class="faq_main__title">{FAQ_NAME}</div>
      <?php foreach ($Set as $row) { ?>
          <div class="article">
            <div class="article__text article__question">
              <?php if ($row->image->id) { ?>
                  <div class="article__image">
                    <a<?php echo $translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                      <img src="/<?php echo htmlspecialchars($row->image->tnURL)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" /></a>
                  </div>
              <?php } ?>
              <div class="article__title">
                <a class="article__name"<?php echo $translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                  <?php echo htmlspecialchars($row->name)?>
                </a>
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
                <div class="article__text article__answer">
                  <?php if ($row->answer_image->id) { ?>
                      <div class="article__image">
                        <a<?php echo $translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                          <img src="/<?php echo htmlspecialchars($row->answer_image->tnURL)?>" alt="<?php echo htmlspecialchars($row->answer_image->name ?: $row->answer_name)?>" /></a>
                      </div>
                  <?php } ?>
                  <div class="article__title">
                    <a class="article__name"<?php echo $translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
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
                    <?php echo $row->answer?>
                  </div>
                </div>
            <?php } ?>
          </div>
      <?php } ?>
    </div>
<?php } ?>