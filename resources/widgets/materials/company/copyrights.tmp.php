<?php
/**
 * Копирайты
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

$company = $Page->company;

?>
<div class="copyrights">
  <?php
  if (!($copyrights = $company->copyrights)) {
      $copyrights = '© '
                  . ($company->legal_name ?: $company->name)
                  . ', ' . date('Y') . '. '
                  . View_Web::i()->_('ALL_RIGHTS_RESERVED') . '.';
  }
  $copyrights = str_replace('{{YEAR}}', date('Y'), $copyrights);
  echo htmlspecialchars($copyrights);
  ?>
</div>
