<?php if ($Item) { ?>
    <article class="article article_opened">
      <?php if (strtotime($Item->date) > 0) { ?>
          <p class="article__date"><?php echo date('d', strtotime($Item->date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($Item->date))] . ' ' . date('Y', strtotime($Item->date))?></p>
      <?php } ?>
      <?php if ($Item->visImages) { ?>
          <div class="article__image">
            <a href="/<?php echo $Item->visImages[0]->fileURL?>">
              <img src="/<?php echo $Item->visImages[0]->tnURL?>" alt="<?php echo htmlspecialchars($Item->visImages[0]->name ?: $row->name)?>" /></a>
          </div>
      <?php } ?>
      <div class="article__text"><?php echo $Item->description?></div>
      <?php if (count($Item->visImages) > 1) { ?>
          <div class="article__images row">
            <?php for ($i = 1; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>
                <div class="col-sm-4 col-md-3 col-xs-6">
                  <a href="/<?php echo htmlspecialchars(addslashes($row->fileURL))?>">
                    <img src="/<?php echo htmlspecialchars(addslashes($row->tnURL))?>" alt="<?php echo htmlspecialchars($row->name)?>" /></a>
                </div>
            <?php } ?>
          </div>
      <?php } ?>
    </article>
<?php } elseif ($Set) { ?>
    <?php foreach ($Set as $row) { ?>
        <article class="article">
          <h3 class="article__title"><a href="<?php echo $Page->url?>?id=<?php echo (int)$row->id?>"><?php echo htmlspecialchars($row->name)?></a></h3>
          <?php if (strtotime($row->date) > 0) { ?>
              <p class="article__date"><?php echo date('d', strtotime($row->date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->date))] . ' ' . date('Y', strtotime($row->date))?></p>
          <?php } ?>
          <?php if ($row->visImages) { ?>
              <div class="article__image">
                <a href="<?php echo $Page->url?>?id=<?php echo (int)$row->id?>">
                  <img src="/<?php echo htmlspecialchars(addslashes($row->visImages[0]->tnURL))?>" alt="<?php echo htmlspecialchars($row->name)?>" /></a>
              </div>
          <?php } ?>
          <div class="article__text">
            <?php echo htmlspecialchars(\SOME\Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
            <p><a href="<?php echo $Page->url?>?id=<?php echo (int)$row->id?>" class="article__read-more">Подробней…</a></p>
          </div>
				</article>
    <?php } ?>
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
<?php } ?>