<?php
/**
 * Виджет постраничной разбивки
 * @param Pages $pages Постраничная разбивка
 */
namespace RAAS\CMS;

use SOME\HTTP;
use SOME\Pages;

include Package::i()->resourcesDir . '/pages.inc.php';

if ($Pages->pages > 1) { ?>
    <ul class="pagination pull-right">
      <?php
      echo $outputNav(
          $Pages,
          [
              'pattern' => '<li><a href="' . HTTP::queryString('page={link}') . '">{text}</a></li>',
              'pattern_active' => '<li class="active"><span>{text}</span></li>',
              'ellipse' => '<li class="disabled"><a>...</a></li>'
          ]
      );
      ?>
    </ul>
<?php } ?>
