<?php
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($Set) {
    ?>
    <div class="documents-main">
      <div class="documents-main__title"><?php echo htmlspecialchars($Block->name)?></div>
      <div class="documents-main__inner">
        <div class="documents-main__list" data-role="slider" data-slider-carousel="jcarousel" data-slider-duration="800" data-slider-interval="3000" data-slider-autoscroll="true">
          <div class="documents-main-list">
            <?php if ($Set) {
                foreach ($Set as $row) {
                    ?>
                    <div class="documents-main-list__item">
                      <a href="<?php echo htmlspecialchars($row->image->fileURL)?>" class="documents-main-item" data-lightbox-gallery="documents-main">
                        <div class="documents-main-item__image"><img src="<?php echo Package::i()->tn($row->image->fileURL, 300, null, 'inline')?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" /></div>
                      </a>
                    </div>
                <?php } ?>
            <?php } ?>
          </div>
        </div>
        <a href="#" class="documents-main__arrow documents-main__arrow_left" data-role="slider-prev"></a>
        <a href="#" class="documents-main__arrow documents-main__arrow_right" data-role="slider-next"></a>
      </div>
      <?php Package::i()->requestJS(['/js/sliders.js', '/js/documents-main.js']);?>
    </div>
<?php } ?>
