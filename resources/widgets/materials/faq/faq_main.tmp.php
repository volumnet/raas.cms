<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}" для главной страницы
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Text;

$nat = true;

if ($Set) {
    ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__title">
        <a<?php echo $nat ? ' href="/{{MATERIAL_TYPE_CSS_CLASSNAME}}/"' : ''?>>
          <?php echo htmlspecialchars($Block->name)?>
        </a>
      </div>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__list">
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-list">
          <?php foreach ($Set as $item) { ?>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-list__item">
                <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item">
                  <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__text {{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__text_question">
                    <?php if ($item->image->id) { ?>
                        <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__image"<?php echo $nat ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                          <img src="/<?php echo htmlspecialchars($item->image->tnURL)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" /></a>
                    <?php } ?>
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__title">
                      <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__name"<?php echo $nat ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                        <?php echo htmlspecialchars($item->name)?></a>,
                      <?php
                      $time = strtotime($item->date);
                      if ($time <= 0) {
                          $time = strtotime($item->post_date);
                      }
                      if ($time > 0) {
                          ?>
                          <span class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__date">
                            <?php echo date('d.m.Y', $time)?>
                          </span>
                      <?php } ?>
                    </div>
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__description">
                      <?php echo $item->description?>
                    </div>
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__more">
                      <a href="<?php echo $nat ? htmlspecialchars($item->url) : '/{{MATERIAL_TYPE_CSS_CLASSNAME}}/'?>">
                        <?php echo READ_ANSWER?>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
    </div>
<?php } ?>
