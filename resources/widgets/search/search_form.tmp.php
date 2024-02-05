<?php
/**
 * Форма поиска
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

$jsonLd = [
    '@context' => 'http://schema.org',
    '@type' => 'WebSite',
    'url' => 'http' . ($_SERVER['HTTPS'] ? 's' : '') . '://'
          .  $_SERVER['HTTP_HOST'],
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => 'http' . ($_SERVER['HTTPS'] ? 's' : '') . '://'
                 .  $_SERVER['HTTP_HOST']
                 .  '/search/?search_string={search_string}',
        'query-input' => 'required name=search_string',
    ]
];
if ($searchBlockId = $Block->additionalParams['searchBlockId']) {
    $searchBlock = Block::spawn($searchBlockId);
}
?>
<div itemscope itemtype="http://schema.org/WebSite">
  <link itemprop="url" href="http<?php echo $_SERVER['HTTPS'] ? 's' : ''?>://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'])?>/" />
  <form action="/search/" data-vue-role="search-form"<?php echo $searchBlock->id ? ' data-v-bind_block-id="' . (int)$searchBlock->id . '" data-v-bind_min-length="' . (int)$searchBlock->min_length . '"' : ''?> data-v-bind_foldable="false" class="search-form" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction" data-v-slot="vm">
    <meta itemprop="target" content="http<?php echo $_SERVER['HTTPS'] ? 's' : ''?>://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'])?>/search/?search_string={search_string}" />
    <!--nodesktop-->
    <button type="button" tabindex="-1" class="search-form__trigger" data-v-on_click.prevent.stop="vm.toggle()"></button>
    <!--/nodesktop-->
    <div class="search-form__inner">
      <div class="search-form__field">
        <input itemprop="query-input" name="search_string" autocomplete="off" class="form-control search-form__input" type="text" value="<?php echo htmlspecialchars($_GET['search_string'])?>" placeholder="<?php echo SITE_SEARCH?>..." required="required" data-role="search-string" />
        <div class="search-form__autocomplete" data-v-if="vm.autocomplete" data-vue-role="search-form-autocomplete" data-v-bind_autocomplete="vm.autocomplete"></div>
      </div>
      <button type="submit" class="btn btn-primary search-form__button"></button>
    </div>
  </form>
</div>
<script type="application/ld+json"><?php echo json_encode($jsonLd)?></script>
<?php AssetManager::requestJS(['/js/search-form.js']);
