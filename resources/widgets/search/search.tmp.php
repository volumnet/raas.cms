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

?>
<div class="search">
  <div class="search__title">
    <?php echo sprintf(SEARCH_RESULTS_FOR_QUERY, htmlspecialchars($search_string))?>
  </div>
  <div class="search__inner">
    <?php if ($Set) { ?>
        <div class="search__list">
          <div class="search-list">
            <?php foreach ($Set as $row) { ?>
                <div class="search-list__item">
                  <?php if ($row instanceof Page) { ?>
                      <div class="search-item">
                        <div class="search-item__image">
                          <a href="<?php echo htmlspecialchars($row->url)?>"<?php echo (!$row->image->id ? ' class="no-image"' : '')?>>
                            <?php if ($row->image->id) { ?>
                                <img src="/<?php echo htmlspecialchars($row->image->tnURL)?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" />
                            <?php } ?>
                          </a>
                        </div>
                        <div class="search-item__text">
                          <div class="search-item__title">
                            <a href="<?php echo htmlspecialchars($row->url)?>">
                              <?php echo htmlspecialchars($row->name)?>
                            </a>
                          </div>
                          <div class="search-item__description">
                            <?php echo htmlspecialchars(Text::cuttext(html_entity_decode(strip_tags($row->_description_), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                          </div>
                          <div class="search-item__more">
                            <a href="<?php echo htmlspecialchars($row->url)?>">
                              <?php echo SHOW_MORE?>
                            </a>
                          </div>
                        </div>
                      </div>
                  <?php } elseif ($row instanceof Material) { ?>
                      <div class="search-item">
                        <div class="search-item__image">
                          <a href="<?php echo htmlspecialchars($row->url)?>"<?php echo (!$row->visImages ? ' class="no-image"' : '')?>>
                            <?php if ($row->visImages) { ?>
                                <img src="/<?php echo htmlspecialchars($row->visImages[0]->tnURL)?>" alt="<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>" />
                            <?php } ?>
                          </a>
                        </div>
                        <div class="search-item__text">
                          <div class="search-item__title"><a href="<?php echo htmlspecialchars($row->url)?>"><?php echo htmlspecialchars($row->name)?></a></div>
                          <?php if (($t = strtotime($row->date)) > 0) { ?>
                              <div class="search-item__date"><?php echo date('d', $t) . ' ' . Text::$months[(int)date('m', $t)] . ' ' . date('Y', $t)?></div>
                          <?php } ?>
                          <div class="search-item__description">
                            <?php echo htmlspecialchars(Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
                          </div>
                          <div class="search-item__more">
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
        </div>
        <?php if ($Pages->pages > 1) { ?>
            <div class="{{MATERIAL_TYPE_CSS_CLASSNAME}}__pagination">
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
