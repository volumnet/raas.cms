<section class="search">
  <h3><?php echo sprintf(SEARCH_RESULTS_FOR_QUERY, $search_string)?></h3>
  <?php if ($Set) { ?>
      <ol>
        <?php foreach ($Set as $row) { ?>
            <li class="search-result">
              <?php if ($row instanceof \RAAS\CMS\Page) { ?>
                  <article class="article">
                    <h3 class="article__title"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></h3>
                    <div class="article__text">
                      <?php echo htmlspecialchars(\SOME\Text::cuttext(html_entity_decode(strip_tags($row->location('content')), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                      <p><a href="<?php echo $row->url?>" class="article__read-more">Подробней...</a></p>
                    </div>
                  </article>
              <?php } elseif ($row instanceof \RAAS\CMS\Material) { ?>
                  <article class="article">
                    <h3 class="article__title"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></h3>
                    <?php if (strtotime($row->date) > 0) { ?>
                        <p class="article__date"><?php echo date('d', strtotime($row->date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->date))] . ' ' . date('Y', strtotime($row->date))?></p>
                    <?php } ?>
                    <?php if ($row->visImages) { ?>
                        <div class="article__image">
                          <a href="<?php echo $row->url?>">
                            <img src="/<?php echo htmlspecialchars(addslashes($row->visImages[0]->tnURL))?>" alt="<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>" /></a>
                        </div>
                    <?php } ?>
                    <div class="article__text">
                      <?php echo htmlspecialchars(\SOME\Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                      <p><a href="<?php echo $row->url?>" class="article__read-more">Подробней...</a></p>
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
</section>