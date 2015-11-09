<?php if ($Set) { ?> 
    <div class="faq faq_main">
      <div class="h2">{FAQ_NAME}</div>
      <?php foreach ($Set as $row) { ?>
          <div class="article">
            <?php if ($row->image->id) { ?>
                <div class="article__image">
                  <a href="<?php echo htmlspecialchars($row->url)?>"><img src="/<?php echo $row->image->tnURL?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" /></a>
                </div>
            <?php } ?>
            <?php if (strlen($row->name) > 1 && !is_numeric($row->name)) { ?>
                <div class="h3 article__title"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></div>
            <?php } ?>
            <div class="article__text article__question">
              <label class="article__label">Вопрос:</label> <?php echo $row->description?>
            </div>
            <?php if ($row->answer) { ?>
                <br />
                <div class="article__text article__answer">
                  <label class="article__label">Ответ:</label> <?php echo htmlspecialchars($row->answer)?>
                </div>
            <?php } ?>
          </div>
      <?php } ?>
    </div>
<?php } ?>