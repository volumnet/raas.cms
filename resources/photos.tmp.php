<?php
namespace RAAS\CMS;

use \SOME\HTTP;

if ($Item) {
    ?>
    <div class="photos">
      <div class="photos-article">
        <div class="photos-article__text">
          <div class="photos-article__description">
            <?php echo $Item->description?>
          </div>
        </div>
        <?php if (count($Item->visImages) > 0) { ?>
            <div class="photos-article__images">
              <?php for ($i = 0; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>
                  <div class="photos-article__additional-image-container">
                    <a href="/<?php echo htmlspecialchars($row->fileURL)?>" data-lightbox-gallery="gallery" class="photos-article__additional-image">
                      <img src="/<?php echo htmlspecialchars($row->tnURL)?>" alt="<?php echo htmlspecialchars($row->name)?>" /></a>
                  </div>
              <?php } ?>
            </div>
        <?php } ?>
      </div>
    </div>
<?php } elseif ($Set) { ?>
    <div class="photos">
      <div class="photos__list">
        <div class="photos-list">
          <?php foreach ($Set as $row) { ?>
              <div class="photos__item">
                <div class="photos-item">
                  <div class="photos-item__text">
                    <div class="photos-item__title">
                      <a<?php echo $Block->nat ? ' href="' . htmlspecialchars($row->url) . '"' : ''?>>
                        <?php echo htmlspecialchars($row->name)?>
                      </a>
                    </div>
                  </div>
                  <?php if (count($row->visImages) > 0) { ?>
                      <div class="photos-item__images">
                        <?php for ($i = 0; $i < count($row->visImages); $i++) { $row2 = $row->visImages[$i]; ?>
                            <div class="photos-item__additional-image-container">
                              <a href="<?php echo htmlspecialchars($Block->nat ? $row->url : '/' . $row2->fileURL)?>" data-lightbox-gallery="gallery" class="photos-item__additional-image">
                                <img src="/<?php echo htmlspecialchars($row2->tnURL)?>" alt="<?php echo htmlspecialchars($row2->name ?: $row->name)?>" /></a>
                            </div>
                        <?php } ?>
                      </div>
                  <?php } ?>
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
