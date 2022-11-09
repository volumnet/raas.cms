<?php
namespace RAAS\CMS;

use \SOME\HTTP;

if ($Item) {
    ?>
    <div class="photos photos-article">
      <div class="photos-article__text photos-article__description">
        <?php echo $Item->description?>
      </div>
      <?php if (count($Item->visImages) > 0) { ?>
          <div class="photos-article__images">
            <?php for ($i = 0; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>
                <a href="/<?php echo htmlspecialchars($row->fileURL)?>" data-lightbox-gallery="gallery" class="photos-article__additional-image">
                  <img src="/<?php echo htmlspecialchars($row->tnURL)?>" alt="<?php echo htmlspecialchars($row->name)?>" /></a>
            <?php } ?>
          </div>
      <?php } ?>
    </div>
<?php } elseif ($Set) { ?>
    <div class="photos">
      <div class="photos__list photos-list">
        <?php foreach ($Set as $row) { ?>
            <a href="<?php echo htmlspecialchars($Block->nat ? $row->url : '/' . $row2->fileURL)?>" class="photos-list__item photos-item">
              <img class="photos-item__image" loading="lazy" src="/<?php echo htmlspecialchars($row->visImages[0]->tnURL)?>" alt="<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>" /></a>
              <div class="photos-item__title">
                <?php echo htmlspecialchars($row->name)?>
              </div>
            </a>
        <?php } ?>
      </div>
    </div>
    <?php if ($Pages->pages > 1) { ?>
        <div class="photos__pagination">
          <?php Snippet::importByURN('pagination')->process(['pages' => $Pages]); ?>
        </div>
    <?php } ?>
<?php } ?>
