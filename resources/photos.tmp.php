<?php
namespace RAAS\CMS;

use \SOME\HTTP;

if ($Item) {
    ?>
    <div class="photos">
      <div class="article_opened">
        <div class="article__text"><div class="article__description"><?php echo $Item->description?></div></div>
        <?php if (count($Item->visImages) > 0) { ?>
            <div class="article__images row">
              <?php for ($i = 0; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>
                  <div class="col-sm-4 col-md-3 col-xs-4">
                    <a href="/<?php echo htmlspecialchars($row->fileURL)?>" data-lightbox-gallery="gallery" class="article__images__image">
                      <img src="/<?php echo htmlspecialchars($row->tnURL)?>" alt="<?php echo htmlspecialchars($row->name)?>" /></a>
                  </div>
              <?php } ?>
            </div>
        <?php } ?>
      </div>
    </div>
<?php } elseif ($Set) { ?>
    <div class="photos">
      <?php foreach ($Set as $row) { ?>
          <div class="article">
            <div class="article__text">
              <div class="article__title">
                <a<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                  <?php echo htmlspecialchars($row->name)?>
                </a>
              </div>
            </div>
            <?php if (count($row->visImages) > 0) { ?>
                <div class="article__images row">
                  <?php for ($i = 0; $i < count($row->visImages); $i++) { $row2 = $row->visImages[$i]; ?>
                      <div class="col-sm-3 col-lg-2 col-xs-4">
                        <a href="/<?php echo htmlspecialchars($row2->fileURL)?>" data-lightbox-gallery="gallery" class="article__images__image">
                          <img src="/<?php echo htmlspecialchars($row2->tnURL)?>" alt="<?php echo htmlspecialchars($row2->name)?>" /></a>
                      </div>
                  <?php } ?>
                </div>
            <?php } ?>
          </div>
      <?php } ?>
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
