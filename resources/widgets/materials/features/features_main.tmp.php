<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}" для главной страницы
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

if ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__title">
        <?php echo htmlspecialchars($Block->name)?>
      </div>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__list">
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-list">
          <?php foreach ($Set as $item) { ?>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-list__item">
                <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item">
                  <?php if ($item->image->id || $item->icon) { ?>
                      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__image">
                        <?php if ($item->image->id) { ?>
                            <img src="/<?php echo htmlspecialchars($item->image->fileURL)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" />
                        <?php } elseif ($item->icon) { ?>
                            <span class="fa fa-<?php echo htmlspecialchars($item->icon)?>"></span>
                        <?php } ?>
                      </div>
                  <?php } ?>
                  <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__text">
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__title">
                      <?php echo htmlspecialchars($item->name)?>
                    </div>
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__description">
                      <?php echo $item->description?>
                    </div>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php if (is_file('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js')) { ?>
        <script src="/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js?v=<?php echo date('Y-m-d', strtotime('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js'))?>"></script>
    <?php } ?>
<?php } ?>
