<?php
/**
 * Виджет списка изображений для статьи модуля "{{MATERIAL_TYPE_NAME}}"
 * @param array<Attachment> $set Список изображений
 */
namespace RAAS\CMS;

use RAAS\Attachment;

?>
<div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-list">
  <?php foreach ($set as $item) { ?>
      <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-list__item">
        <?php Snippet::importByURN('{{MATERIAL_TYPE_URN}}_article_images_item')->process(['item' => $item]); ?>
      </div>
  <?php } ?>
</div>
