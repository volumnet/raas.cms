<?php
/**
 * Виджет изображения из списка модуля "{{MATERIAL_TYPE_NAME}}"
 * @param Attachment $item Изображение из списка
 */
namespace RAAS\CMS;

use RAAS\Attachment;

?>
<a href="/<?php echo htmlspecialchars($item->fileURL)?>" class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-article-images-item" data-lightbox-gallery="g">
  <img src="/<?php echo htmlspecialchars($item->tnURL)?>" alt="<?php echo htmlspecialchars($item->name)?>" /></a>
