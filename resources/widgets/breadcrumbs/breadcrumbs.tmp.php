<?php
/**
 * Виджет строки навигации ("хлебные крошки")
 * @param Page $page Текущая страница
 */
namespace RAAS\CMS;

$navItemsCounter = count($page->parents)
                 + (bool)$page->Material->id
                 + (bool)$page->Item->id;
$jsonLd = [
              '@context' => 'http://schema.org',
              '@type' => 'BreadcrumbList',
              'itemListElement' => [],
          ];
$host = 'http' . (mb_strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '') . '://'
      . $_SERVER['HTTP_HOST'];
if ($navItemsCounter > 1) { ?>
  <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
    <?php foreach ($page->parents as $i => $row) {
        $jsonLd['itemListElement'][] = [
            '@type' => 'ListItem',
            'item' => [
              '@id' => $host . $row->url,
              'name' => $row->getBreadcrumbsName(),
            ],
            'position' => ($i + 1),
        ];
        ?>
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
          <a itemprop="item" href="<?php echo htmlspecialchars($row->url)?>">
            <span itemprop="name">
              <?php echo htmlspecialchars($row->getBreadcrumbsName())?>
            </span>
          </a>
          <meta itemprop="position" content="<?php echo ($i + 1)?>" />
        </li>
    <?php }
    if ($page->Material->id || $page->Item->id) {
        $jsonLd['itemListElement'][] = [
            '@type' => 'ListItem',
            'item' => [
              '@id' => $host . $page->url,
              'name' => $page->getBreadcrumbsName(),
            ],
            'position' => $navItemsCounter,
        ];
        ?>
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
          <a itemprop="item" href="<?php echo htmlspecialchars($page->url)?>">
            <span itemprop="name">
              <?php echo htmlspecialchars($page->getBreadcrumbsName())?>
            </span>
          </a>
          <meta itemprop="position" content="<?php echo $navItemsCounter?>" />
        </li>
    <?php } ?>
  </ol>
  <script type="application/ld+json"><?php echo json_encode($jsonLd)?></script>
<?php } ?>

