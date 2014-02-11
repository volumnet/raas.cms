<section class="infosection">
  <article class="article">
    <h2 class="article-title text-normal"><?php echo sprintf(SEARCH_RESULTS_FOR_QUERY, $search_string)?></h2>
  </article>
  <?php if ($Set) { ?>
      <?php foreach ($Set as $row) { ?>
          <article class="article">
            <?php if ($row->cover->tnURL || $row->visImages) { ?>
                <?php if ($row instanceof \RAAS\CMS\Page) { ?>
                    <a href="<?php echo $row->url?>" class="context-image thumbnail w130 zoom-in pull-left">
                      <img src="/files/common<?php echo $row->cover->tnURL ? $row->cover->tnURL : $row->visImages[0]->tnURL?>"></a>
                <?php } else { ?>
                    <a href="<?php echo $row->page->url?>?id=<?php echo (int)$row->id?>" class="context-image thumbnail w130 zoom-in pull-left">
                      <img src="/files/common<?php echo $row->cover->tnURL ? $row->cover->tnURL : $row->visImages[0]->tnURL?>"></a>
                <?php } ?>
            <?php } ?>
            <div class="text">
              <?php if ($row instanceof \RAAS\CMS\Page) { ?>
                  <h2 class="list-title text-normal"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></h2>
                  <p><?php echo htmlspecialchars(\SOME\Text::cuttext(html_entity_decode(strip_tags($row->full_text), ENT_COMPAT | ENT_HTML401, 'UTF-8'), 256, '...'))?></p>
              <?php } elseif ($row instanceof \RAAS\CMS\Material) { ?>
                  <h2 class="list-title text-normal"><a href="<?php echo $row->page->url?>?id=<?php echo (int)$row->id?>"><?php echo htmlspecialchars($row->name)?></a></h2>
                  <?php if ($row->date) { ?>
                      <p class="date italic muted">
                        <small>
                          <?php echo date('d', strtotime($row->date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->date))] . ' ' . date('Y', strtotime($row->date))?> г.
                        </small>
                      </p>
                  <?php } ?>
                  <p><?php echo htmlspecialchars(\SOME\Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML401, 'UTF-8'), 256, '...'))?></p>
              <?php } ?>
            </div>
          </article>
      <?php } ?>
  <?php } elseif ($localError == 'NO_SEARCH_QUERY') { ?>
      <div class="notifications"><div class="alert alert-error"><?php echo NO_SEARCH_QUERY?></div></div>
  <?php } elseif ($localError == 'SEARCH_QUERY_TOO_SHORT') { ?>
      <div class="notifications">
        <div class="alert alert-error"><?php echo sprintf(SEARCH_QUERY_TOO_SHORT, $config['min_length'])?></div>
      </div>
  <?php } elseif ($localError == 'NO_RESULTS_FOUND') { ?>
      <div class="notifications"><div class="alert alert-error"><?php echo NO_RESULTS_FOUND?></div></div>
  <?php } ?>
</section>

<?php if ($Set) { ?>
    <div class="paginator">
      <?php
      $outputPager = function($Pages) 
      {
          if ($Pages->page > 1) {
              $pages_list[] = '<li class="previous"><a href="' . \SOME\HTTP::queryString('page=' . ($Pages->page - 1)) . '">← Старее</a></li>';
          }
          if ($Pages->page < $Pages->pages) {
              $pages_list[] = '<li class="next"><a href="' . \SOME\HTTP::queryString('page=' . ($Pages->page + 1)) . '">Новее →</a></li>';
          }
          $pages_list = implode($sep, $pages_list);
          return $pages_list;
      };
      $outputNav = function($Pages, array $options = array()) 
      {
          $pages_list = array();
          $default = array();
          $default['pattern_active'] = '<li class="active"><a>{text}</a></li>';
          $default['pattern'] = '<li><a href="' . \SOME\HTTP::queryString('page={link}') . '">{text}</a></li>';
          $default['trace'] = 2;
          $default['ellipse'] = '<li class="disabled"><a>...</a></li>';
          $default['sep'] = ' ';
          $options = array_merge($default, $options);
          extract($options);
          
          if ($Pages->page > 1 + $trace) {
              $pages_list[] = strtr($pattern, array(urlencode('{link}') => 1, '{text}' => 1));
          }
          if ($Pages->page > 2 + $trace) {
              if ($Pages->page == 3 + $trace) {
                  $pages_list[] = strtr($pattern, array(urlencode('{link}') => 2, '{text}' => 2));
              } else {
                  $pages_list[] = $ellipse;
              }
          }
          for ( $i = max(1, $Pages->page - $trace);
                $i <= min($Pages->page + $trace, $Pages->pages);
                $i++) {
              $pages_list[] = strtr(($i == $Pages->page ? $pattern_active : $pattern), array(urlencode('{link}') => $i, '{text}' => $i));
          }
          if ($Pages->page < $Pages->pages - $trace - 1) {
              if ($Pages->page == $Pages->pages - $trace - 2) {
                  $pages_list[] = strtr($pattern, array(urlencode('{link}') =>  $Pages->pages - 1, '{text}' =>  $Pages->pages - 1));
              } else {
                  $pages_list[] = $ellipse;
              }
          }
          if ($Pages->page < $Pages->pages - $trace) {
              $pages_list[] = strtr($pattern, array(urlencode('{link}') => $Pages->pages, '{text}' => $Pages->pages));
          }
          
          $pages_list = implode($sep, $pages_list);
          return $pages_list;
      };
      ?>
      <?php if ($Pages->pages > 1) { ?>
          <ul class="pager"><?php echo $outputPager($Pages)?></ul>
          <div class="pagination pagination-centered"><ul><?php echo $outputNav($Pages)?></ul></div>
      <?php } ?>
    </div>
<?php } ?>