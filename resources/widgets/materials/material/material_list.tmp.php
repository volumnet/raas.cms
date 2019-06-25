<?php
/**
 * Виджет списка модуля "{{MATERIAL_TYPE_NAME}}"
 * @param array<Material> $set Список материалов
 * @param bool $nat Трансляция адресов
 */
namespace RAAS\CMS;

if ($set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-list">
      <?php foreach ($set as $item) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-list__item">
            <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_item')->process([
                'item' => $item,
                'nat' => $nat
            ]); ?>
          </div>
      <?php } ?>
    </div>
<?php } ?>