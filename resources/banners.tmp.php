<?php if ($Set) { ?>
    <div class="">
      <div id="carousel-main" data-ride="carousel" class="carousel slide hidden-xs">
        <div class="carousel-indicators">
          <?php for ($i = 0; $i < count($Set); $i++) { $row = $Set[$i]; ?>
              <div data-target="#carousel-main" data-slide-to="<?php echo (int)$i?>" class="<?php echo !$i ? 'active' : ''?>"></div>
          <?php } ?>
        </div>
        <div class="carousel-inner">
          <?php for ($i = 0; $i < count($Set); $i++) { $row = $Set[$i]; ?>
              <div class="item <?php echo !$i ? 'active' : ''?>">
                <a <?php echo $row->url ? 'href="' . htmlspecialchars($row->url) . '"' : ''?>>
                  <img src="/<?php echo htmlspecialchars(addslashes($row->image->fileURL))?>.1350x300" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" />
                </a>
              </div>
          <?php } ?>
        </div>
        <a href="#carousel-main" data-slide="prev" class="left carousel-control"><span class="glyphicon glyphicon-chevron-left"></span></a>
        <a href="#carousel-main" data-slide="next" class="right carousel-control"><span class="glyphicon glyphicon-chevron-right"></span></a>
      </div>
    </div>
<?php } ?>