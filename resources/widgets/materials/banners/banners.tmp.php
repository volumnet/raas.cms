<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

if ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}">
      <div id="{{MATERIAL_TYPE_CSS_CLASSNAME}}<?php echo (int)$Block->id?>" class="carousel slide {{MATERIAL_TYPE_CSS_CLASSNAME}}__inner" data-role="slider" data-slider-carousel="bootstrap" data-slider-autoscroll="true">
        <?php if (count($Set) > 1) { ?>
            <ul class="carousel-indicators {{MATERIAL_TYPE_CSS_CLASSNAME}}__nav">
              <?php for ($i = 0; $i < count($Set); $i++) { ?>
                  <li data-target="#{{MATERIAL_TYPE_CSS_CLASSNAME}}<?php echo (int)$Block->id?>" data-slide-to="<?php echo (int)$i?>" class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__nav-item <?php echo !$i ? 'active' : ''?>"></li>
              <?php } ?>
            </ul>
        <?php } ?>
        <div class="carousel-inner {{MATERIAL_TYPE_CSS_CLASSNAME}}__list {{MATERIAL_TYPE_CSS_CLASSNAME}}-list">
          <?php for ($i = 0; $i < count($Set); $i++) { $item = $Set[$i]; ?>
              <div class="item <?php echo !$i ? 'active' : ''?> {{MATERIAL_TYPE_CSS_CLASSNAME}}-list__item">
                <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item">
                  <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__image" <?php echo $item->url ? 'href="' . htmlspecialchars($item->url) . '"' : ''?>>
                    <img src="/<?php echo Package::tn($item->image->fileURL, 1920, 654)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" />
                  </a>
                  <?php if ($item->name[0] != '.') { ?>
                      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__text">
                        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__title">
                          <?php echo htmlspecialchars($item->name)?>
                        </div>
                        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__description">
                          <?php echo $item->description?>
                        </div>
                      </div>
                  <?php } ?>
                </div>
              </div>
          <?php } ?>
        </div>
        <?php if (count($Set) > 1) { ?>
            <a href="#{{MATERIAL_TYPE_CSS_CLASSNAME}}<?php echo (int)$Block->id?>" data-slide="prev" class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__arrow {{MATERIAL_TYPE_CSS_CLASSNAME}}__arrow_left"></a>
            <a href="#{{MATERIAL_TYPE_CSS_CLASSNAME}}<?php echo (int)$Block->id?>" data-slide="next" class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__arrow {{MATERIAL_TYPE_CSS_CLASSNAME}}__arrow_right"></a>
        <?php } ?>
      </div>
    </div>
<?php } ?>
