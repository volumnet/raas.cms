<?php
/**
 * Отображение страницы
 */
namespace RAAS\CMS;

use RAAS\Application;

/**
 * Форматирует блок
 * @param Block $block
 * @param Page $page Страница
 * @param int $i Порядковый номер
 * @return array
 */
$formatBlock = function (Block $block, Page $page, $i) {
    $blocksByLocations = $page->blocksByLocations;
    $contextMenu = ViewSub_Main::i()->getBlockContextMenu(
        $block,
        $page,
        $i,
        count($blocksByLocations[$block->location] ?? [])
    );
    $result = [
        'id' => $block->id,
        'vis' => (bool)(int)$block->vis,
        'url' => Package::i()->view->url . '&action=edit_block&id=' . (int)$block->id . '&pid=' . (int)$page->id,
        'name' => $block->name,
        'cssClass' => Block_Type::getType($block->block_type)->viewer->cssClass,
        'contextMenu' => getMenu($contextMenu),
    ];
    return $result;
};

/**
 * Форматирует размещение
 * @param Location $location Размещение
 * @param Page $page Страница
 * @return array
 */
$formatLocation = function (Location $location, Page $page) use ($formatBlock) {
    $blocksByLocations = $page->blocksByLocations;
    $result['urn'] = $location->urn;
    if ($result['urn']) {
        foreach (['x', 'y', 'width', 'height'] as $key) {
            $result[$key] = $location->$key;
        }
    }
    $result['blocks'] = [];
    foreach (($blocksByLocations[$location->urn] ?? []) as $i => $block) {
        $result['blocks'][] = $formatBlock($block, $page, $i);
    }
    $result['contextMenu'] = getMenu(ViewSub_Main::i()->getLocationContextMenu($location, $page));
    return $result;
};
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
    <?php if ($Item->id) {
        $templateJSON = $emptyLocationJSON = null;
        $blocksByLocations = $Item->blocksByLocations;
        $template = $Item->Template;
        if ($template->id) {
            $templateJSON = $template->getArrayCopy();
            unset($templateJSON['description'], $templateJSON['background']);
            $templateJSON['locations_info'] = array_map(function ($x) use ($Item, $formatLocation) {
                return $formatLocation($x, $Item);
            }, $template->locations);
        }
        $blocksByLocations = $Item->blocksByLocations;
        if ((isset($blocksByLocations['']) && $blocksByLocations['']) || !$template->locations) {
            $emptyLocationJSON = $formatLocation(new Location(), $Item);
        }

        $legendJSON = [];
        foreach (Block_Type::getTypes() as $blockType) {
            $legendBlockJSON = [
                'vis' => true,
                'name' => $blockType->viewer->renderBlockTypeName(),
                'cssClass' => $blockType->viewer->cssClass,
            ];
            $legendJSON[] = $legendBlockJSON;
        }
        ?>
        <div class="tab-pane active" id="layout">
          <cms-page-layout
            :template-data="<?php echo htmlspecialchars(json_encode($templateJSON))?>"
            :empty-location="<?php echo htmlspecialchars(json_encode($emptyLocationJSON))?>"
            :legend="<?php echo htmlspecialchars(json_encode($legendJSON))?>"
          ></cms-page-layout>
        </div>
    <?php } ?>

    <div class="tab-pane" id="subsections">
      <p>
        <a href="?p=<?php echo ViewSub_Main::i()->packageName?>&action=edit&pid=<?php echo (int)$Item->id?>" class="btn btn-small pull-right">
          <i class="icon-plus"></i>
          <?php echo \CMS\CREATE_PAGE?>
        </a>
      </p>
      <?php
      echo $Table->render(false, 'subsections');
      ?>
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
                  <input type="search" class="span2 search-query" name="m<?php echo (int)$mtype->id?>search_string" value="<?php echo htmlspecialchars(ViewSub_Main::i()->nav['m' . (int)$mtype->id . 'search_string'] ?? '')?>" />
                  <button type="submit" class="btn">
                    <i class="icon-search"></i>
                  </button>
                </div>
              </form>
              <?php
              if ($MSet[$mtype->urn]) {
                  echo $MTable[$mtype->urn]->render(false, '_' . $mtype->urn);
              } ?>
            </div>
        <?php }
    } ?>
  </div>
</div>
