<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Pages $Pages Постраничная разбивка
 * @param array<Material> $Set Список материалов
 * @param Material $Item Активный материал
 */
namespace RAAS\CMS;

if ($Item) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__article">
        <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_article')->process(['item' => $Item]); ?>
      </div>
    </div>
<?php } elseif ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__list">
        <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_list')->process([
            'set' => $Set,
            'nat' => true
        ]); ?>
      </div>
      <?php if ($Pages->pages > 1) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__pagination">
            <?php Snippet::importByURN('pagination')->process(['pages' => $Pages]); ?>
          </div>
      <?php } ?>
    </div>
<?php } ?>
<?php if (is_file('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}.js')) { ?>
    <script src="/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}.js?v=<?php echo date('Y-m-d', strtotime('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}.js'))?>"></script>
<?php } ?>
