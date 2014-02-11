<?php
function displayLocation($VIEW, $loc, $Item)
{
    $text = '';
    if ($temp = $VIEW->context->getLocationContextMenu($loc, $Item)) {
        $text .= '<div class="btn-group pull-right">
                    <a href="#" class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><i class="icon-plus"></i></a>
                    <ul class="dropdown-menu">' . showMenu($temp) . '</ul>
                  </div>';
    }
    $text .=  ' <h6>' . htmlspecialchars($loc->urn) . '</h6>
                <input type="hidden" value="' . $loc->urn . '" />';
    //ob_clean(); print_r ($Item->blocksByLocations); exit;
    if (isset($Item->blocksByLocations[$loc->urn])) {
        for ($i = 0; $i < count($Item->blocksByLocations[$loc->urn]); $i++) { 
            $row = $Item->blocksByLocations[$loc->urn][$i];
            $text .= \RAAS\CMS\Block_Type::getType($row->block_type)->viewer->renderBlock($row, $Item, $loc);
        }
    }
    return $text;
}
?>
<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#layout" data-toggle="tab"><?php echo CMS\LAYOUT?></a></li>
    <li><a href="#subsections" data-toggle="tab"><?php echo CMS\SUBSECTIONS?></a></li>
    <?php if ($Item->affectedMaterialTypes) { ?>
        <?php foreach ($Item->affectedMaterialTypes as $row) { ?>
            <li><a href="#_<?php echo htmlspecialchars($row->urn)?>" data-toggle="tab"><?php echo htmlspecialchars($row->name)?></a></li>
        <?php } ?>
    <?php } ?>
  </ul>
  <div class="tab-content">
    <?php if ($Item->id) { ?>
        <div class="tab-pane active" id="layout">
          <div class="row">
            <div class="span7" style="min-width: 640px; margin-bottom: 20px;">
              <?php if ($Item->Template->id) { ?>
                  <div class="cms-template" style="<?php echo htmlspecialchars($Item->Template->style)?>">
                    <?php foreach ($Item->Template->locations as $loc) { ?>
                        <div class="cms-location<?php echo $loc->horizontal ? ' cms-horizontal' : ''?>" style="<?php echo htmlspecialchars($loc->style)?>">
                          <?php echo displayLocation($VIEW, $loc, $Item)?>
                        </div>
                    <?php } ?>
                  </div>
              <?php } ?>
              <?php if (isset($Item->blocksByLocations['']) || !$Item->Template->locations) { ?>
                  <div class="cms-location" style="position: relative; width: <?php echo $Item->Template->width?>px">
                    <?php echo displayLocation($VIEW, new \RAAS\CMS\Location(), $Item)?>
                  </div>
              <?php } ?>
            </div>
            <div class="span2">
              <?php 
              foreach (\RAAS\CMS\Block_Type::getTypes() as $key => $row) { 
                  echo $row->viewer->renderLegend($row);
              } 
              ?>
            </div>
          </div>
        </div>
    <?php } ?>
    
    <div class="tab-pane" id="subsections">
      <p><a href="?p=<?php echo $VIEW->packageName?>&action=edit&pid=<?php echo (int)$Item->id?>" class="btn btn-small pull-right"><i class="icon-plus"></i> <?php echo CMS\CREATE_PAGE?></a></p>
      <?php include \RAAS\Application::i()->view->context->tmp('/table.tmp.php');?>
    </div>
    
    <?php if ($Item->affectedMaterialTypes) { ?>
        <?php foreach ($Item->affectedMaterialTypes as $mtype) { ?>
            <div class="tab-pane" id="_<?php echo htmlspecialchars($mtype->urn)?>">
              <p>
                <a href="?p=<?php echo $VIEW->packageName?>&action=edit_material&pid=<?php echo (int)$Item->id?>&mtype=<?php echo $mtype->id?>" class="btn btn-small pull-right">
                  <i class="icon icon-plus"></i> <?php echo CMS\CREATE_MATERIAL?>
                </a>
              </p>
              <form class="form-search" action="" method="get">
                <?php foreach ($VIEW->nav as $key => $val) { ?>
                    <?php if (!in_array($key, array('page', 'm' . (int)$mtype->id . 'search_string'))) { ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($val)?>" />
                    <?php } ?>
                <?php } ?>
                <div class="input-append">
                  <input type="search" class="span2 search-query" name="m<?php echo (int)$mtype->id?>search_string" value="<?php echo htmlspecialchars($VIEW->nav['m' . (int)$mtype->id . 'search_string'])?>" />
                  <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
              </form>
              <?php 
              if ($MSet[$mtype->urn]) {
                  $Table = $MTable[$mtype->urn];
                  include \RAAS\Application::i()->view->context->tmp('/table.tmp.php');
                  ?>
              <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
  </div>
</div>