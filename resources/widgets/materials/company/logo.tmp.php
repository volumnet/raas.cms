<?php
/**
 * Виджет блока "Логотип"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param Material[]|null $Set Список материалов
 */
namespace RAAS\CMS;

$company = $Set[0];

?>
<a<?php echo ($Page->pid || $Page->Material->id) ? ' href="/"' : ''?> class="logo">
  <span class="logo__image">
    <img src="/<?php echo htmlspecialchars($company->logo->fileURL)?>" alt="<?php echo htmlspecialchars($company->logo->name ?: $company->name)?>" />
  </span>
  <?php if ($company->logo->name || $company->logo->description) { ?>
      <span class="logo__text">
        <span class="logo__title">
          <?php echo htmlspecialchars($company->logo->name ?: $company->name)?>
        </span>
        <?php if (trim($company->logo->description)) { ?>
            <span class="logo__description">
              <?php echo htmlspecialchars($company->logo->description)?>
            </span>
        <?php } ?>
      </span>
  <?php } ?>
</a>
<?php
Package::i()->requestCSS('/css/logo.css');
Package::i()->requestJS('/js/logo.js');
