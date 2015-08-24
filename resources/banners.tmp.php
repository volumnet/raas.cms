<?php if ($Set) { ?>
    <div class="banners">
      <div id="carousel-main" data-ride="carousel" class="carousel slide hidden-xs">
        <?php if (count($Set) > 1) { ?>
            <ul class="carousel-indicators">
              <?php for ($i = 0; $i < count($Set); $i++) { $row = $Set[$i]; ?>
                  <li data-target="#carousel-main" data-slide-to="<?php echo (int)$i?>" class="<?php echo !$i ? 'active' : ''?>"></li>
              <?php } ?>
            </ul>
        <?php } ?>
        <div class="carousel-inner">
          <?php for ($i = 0; $i < count($Set); $i++) { $row = $Set[$i]; ?>
              <div class="item <?php echo !$i ? 'active' : ''?>">
                <a <?php echo $row->url ? 'href="' . htmlspecialchars($row->url) . '"' : ''?>>
                  <img src="/<?php echo htmlspecialchars(addslashes($row->image->fileURL))?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" />
                </a>
                <?php if ($row->name[0] != '.') { ?>
                    <div class="banners__caption">
                      <h3 class="banners__title"><?php echo htmlspecialchars($row->name)?></h3>
                      <div class="banners__description"><?php echo $row->description?></div>
                    </div>
                <?php } ?>
              </div>
          <?php } ?>
        </div>
        <?php if (count($Set) > 1) { ?>
            <a href="#carousel-main" data-slide="prev" class="left carousel-control"><span class="fa fa-chevron-left"></span></a>
            <a href="#carousel-main" data-slide="next" class="right carousel-control"><span class="fa fa-chevron-right"></span></a>
        <?php } ?>
      </div>
    </div>
<?php } ?>