<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}" для главной страницы
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;


if ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__title">
        <?php echo htmlspecialchars($Block->name)?>
      </div>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__list">
        <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_main_list')->process([
            'set' => $Set,
            'nat' => false,
        ]); ?>
      </div>
    </div>
    <?php if (is_file('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js')) { ?>
        <script src="/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js?v=<?php echo date('Y-m-d', strtotime('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js'))?>"></script>
    <?php } ?>
<?php } ?>
