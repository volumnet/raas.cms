<?php
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($Set) {
    ?>
    <div class="features-main">
      <div class="features-main__title h2"><?php echo htmlspecialchars($Block->name)?></div>
      <div class="features-main__list">
        <div class="features-main-list">
          <?php foreach ($Set as $row) { ?>
              <div class="features-main-list__item">
                <div class="features-main-item">
                  <?php if ($row->image->id || $row->icon) { ?>
                      <div class="features-main-item__image">
                        <?php if ($row->image->id) { ?>
                            <img src="/<?php echo htmlspecialchars($row->image->fileURL)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" />
                        <?php } elseif ($row->icon) { ?>
                            <span class="fa fa-<?php echo htmlspecialchars($row->icon)?>"></span>
                        <?php } ?>
                      </div>
                  <?php } ?>
                  <div class="features-main-item__text">
                    <div class="features-main-item__title">
                      <?php echo htmlspecialchars($row->name)?>
                    </div>
                    <div class="features-main-item__description">
                      <?php echo $row->description?>
                    </div>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
    </div>
<?php } ?>
