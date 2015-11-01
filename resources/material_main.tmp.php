<?php if ($Set) { ?>
    <div class="{BLOCK_NAME}">
      <div class="h2">{MATERIAL_NAME}</div>
      <?php foreach ($Set as $row) { ?>
          <div class="article">
            <?php if ($row->visImages) { ?>
                <div class="article__image">
                  <a href="<?php echo $row->url?>">
                    <img src="/<?php echo htmlspecialchars(addslashes($row->visImages[0]->tnURL))?>" alt="<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>" /></a>
                </div>
            <?php } ?>
            <div class="article__text">
              <div class="h3 article__title"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></div>
              <?php if (strtotime($row->date) > 0) { ?>
                  <div class="article__date"><?php echo date('d', strtotime($row->date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->date))] . ' ' . date('Y', strtotime($row->date))?></div>
              <?php } ?>
              <?php echo htmlspecialchars($row->brief ?: \SOME\Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...'))?>
              <div class="article__read-more"><a href="<?php echo $row->url?>">Подробней…</a></div>
            </div>
          </div>
      <?php } ?>
    </div>
<?php } ?>