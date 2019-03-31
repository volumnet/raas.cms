<?php
/**
 * Виджет элемента списка модуля "{{MATERIAL_TYPE_NAME}}"
 * @param Material $item Элемент списка
 */
namespace RAAS\CMS;

if ($item->id) { ?>
    <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item">
      <a class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__image" <?php echo $item->url ? 'href="' . htmlspecialchars($item->url) . '"' : ''?>>
        <img src="/<?php echo Package::tn($item->image->fileURL, 1920, 654)?>" alt="<?php echo htmlspecialchars($item->image->name ?: $item->name)?>" />
      </a>
      <?php if ($item->name[0] != '.') { ?>
          <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__text">
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__title">
              <?php echo htmlspecialchars($item->name)?>
            </div>
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}-item__description">
              <?php echo $item->description?>
            </div>
          </div>
      <?php } ?>
    </div>
<?php } ?>
