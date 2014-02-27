<?php if ($Item) { ?>
    <h1><?php echo htmlspecialchars($Item->name)?></h1>
    <article class="article">
      <?php if (strtotime($Item->date) > 0) { ?>
          <p class="date"><small><?php echo date('d', strtotime($Item->date)) . ' ' . \SOME\Text::$months[date('m', strtotime($Item->date))] . ' ' . date('Y', strtotime($Item->date))?></small></p>
      <?php } ?>
      <?php if ($Item->visImages) { ?>
          <a href="/<?php echo $Item->visImages[0]->fileURL?>" class="context-image thumbnail zoom-in pull-left">
            <img src="/<?php echo $Item->visImages[0]->tnURL?>" /></a>
      <?php } ?>
      <div class="text"><?php echo $Item->description?></div>
      <?php if (count($Item->visImages) > 1) { ?>
          <div class="images row">
            <?php for ($i = 1; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>
                <div class="col-sm-2">
                  <a href="/<?php echo htmlspecialchars(addslashes($row->fileURL))?>" class="thumbnail zoom-in"><img src="/<?php echo htmlspecialchars(addslashes($row->tnURL))?>" /></a>
                </div>
            <?php } ?>
          </div>
      <?php } ?>
    </article>
<?php } elseif ($Set) { ?>
    <?php foreach ($Set as $row) { ?>
        <article class="article">
          <h2 class="article-title text-normal"><?php echo htmlspecialchars($row->name)?></h2>
          <?php if (strtotime($row->date) > 0) { ?>
              <p class="date"><small><?php echo date('d', strtotime($row->date)) . ' ' . \SOME\Text::$months[date('m', strtotime($row->date))] . ' ' . date('Y', strtotime($row->date))?></small></p>
          <?php } ?>
          <?php if ($row->visImages) { ?>
              <a href="<?php echo $Page->url?>?id=<?php echo (int)$row->id?>" class="context-image thumbnail w130 zoom-in pull-left">
                <img src="/<?php echo htmlspecialchars(addslashes($row->visImages[0]->tnURL))?>" /></a>
          <?php } ?>
          <div class="text">
            <?php echo htmlspecialchars($row->brief)?>
            <p><a href="<?php echo $Page->url?>?id=<?php echo (int)$row->id?>" class="read-more">Подробней…</a></p>
          </div>
				</article>
    <?php } ?>
    <?php include \RAAS\CMS\Package::i()->resourcesDir . '/pages.inc.php'?>
    <?php if ($Pages->pages > 1) { ?>
        <div class="pagination pagination-pull-right">
          <ul>
            <?php 
            echo $outputNav(
                $Pages, 
                array(
                    'pattern' => '<li><a href="' . \SOME\HTTP::queryString('page={link}') . '">{text}</a></li>', 
                    'pattern_active' => '<li class="active"><span>{text}</span></li>',
                    'ellipse' => '<li class="disabled"><a>...</a></li>'
                )
            );
            ?>
          </ul>
        </div>
    <?php } ?>
<?php } ?>