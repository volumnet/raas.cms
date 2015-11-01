<div class="search materials">
  <div class="h3"><?php echo sprintf(SEARCH_RESULTS_FOR_QUERY, $search_string)?></div>
  <?php if ($Set) { ?>
      <div>
        <?php foreach ($Set as $row) { ?>
            <div class="search-result">
              <?php if ($row instanceof \RAAS\CMS\Page) { ?>
                  <div class="article">
                    <div class="h3 article__title"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></div>
                    <div class="article__text">
                      <?php echo htmlspecialchars(\SOME\Text::cuttext(html_entity_decode(strip_tags($row->location('content')), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                      <p><a href="<?php echo $row->url?>" class="article__read-more">Подробней...</a></p>
                    </div>
                  </div>
              <?php } elseif ($row instanceof \RAAS\CMS\Material) { ?>
                  <div class="article">
                    <div class="h3 article__title"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></div>
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
                  </div>
              <?php } ?>
            </div>
        <?php } ?>
      </div>
      <?php include \RAAS\CMS\Package::i()->resourcesDir . '/pages.inc.php'?>
      <?php if ($Pages->pages > 1) { ?>
          <ul class="pagination pull-right">
            <?php 
            echo $outputNav(
                $Pages, 
                array(
                    'pattern' => '<li><a href="' . \SOME\HTTP::queryString('page={link}') . '">{text}</a></li>', 
                    'pattern_active' => '<li class="active"><a>{text}</a></li>',
                    'ellipse' => '<li class="disabled"><a>...</a></li>'
                )
            );
            ?>
          </ul>
      <?php } ?>
  <?php } elseif ($localError == 'NO_SEARCH_QUERY') { ?>
    <p><?php echo NO_SEARCH_QUERY?></p>
  <?php } elseif ($localError == 'SEARCH_QUERY_TOO_SHORT') { ?>
      <p><?php echo sprintf(SEARCH_QUERY_TOO_SHORT, $config['min_length'])?></p>
  <?php } elseif ($localError == 'NO_RESULTS_FOUND') { ?>
      <p><?php echo NO_RESULTS_FOUND?></p>
  <?php } ?>
</div>