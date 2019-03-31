<?php
/**
 * Виджет списка модуля "{{MATERIAL_TYPE_NAME}}"
 * @param array<Material> $set Список материалов
 */
namespace RAAS\CMS;

if ($set) { ?>
    <div class="carousel-inner {{MATERIAL_TYPE_CSS_CLASSNAME}}__list {{MATERIAL_TYPE_CSS_CLASSNAME}}-list">
      <?php for ($i = 0; $i < count($set); $i++) { $item = $set[$i]; ?>
          <div class="item <?php echo !$i ? 'active' : ''?> {{MATERIAL_TYPE_CSS_CLASSNAME}}-list__item">
            <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_item')->process(['item' => $item]); ?>
          </div>
      <?php } ?>
    </div>
<?php } ?>
