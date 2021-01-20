<?php
/**
 * Виджет строки навигации ("хлебные крошки")
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
if ($page->parents || $page->Material->id || $page->Item->id) {
    $j = 0; ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
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
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="<?php echo htmlspecialchars($row->url)?>">
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
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="<?php echo htmlspecialchars($page->url)?>">
              <span itemprop="name">
                <?php echo htmlspecialchars($page->getBreadcrumbsName())?>
              </span>
            </a>
            <meta itemprop="position" content="<?php echo $j?>" />
          </li>
      <?php } ?>
    </ol>
    <script type="application/ld+json"><?php echo json_encode($jsonLd)?></script>
<?php } ?>
