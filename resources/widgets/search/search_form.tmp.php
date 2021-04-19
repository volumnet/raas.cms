<?php
/**
 * Виджет блока "Форма поиска"
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

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
?>
<div itemscope itemtype="http://schema.org/WebSite">
  <link itemprop="url" href="http<?php echo $_SERVER['HTTPS'] ? 's' : ''?>://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'])?>/" />
  <form action="/search/" data-vue-role="search-form" class="search-form" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction" data-v-slot="vm">
    <a href="#" class="search-form__trigger" data-v-on_click.prevent.stop="vm.toggle()"></a>
    <meta itemprop="target" content="http<?php echo $_SERVER['HTTPS'] ? 's' : ''?>://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'])?>/search/?search_string={search_string}" />
    <div class="search-form__inner">
      <input itemprop="query-input" name="search_string" autocomplete="off" class="form-control search-form__input" type="text" value="<?php echo htmlspecialchars($_GET['search_string'])?>" placeholder="<?php echo SITE_SEARCH?>..." required="required" />
      <button class="btn btn-primary search-form__button"></button>
    </div>
  </form>
</div>
<script type="application/ld+json"><?php echo json_encode($jsonLd)?></script>
<?php echo Package::i()->asset(['/js/search-form.js'])?>
