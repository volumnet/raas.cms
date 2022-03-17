<?php
/**
 * Перенос поля материалов
 */
namespace RAAS\CMS;

use SOME\HTTP;
use RAAS\Application;

?>
<p>
  <?php echo \CMS\CHOOSE_FIELD_TO_MOVE_VALUES?>:
</p>
<ul>
  <?php foreach ($Set as $field) { ?>
      <li>
        <a href="<?php echo HTTP::queryString('pid=' . (int)$field->id)?>">
          <?php echo htmlspecialchars($field->name)?>
        </a>
      </li>
  <?php } ?>
</ul>
