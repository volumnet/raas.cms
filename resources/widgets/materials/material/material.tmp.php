<?php
/**
 * Виджет модуля "{{MATERIAL_TYPE_NAME}}"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Pages $Pages Постраничная разбивка
 * @param array<Material>|null $Set Список материалов
 * @param Material|null $Item Активный материал
 */
namespace RAAS\CMS;

use SOME\Pages;
use SOME\Text;

if ($Item) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__article">
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article">
          <?php if (($time = strtotime($Item->date)) > 0) { ?>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__date">
                <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
              </div>
          <?php } ?>
          <?php if ($Item->visImages) { ?>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__image">
                <a href="/<?php echo $Item->visImages[0]->fileURL?>" data-lightbox-gallery="g">
                  <img loading="lazy" src="/<?php echo htmlspecialchars($Item->visImages[0]->tnURL)?>" alt="<?php echo htmlspecialchars($Item->visImages[0]->name ?: $row->name)?>" /></a>
              </div>
          <?php } ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__text">
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__description">
              <?php echo $Item->description; ?>
            </div>
          </div>
          <?php if (count($visImages = $Item->visImages) > 1) { ?>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images">
                <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images-title h3">
                  <?php echo PHOTOS ?>
                </div>
                <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images-list">
                  <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-list">
                    <?php for ($i = 1; $i < count($visImages); $i++) { $image = $visImages[$i]; ?>
                        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-list__item">
                          <a href="/<?php echo htmlspecialchars($image->fileURL)?>" class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-item" data-lightbox-gallery="g">
                            <img loading="lazy" src="/<?php echo htmlspecialchars($image->tnURL)?>" alt="<?php echo htmlspecialchars($image->name)?>" /></a>
                        </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php
    Package::i()->requestCSS(['/css/{{MATERIAL_TYPE_CSS_CLASSNAME}}-article.css']);
    Package::i()->requestJS(['/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-article.js']);
} elseif ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__list">
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-list">
          <?php foreach ($Set as $item) { ?>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-list__item">
                <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item">
                  <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__image<?php echo !$item->visImages ? ' {{MATERIAL_TYPE_CSS_CLASSNAME}}-item__image_no-image' : ''?>"<?php echo ($Block->nat ? ' href="' . htmlspecialchars($item->url) . '"' : '')?>>
                    <img loading="lazy" src="/<?php echo htmlspecialchars($item->visImages ? $item->visImages[0]->tnURL : '/files/cms/common/image/design/nophoto.jpg')?>" alt="<?php echo htmlspecialchars($item->visImages[0]->name ?: $item->name)?>" />
                  </a>
                  <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__text">
                    <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__title"<?php echo $Block->nat ? ' href="' . htmlspecialchars($item->url) . '"' : ''?>>
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
                    <?php if ($Block->nat) { ?>
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
    <?php
    Package::i()->requestCSS(['/css/{{MATERIAL_TYPE_CSS_CLASSNAME}}-list.css']);
    Package::i()->requestJS(['/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-list.js']);
}
