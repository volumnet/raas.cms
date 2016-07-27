<?php
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($Set) {
    ?>
    <div class="features_main">
      <div class="features_main__title h2"><?php echo htmlspecialchars($Block->name)?></div>
      <div class="features_main__inner">
        <?php foreach ($Set as $row) { ?>
            <div class="article">
              <?php if ($row->image->id || $row->icon) { ?>
                  <div class="article__image">
                    <?php if ($row->image->id) { ?>
                        <img src="/<?php echo htmlspecialchars($row->image->fileURL)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" />
                    <?php } elseif ($row->icon) { ?>
                        <span class="fa fa-<?php echo htmlspecialchars($row->icon)?>"></span>
                    <?php } ?>
                  </div>
              <?php } ?>
              <div class="article__text">
                <div class="article__title">
                  <?php echo htmlspecialchars($row->name)?>
                </div>
                <div class="article__description">
                  <?php echo $row->description?>
                </div>
              </div>
            </div>
        <?php } ?>
      </div>
    </div>
<?php } ?>
