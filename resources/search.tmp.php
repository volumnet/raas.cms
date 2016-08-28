<?php
namespace RAAS\CMS;

use \SOME\Text;
use \SOME\HTTP;

?>
<div class="search materials">
  <div class="search__title">
    <?php echo sprintf(SEARCH_RESULTS_FOR_QUERY, htmlspecialchars($search_string))?>
  </div>
  <?php if ($Set) { ?>
      <div>
        <?php foreach ($Set as $row) { ?>
            <div class="search__result">
              <?php if ($row instanceof Page) { ?>
                  <div class="article">
                    <div class="article__image">
                      <a href="<?php echo htmlspecialchars($row->url)?>"<?php echo (!$row->image->id ? ' class="no-image"' : '')?>>
                        <?php if ($row->image->id) { ?>
                            <img src="/<?php echo htmlspecialchars($row->image->tnURL)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" />
                        <?php } ?>
                      </a>
                    </div>
                    <div class="article__text">
                      <div class="article__title">
                        <a href="<?php echo htmlspecialchars($row->url)?>">
                          <?php echo htmlspecialchars($row->name)?>
                        </a>
                      </div>
                      <div class="article__description">
                        <?php echo htmlspecialchars(Text::cuttext(html_entity_decode(strip_tags($row->location('content')), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                      </div>
                      <div class="article__more">
                        <a href="<?php echo htmlspecialchars($row->url)?>">
                          <?php echo SHOW_MORE?>
                        </a>
                      </div>
                    </div>
                  </div>
              <?php } elseif ($row instanceof Material) { ?>
                  <div class="article">
                    <div class="article__image">
                      <a href="<?php echo htmlspecialchars($row->url)?>"<?php echo (!$row->visImages ? ' class="no-image"' : '')?>>
                        <?php if ($row->visImages) { ?>
                            <img src="/<?php echo htmlspecialchars($row->visImages[0]->tnURL)?>" alt="<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>" />
                        <?php } ?>
                      </a>
                    </div>
                    <div class="article__text">
                      <div class="article__title"><a href="<?php echo htmlspecialchars($row->url)?>"><?php echo htmlspecialchars($row->name)?></a></div>
                      <?php if (strtotime($row->date) > 0) { ?>
                          <div class="article__date"><?php echo date('d', strtotime($row->date)) . ' ' . Text::$months[(int)date('m', strtotime($row->date))] . ' ' . date('Y', strtotime($row->date))?></div>
                      <?php } ?>
                      <div class="article__description">
                        <?php echo htmlspecialchars(Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                      </div>
                      <div class="article__more">
                        <a href="<?php echo htmlspecialchars($row->url)?>">
                          <?php echo SHOW_MORE?>
                        </a>
                      </div>
                    </div>
                  </div>
              <?php } ?>
            </div>
        <?php } ?>
      </div>
      <?php include Package::i()->resourcesDir . '/pages.inc.php'?>
      <?php if ($Pages->pages > 1) { ?>
          <ul class="pagination pull-right">
            <?php
            echo $outputNav(
                $Pages,
                array(
                    'pattern' => '<li><a href="' . HTTP::queryString('page={link}') . '">{text}</a></li>',
                    'pattern_active' => '<li class="active"><a>{text}</a></li>',
                    'ellipse' => '<li class="disabled"><a>...</a></li>'
                )
            );
            ?>
          </ul>
      <?php } ?>
  <?php } elseif ($localError) { ?>
      <div class="alert alert-danger">
        <?php
        switch ($localError) {
            case 'NO_SEARCH_QUERY':
                echo NO_SEARCH_QUERY;
                break;
            case 'SEARCH_QUERY_TOO_SHORT':
                echo sprintf(SEARCH_QUERY_TOO_SHORT, $config['min_length']);
                break;
            case 'NO_RESULTS_FOUND':
                echo NO_RESULTS_FOUND;
                break;
        }
        ?>
      </div>
  <?php } ?>
</div>
