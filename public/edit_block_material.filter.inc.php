<?php
/**
 * Группа полей "Параметры фильтрации" в редактировании блока материалов
 */
namespace RAAS\CMS;

use RAAS\FieldSet;

/**
 * Отображение группы полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function (FieldSet $fieldSet) {
    $DATA = $fieldSet->Form->DATA;
    $CONTENT = $fieldSet->Form->meta['CONTENT'];
    $relationsSource = [];
    foreach (Block_Material::$filterRelations as $key => $val) {
        $relationsSource[] = ['value' => $key, 'caption' => constant('CMS\\' . $val)];
    }
    $repoData = [];
    foreach ((array)($DATA['filter_var'] ?? []) as $i => $temp) {
        $repoRow = [
            'var' => $DATA['filter_var'][$i] ?? '',
            'relation' => $DATA['filter_relation'][$i] ?? '',
            'field' => $DATA['filter_field'][$i] ?? '',
        ];
        $repoData[] = $repoRow;
    }
    ?>

    <fieldset>
      <legend><?php echo htmlspecialchars($fieldSet->caption)?></legend>
      <raas-repo-table
        class="table table-striped table-condensed"
        :model-value="<?php echo htmlspecialchars(json_encode($repoData))?>"
        :defval="{ var: '', relation: '', field: '' }"
        :columns="<?php echo htmlspecialchars(json_encode([\CMS\GET_VARIABLE, \CMS\RELATION, \CMS\MATERIAL_FIELD]))?>"
        :sortable="true"
        v-slot="repo"
      >
        <component is="td">
          <raas-field-text
            name="filter_var[]"
            :model-value="repo.modelValue.var"
            @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, var: $event })"
          ></raas-field-text>
        </component>
        <component is="td">
          <raas-field-select
            name="filter_relation[]"
            :source="<?php echo htmlspecialchars(json_encode($relationsSource))?>"
            :model-value="repo.modelValue.relation"
            @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, relation: $event })"
          ></raas-field-select>
        </component>
        <component is="td">
          <raas-field-select
            data-role="material-type-field"
            name="filter_field[]"
            :source="<?php echo htmlspecialchars(json_encode($CONTENT['fields'] ?? []))?>"
            :model-value="repo.modelValue.field"
            @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, field: $event })"
          ></raas-field-select>
        </component>
      </raas-repo-table>
    </fieldset>
<?php } ?>
