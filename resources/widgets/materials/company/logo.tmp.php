<?php
/**
 * Виджет блока "{{WIDGET_NAME}}"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

$company = $Set[0];

?>
<a<?php echo $Page->pid ? ' href="/"' : ''?> class="{{WIDGET_CSS_CLASSNAME}}">
  <span class="{{WIDGET_CSS_CLASSNAME}}__image">
    <img src="/<?php echo htmlspecialchars($company->logo->fileURL)?>" alt="<?php echo htmlspecialchars($company->logo->name ?: $company->name)?>" />
  </span>
  <span class="{{WIDGET_CSS_CLASSNAME}}__text">
    <span class="{{WIDGET_CSS_CLASSNAME}}__title">
      <?php echo htmlspecialchars($company->logo->name ?: $company->name)?>
    </span>
    <?php if (trim($company->logo->description)) { ?>
        <span class="{{WIDGET_CSS_CLASSNAME}}__description">
          <?php echo htmlspecialchars($company->logo->description)?>
        </span>
    <?php } ?>
  </span>
</a>
