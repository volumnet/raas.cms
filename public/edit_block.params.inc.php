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
$_RAASForm_FieldSet = function (FieldSet $fieldSet) {
    $DATA = $fieldSet->Form->DATA;
    $err = (bool)array_filter(
        (array)(isset($field->Form->localError) ? $field->Form->localError : []),
        function ($x) use ($fieldSet) {
            return $x['value'] == $fieldSet->name;
        }
    );
    $repoData = [];
    foreach ((array)($DATA['params_name'] ?? []) as $i => $temp) {
        $repoRow = [
            'name' => $DATA['params_name'][$i] ?? '',
            'value' => $DATA['params_value'][$i] ?? '',
        ];
        $repoData[] = $repoRow;
    }
    ?>
    <div class="control-group<?php echo $err ? ' error' : ''?>">
      <label class="control-label">
        <?php echo htmlspecialchars($fieldSet->caption ? $fieldSet->caption . ':' : '')?>
      </label>
      <div class="controls">
        <raas-repo-table
          :model-value="<?php echo htmlspecialchars(json_encode($repoData))?>"
          :defval="{ name: '', value: '' }"
          :columns-counter="3"
          :sortable="true"
        >
          <template #default="repo">
           <component is="td">
              <raas-field-text
                name="params_name[]"
                :model-value="repo.modelValue.name"
                @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, name: $event })"
              ></raas-field-text>
            </component>
            <component is="td">
              =
            </component>
            <component is="td">
              <raas-field-text
                name="params_value[]"
                :model-value="repo.modelValue.value"
                @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, value: $event })"
              ></raas-field-text>
            </component>
          </template>
        </raas-repo-table>
      </div>
    </div>
<?php } ?>
