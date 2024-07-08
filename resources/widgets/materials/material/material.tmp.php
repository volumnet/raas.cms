<?php
/**
 * {{MATERIAL_TYPE_NAME}}
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Pages $Pages Постраничная разбивка
 * @param array<Material>|null $Set Список материалов
 * @param Material|null $Item Активный материал
 */
namespace RAAS\CMS;

use SOME\Pages;
use SOME\Text;
use RAAS\AssetManager;

if ($Item) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}} {{MATERIAL_TYPE_CSS_CLASSNAME}}__article {{MATERIAL_TYPE_CSS_CLASSNAME}}-article">
      <?php if (($time = strtotime($Item->date)) > 0) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__date">
            <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
          </div>
      <?php } ?>
      <?php if ($Item->visImages) { ?>
          <a
            class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__image"
            href="/<?php echo $Item->visImages[0]->fileURL?>"
            data-lightbox-gallery="g"
          >
            <img
              loading="lazy"
              src="/<?php echo htmlspecialchars($Item->visImages[0]->tnURL)?>"
              alt="<?php echo htmlspecialchars($Item->visImages[0]->name ?: $Item->name)?>"
            />
          </a>
      <?php } ?>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__description">
        <?php echo $Item->description; ?>
      </div>
      <?php if (count($visImages = $Item->visImages) > 1) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images">
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images-title h3">
              <?php echo PHOTOS ?>
            </div>
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images-list {{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-list">
              <?php for ($i = 1; $i < count($visImages); $i++) { $image = $visImages[$i]; ?>
                  <a
                    href="/<?php echo htmlspecialchars($image->fileURL)?>"
                    class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-list__item {{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-item"
                    data-lightbox-gallery="g"
                  >
                    <img
                      loading="lazy"
                      src="/<?php echo htmlspecialchars($image->tnURL)?>"
                      alt="<?php echo htmlspecialchars($image->name)?>"
                    />
                  </a>
              <?php } ?>
            </div>
          </div>
      <?php } ?>
    </div>
    <?php
    AssetManager::requestCSS(['/css/{{MATERIAL_TYPE_CSS_CLASSNAME}}-article.css']);
    AssetManager::requestJS(['/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-article.js']);
} elseif ($Set) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}">
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__list {{MATERIAL_TYPE_CSS_CLASSNAME}}-list">
        <?php foreach ($Set as $item) { ?>
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-list__item {{MATERIAL_TYPE_CSS_CLASSNAME}}-item">
              <<?php echo ($Block->nat ? 'a href="' . htmlspecialchars($item->url) . '"' : 'span')?>
                class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__image<?php echo !$item->visImages ? ' {{MATERIAL_TYPE_CSS_CLASSNAME}}-item__image_no-image' : ''?>"
              >
                <img
                  loading="lazy"
                  src="/<?php echo htmlspecialchars($item->visImages ? $item->visImages[0]->tnURL : '/files/cms/common/image/design/nophoto.jpg')?>"
                  alt="<?php echo htmlspecialchars($item->visImages[0]->name ?: $item->name)?>"
                />
              </<?php echo ($Block->nat ? 'a' : 'span')?>>
              <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__text">
                <<?php echo ($Block->nat ? 'a href="' . htmlspecialchars($item->url) . '"' : 'span')?>
                  class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__title"
                >
                  <?php echo htmlspecialchars($item->name)?>
                </<?php echo ($Block->nat ? 'a' : 'span')?>>
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
        <?php } ?>
      </div>
      <?php if ($Pages->pages > 1) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__pagination">
            <?php Snippet::importByURN('pagination')->process(['pages' => $Pages]); ?>
          </div>
      <?php } ?>
    </div>
    <?php
    AssetManager::requestCSS(['/css/{{MATERIAL_TYPE_CSS_CLASSNAME}}-list.css']);
    AssetManager::requestJS(['/js/{{MATERIAL_TYPE_CSS_CLASSNAME}}-list.js']);
}
