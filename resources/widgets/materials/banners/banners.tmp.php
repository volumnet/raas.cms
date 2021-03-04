<?php
/**
 * Виджет модуля "Баннеры"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

if ($Set) { ?>
    <div class="banners" data-vue-role="raas-slider" data-vue-type="fade" data-v-bind_autoscroll="true" data-v-slot="vm">
      <div class="banners__list banners-list" data-role="slider-list">
        <div class="banners-list slider-list slider-list_fade">
          <?php for ($i = 0; $i < count($Set); $i++) { $item = $Set[$i]; ?>
              <div class="banners-list__item slider-list__item" data-role="slider-item" data-v-bind_class="{ 'banners-list__item_active': (vm.activeFrame == <?php echo $i?>), 'slider-list__item_active': (vm.activeFrame == <?php echo $i?>) }">
                <div class="banners-item">
                  <a class="banners-item__image" <?php echo $item->url ? 'href="' . htmlspecialchars($item->url) . '"' : ''?>>
                    <img src="/<?php echo Package::tn($item->image->fileURL, 1920, 654)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" />
                  </a>
                  <?php if ($item->name[0] != '.') { ?>
                      <div class="banners-item__text">
                        <div class="banners-item__title">
                          <?php echo htmlspecialchars($item->name)?>
                        </div>
                        <div class="banners-item__description">
                          <?php echo $item->description?>
                        </div>
                      </div>
                  <?php } ?>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
      <?php if (count($Set) > 1) { ?>
          <div class="banners__nav">
            <div class="banners-nav slider-nav">
              <?php for ($i = 0; $i < count($Set); $i++) { ?>
                  <a class="banners-nav__item slider-nav__item" data-v-on_click="vm.slideTo(<?php echo (int)$i?>)" data-v-bind_class="{ 'banners-nav__item_active': (vm.activeFrame == <?php echo (int)$i?>), 'slider-nav__item_active': (vm.activeFrame == <?php echo (int)$i?>), }"></a>
              <?php } ?>
            </div>
          </div>
      <?php } ?>
      <?php if (count($Set) > 1) { ?>
          <a data-v-on_click="vm.prev()" class="banners__arrow banners__arrow_prev slider__arrow slider__arrow_prev" data-v-bind_class="{ 'banners__arrow_active': vm.prevAvailable, 'slider__arrow_active': vm.prevAvailable }"></a>
          <a data-v-on_click="vm.next()" class="banners__arrow banners__arrow_next slider__arrow slider__arrow_next" data-v-bind_class="{ 'banners__arrow_active': vm.nextAvailable, 'slider__arrow_active': vm.nextAvailable }"></a>
      <?php } ?>
    </div>
    <?php
    Package::i()->requestCSS('/css/banners.css');
    Package::i()->requestJS('/js/banners.js');
} ?>
