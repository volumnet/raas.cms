<?php
/**
 * Виджет блока "Копирайты"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

$company = $Set[0];

?>
<div class="copyrights">
  <?php
  if (!($copyrights = $company->copyrights)) {
      $copyrights = '© '
                  . htmlspecialchars($company->legal_name ?: $company->name)
                  . ', ' . date('Y') . '. '
                  . View_Web::i()->_('ALL_RIGHTS_RESERVED') . '.';
  }
  echo htmlspecialchars($copyrights)
  ?>
</div>
<?php
Package::i()->requestCSS('/css/copyrights.css');
Package::i()->requestJS('/js/copyrights.js');
