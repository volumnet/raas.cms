<?php
/**
 * Виджет категории для отображения в списке
 * @param Page $page Категория для отоображения
 */
namespace RAAS\CMS\Shop;

$queryString = http_build_query(array_intersect_key($_GET, array_flip(['brand'])));
$queryString = $queryString ? '?' . $queryString : '';
?>
<a class="category" href="<?php echo $page->url . $queryString ?>">
  <div class="category__image<?php echo !$page->image->id ? ' category__image_nophoto' : ''?>">
    <?php if ($page->image->id) { ?>
        <img src="/<?php echo htmlspecialchars($page->image->smallURL)?>" alt="<?php echo htmlspecialchars($page->image->name ?: $page->name)?>" />
    <?php } ?>
  </div>
  <div class="category__text">
    <div class="category__title">
      <?php echo htmlspecialchars($page->name . ((int)$page->counter ? ' (' . (int)$page->counter . ')' : ''))?>
    </div>
  </div>
</a>
