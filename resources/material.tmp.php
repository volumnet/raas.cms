<?php
namespace RAAS\CMS;

use \SOME\Text;
use \SOME\HTTP;

if ($Item) {
    ?>
    <div class="{BLOCK_NAME}">
      <div class="{BLOCK_NAME}-article">
        <?php if (($t = strtotime($Item->date)) > 0) { ?>
            <div class="{BLOCK_NAME}-article__date"><?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?></div>
        <?php } ?>
        <?php if ($Item->visImages) { ?>
            <div class="{BLOCK_NAME}-article__image">
              <a href="/<?php echo $Item->visImages[0]->fileURL?>" data-lightbox-gallery="g">
                <img src="/<?php echo $Item->visImages[0]->tnURL?>" alt="<?php echo htmlspecialchars($Item->visImages[0]->name ?: $row->name)?>" /></a>
            </div>
        <?php } ?>
        <div class="{BLOCK_NAME}-article__text">
          <div class="{BLOCK_NAME}-article__description">
            <?php echo $Item->description?>
          </div>
        </div>
        <?php if (count($Item->visImages) > 1) { ?>
            <div class="{BLOCK_NAME}-article__images">
              <div class="h2 {BLOCK_NAME}-article__images-title">
                Фотографии
              </div>
              <div class="{BLOCK_NAME}-article__images-inner">
                <?php for ($i = 1; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>
                    <div class="{BLOCK_NAME}-article__additional-image-container">
                      <a href="/<?php echo htmlspecialchars($row->fileURL)?>" class="{BLOCK_NAME}-article__additional-image" data-lightbox-gallery="g">
                        <img src="/<?php echo htmlspecialchars($row->tnURL)?>" alt="<?php echo htmlspecialchars($row->name)?>" /></a>
                    </div>
                <?php } ?>
              </div>
            </div>
        <?php } ?>
      </div>
    </div>
<?php } elseif ($Set) { ?>
    <div class="{BLOCK_NAME}">
      <div class="{BLOCK_NAME}__list">
        <div class="{BLOCK_NAME}-list">
          <?php foreach ($Set as $row) { ?>
              <div class="{BLOCK_NAME}-list__item">
                <div class="{BLOCK_NAME}-item">
                  <div class="{BLOCK_NAME}-item__image">
                    <a<?php echo ($Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : '') . (!$row->visImages ? ' class="no-image"' : '')?>>
                      <?php if ($row->visImages) { ?>
                          <img src="/<?php echo htmlspecialchars($row->visImages[0]->tnURL)?>" alt="<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>" />
                      <?php } ?>
                    </a>
                  </div>
                  <div class="{BLOCK_NAME}-item__text">
                    <div class="{BLOCK_NAME}-item__title">
                      <a<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                        <?php echo htmlspecialchars($row->name)?>
                      </a>
                    </div>
                    <?php if (($t = strtotime($row->date)) > 0) { ?>
                        <div class="{BLOCK_NAME}-item__date">
                          <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                        </div>
                    <?php } ?>
                    <div class="{BLOCK_NAME}-item__description">
                      <?php echo htmlspecialchars($row->brief ?: Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                    </div>
                    <?php if ($Block->nat) { ?>
                        <div class="{BLOCK_NAME}-item__more">
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
    <?php include Package::i()->resourcesDir . '/pages.inc.php'?>
    <?php if ($Pages->pages > 1) { ?>
        <ul class="pagination pull-right">
          <?php
          echo $outputNav(
              $Pages,
              array(
                  'pattern' => '<li><a href="' . HTTP::queryString('page={link}') . '">{text}</a></li>',
                  'pattern_active' => '<li class="active"><a>{text}</a></li>',
                  'ellipse' => '<li class="disabled"><a>...</a></li>'
              )
          );
          ?>
        </ul>
    <?php } ?>
<?php } ?>
