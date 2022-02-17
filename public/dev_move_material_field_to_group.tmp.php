<?php
/**
 * Перенос поля материалов
 */
namespace RAAS\CMS;

use SOME\HTTP;
use RAAS\Application;

?>
<p>
  <?php echo \CMS\CHOOSE_FIELDGROUP?>:
</p>
<ul>
  <?php foreach ($Parent->fieldGroups as $fieldGroup) { ?>
      <li>
        <a href="<?php echo HTTP::queryString('gid=' . (int)$fieldGroup->id)?>">
          <?php echo htmlspecialchars($fieldGroup->name ?: Application::i()->view->context->_('GENERAL'))?>
        </a>
      </li>
  <?php } ?>
</ul>
