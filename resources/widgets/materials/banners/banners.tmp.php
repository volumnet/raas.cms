<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
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
        <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_list')->process(['set' => $Set]); ?>
        <?php if (count($Set) > 1) { ?>
            <a href="#{{MATERIAL_TYPE_CSS_CLASSNAME}}<?php echo (int)$Block->id?>" data-slide="prev" class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__arrow {{MATERIAL_TYPE_CSS_CLASSNAME}}__arrow_left"></a>
            <a href="#{{MATERIAL_TYPE_CSS_CLASSNAME}}<?php echo (int)$Block->id?>" data-slide="next" class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__arrow {{MATERIAL_TYPE_CSS_CLASSNAME}}__arrow_right"></a>
        <?php } ?>
      </div>
    </div>
<?php } ?>
