<?php
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($Set) {
    ?>
    <div class="faq-main left-block">
      <div class="faq-main__title left-block__title"><a href="/{BLOCK_NAME}/">{FAQ_NAME}</a></div>
      <div class="left-block__inner faq-main__list">
        <div class="faq-main-list">
          <?php foreach ($Set as $row) { ?>
              <div class="faq-main-list__item">
                <div class="faq-main-item">
                  <div class="faq-main-item__text faq-main-item__text_question">
                    <?php if ($row->image->id) { ?>
                        <a class="faq-main-item__image"<?php echo $translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                          <img src="/<?php echo htmlspecialchars($row->image->tnURL)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" /></a>
                    <?php } ?>
                    <div class="faq-main-item__title">
                      <a class="faq-main-item__name"<?php echo $translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                        <?php echo htmlspecialchars($row->name)?></a>,
                      <?php
                      $t = strtotime($row->date);
                      if ($t <= 0) {
                          $t = strtotime($row->post_date);
                      }
                      if ($t > 0) {
                          ?>
                          <span class="faq-main-item__date">
                            <?php echo date('d.m.Y', $t)?>
                          </span>
                      <?php } ?>
                    </div>
                    <div class="faq-main-item__description">
                      <?php echo $row->description?>
                    </div>
                    <div class="faq-main-item__more">
                      <a href="<?php echo $translateAddresses ? htmlspecialchars($row->url) : '/{BLOCK_NAME}/'?>">
                        <?php echo READ_ANSWER?>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
    </div>
<?php } ?>
