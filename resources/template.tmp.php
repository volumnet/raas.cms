<?php 
namespace RAAS\CMS;
$colspanSM = 4;
$colspanMD = 3;
?>
<!DOCTYPE html>
<html>
  <head>
    <?php echo eval('?' . '>' . Snippet::importByURN('head')->description)?>
    <?php echo $Page->location('head_counters')?>
  </head>
  <body<?php echo !$Page->pid ? ' class="body_main"' : ''?>>
    <div class="background-holder">
      <header class="location_header">
        <div class="container">
          <div class="location_header__inner">
            <div class="row">
              <div class="col-sm-6"><?php echo $Page->locationBlocksText['header'][0]?></div>
              <div class="col-sm-6"><?php echo $Page->locationBlocksText['header'][1]?></div>
            </div>
            <?php 
            for ($i = 2; $i < count($Page->locationBlocksText['header']); $i++) { 
                echo $Page->locationBlocksText['header'][$i];
            } 
            ?> 
          </div>
        </div>
      </header>
      <div class="main-container">
        <div class="container">
          <div class="row">
            <?php 
            $leftText = $Page->location('left');
            $rightText = $Page->location('right');
            if ($leftText) { ?>
                <aside class="location_left col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                  <div class="location_left__inner"><?php echo $leftText?></div>
                </aside>
            <?php } ?>
            <?php if (count($Page->locationBlocksText['content'])) { 
                $spanSM = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanSM);
                $spanMD = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanMD);
                ?>
                <div class="location_content col-sm-<?php echo $spanSM?> col-md-<?php echo $spanMD?>">
                  <div class="location_content__inner">
                    <?php if (!$Page->pid) { ?>
                        <?php echo $Page->location('content')?>
                    <?php } else { ?>
                          <?php if ((count($Page->parents) + (bool)$Page->Material->id + (bool)$Page->Item->id) > 1) { ?>
                              <ol class="breadcrumb">
                                <?php foreach ($Page->parents as $row) { ?>
                                    <li><a href="<?php echo htmlspecialchars($row->url)?>"><?php echo htmlspecialchars($row->getBreadcrumbsName())?></a></li>
                                <?php } ?>
                                <?php if ($Page->Material->id || $Page->Item->id) { ?>
                                    <li><a href="<?php echo htmlspecialchars($Page->url)?>"><?php echo htmlspecialchars($Page->getBreadcrumbsName())?></a></li>
                                <?php } ?>
                              </ol>
                          <?php } ?>
                          <h1><?php echo htmlspecialchars($Page->getH1())?></h1>
                          <?php echo $Page->location('content')?>
                    <?php } ?>
                  </div>
                </div>
            <?php } ?>
            <?php if ($rightText) { ?>
                <aside class="location_right col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                  <div class="location_right__inner"><?php echo $rightText?></div>
                </aside>
            <?php } ?>
          </div>
        </div>
      </div>
      <footer class="location_footer">
        <div class="container">
          <div class="location_footer__inner">
            <div class="row">
              <div class="col-sm-5"><?php echo $Page->locationBlocksText['footer'][0]?></div>
              <div class="col-sm-2"><?php echo $Page->locationBlocksText['footer'][1]?></div>
              <div class="col-sm-5"><?php echo $Page->locationBlocksText['footer'][2]?></div>
            </div>
            <?php 
            for ($i = 3; $i < count($Page->locationBlocksText['footer']); $i++) { 
                echo $Page->locationBlocksText['footer'][$i];
            } 
            ?>
          </div>
          <div class="developer">Разработка и сопровождение сайта <a href="http://volumnet.ru" target="_blank">Volume&nbsp;Networks</a></div>
        </div>
      </footer>
    </div>
    <?php echo $Page->location('footer_counters')?>
  </body>
</html>