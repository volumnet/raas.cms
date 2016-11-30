<?php
namespace RAAS\CMS;

if ($Set) {
    ?>
    <div class="banners">
      <div id="banners<?php echo (int)$Block->id?>" class="carousel slide banners__inner" data-role="slider" data-slider-carousel="bootstrap" data-slider-autoscroll="true">
        <?php if (count($Set) > 1) { ?>
            <ul class="carousel-indicators banners__nav">
              <?php for ($i = 0; $i < count($Set); $i++) { ?>
                  <li data-target="#banners<?php echo (int)$Block->id?>" data-slide-to="<?php echo (int)$i?>" class="banners__nav-item <?php echo !$i ? 'active' : ''?>"></li>
              <?php } ?>
            </ul>
        <?php } ?>
        <div class="carousel-inner banners__list banners-list">
          <?php for ($i = 0; $i < count($Set); $i++) { $row = $Set[$i]; ?>
              <div class="item <?php echo !$i ? 'active' : ''?> banners-list__item">
                <div class="banners-item">
                  <a class="banners-item__image" <?php echo $row->url ? 'href="' . htmlspecialchars($row->url) . '"' : ''?>>
                    <img src="/<?php echo Package::tn($row->image->fileURL, 1920, 654)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" />
                  </a>
                  <?php if ($row->name[0] != '.') { ?>
                      <div class="banners-item__text">
                        <div class="banners-item__title">
                          <?php echo htmlspecialchars($row->name)?>
                        </div>
                        <div class="banners-item__description">
                          <?php echo $row->description?>
                        </div>
                      </div>
                  <?php } ?>
                </div>
              </div>
          <?php } ?>
        </div>
        <?php if (count($Set) > 1) { ?>
            <a href="#banners<?php echo (int)$Block->id?>" data-slide="prev" class="left carousel-control banners__arrow banners__arrow_left"></a>
            <a href="#banners<?php echo (int)$Block->id?>" data-slide="next" class="right carousel-control banners__arrow banners__arrow_right"></a>
        <?php } ?>
      </div>
    </div>
<?php } ?>
