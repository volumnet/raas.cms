<?php
/**
 * Виджет строки навигации ("хлебные крошки")
 * @param Page $page Текущая страница
 */
namespace RAAS\CMS;

$navItemsCounter = count($page->parents)
                 + (bool)$page->Material->id
                 + (bool)$page->Item->id;
if ($navItemsCounter > 1) { ?>
  <ol class="breadcrumb">
    <?php foreach ($page->parents as $row) { ?>
        <li>
          <a href="<?php echo htmlspecialchars($row->url)?>">
            <?php echo htmlspecialchars($row->getBreadcrumbsName())?>
          </a>
        </li>
    <?php }
    if ($page->Material->id || $page->Item->id) { ?>
        <li>
          <a href="<?php echo htmlspecialchars($page->url)?>">
            <?php echo htmlspecialchars($page->getBreadcrumbsName())?>
          </a>
        </li>
    <?php } ?>
  </ol>
<?php } ?>
