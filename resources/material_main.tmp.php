<?php
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($Set) {
    ?>
    <div class="{BLOCK_NAME}-main left-block">
      <div class="{BLOCK_NAME}-main__title left-block__title"><a href="/{BLOCK_NAME}/">{MATERIAL_NAME}</a></div>
      <div class="{BLOCK_NAME}-main__list left-block__inner">
        <div class="{BLOCK_NAME}-main-list">
          <?php foreach ($Set as $row) { ?>
              <div class="{BLOCK_NAME}-main-list__item">
                <div class="{BLOCK_NAME}-main-item">
                  <div class="{BLOCK_NAME}-main-item__image">
                    <a<?php echo ($translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : '') . (!$row->visImages ? ' class="no-image"' : '')?>>
                      <?php if ($row->visImages) { ?>
                          <img src="/<?php echo Package::tn($row->visImages[0]->fileURL, 1920, 654)?>" alt="<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>" />
                      <?php } ?>
                    </a>
                  </div>
                  <div class="{BLOCK_NAME}-main-item__text">
                    <div class="{BLOCK_NAME}-main-item__title">
                      <a<?php echo $translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                        <?php echo htmlspecialchars($row->name)?>
                      </a>
                    </div>
                    <?php if (($t = strtotime($row->date)) > 0) { ?>
                        <div class="{BLOCK_NAME}-main-item__date">
                          <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                        </div>
                    <?php } ?>
                    <div class="{BLOCK_NAME}-main-item__description">
                      <?php echo htmlspecialchars($row->brief ?: Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                    </div>
                    <?php if ($translateAddresses) { ?>
                        <div class="{BLOCK_NAME}-main-item__more">
                          <a href="<?php echo htmlspecialchars($row->url)?>">
                            <?php echo SHOW_MORE?>
                          </a>
                        </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
    </div>
<?php } ?>
