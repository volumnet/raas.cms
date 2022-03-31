<?php
/**
 * Наследуемые поля в редактировании страницы
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FieldSet;

include Application::i()->context->view->tmp('field.inc.php');

/**
 * Отображает группу полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function (FieldSet $fieldSet) use (
    &$_RAASForm_Form_Tabbed,
    &$_RAASForm_Form_Plain,
    &$_RAASForm_Control,
    &$_RAASForm_Options
) {
    $fields = array_values((array)$fieldSet->children);
    $Field = $fields[0];
    $inheritField = $fields[1];
    $dataHint = $Field->{'data-hint'};
    unset($Field->{'data-hint'});
    $err = (bool)array_filter(
        (array)$Field->Form->localError,
        function ($x) use ($Field) {
            return $x['value'] == $Field->name;
        }
    );
    include Application::i()->view->tmp('form.inc.php');
    include Application::i()->context->view->tmp('field.inc.php');
    if ($Field->template) {
        include Application::i()->view->context->tmp($Field->template);
    }
    if (in_array($Field->type, ['htmlarea', 'codearea'])) { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <?php if ($Field->caption) { ?>
              <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>">
                <?php echo htmlspecialchars($Field->caption)?>:
              </label>
              <div class="controls clearfix">
                <label class="checkbox">
                  <?php echo $_RAASForm_Control($inheritField, false) .
                             htmlspecialchars($inheritField->caption)?>
                </label>
                <?php if ($dataHint) { ?>
                    <a class="btn" href="#" rel="popover" data-content="<?php echo htmlspecialchars($dataHint)?>">
                      <i class="icon-question-sign"></i>
                    </a>
                <?php } ?>
              </div>
          <?php } ?>
        </div>
        <div class="control-group control-group_full"><?php echo $_RAASForm_Control($Field)?></div>
    <?php } elseif (($Field->type == 'password') && $Field->confirm) {
        $err2 = (bool)array_filter(
            (array)$Field->Form->localError,
            function ($x) use ($Field) {
                return $x['value'] == $Field->name . '@confirm';
            }
        );
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>">
            <?php echo htmlspecialchars($Field->caption)?>:
          </label>
          <div class="">
            <div class="span5" style="margin-left: 0px">
              <?php echo $_RAASForm_Control($Field, false)?>
            </div>
            <div class="span2">
              <label class="checkbox">
                <?php echo $_RAASForm_Control($inheritField, false) .
                           htmlspecialchars($inheritField->caption)?>
              </label>
            </div>
          </div>
        </div>
        <div class="control-group<?php echo $err2 ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>@confirm">
            <?php echo PASSWORD_CONFIRM?>:
          </label>
          <div class="controls">
            <?php echo $_RAASForm_Control($Field, true)?>
          </div>
        </div>
    <?php } elseif ($Field->type == 'checkbox' && !$Field->multiple) { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <div class="controls">
            <div class="">
              <div class="span5" style="margin-left: 0px">
                <label class="checkbox"<?php echo $Field->{'data-hint'} ? ' style="width: 174px;"' : ''?>>
                  <?php echo $_RAASForm_Control($Field, false)?>
                  <?php echo htmlspecialchars($Field->caption)?>
                </label>
              </div>
              <div class="span2">
                <label class="checkbox">
                  <?php echo $_RAASForm_Control($inheritField, false) .
                             htmlspecialchars($inheritField->caption)?>
                </label>
                <?php if ($dataHint) { ?>
                    <a class="btn" href="#" rel="popover" data-content="<?php echo htmlspecialchars($dataHint)?>">
                      <i class="icon-question-sign"></i>
                    </a>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
    <?php } else { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>">
            <?php echo htmlspecialchars(
                $Field->caption ?
                $Field->caption . ':' :
                ''
            )?>
          </label>
          <div class="controls">
            <div class="">
              <div class="span5" style="margin-left: 0px">
                <?php echo $_RAASForm_Control($Field, false)?>
              </div>
              <div class="span2">
                <label class="checkbox">
                  <?php echo $_RAASForm_Control($inheritField, false) .
                             htmlspecialchars($inheritField->caption)?>
                </label>
                <?php if ($dataHint) { ?>
                    <a class="btn" href="#" rel="popover" data-content="<?php echo htmlspecialchars($dataHint)?>">
                      <i class="icon-question-sign"></i>
                    </a>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
    <?php }
};
