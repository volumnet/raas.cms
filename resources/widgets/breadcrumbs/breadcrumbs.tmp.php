<?php
/**
 * Строка навигации ("хлебные крошки")
 * @param Page $page Текущая страница
 */
namespace RAAS\CMS;

use RAAS\Controller_Frontend as RAASControllerFrontend;

$controllerFrontend = RAASControllerFrontend::i();

$jsonLd = [
    '@context' => 'http://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [],
];
$host = $controllerFrontend->scheme . '://' . $controllerFrontend->host;
if (count((array)$page->parents) + (int)($page->Material->id || $page->Item->id) > 1) {
    $j = 0; ?>
    <ul class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
      <?php foreach ($page->parents as $i => $row) {
          $jsonLd['itemListElement'][] = [
              '@type' => 'ListItem',
              'item' => [
                  '@id' => $host . $row->url,
                  'name' => $row->getBreadcrumbsName(),
              ],
              'position' => ++$j,
          ];
          ?>
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a class="breadcrumbs__link" itemprop="item" href="<?php echo htmlspecialchars($row->url)?>">
              <span itemprop="name">
                <?php echo htmlspecialchars($row->getBreadcrumbsName())?>
              </span>
            </a>
            <meta itemprop="position" content="<?php echo $j?>" />
          </li>
      <?php }
      if ($page->Material->id || $page->Item->id) {
          $jsonLd['itemListElement'][] = [
              '@type' => 'ListItem',
              'item' => [
                  '@id' => $host . $page->url,
                  'name' => $page->getBreadcrumbsName(),
              ],
              'position' => ++$j,
          ];
          ?>
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a class="breadcrumbs__link" itemprop="item" href="<?php echo htmlspecialchars($page->url)?>">
              <span itemprop="name">
                <?php echo htmlspecialchars($page->getBreadcrumbsName())?>
              </span>
            </a>
            <meta itemprop="position" content="<?php echo $j?>" />
          </li>
      <?php } ?>
    </ul>
    <script type="application/ld+json"><?php echo json_encode($jsonLd)?></script>
<?php }
