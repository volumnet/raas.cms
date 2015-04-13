<div class="photos">
  <?php if ($Item) { ?>
      <article class="article_opened">
        <div class="article__text"><?php echo $Item->description?></div>
        <?php if (count($Item->visImages) > 0) { ?>
            <div class="article__images row">
              <?php for ($i = 0; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>
                  <div class="col-sm-4 col-md-3 col-xs-6">
                    <a href="/<?php echo htmlspecialchars(addslashes($row->fileURL))?>" rel="prettyPhoto[g<?php echo (int)$Item->id?>]" class="article__images__image">
                      <img src="/<?php echo htmlspecialchars(addslashes($row->tnURL))?>" alt="<?php echo htmlspecialchars($row->name)?>" /></a>
                  </div>
              <?php } ?>
            </div>
        <?php } ?>
      </article>
  <?php } elseif ($Set) { ?>
      <?php foreach ($Set as $row) { ?>
          <article class="article">
            <?php if (count($row->visImages) > 0) { ?>
                <h2 class="article__title"><a href="<?php echo htmlspecialchars($row->url)?>"><?php echo htmlspecialchars($row->name)?></a></h2>
                <div class="article__images row">
                  <?php for ($i = 0; $i < count($row->visImages); $i++) { $row2 = $row->visImages[$i]; ?>
                      <div class="col-sm-4 col-md-3 col-xs-6">
                        <a href="/<?php echo htmlspecialchars(addslashes($row2->fileURL))?>" rel="prettyPhoto[gallery<?php echo (int)$row->id?>]" class="article__images__image">
                          <img src="/<?php echo htmlspecialchars(addslashes($row2->tnURL))?>" alt="<?php echo htmlspecialchars($row2->name)?>" /></a>
                      </div>
                  <?php } ?>
                </div>
            <?php } ?>
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
</div>