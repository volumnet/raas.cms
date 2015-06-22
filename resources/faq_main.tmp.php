<?php if ($Set) { ?> 
    <div class="faq faq_main">
      <h2>{FAQ_NAME}</h2>
      <?php foreach ($Set as $row) { ?>
          <article class="article">
            <?php if ($row->image->id) { ?>
                <div class="article__image">
                  <a href="<?php echo htmlspecialchars($row->url)?>"><img src="/<?php echo $row->image->tnURL?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" /></a>
                </div>
            <?php } ?>
            <?php if (strlen($row->name) > 1 && !is_numeric($row->name)) { ?>
                <h3 class="article__title"><?php echo htmlspecialchars($row->name)?></h3>
            <?php } ?>
            <div class="article__text article__question">
              <label class="article__label">Вопрос:</label> <?php echo htmlspecialchars($row->description)?>
            </div>
            <?php if ($row->answer) { ?>
                <br />
                <div class="article__text article__answer">
                  <label class="article__label">Ответ:</label> <?php echo htmlspecialchars($row->answer)?>
                </div>
            <?php } ?>
          </article>
      <?php } ?>
    </div>
<?php } ?>