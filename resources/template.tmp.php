<?php 
namespace RAAS\CMS;
if (!function_exists('RAAS\\CMS\\androidTablet')) {
    function androidTablet($ua){ //Find out if it is a tablet 
        if(strstr(strtolower($ua), 'android') ){//Search for android in user-agent 
            if(!strstr(strtolower($ua), 'mobile')){ //If there is no ''mobile' in user-agent (Android have that on their phones, but not tablets) 
                return true; 
            } 
        } 
    } 
}
if (!function_exists('RAAS\\CMS\\isMobileUA')) {
    function isMobileUA($ua)
    { 
        $iphone = strstr(strtolower($ua), 'mobile'); //Search for 'mobile' in user-agent (iPhone have that) 
        $android = strstr(strtolower($ua), 'android'); //Search for 'android' in user-agent 
        $windowsPhone = strstr(strtolower($ua), 'phone'); //Search for 'phone' in user-agent (Windows Phone uses that) 
        $androidTablet = androidTablet($ua); //Do androidTablet function 
        $ipad = strstr(strtolower($ua), 'ipad'); //Search for iPad in user-agent 
        if($androidTablet || $ipad){ //If it's a tablet (iPad / Android) 
            return false; 
        } elseif(($iphone && !$ipad) || ($android && !$androidTablet) || $windowsPhone) { //If it's a phone and NOT a tablet 
            return true; 
        } 
        return false; 
    } 
}
$isMobile = isMobileUA($_SERVER['HTTP_USER_AGENT']);
$sideColWidth = 3;
?>
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
          <div class="row">
            <div class="col-sm-5"><div class="logo"><?php echo $Page->locationBlocksText['header'][0]?></div></div>
            <div class="col-sm-7"><div class="address"><?php echo $Page->locationBlocksText['header'][1]?></div></div>
          </div>
          <?php 
          for ($i = (int)(!$Page->pid); $i < count($Page->locationBlocksText['content']); $i++) { 
              echo $Page->locationBlocksText['content'][$i];
          } 
          ?> 
        </header>
        <div class="main-container">
          <div class="row">
            <?php if (count($Page->locationBlocksText['left'])) { ?>
                <aside class="location_left col-sm-<?php echo $sideColWidth?>"><?php echo $Page->location('left')?></aside>
            <?php } ?>
            <?php if (!$Page->pid) { ?>
                <?php if (count($Page->locationBlocksText['content'])) { ?>
                    <div class="location_content col-sm-<?php echo 12 - (((int)(bool)count($Page->locationBlocksText['left']) + (int)(bool)count($Page->locationBlocksText['right'])) * $sideColWidth)?>">
                      <main class="block_content"><?php echo $Page->location('content')?></main>
                    </div> 
                <?php } ?>
            <?php } else { ?>
                <?php if (count($Page->locationBlocksText['content'])) { ?>
                    <div class="location_content col-sm-<?php echo 12 - (((int)(bool)count($Page->locationBlocksText['left']) + (int)(bool)count($Page->locationBlocksText['right'])) * $sideColWidth)?>">
                      <main class="block_content">
                        <h1><?php echo htmlspecialchars($Page->name)?></h1>
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
                        <?php echo $Page->location('content')?>
                      </main>
                    </div> 
                <?php } ?>
            <?php } ?>
            <?php if (count($Page->locationBlocksText['right'])) { ?>
                <aside class="location_right col-sm-<?php echo $sideColWidth?>"><?php echo $Page->location('right')?></aside>
            <?php } ?>
          </div>
        </div>
        <footer class="location_footer">
          <div class="location_footer__inner">
            <div><?php echo $Page->location('footer')?></div>
            <p class="developer">Разработка и сопровождение сайта <a href="http://volumnet.ru" target="_blank">Volume Networks</a></p>
          </div>
        </footer>
      </div>
    </div>
    <?php echo $Page->location('footer_counters')?>
  </body>
</html>