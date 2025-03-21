<?php
/**
 * Форма редактирования формы
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Form as RAASForm;
use RAAS\FormTab;

echo $Form->renderFull();

if ($Item->id) { ?>
    <ul class="nav nav-tabs" id="myTab">
      <li class="active">
        <a href="#common" data-toggle="tab">
          <?php echo Application::i()->view->context->_('FIELDS')?>
        </a>
      </li>
      <?php if ($Form->meta['blocksTable'] ?? null) { ?>
          <li>
            <a href="#blocks" data-toggle="tab">
              <?php echo htmlspecialchars($Form->meta['blocksTable']->caption)?>
            </a>
          </li>
      <?php } ?>
      <?php if ($Form->meta['cartTypesTable'] ?? null) { ?>
          <li>
            <a href="#cartTypes" data-toggle="tab">
              <?php echo htmlspecialchars($Form->meta['cartTypesTable']->caption)?>
            </a>
          </li>
      <?php } ?>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="common">
        <?php echo $Table->render() ?>
      </div>
      <?php if ($Form->meta['blocksTable'] ?? null) { ?>
          <div class="tab-pane" id="blocks">
            <?php echo $Form->meta['blocksTable']->render(); ?>
          </div>
      <?php }
      if ($Form->meta['cartTypesTable'] ?? null) { ?>
          <div class="tab-pane" id="cartTypes">
            <?php echo $Form->meta['cartTypesTable']->render(); ?>
          </div>
      <?php } ?>
    </div>
<?php } ?>
