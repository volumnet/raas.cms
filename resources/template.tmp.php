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
            <section class="main-container container-fluid">
              <div class="row">
                <aside class="location_left col-sm-3">
                  <nav class="menu_left"><?php echo $Page->locationBlocksText['left'][0]?></nav>
                  <?php for ($i = 1; $i < count($Page->locationBlocksText['left']); $i++) { ?>
                      <section class="block_left"><?php echo $Page->locationBlocksText['left'][$i]?></section>    
                  <?php } ?>
                </aside>
                <main class="location_content col-sm-9">
                  <?php 
                  if (!$Page->pid) { 
                      echo $Page->locationBlocksText['content'][0];
                  }
                  ?>
                  <div class="block_content">
                    <?php if (count($Page->parents) > 1) { ?>
                        <ol class="breadcrumb">
                          <?php foreach ($Page->parents as $row) { ?>
                              <li><a href="<?php echo htmlspecialchars($row->url)?>"><?php echo htmlspecialchars($row->name)?></a></li>
                          <?php } ?>
                        </ol>
                    <?php } ?>
                    <?php if ($Page->pid) { ?>
                        <h1><?php echo htmlspecialchars($Page->name)?></h1>
                    <?php } ?>
                    <?php 
                    for ($i = (int)(!$Page->pid); $i < count($Page->locationBlocksText['content']); $i++) { 
                        echo $Page->locationBlocksText['content'][$i];
                    } 
                    ?>
                  </div>
                </main>
              </div>
            </section>
            <footer class="location_footer container-fluid">
              <div class="row footer__inner">
                <?php echo $Page->location('footer')?>
              </div>
            </footer>
          </div>
        </div>
      </div>
    </div>
    <?php echo $Page->location('footer_counters')?>
  </body>
</html>