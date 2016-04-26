<?php 
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($Set) { 
    ?>
    <div class="{BLOCK_NAME}">
      <div class="{BLOCK_NAME}__title">{MATERIAL_NAME}</div>
      <?php foreach ($Set as $row) { ?>
          <div class="article">
            <div class="article__image">
              <a<?php echo ($translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : '') . (!$row->visImages ? ' class="no-image"' : '')?>>
                <?php if ($row->visImages) { ?>
                    <img src="/<?php echo htmlspecialchars($row->visImages[0]->tnURL)?>" alt="<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>" />
                <?php } ?>
              </a>
            </div>
            <div class="article__text">
              <div class="article__title">
                <a<?php echo $translateAddresses ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                  <?php echo htmlspecialchars($row->name)?>
                </a>
              </div>
              <?php if (($t = strtotime($row->date)) > 0) { ?>
                  <div class="article__date">
                    <?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?>
                  </div>
              <?php } ?>
              <div class="article__description">
                <?php echo htmlspecialchars($row->brief ?: Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
              </div>
              <?php if ($translateAddresses) { ?>
                  <div class="article__read-more">
                    <a href="<?php echo htmlspecialchars($row->url)?>">
                      <?php echo SHOW_MORE?>
                    </a>
                  </div>
              <?php } ?>
            </div>
          </div>
      <?php } ?>
    </div>
<?php } ?>