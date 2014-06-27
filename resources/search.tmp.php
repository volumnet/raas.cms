<h3><?php echo sprintf(SEARCH_RESULTS_FOR_QUERY, $search_string)?></h3>
<?php if ($Set) { ?>
    <ol class="search-results node-results">
      <?php foreach ($Set as $row) { ?>
          <li class="search-result">
            <?php if ($row instanceof \RAAS\CMS\Page) { ?>
                <article class="article">
                  <h2 class="article-title text-normal"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></h2>
                  <div class="text">
                    <?php echo htmlspecialchars(\SOME\Text::cuttext(html_entity_decode(strip_tags($row->location('content')), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                    <p><a href="<?php echo $row->url?>" class="read-more">Подробней…</a></p>
                  </div>
                </article>
            <?php } elseif ($row instanceof \RAAS\CMS\Material) { ?>
                <article class="article">
                  <h2 class="article-title text-normal"><a href="<?php echo $Page->url?>?id=<?php echo (int)$row->id?>"><?php echo htmlspecialchars($row->name)?></a></h2>
                  <?php if (strtotime($row->date) > 0) { ?>
                      <p class="date"><small><?php echo date('d', strtotime($row->date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->date))] . ' ' . date('Y', strtotime($row->date))?></small></p>
                  <?php } ?>
                  <?php if ($row->visImages) { ?>
                      <a href="<?php echo $Page->url?>?id=<?php echo (int)$row->id?>" class="context-image thumbnail w130 zoom-in pull-left">
                        <img src="/<?php echo htmlspecialchars(addslashes($row->visImages[0]->tnURL))?>" /></a>
                  <?php } ?>
                  <div class="text">
                    <?php echo htmlspecialchars(\SOME\Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                    <p><a href="<?php echo $Page->url?>?id=<?php echo (int)$row->id?>" class="read-more">Подробней…</a></p>
                  </div>
                </article>
            <?php } ?>
          </li>
      <?php } ?>
    </ol>
<?php } elseif ($localError == 'NO_SEARCH_QUERY') { ?>
  <p><?php echo NO_SEARCH_QUERY?></p>
<?php } elseif ($localError == 'SEARCH_QUERY_TOO_SHORT') { ?>
    <p><?php echo sprintf(SEARCH_QUERY_TOO_SHORT, $config['min_length'])?></p>
<?php } elseif ($localError == 'NO_RESULTS_FOUND') { ?>
    <p><?php echo NO_RESULTS_FOUND?></p>
<?php } ?>