<?php
/**
 * Виджет элемента списка модуля "{{MATERIAL_TYPE_NAME}}" для главной страницы
 * @param Material $item Элемент списка
 * @param bool $nat Трансляция адресов
 */
namespace RAAS\CMS;

use SOME\Text;

if ($item->id) { ?>
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
<?php } ?>
