<div class="materials faq">
  <?php if ($Set) { ?>
      <?php foreach ($Set as $row) { ?>
          <article class="article">
            <p class="article__date"><?php echo date('d', strtotime($row->post_date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->post_date))] . ' ' . date('Y', strtotime($row->post_date))?></p>
            <?php if (strlen($row->name) > 1 && !is_numeric($row->name)) { ?>
                <h3 class="article__title"><?php echo htmlspecialchars($row->name)?></h3>
            <?php } ?>
            <div class="article__text">
              <strong>Вопрос:</strong> <?php echo htmlspecialchars($row->description)?>
            </div>
            <?php if ($row->answer) { ?>
                <br />
                <div class="article__text faq__answer">
                  <strong>Ответ:</strong> <?php echo htmlspecialchars($row->answer)?>
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
                    'pattern_active' => '<li class="active"><span>{text}</span></li>',
                    'ellipse' => '<li class="disabled"><a>...</a></li>'
                )
            );
            ?>
          </ul>
      <?php } ?>
  <?php } ?>
</div>