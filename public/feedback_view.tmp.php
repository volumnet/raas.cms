<?php
/**
 * Просмотр сообщения обратной связи
 */
namespace RAAS\CMS;

use RAAS\FormTab;

include $VIEW->tmp('/form.inc.php')
?>
<form<?php echo $_RAASForm_Attrs($Form)?>>
  <?php
  if (array_filter((array)$Form->children, function ($x) {
      return $x instanceof FormTab;
  })) {
      $_RAASForm_Form_Tabbed($Form->children);
  } else {
      $_RAASForm_Form_Plain($Form->children);
  }
  ?>
</form>
