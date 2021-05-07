<?php
/**
 * Группа полей "Дополнительные параметры" в редактировании блока материалов
 */
namespace RAAS\CMS;

use RAAS\FieldSet;

/**
 * Отображает группу полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function (FieldSet $fieldSet) use (&$_RAASForm_Control)
{
    $DATA = $fieldSet->Form->DATA;
    $err = (bool)array_filter(
        (array)$field->Form->localError,
        function ($x) use ($field) {
            return $x['value'] == $field->name;
        }
    );
    ?>
    <div class="control-group<?php echo $err ? ' error' : ''?>">
      <label class="control-label">
        <?php echo htmlspecialchars(
            $fieldSet->caption ?
            $fieldSet->caption . ':' :
            ''
        )?>
      </label>
      <div class="controls">
        <table data-role="raas-repo-block">
          <tbody data-role="raas-repo-container">
            <?php foreach ((array)$DATA['params_name'] as $i => $temp) { ?>
                <tr data-role="raas-repo-element">
                  <td>
                    <input type="text" name="params_name[]" value="<?php echo htmlspecialchars($DATA['params_name'][$i])?>" class="span2" />
                  </td>
                  <td>
                    =
                  </td>
                  <td>
                    <input type="text" name="params_value[]" value="<?php echo htmlspecialchars($DATA['params_value'][$i])?>" class="span2" />
                  </td>
                  <td>
                    <a href="#" class="close" data-role="raas-repo-del">
                      &times;
                    </a>
                  </td>
                </tr>
            <?php } ?>
            <tbody>
              <tr data-role="raas-repo">
                <td>
                  <input type="text" name="params_name[]" disabled="disabled" class="span2" />
                </td>
                <td>
                  =
                </td>
                <td>
                  <input type="text" name="params_value[]" disabled="disabled" class="span2" />
                </td>
                <td>
                  <a href="#" class="close" data-role="raas-repo-del">
                    &times;
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <a href="#" data-role="raas-repo-add" title="<?php echo ADD?>">
                    <span class="icon icon-plus"></span>
                  </a>
                </td>
                <td></td>
                <td></td>
                <td>
                </td>
              </tr>
            </tbody>
          </tbody>
        </table>

      </div>
    </div>
<?php } ?>
