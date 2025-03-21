<?php
/**
 * Вкладка "Права доступа"
 */
namespace RAAS\CMS;

use RAAS\FormTab;

/**
 * Отображает вкладку
 * @param FormTab $formTab Вкладка для отображения
 */
$_RAASForm_FormTab = function (FormTab $formTab) {
    $DATA = $formTab->Form->DATA;
    $getGroups = function (Group $node) use (&$getGroups) {
        static $level = 0;
        $result = [];
        foreach ($node->children as $child) {
            $row = ['value' => (int)$child->id, 'caption' => $child->name];
            if ($ch = $getGroups($child)) {
                $row['children'] = $ch;
            }
            $result[] = $row;
        }
        return $result;
    };
    $usersControllerAjax = Users\Controller_Ajax::i();
    $repoData = [];
    foreach ((array)($DATA['access_id'] ?? []) as $i => $temp) {
        $repoRow = [
            'id' => $DATA['access_id'][$i] ?? null,
            'allow' => $DATA['access_allow'][$i] ?? null,
            'to_type' => $DATA['access_to_type'][$i] ?? null,
            'user' => $usersControllerAjax->formatUser(new User($DATA['access_uid'][$i] ?? null)),
            'gid' => $DATA['access_gid'][$i] ?? null,
        ];
        $repoData[] = $repoRow;
    }
    ?>

    <raas-repo-table
      class="table table-striped table-condensed"
      :model-value="<?php echo htmlspecialchars(json_encode($repoData))?>"
      :defval="{ id: null, allow: 0, to_type: 0, user: null, gid: null }"
      :columns-counter="3"
      :sortable="true"
      v-slot="repo"
    >
      <component is="td">
        <input type="hidden" name="access_id[]" :value="repo.modelValue.id || ''">
        <raas-field-select
          name="access_allow[]"
          :source="<?php echo htmlspecialchars(json_encode($formTab->children['access_allow']->getArrayCopy()['children'] ?? null))?>"
          :model-value="repo.modelValue.allow"
          @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, allow: $event })"
        ></raas-field-select>
      </component>
      <component is="td">
        <raas-field-select
          name="access_to_type[]"
          :source="<?php echo htmlspecialchars(json_encode($formTab->children['access_to_type']->getArrayCopy()['children'] ?? null))?>"
          :model-value="repo.modelValue.to_type"
          @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, to_type: $event })"
        ></raas-field-select>
      </component>
      <component is="td">
        <raas-field-ajax
          v-if="repo.modelValue.to_type == <?php echo (int)CMSAccess::TO_USER?>"
          name="access_uid[]"
          :autocomplete-url="'ajax.php?p=cms&m=users&action=get_users&search_string='"
          :model-value="repo.modelValue.user"
          @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, user: $event })"
        ></raas-field-ajax>
        <input v-else type="hidden" name="access_uid[]" value="">

        <raas-field-select
          v-if="repo.modelValue.to_type == <?php echo (int)CMSAccess::TO_GROUP?>"
          name="access_gid[]"
          :source="<?php echo htmlspecialchars(json_encode($getGroups(new Group())))?>"
          :model-value="repo.modelValue.gid"
          @update:model-value="repo.emit('update:modelValue', {...repo.modelValue, gid: $event })"
        ></raas-field-select>
        <input v-else type="hidden" name="access_gid[]" value="">
      </component>
    </raas-repo-table>
<?php } ?>
