<?php
/**
 * Виджет постраничной разбивки
 * @param Pages $pages Постраничная разбивка
 */
namespace RAAS\CMS;

use SOME\HTTP;
use SOME\Pages;
use RAAS\AssetManager;

if ($pages->pages > 1) { ?>
    <ul class="pagination">
      <?php
      $links = [];
      $trace = 2;

      if ($pages->page > 1) {
          $link = '';
          if (($pages->page - 1) > 1) {
              $link = ($pages->page - 1);
          }
          $links[] = [
              'href' => HTTP::queryString('page=' . $link, false, null, true, true),
              'text' => '«'
          ];
      }
      if ($pages->page > 1 + $trace) {
          $links[] = [
              'href' => HTTP::queryString('page=', false, null, true, true),
              'text' => '1'
          ];
      }
      if ($pages->page == 3 + $trace) {
          $links[] = ['href' => HTTP::queryString('page=2'), 'text' => '2'];
      } elseif ($pages->page > 2 + $trace) {
          $links[] = ['ellipsis' => true];
      }
      for ($i = max(1, $pages->page - $trace);
          $i <= min($pages->page + $trace, $pages->pages);
          $i++
      ) {
          $links[] = [
              'href' => HTTP::queryString(
                  'page=' . (($i > 1) ? $i : ''),
                  false,
                  null,
                  true,
                  true
              ),
              'text' => $i,
              'active' => ($pages->page == $i),
          ];
      }
      if ($pages->page == $pages->pages - $trace - 2) {
          $links[] = [
              'href' => HTTP::queryString('page=' . ($pages->pages - 1)),
              'text' => ($pages->pages - 1)
          ];
      } elseif ($pages->page < $pages->pages - $trace - 1) {
          $links[] = ['ellipsis' => true];
      }
      if ($pages->page < $pages->pages - $trace) {
          $links[] = [
              'href' => HTTP::queryString('page=' . $pages->pages),
              'text' => $pages->pages
          ];
      }
      if ($pages->page < $pages->pages) {
          $links[] = [
              'href' => HTTP::queryString('page=' . ($pages->page + 1)),
              'text' => '»'
          ];
      }
      foreach ($links as $link) {
          if ($link['ellipsis']) { ?>
              <li class="pagination__item pagination__item_disabled">
                <a class="pagination__link pagination__link_disabled">...</a>
              </li>
          <?php } elseif ($link['active']) { ?>
              <li class="pagination__item pagination__item_active">
                <span class="pagination__link pagination__link_active">
                  <?php echo htmlspecialchars($link['text'])?>
                </span>
              </li>
          <?php } else { ?>
              <li class="pagination__item">
                <a class="pagination__link" href="<?php echo htmlspecialchars($link['href'])?>">
                  <?php echo htmlspecialchars($link['text'])?>
                </a>
              </li>
          <?php }
      } ?>
    </ul>
    <?php
    AssetManager::requestCSS('/css/pagination.css');
}
