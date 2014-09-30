<?php namespace RAAS\CMS?>
<!DOCTYPE html>
<html>
  <head>
    <?php echo eval('?' . '>' . Snippet::importByURN('head')->description)?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <?php echo $Page->location('head_counters')?>
  </head>
  <body>
    <div class="background-holder">
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
            <header class="location_header container-fluid"><?php echo $Page->location('header')?></header>
            <header class="location_menu_top"><?php echo $Page->location('menu_top')?></header>
            <section class="main-container container-fluid">
              <div class="row">
                <main class="location_content col-sm-12">
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
                </main>
                <?php if (count($Page->locationBlocksText['left'])) { ?>
                    <aside class="location_left col-sm-4"><?php echo $Page->location('left')?></aside>
                <?php } ?>
                <?php if (count($Page->locationBlocksText['center'])) { ?>
                    <section class="location_center col-sm-<?php echo (3 - (int)(bool)count($Page->locationBlocksText['left']) - (int)(bool)count($Page->locationBlocksText['right'])) * 4?>">
                      <?php echo $Page->location('center')?>
                    </section> 
                <?php } ?>
                <?php if (count($Page->locationBlocksText['right'])) { ?>
                    <aside class="location_left col-sm-4"><?php echo $Page->location('right')?></aside>
                <?php } ?>
              </div>
            </section>
            <footer class="location_footer container-fluid">
              <div class="row location_footer__inner">
                <?php echo $Page->location('footer')?>
                <p class="developer">Разработка и сопровождение сайта <a href="http://volumnet.ru" target="_blank">Volume Networks</a></p>
              </div>
            </footer>
          </div>
        </div>
      </div>
    </div>
    <?php echo $Page->location('footer_counters')?>
  </body>
</html>