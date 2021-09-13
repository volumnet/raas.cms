<?php
/**
 * Виджет поиска по сайту
 * @param Block_Search $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Page|Material> $Set Набор результатов для отображения
 * @param string $search_string Строка поиска
 * @param string $localError Ошибка поиска
 */
namespace RAAS\CMS;

use SOME\Text;
use SOME\HTTP;

$materialsSet = array_values(array_filter($Set, function ($x) {
    return $x instanceof Material;
}));
$pagesSet = array_values(array_filter($Set, function ($x) {
    return $x instanceof Page;
}));
if ($catalogMaterialType = Material_Type::importByURN('catalog')) {
    $catalogMaterialTypesIds = (array)MaterialTypeRecursiveCache::i()->getSelfAndChildrenIds($catalogMaterialType->id);
    $productsSet = array_values(array_filter(
        $materialsSet,
        function ($x) use ($catalogMaterialTypesIds) {
            return in_array($x->pid, $catalogMaterialTypesIds);
        }
    ));
    $nonCatalogMaterialsSet = array_values(array_filter(
        $materialsSet,
        function ($x) use ($catalogMaterialTypesIds) {
            return !in_array($x->pid, $catalogMaterialTypesIds);
        }
    ));
    $nonCatalogSet = array_values(array_filter(
        $Set,
        function ($x) use ($catalogMaterialTypesIds) {
            return !($x instanceof Material) || !in_array($x->pid, $catalogMaterialTypesIds);
        }
    ));
} else {
    $nonCatalogMaterialsSet = $materialsSet;
    $nonCatalogSet = $Set;
    $productsSet = [];
}

$searchFormatter = function ($item) {
    $url = $item->url;
    $imageAtt = null;
    $image = $imageName = '';
    if ($visImages = $item->visImages) {
        $imageAtt = $visImages[0];
    } elseif ($imageAttTemp = $item->image) {
        if ($imageAttTemp->id) {
            $imageAtt = $imageAttTemp;
        }
    }
    if ($item instanceof Page) {
        $description = $item->_description_;
    } else {
        $description = $item->description;
    }
    $result = [
        'id' => $item->id,
        'name' => $item->name,
        'url' => $item->url,
        'type' => ($item instanceof Page) ? 'page' : 'material',
    ];
    if (($item instanceof Material) &&
        (($t = strtotime($item->date)) > 0)
    ) {
        $result['date'] = date('d', $t) . ' '
            . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t);
    }
    if ($description) {
        $description = strip_tags($description);
        $description = html_entity_decode(
            $description,
            ENT_COMPAT | ENT_HTML5,
            'UTF-8'
        );
        $description = Text::cuttext($description, 256, '...');
        $result['description'] = $description;
    }
    if ($imageAtt->id) {
        $result['image'] = [
            'url' => '/' . $imageAtt->tnURL,
        ];
        if ($imageName = $imageAtt->name) {
            $result['image']['name'] = $imageName;
        }
    }
    return $result;
};

if ($_GET['AJAX'] == $Block->id) {
    $result = [];
    $result['searchString'] = $search_string;
    if ($Pages) {
        $result['pagination'] = [
            'page' => (int)$Pages->page,
            'rowsPerPage' => (int)$Pages->rows_per_page,
            'pages' => (int)$Pages->pages,
            'from' => (int)$Pages->from,
            'to' => (int)$Pages->to,
            'count' => (int)$Pages->count,
        ];
    }

    if ($pagesSet) {
        $result['pages'] = array_map($searchFormatter, $pagesSet);
    }
    if ($productsSet) {
        $result['catalog'] = array_map($searchFormatter, $productsSet);
    }
    if ($nonCatalogSet) {
        $result['materials'] = array_map($searchFormatter, $nonCatalogMaterialsSet);
    }
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
<div class="search">
  <div class="search__title h3">
    <?php echo sprintf(
        SEARCH_RESULTS_FOR_QUERY,
        htmlspecialchars($search_string)
    )?>
  </div>
  <div class="search__inner">
    <?php if ($productsSet) { ?>
        <div class="search__list">
          <div class="catalog-list">
            <?php foreach ($productsSet as $item) { ?>
                <div class="catalog-list__item">
                  <?php Snippet::importByURN('catalog_item')->process([
                      'item' => $item,
                  ])?>
                </div>
            <?php } ?>
          </div>
        </div>
    <?php } if ($nonCatalogSet) { ?>
        <div class="search__list">
          <div class="search-list">
            <?php foreach ($nonCatalogSet as $item) {
                $itemData = $searchFormatter($item);
                ?>
                <div class="search-list__item">
                  <div class="search-item">
                    <div class="search-item__image">
                      <a href="<?php echo htmlspecialchars($itemData['url'])?>">
                        <img loading="lazy" src="<?php echo htmlspecialchars($itemData['image']['url'] ?: '/files/cms/common/image/design/nophoto.jpg')?>" alt="<?php echo htmlspecialchars($itemData['image']['name'] ?: $itemData['name'])?>" />
                      </a>
                    </div>
                    <div class="search-item__text">
                      <div class="search-item__title">
                        <a href="<?php echo htmlspecialchars($itemData['url'])?>">
                          <?php echo htmlspecialchars($itemData['name'])?>
                        </a>
                      </div>
                      <?php if ($itemData['date']) { ?>
                          <div class="search-item__date">
                            <?php echo htmlspecialchars($itemData['date'])?>
                          </div>
                      <?php } ?>
                      <div class="search-item__description">
                        <?php echo htmlspecialchars($itemData['description'])?>
                      </div>
                    </div>
                  </div>
                </div>
            <?php } ?>
          </div>
        </div>
        <?php if ($Pages->pages > 1) { ?>
            <div class="search__pagination">
              <?php Snippet::importByURN('pagination')->process(['pages' => $Pages]); ?>
            </div>
        <?php } ?>
    <?php } elseif ($localError) { ?>
        <div class="alert alert-danger">
          <?php
          switch ($localError) {
              case 'NO_SEARCH_QUERY':
                  echo NO_SEARCH_QUERY;
                  break;
              case 'SEARCH_QUERY_TOO_SHORT':
                  echo sprintf(SEARCH_QUERY_TOO_SHORT, $Block->min_length);
                  break;
              case 'NO_RESULTS_FOUND':
                  echo NO_RESULTS_FOUND;
                  break;
          }
          ?>
        </div>
    <?php } ?>
  </div>
</div>
<?php
Package::i()->requestCSS(['/css/search.css']);
