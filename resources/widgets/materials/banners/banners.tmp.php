<?php
/**
 * Баннеры
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

if ($Set) { ?>
    <div class="banners slider slider_fade" data-vue-role="raas-slider" data-vue-type="fade" data-v-bind_autoscroll="true" data-v-slot="vm">
      <div class="banners__list slider__list" data-role="slider-list">
        <div class="banners-list slider-list slider-list_fade">
          <?php for ($i = 0; $i < count($Set); $i++) { $item = $Set[$i]; ?>
              <a
                class="banners-list__item slider-list__item banners-item banners-item__image"
                <?php echo $item->url ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>
                data-role="slider-item"
                data-slider-index="<?php echo (int)$i?>"
                <?php echo !$i ? ' style="position: relative; opacity: 1;" data-v-bind_style="{ position: \'\', opacity: \'\' }"' : ''?>
                data-v-bind_class="{ 'banners-list__item_active': (vm.activeFrame == <?php echo $i?>), 'slider-list__item_active': (vm.activeFrame == <?php echo $i?>) }"
              >
                <img class="banners-item__image"<?php echo $i ? ' loading="lazy"' : ''?> src="/<?php echo htmlspecialchars($item->image->fileURL)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" />
              </a>
          <?php } ?>
        </div>
      </div>
      <?php if (count($Set) > 1) { ?>
          <div class="banners__nav banners-nav slider-nav">
            <?php for ($i = 0; $i < count($Set); $i++) { ?>
                <a class="banners-nav__item slider-nav__item" data-v-on_click="vm.slideTo(<?php echo (int)$i?>)" data-v-bind_class="{ 'banners-nav__item_active': (vm.activeFrame == <?php echo (int)$i?>), 'slider-nav__item_active': (vm.activeFrame == <?php echo (int)$i?>), }"></a>
            <?php } ?>
          </div>
          <a data-v-on_click="vm.prev()" class="banners__arrow banners__arrow_prev slider__arrow slider__arrow_prev" data-v-bind_class="{ 'banners__arrow_active': vm.prevAvailable, 'slider__arrow_active': vm.prevAvailable }"></a>
          <a data-v-on_click="vm.next()" class="banners__arrow banners__arrow_next slider__arrow slider__arrow_next" data-v-bind_class="{ 'banners__arrow_active': vm.nextAvailable, 'slider__arrow_active': vm.nextAvailable }"></a>
      <?php } ?>
    </div>
    <?php
    AssetManager::requestCSS('/css/banners.css');
    AssetManager::requestJS('/js/banners.js');
}
