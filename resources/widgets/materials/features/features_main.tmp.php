<?php
/**
 * Виджет модуля "Преимущества" для главной страницы
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

if ($Set) { ?>
    <div class="features-main">
      <div class="features-main__title h2">
        <?php echo htmlspecialchars($Block->name)?>
      </div>
      <div class="features-main__list features-main-list">
        <?php foreach ($Set as $item) { ?>
            <div class="features-main-list__item features-main-item">
              <?php if ($item->image->id || $item->icon) { ?>
                  <?php if ($item->image->id) { ?>
                      <img class="features-main-item__image" loading="lazy" src="/<?php echo htmlspecialchars($item->image->fileURL)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" />
                  <?php } elseif ($item->icon) { ?>
                      <span class="features-main-item__image fa fa-<?php echo htmlspecialchars($item->icon)?>"></span>
                  <?php } ?>
              <?php } ?>
              <div class="features-main-item__text">
                <div class="features-main-item__title h5">
                  <?php echo htmlspecialchars($item->name)?>
                </div>
                <div class="features-main-item__description">
                  <?php echo $item->description?>
                </div>
              </div>
            </div>
        <?php } ?>
      </div>
    </div>
    <?php
    AssetManager::requestCSS('/css/features-main.css');
    // AssetManager::requestJS('/js/features-main.js');
}
