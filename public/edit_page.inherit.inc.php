<?php
/**
 * Наследуемые поля в редактировании страницы
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FieldSet;

/**
 * Отображает группу полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function (FieldSet $fieldSet) {
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
    if (in_array($Field->type, ['htmlarea', 'codearea'])) { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <?php if ($Field->caption) { ?>
              <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>">
                <?php echo htmlspecialchars($Field->caption)?>:
              </label>
              <div class="controls clearfix">
                <label class="checkbox">
                  <?php echo $inheritField->render() . htmlspecialchars($inheritField->caption)?>
                </label>
                <?php if ($dataHint) { ?>
                    <raas-hint><?php echo htmlspecialchars($dataHint)?></raas-hint>
                <?php } ?>
              </div>
          <?php } ?>
        </div>
        <div class="control-group control-group_full"><?php echo $Field->render()?></div>
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
              <?php echo $Field->render()?>
            </div>
            <div class="span2">
              <label class="checkbox">
                <?php echo $inheritField->render() . htmlspecialchars($inheritField->caption)?>
              </label>
            </div>
          </div>
        </div>
        <div class="control-group<?php echo $err2 ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>@confirm">
            <?php echo PASSWORD_CONFIRM?>:
          </label>
          <div class="controls">
            <?php echo $Field->render(true)?>
          </div>
        </div>
    <?php } elseif ($Field->type == 'checkbox' && !$Field->multiple) { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <div class="controls">
            <div class="">
              <div class="span5" style="margin-left: 0px">
                <label class="checkbox"<?php echo $Field->{'data-hint'} ? ' style="width: 174px;"' : ''?>>
                  <?php echo $Field->render()?>
                  <?php echo htmlspecialchars($Field->caption)?>
                </label>
              </div>
              <div class="span2">
                <label class="checkbox">
                  <?php echo $inheritField->render() . htmlspecialchars($inheritField->caption)?>
                </label>
                <?php if ($dataHint) { ?>
                    <raas-hint><?php echo htmlspecialchars($dataHint)?></raas-hint>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
    <?php } else { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>">
            <?php echo htmlspecialchars($Field->caption ? $Field->caption . ':' : '')?>
          </label>
          <div class="controls">
            <div class="">
              <div class="span5" style="margin-left: 0px">
                <?php echo $Field->render()?>
              </div>
              <div class="span2">
                <label class="checkbox">
                  <?php echo $inheritField->render() . htmlspecialchars($inheritField->caption)?>
                </label>
                <?php if ($dataHint) { ?>
                    <raas-hint><?php echo htmlspecialchars($dataHint)?></raas-hint>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
    <?php }
};
