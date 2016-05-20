<?php
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($Set) { 
    ?> 
    <div class="faq_main block_left">
      <div class="faq_main__title block_left__title"><a href="/{BLOCK_NAME}/">{FAQ_NAME}</a></div>
      <div class="block_left__inner">
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
                    <?php echo htmlspecialchars($row->name)?></a>,
                  <?php 
                  $t = strtotime($row->date);
                  if ($t <= 0) {
                      $t = strtotime($row->post_date);
                  }
                  if ($t > 0) { 
                      ?>
                      <span class="article__date">
                        <?php echo date('d.m.Y', $t)?>
                      </span>
                  <?php } ?>
                </div>
                <div class="article__description">
                  <?php echo $row->description?>
                </div>
                <div class="article__more">
                  <a href="<?php echo $translateAddresses ? htmlspecialchars($row->url) : '/{BLOCK_NAME}/'?>">
                    <?php echo READ_ANSWER?>
                  </a>
                </div>
              </div>
            </div>
        <?php } ?>
      </div>
    </div>
<?php } ?>