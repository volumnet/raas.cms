<?php
/**
 * Таблица с формой (редактирование типа материалов или формы)
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
      <?php if ($Item->children) { ?>
          <li>
            <a href="#subtypes" data-toggle="tab">
              <?php echo Application::i()->view->context->_('CHILD_TYPES')?>
            </a>
          </li>
      <?php } ?>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="common">
        <?php echo $Table->render()?>
      </div>
      <?php if ($Item->children) { ?>
          <div class="tab-pane" id="subtypes">
            <?php echo $childrenTable->render() ?>
          </div>
      <?php } ?>
    </div>
<?php } ?>
