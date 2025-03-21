<?php
/**
 * Группа полей "Параметры сортировки" в редактировании блока материалов
 */
namespace RAAS\CMS;

use RAAS\FieldSet;

/**
 * Отображает группу полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function (FieldSet $fieldSet) {
    $DATA = $fieldSet->Form->DATA;
    $CONTENT = $fieldSet->Form->meta['CONTENT'];
    $relationsSource = [];
    foreach (Block_Material::$orderRelations as $key => $val) {
        $relationsSource[] = ['value' => $key, 'caption' => constant('CMS\\' . $val)];
    }
    $repoData = [];
    foreach ((array)($DATA['sort_var'] ?? []) as $i => $temp) {
        $repoRow = [
            'var' => $DATA['sort_var'][$i] ?? '',
            'field' => $DATA['sort_field'][$i] ?? '',
            'relation' => $DATA['sort_relation'][$i] ?? '',
        ];
        $repoData[] = $repoRow;
    }
    ?>
    <fieldset>
      <legend><?php echo htmlspecialchars($fieldSet->caption)?></legend>
      <raas-repo-table
        class="table table-striped table-condensed"
        :model-value="<?php echo htmlspecialchars(json_encode($repoData))?>"
        :defval="{ var: '', field: '', relation: '' }"
        :columns-counter="3"
        :sortable="true"
      >
        <template #header>
          <component is="tr">
            <component is="th"><?php echo \CMS\VARIABLE_VALUE?></component>
            <component is="th"><?php echo \CMS\MATERIAL_FIELD?></component>
            <component is="th"><?php echo \CMS\SORTING_ORDER?></component>
            <component is="th"></component>
          </component>
          <component is="tr">
            <component is="th"><?php echo \CMS\GET_VARIABLE?></component>
            <component is="td"><?php echo $fieldSet->children['sort_var_name']->render()?></component>
            <component is="td"><?php echo $fieldSet->children['order_var_name']->render()?></component>
            <component is="td"></component>
          </component>
          <component is="tr">
            <component is="th"><?php echo \CMS\DEFAULT_SORTING?></component>
            <component is="td"><?php echo $fieldSet->children['sort_field_default']->render()?></component>
            <component is="td"><?php echo $fieldSet->children['sort_order_default']->render()?></component>
            <component is="td"></component>
          </component>
        </template>
        <template #default="repo">
          <component is="td">
            <raas-field-text
              name="sort_var[]"
              :model-value="repo.modelValue.var"
              @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, var: $event })"
            ></raas-field-text>
          </component>
          <component is="td">
            <raas-field-select
              data-role="material-type-field"
              name="sort_field[]"
              :source="<?php echo htmlspecialchars(json_encode($CONTENT['fields'] ?? []))?>"
              :model-value="repo.modelValue.field"
              @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, field: $event })"
            ></raas-field-select>
          </component>
          <component is="td">
            <raas-field-select
              name="sort_relation[]"
              :source="<?php echo htmlspecialchars(json_encode($relationsSource))?>"
              :model-value="repo.modelValue.relation"
              @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, relation: $event })"
            ></raas-field-select>
          </component>
        </template>
      </raas-repo-table>
    </fieldset>
<?php } ?>
