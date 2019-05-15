<?php
/**
 * Отображение страницы
 */
namespace RAAS\CMS;

use RAAS\Application;

/**
 * Отображает размещение
 * @param Location $location Размещение для отображения
 * @param Page $page Страница для отображения
 * @return string
 */
function displayLocation(Location $location, Page $page)
{
    $text = '';
    if ($temp = ViewSub_Main::i()->getLocationContextMenu($location, $page)) {
        $temp = array_map(
            function($x) {
                return ['text' => $x['name'], 'href' => $x['href']];
            },
            $temp
        );
        $temp = json_encode($temp);
        $text .= '<script type="text/javascript">
                  jQuery(document).ready(function($) {
                      context.attach(
                          "#location-' . htmlspecialchars($location->urn) . '",
                          ' . $temp . '
                      );
                  })
                  </script>';
    }
    $text .=  ' <h6>' . htmlspecialchars($location->urn) . '</h6>
                <input type="hidden" value="' . $location->urn . '" />';
    if (isset($page->blocksByLocations[$location->urn])) {
        for ($i = 0; $i < count($page->blocksByLocations[$location->urn]); $i++) {
            $row = $page->blocksByLocations[$location->urn][$i];
            $text .= Block_Type::getType($row->block_type)->viewer->renderBlock(
                $row,
                $page,
                $location,
                $i
            );
        }
    }
    return $text;
}
?>
<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active">
      <a href="#layout" data-toggle="tab"><?php echo \CMS\LAYOUT?></a>
    </li>
    <li><a href="#subsections" data-toggle="tab">
      <?php echo \CMS\SUBSECTIONS?></a>
    </li>
    <?php if ($Item->affectedMaterialTypes) {
        foreach ($Item->affectedMaterialTypes as $row) { ?>
            <li>
              <a href="#_<?php echo htmlspecialchars($row->urn)?>" data-toggle="tab">
                <?php echo htmlspecialchars($row->name)?>
              </a>
            </li>
        <?php }
    } ?>
  </ul>
  <div class="tab-content">
    <?php if ($Item->id) { ?>
        <div class="tab-pane active" id="layout">
          <div class="row">
            <div class="span7" style="min-width: 640px; margin-bottom: 20px;">
              <?php if ($Item->Template->id) { ?>
                  <div class="cms-template" style="<?php echo htmlspecialchars($Item->Template->style)?>">
                    <?php foreach ($Item->Template->locations as $loc) { ?>
                        <div class="cms-location<?php echo $loc->horizontal ? ' cms-horizontal' : ''?>" style="<?php echo htmlspecialchars($loc->style)?>" id="location-<?php echo htmlspecialchars($loc->urn)?>">
                          <?php echo displayLocation($loc, $Item)?>
                        </div>
                    <?php } ?>
                  </div>
              <?php }
              if (isset($Item->blocksByLocations['']) ||
                  !$Item->Template->locations
              ) { ?>
                  <div class="cms-location" style="position: relative; width: <?php echo $Item->Template->width?>px" id="location-">
                    <?php echo displayLocation(new Location(), $Item)?>
                  </div>
              <?php } ?>
            </div>
            <div class="span2">
              <?php foreach (Block_Type::getTypes() as $key => $row) {
                  echo $row->viewer->renderLegend($row);
              } ?>
            </div>
          </div>
        </div>
    <?php } ?>

    <div class="tab-pane" id="subsections">
      <p>
        <a href="?p=<?php echo ViewSub_Main::i()->packageName?>&action=edit&pid=<?php echo (int)$Item->id?>" class="btn btn-small pull-right">
          <i class="icon-plus"></i>
          <?php echo \CMS\CREATE_PAGE?>
        </a>
      </p>
      <?php include ViewSub_Main::i()->tmp('/table.inc.php');
      if ((array)$Table->Set || ($Table->emptyHeader && $Table->header)) { ?>
          <form action="#subsections" method="post">
            <table<?php echo $_RAASTable_Attrs($Table)?>>
              <?php if ($Table->header) { ?>
                  <thead>
                    <tr>
                      <th>
                        <?php if ($Table->meta['allValue']) { ?>
                            <input type="checkbox" data-role="checkbox-all" value="<?php echo htmlspecialchars($Table->meta['allValue'])?>">
                        <?php } ?>
                      </th>
                      <?php foreach ($Table->columns as $key => $col) {
                          include Application::i()->view->context->tmp('/column.inc.php');
                          if ($col->template) {
                              include Application::i()->view->context->tmp($col->template);
                          }
                          $_RAASTable_Header($col, $key);
                      } ?>
                    </tr>
                  </thead>
              <?php }
              if ((array)$Table->Set) { ?>
                  <tbody>
                    <?php for ($i = 0; $i < count($Table->rows); $i++) {
                        $row = $Table->rows[$i];
                        include Package::i()->view->context->tmp('multirow.inc.php');
                        if ($row->template) {
                            include Application::i()->view->context->tmp($row->template);
                        }
                        $_RAASTable_Row($row, $i);
                    } ?>
                  </tbody>
              <?php } ?>
              <tfoot>
                <tr>
                  <td colspan="2">
                    <?php echo rowContextMenu(
                        $Table->meta['allContextMenu'],
                        Application::i()->view->context->_('WITH_SELECTED'),
                        '',
                        'btn-mini'
                    )?>
                  </td>
                  <td colspan="<?php echo (count($Table->columns) - 3)?>">
                    &nbsp;
                  </td>
                  <td>
                    <input type="submit" class="btn btn-small btn-default" style="width: 70px; padding: 2px 0;" value="<?php echo DO_UPDATE?>" />
                  </td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </form>
      <?php }
      if (!(array)$Table->Set && $Table->emptyString) { ?>
          <p><?php echo htmlspecialchars($Table->emptyString)?></p>
      <?php }
      if ($Table->Set &&
          ($Pages = $Table->Pages) &&
          ($pagesVar = $Table->pagesVar)
      ) {
          include ViewSub_Main::i()->tmp('/pages.tmp.php');
      } ?>
    </div>

    <?php if ($Item->affectedMaterialTypes) {
        foreach ($Item->affectedMaterialTypes as $mtype) { ?>
            <div class="tab-pane" id="_<?php echo htmlspecialchars($mtype->urn)?>">
              <p>
                <a href="?p=<?php echo ViewSub_Main::i()->packageName?>&action=edit_material&pid=<?php echo (int)$Item->id?>&mtype=<?php echo $mtype->id?>" class="btn btn-small pull-right">
                  <i class="icon icon-plus"></i>
                   <?php echo \CMS\CREATE_MATERIAL?>
                </a>
              </p>
              <form class="form-search" action="#_<?php echo htmlspecialchars($mtype->urn)?>" method="get">
                <?php foreach (ViewSub_Main::i()->nav as $key => $val) {
                    if (!in_array(
                        $key,
                        ['page', 'm' . (int)$mtype->id . 'search_string']
                    )) { ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($val)?>" />
                    <?php }
                } ?>
                <div class="input-append">
                  <input type="search" class="span2 search-query" name="m<?php echo (int)$mtype->id?>search_string" value="<?php echo htmlspecialchars(ViewSub_Main::i()->nav['m' . (int)$mtype->id . 'search_string'])?>" />
                  <button type="submit" class="btn">
                    <i class="icon-search"></i>
                  </button>
                </div>
              </form>
              <?php
              if ($MSet[$mtype->urn]) {
                  $Table = $MTable[$mtype->urn];
                  $pagesHash = '_' . $mtype->urn;
                  include Application::i()->view->context->tmp('materialstable.tmp.php');
              } ?>
            </div>
        <?php }
    } ?>
  </div>
</div>
