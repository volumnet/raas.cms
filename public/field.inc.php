<?php
/**
 * Отображение кастомных полей - набор функций
 */
namespace RAAS\CMS;

use RAAS\Attachment;
use RAAS\Field as RAASField;

/**
 * Отображение элемента управления
 * @param RAASField $field Поле для отображения
 * @param bool $confirm Добавить подтверждение пароля для элемента пароля
 */
$_RAASForm_Control = function (RAASField $field, $confirm = true) {
    $attrs = [];
    // 2025-02-27, AVS: здесь используются только переопределенные типы (field.class.php:71), остальные указывать не нужно
    switch ($field->type) {
        case 'material':
            $originalField = $field->meta['CustomField'];

            $attrs = [':field-id' => $originalField->id];

            if ($field->multiple) {
                $set = (array)($field->Form->DATA[$field->name] ?? []);
                $set = array_map(function ($val) {
                    if (is_scalar($val) || is_null($val)) {
                        $val = new Material($val);
                    }
                    return $val;
                }, $set);
                $data = array_map(fn($val) => Controller_Ajax::i()->formatMaterial($val), $set);
                $attrs['multiple'] = null; // Чтобы перекрыть стандартный атрибут multiple="1"
                $attrs[':multiple'] = 'true';
                $attrs[':model-value'] = 'repo.modelValue';
                $attrs['@update:model-value'] = 'repo.emit(\'update:modelValue\', repo.modelValue = $event)';
                ?>
                <raas-repo
                  :model-value="<?php echo htmlspecialchars(json_encode($data))?>"
                  :defval="null"
                  :sortable="true"
                  :required="<?php echo htmlspecialchars(json_encode((bool)$field->required))?>"
                  v-slot="repo"
                >
                  <raas-cms-field-material<?php echo $field->getAttrsString($attrs)?>></raas-cms-field-material>
                </raas-repo>
            <?php } else {
                // 2015-06-08, AVS: В выражении (int)$field->Form->DATA[$field->name] убрал (int),
                // т.к. $val типа материал
                $val = $field->Form->DATA[$field->name] ?? null;
                if (is_scalar($val) || is_null($val)) {
                    $val = new Material($val);
                }
                $data = Controller_Ajax::i()->formatMaterial($val);
                $attrs[':model-value'] = json_encode($data);
                ?>
                <raas-cms-field-material<?php echo $field->getAttrsString($attrs)?>></raas-cms-field-material>
            <?php }
            break;
        case 'image':
        case 'file':
            $data = [];
            if (!$field->multiple) {
                $attachmentsArr = [$field->Form->DATA[$field->name] ?? null];
                $attachmentsIdsArr = [$field->Form->DATA[$field->name . '@attachment'] ?? null];
                $visArr = [$field->Form->DATA[$field->name . '@vis'] ?? null];
                $nameArr = [$field->Form->DATA[$field->name . '@name'] ?? null];
                $descriptionArr = [$field->Form->DATA[$field->name . '@description'] ?? null];
            } else {
                $attachmentsArr = (array)($field->Form->DATA[$field->name] ?? []);
                $attachmentsIdsArr = (array)($field->Form->DATA[$field->name . '@attachment'] ?? []);
                $visArr = (array)($field->Form->DATA[$field->name . '@vis'] ?? []);
                $nameArr = (array)($field->Form->DATA[$field->name . '@name'] ?? []);
                $descriptionArr = (array)($field->Form->DATA[$field->name . '@description'] ?? []);
            }

            $count = count($field->Form->isPost ? $attachmentsIdsArr : $attachmentsArr);

            for ($i = 0; $i < $count; $i++) {
                if ($field->Form->isPost) {
                    $att = new Attachment($attachmentsIdsArr[$i] ?? 0);
                    $dataRow = [
                        'attachment' => (int)($attachmentsIdsArr[$i] ?? 0),
                        'vis' => (int)($visArr[$i] ?? 1),
                        'name' => trim((string)($nameArr[$i] ?? '')),
                        'description' => trim((string)($descriptionArr[$i] ?? '')),
                    ];
                } else {
                    $att = $attachmentsArr[$i] ?? new Attachment();
                    $dataRow = [
                        'attachment' => (int)$att->id,
                        'vis' => (int)($att->id ? $att->vis : 1),
                        'name' => trim((string)$att->name),
                        'description' => trim((string)$att->description),
                    ];
                }
                $dataRow['file'] = null;
                $dataRow['upload'] = null;

                if ($att && $att->id) {
                    $dataRow['file'] = ['fileURL' => '/' . $att->fileURL];
                    if ($field->type == 'image') {
                        $dataRow['file']['tnURL'] = '/' . $att->tnURL;
                    }
                }

                $data[] = $dataRow;
            }

            if (!$field->multiple) {
                $data = $data[0] ?? [];
            }

            $attrs['type'] = $field->type;
            $attrs[':model-value'] = json_encode($data);
            if ($field->type == 'image') {
                $attrs['accept'] = 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml';
            }

            if (!$field->multiple) { ?>
                <raas-cms-field-file<?php echo $field->getAttrsString($attrs)?>></raas-cms-field-file>
            <?php } else { ?>
                <raas-cms-field-file-multiple<?php echo $field->getAttrsString($attrs)?>></raas-cms-field-file-multiple>
            <?php }
            break;
    }
};
