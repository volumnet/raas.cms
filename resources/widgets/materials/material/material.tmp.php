<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Pages $Pages Постраничная разбивка
 * @param array<Material> $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Pages;

if ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__list">
        <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_list')->process([
            'set' => $Set,
            'nat' => false
        ]); ?>
      </div>
      <?php if ($Pages->pages > 1) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__pagination">
            <?php Snippet::importByURN('pagination')->process(['pages' => $Pages]); ?>
          </div>
      <?php } ?>
    </div>
    <?php if (is_file('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}.js')) { ?>
        <script src="/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}.js?v=<?php echo date('Y-m-d', strtotime('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}.js'))?>"></script>
    <?php } ?>
<?php } ?>
