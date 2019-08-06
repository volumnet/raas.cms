<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}" для главной страницы
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Pages $Pages Постраничная разбивка
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

use SOME\Pages;
use SOME\Text;

$nat = true;

if ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__title">
        <a<?php echo $translateAddresses ? ' href="/{{MATERIAL_TYPE_CSS_CLASSNAME}}/"' : ''?>>
          <?php echo htmlspecialchars($Block->name)?>
        </a>
      </div>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main__list">
        <?php if ($Set) { ?>
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-list">
              <?php foreach ($Set as $item) { ?>
                  <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-list__item">
                    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item">
                      <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__image<?php echo !$item->visImages ? ' {{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__image_no-image' : ''?>"<?php echo ($nat ? ' href="' . htmlspecialchars($item->url) . '"' : '')?>>
                        <?php if ($item->visImages) { ?>
                            <img src="/<?php echo htmlspecialchars($item->visImages[0]->tnURL)?>" alt="<?php echo htmlspecialchars($item->visImages[0]->name ?: $item->name)?>" />
                        <?php } ?>
                      </a>
                      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__text">
                        <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__title"<?php echo $nat ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
                          <?php echo htmlspecialchars($item->name)?>
                        </a>
                        <?php if (($time = strtotime($item->date)) > 0) { ?>
                            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__date">
                              <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
                            </div>
                        <?php } ?>
                        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__description">
                          <?php echo htmlspecialchars($item->brief ?: Text::cuttext(html_entity_decode(strip_tags($item->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                        </div>
                        <?php if ($nat) { ?>
                            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-main-item__more">
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
        <?php } ?>
      </div>
    </div>
    <?php if (is_file('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js')) { ?>
        <script src="/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js?v=<?php echo date('Y-m-d', strtotime('js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-main.js'))?>"></script>
    <?php } ?>
<?php } ?>
