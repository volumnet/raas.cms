<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}" для главной страницы
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

if ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__title h2">
        <?php echo htmlspecialchars($Block->name)?>
      </div>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__list">
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-list">
          <?php foreach ($Set as $item) { ?>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-list__item">
                <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item">
                  <?php if ($item->image->id || $item->icon) { ?>
                      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__image">
                        <?php if ($item->image->id) { ?>
                            <img loading="lazy" src="/<?php echo htmlspecialchars($item->image->fileURL)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" />
                        <?php } elseif ($item->icon) { ?>
                            <span class="fa fa-<?php echo htmlspecialchars($item->icon)?>"></span>
                        <?php } ?>
                      </div>
                  <?php } ?>
                  <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__text">
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__title h5">
                      <?php echo htmlspecialchars($item->name)?>
                    </div>
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__description">
                      <?php echo $item->description?>
                    </div>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php
    AssetManager::requestCSS('/css/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.css');
    // AssetManager::requestJS('/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js');
} ?>
