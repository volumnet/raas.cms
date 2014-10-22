<?php namespace RAAS\CMS?>
<!DOCTYPE html>
<html>
  <head>
    <?php echo eval('?' . '>' . Snippet::importByURN('head')->description)?>
    <?php echo $Page->location('head_counters')?>
  </head>
  <body>
    <div class="background-holder">
      <div class="container">
        <header class="location_header">
          <div class="col-sm-5 location_header__logo"><?php echo $Page->locationBlocksText['header'][0]?></div>
          <div class="col-sm-7 location_header__address"><?php echo $Page->locationBlocksText['header'][1]?></div> 
        </header>
        <header class="location_menu_top"><?php echo $Page->location('menu_top')?></header>
        <section class="main-container row">
          <?php if (count($Page->locationBlocksText['left'])) { ?>
              <aside class="location_left col-sm-4"><?php echo $Page->location('left')?></aside>
          <?php } ?>
          <?php if (count($Page->locationBlocksText['content'])) { ?>
              <section class="location_content col-sm-<?php echo (3 - (int)(bool)count($Page->locationBlocksText['left']) - (int)(bool)count($Page->locationBlocksText['right'])) * 4?>">
                <?php 
                if (!$Page->pid) { 
                    echo $Page->locationBlocksText['content'][0];
                }
                ?>
                <div class="block_content">
                  <?php if ($Page->pid) { ?>
                      <h1><?php echo htmlspecialchars($Page->name)?></h1>
                  <?php } ?>
                  <?php if ((count($Page->parents) + (bool)$Page->Material->id) > 1) { ?>
                      <ol class="breadcrumb">
                        <?php foreach ($Page->parents as $row) { ?>
                            <li><a href="<?php echo htmlspecialchars($row->url)?>"><?php echo htmlspecialchars($row->name)?></a></li>
                        <?php } ?>
                        <?php if ($Page->Material->id) { ?>
                            <li><a href="<?php echo htmlspecialchars($Page->url)?>"><?php echo htmlspecialchars($Page->oldName)?></a></li>
                        <?php } ?>
                      </ol>
                  <?php } ?>
                  <?php 
                  for ($i = (int)(!$Page->pid); $i < count($Page->locationBlocksText['content']); $i++) { 
                      echo $Page->locationBlocksText['content'][$i];
                  } 
                  ?>
                </div>
              </section> 
          <?php } ?>
          <?php if (count($Page->locationBlocksText['right'])) { ?>
              <aside class="location_right col-sm-4"><?php echo $Page->location('right')?></aside>
          <?php } ?>
        </section>
        <footer class="location_footer">
          <div class="location_footer__inner row">
            <div class="col-sm-4"><?php echo $Page->locationBlocksText['footer'][0]?></div>
            <div class="col-sm-8 clearfix">
              <?php 
              for ($i = 1; $i < count($Page->locationBlocksText['footer']); $i++) { 
                  echo $Page->locationBlocksText['footer'][$i];
              } 
              ?>
            </div>
            <p class="developer">Разработка и сопровождение сайта <a href="http://volumnet.ru" target="_blank">Volume Networks</a></p>
          </div>
        </footer>
      </div>
    </div>
    <?php echo $Page->location('footer_counters')?>
  </body>
</html>