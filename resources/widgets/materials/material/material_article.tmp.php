<?php
/**
 * Виджет статьи материала модуля "{{MATERIAL_TYPE_NAME}}"
 * @param Material $item Элемент списка
 */
namespace RAAS\CMS;

use SOME\Text;
use SOME\HTTP;

if ($item->id) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article">
      <?php if (($time = strtotime($item->date)) > 0) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__date">
            <?php echo date('d', $time) . ' ' . Text::$months[(int)date('m', $time)] . ' ' . date('Y', $time)?>
          </div>
      <?php } ?>
      <?php if ($item->visImages) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__image">
            <a href="/<?php echo $item->visImages[0]->fileURL?>" data-lightbox-gallery="g">
              <img src="/<?php echo $item->visImages[0]->tnURL?>" alt="<?php echo htmlspecialchars($item->visImages[0]->name ?: $row->name)?>" /></a>
          </div>
      <?php } ?>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__text">
        <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__description">
          <?php echo $item->description; ?>
        </div>
      </div>
      <?php if (count($item->visImages) > 1) { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images">
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images-title">
              <?php echo PHOTOS ?>
            </div>
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article__images-list">
              <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_article_images_list')->process([
                  'set' => array_slice($item->visImages, 1)
              ]); ?>
            </div>
          </div>
      <?php } ?>
    </div>
<?php } ?>
