<?php
namespace RAAS\CMS;

$colspanSM = 4;
$colspanMD = 3;

$bgPage = $Page;
while (!$bgPage->background->id && $bgPage->pid) {
    $bgPage = $bgPage->parent;
}
$bg = $bgPage->background;
unset($bgPage);

$separateScripts = function($text)
{
    $rx = '/\\<script.*?\\>.*?\\<\\/script\\>/umis';
    $scripts = '';
    $result = $text;
    if (preg_match_all($rx, $text, $regs)) {
        foreach ($regs[0] as $i => $script) {
            if (!preg_match('/maps.*?yandex.*constructor?/umis', $script)) {
                $scripts .= $script . "\n";
                $result = str_replace($script, '', $result);
            }
        }
    }
    return array($result, $scripts);
};

ob_start();
?>
<!DOCTYPE html>
<?php if ($Page->noindex || $Page->Material->noindex) { ?>
    <!--noindex-->
<?php } ?>
<html lang="<?php echo htmlspecialchars($Page->lang)?>">
  <head>
    <?php echo eval('?' . '>' . Snippet::importByURN('head')->description)?>
    <?php echo $Page->location('head_counters')?>
  </head>
  <body<?php echo !$Page->pid ? ' class="body_main"' : ''?>>
    <div id="top" class="background-holder"<?php echo $bg->id ? ' style="background-image: url(\'/' . htmlspecialchars($bg->fileURL) . '\')"' : ''?>>
      <header class="location_header">
        <div class="container">
          <div class="location_header__inner">
            <div class="row">
              <div class="col-sm-6">
                <div class="location_logo">
                  <?php echo $Page->location('logo')?>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="location_contacts_top">
                  <?php echo $Page->location('contacts_top')?>
                </div>
              </div>
            </div>
          </div>
          <div class="location_menu_top">
            <div class="row">
              <div class="col-sm-9"><?php echo $Page->location('menu_top')?></div>
              <div class="col-sm-3"><?php echo $Page->location('search_form')?></div>
            </div>
          </div>
          <div class="location_banners">
            <?php echo $Page->location('banners')?>
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
                          <?php echo $Page->location('share')?>
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
        <?php if ($text = $Page->location('content2')) { ?>
            <div class="location_content2"><?php echo $text?></div>
        <?php } ?>
        <?php if ($text = $Page->location('content3')) { ?>
            <div class="location_content3">
              <div class="container">
                <div class="location_content3__inner"><?php echo $text?></div>
              </div>
            </div>
        <?php } ?>
        <?php if ($text = $Page->location('content4')) { ?>
            <div class="location_content4"><?php echo $text?></div>
        <?php } ?>
        <?php if ($text = $Page->location('content5')) { ?>
            <div class="location_content5">
              <div class="container">
                <div class="location_content5__inner"><?php echo $text?></div>
              </div>
            </div>
        <?php } ?>
      </div>
      <footer class="location_footer">
        <div class="container">
          <div class="location_footer__inner">
            <div class="row">
              <div class="col-sm-6"><?php echo $Page->location('copyrights')?></div>
              <div class="col-sm-6"><?php echo $Page->location('menu_bottom')?></div>
            </div>
          </div>
          <div class="developer">Разработка и сопровождение сайта <a href="http://volumnet.ru" target="_blank">Volume&nbsp;Networks</a></div>
        </div>
      </footer>
    </div>
    <?php
    echo $Page->location('footer_counters');
    $content = ob_get_contents();
    ob_end_clean();
    $content = $separateScripts($content);
    echo $content[0] . $content[1];
    ?>
  </body>
</html>
<?php if ($Page->noindex || $Page->Material->noindex) { ?>
    <!--/noindex-->
<?php } ?>
