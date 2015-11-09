<div class="materials faq">
  <?php if ($Item) { ?>
      <div class="article_opened">
        <?php if ($Item->image->id) { ?>
            <div class="article__image">
              <a href="/<?php echo $Item->image->fileURL?>">
                <img src="/<?php echo $Item->image->tnURL?>" alt="<?php echo htmlspecialchars($Item->image->name ?: $row->name)?>" /></a>
            </div>
        <?php } ?>
        <div class="article__date"><?php echo date('d', strtotime($row->post_date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->post_date))] . ' ' . date('Y', strtotime($row->post_date))?></div>
        <div class="article__text article__question">
          <label class="article__label">Вопрос:</label> <?php echo $Item->description?>
        </div>
        <?php if ($Item->answer) { ?>
            <div class="article__text article__answer">
              <label class="article__label">Ответ:</label> <?php echo htmlspecialchars($Item->answer)?>
            </div>
        <?php } ?>
      </div>
  <?php } elseif ($Set) { ?> 
      <?php foreach ($Set as $row) { ?>
          <div class="article">
            <?php if ($row->image->id) { ?>
                <div class="article__image">
                  <a href="<?php echo $row->url?>">
                    <img src="/<?php echo $row->image->tnURL?>" alt="<?php echo htmlspecialchars($row->image->name ?: $row->name)?>" /></a>
                </div>
            <?php } ?>
            <div class="article__date"><?php echo date('d', strtotime($row->post_date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->post_date))] . ' ' . date('Y', strtotime($row->post_date))?></div>
            <?php if (strlen($row->name) > 1 && !is_numeric($row->name)) { ?>
                <div class="h3 article__title"><a href="<?php echo $row->url?>"><?php echo htmlspecialchars($row->name)?></a></div>
            <?php } ?>
            <div class="article__text article__question">
              <label class="article__label">Вопрос:</label> <?php echo $row->description?>
            </div>
            <?php if ($row->answer) { ?>
                <div class="article__text article__answer">
                  <label class="article__label">Ответ:</label> <?php echo htmlspecialchars($row->answer)?>
                </div>
            <?php } ?>
          </div>
      <?php } ?>
      <?php include \RAAS\CMS\Package::i()->resourcesDir . '/pages.inc.php'?>
      <?php if ($Pages->pages > 1) { ?>
          <ul class="pagination pull-right">
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
      <?php } ?>
  <?php } ?>
</div>