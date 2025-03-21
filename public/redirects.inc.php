<?php
/**
 * Группа полей - таблица редиректов
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FieldSet;

/**
 * Отображает группу полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function(FieldSet $fieldSet) {

    $DATA = $fieldSet->Form->DATA;
    $repoData = [];
    foreach ((array)($DATA['redirect_id'] ?? []) as $i => $temp) {
        $repoRow = [
            'id' => $DATA['redirect_id'][$i] ?? null,
            'rx' => (int)($DATA['redirect_rx'][$i] ?? 0),
            'url_from' => $DATA['redirect_url_from'][$i] ?? '',
            'url_to' => $DATA['redirect_url_to'][$i] ?? '',
        ];
        $repoData[] = $repoRow;
    }
    ?>

    <raas-repo-table
      class="table table-striped table-condensed"
      :model-value="<?php echo htmlspecialchars(json_encode($repoData))?>"
      :defval="{ id: null, rx: 0, url_from: '', url_to: '' }"
      :columns-counter="3"
      :sortable="true"
    >
      <template #header>
        <component is="tr">
          <?php foreach ($fieldSet->children as $field) {
              if ($field->type != 'hidden') { ?>
                  <component is="th">
                    <?php
                    echo htmlspecialchars($field->caption);
                    if ($field->name == 'redirect_rx') { ?>
                        <raas-hint><?php echo ViewSub_Dev::i()->_('REGULAR_EXPRESSION')?></raas-hint>
                    <?php } ?>
                  </component>
              <?php }
          } ?>
          <component is="th"></component>
        </component>
      </template>
      <template #default="repo">
        <component is="td">
          <input type="hidden" name="redirect_id[]" :value="repo.modelValue.id || ''">
          <raas-field-checkbox
            name="redirect_rx[]"
            mask="0"
            :model-value="repo.modelValue.rx"
            @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, rx: $event })"
          ></raas-field-checkbox>
        </component>
        <component is="td">
          <raas-field-text
            name="redirect_url_from[]"
            :model-value="repo.modelValue.url_from"
            @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, url_from: $event })"
          ></raas-field-text>
        </component>
        <component is="td">
          <raas-field-text
            name="redirect_url_to[]"
            :model-value="repo.modelValue.url_to"
            @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, url_to: $event })"
          ></raas-field-text>
        </component>
      </template>
    </raas-repo-table>
    <?php
};
