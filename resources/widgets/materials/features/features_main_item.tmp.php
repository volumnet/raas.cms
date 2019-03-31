<?php
/**
 * Виджет элемента списка модуля "{{MATERIAL_TYPE_NAME}}" для главной страницы
 * @param Material $item Элемент списка
 */
namespace RAAS\CMS;

use \SOME\Text;

$translateAddresses = true;

if ($item->id) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item">
      <?php if ($item->image->id || $item->icon) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__image">
            <?php if ($item->image->id) { ?>
                <img src="/<?php echo htmlspecialchars($item->image->fileURL)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" />
            <?php } elseif ($item->icon) { ?>
                <span class="fa fa-<?php echo htmlspecialchars($item->icon)?>"></span>
            <?php } ?>
          </div>
      <?php } ?>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__text">
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__title">
          <?php echo htmlspecialchars($item->name)?>
        </div>
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__description">
          <?php echo $item->description?>
        </div>
      </div>
    </div>
<?php } ?>
