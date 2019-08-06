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
use SOME\Text;

$nat = false;

if ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__list">
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-list">
          <?php foreach ($Set as $item) { ?>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-list__item">
                <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item">
                  <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__image<?php echo !$item->visImages ? ' {{MATERIAL_TYPE_CSS_CLASSNAME}}-item__image_no-image' : ''?>"<?php echo ($nat ? ' href="' . htmlspecialchars($item->url) . '"' : '')?>>
                    <?php if ($item->visImages) { ?>
                        <img src="/<?php echo htmlspecialchars($item->visImages[0]->tnURL)?>" alt="<?php echo htmlspecialchars($item->visImages[0]->name ?: $item->name)?>" />
                    <?php } ?>
                  </a>
                  <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__text">
                    <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__title"<?php echo $nat ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                      <?php echo htmlspecialchars($item->name)?>
                    </a>
                    <?php if (($time = strtotime($item->date)) > 0) { ?>
                        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__date">
                          <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
                        </div>
                    <?php } ?>
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__description">
                      <?php echo htmlspecialchars($item->brief ?: Text::cuttext(html_entity_decode(strip_tags($item->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                    </div>
                    <?php if ($nat) { ?>
                        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__more">
                          <a href="<?php echo htmlspecialchars($item->url)?>">
                            <?php echo SHOW_MORE?>
                          </a>
                        </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>
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
