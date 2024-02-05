<?php
/**
 * Категория для отображения в списке
 * @param Page $page Категория для отоображения
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

?>
<a class="category" href="<?php echo $page->url?>">
  <img class="category__image" loading="lazy" src="/<?php echo htmlspecialchars($page->image->id ? $page->image->tnURL : 'files/cms/common/image/design/nophoto.jpg')?>" alt="<?php echo htmlspecialchars($page->image->name ?: $page->name)?>" />
  <div class="category__title">
    <?php echo htmlspecialchars($page->name)?>
  </div>
</a>
<?php
AssetManager::requestCSS('/css/category.css');
AssetManager::requestJS('/js/category.js');
