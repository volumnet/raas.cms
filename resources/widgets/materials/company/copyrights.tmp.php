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
<div class="{{WIDGET_CSS_CLASSNAME}}">
  <?php
  if (!($copyrights = $company->copyrights)) {
      $copyrights = '© ' . View_Web::i()->_('COMPANY') . ', ' . date('Y') . '. '
                  . View_Web::i()->_('ALL_RIGHTS_RESERVED') . '.';
  }
  echo htmlspecialchars($copyrights)
  ?>
</div>
