<?php if ($Set) { ?>
    <?php foreach ($Set as $row) { ?>
        <article class="article faq">
          <p class="faqheader"><small><?php echo date('d', strtotime($row->post_date)) . ' ' . \SOME\Text::$months[(int)date('m', strtotime($row->post_date))] . ' ' . date('Y', strtotime($row->post_date))?></small><?php echo (strlen($row->name) > 1 && !is_numeric($row->name)) ? ', ' . htmlspecialchars($row->name) : ''?></p>
          <div class="text">
            <strong>Вопрос:</strong> <?php echo htmlspecialchars($row->description)?>
          </div>
          <?php if ($row->answer) { ?>
              <br />
              <div class="text">
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