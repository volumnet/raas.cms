<?php
/**
 * Отображение кастомных полей - набор функций
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Attachment;
use RAAS\Field as RAASField;
use RAAS\OptGroup;
use RAAS\Option;
use RAAS\OptionCollection;

/**
 * Отображение опций
 * @param OptionCollection $options Набор опций для отображения
 * @param int $level Уровень вложенности опций
 */
$_RAASForm_Options = function (
    OptionCollection $options,
    $level = 0
) use (
    &$_RAASForm_Options,
    &$_RAASForm_Attrs
) {
    foreach ($options as $row) {
        switch (get_class($row)) {
            case OptGroup::class:
                include Application::i()->view->context->tmp('/optgroup.inc.php');
                break;
            case Option::class:
                include Application::i()->view->context->tmp('/option.inc.php');
                break;
        }
        if ($row->template) {
            include Application::i()->view->context->tmp($row->template);
        }
        switch (get_class($row)) {
            case OptGroup::class:
                $_RAASForm_OptGroup($row, $level);
                break;
            case Option::class:
                $_RAASForm_Option($row, $level);
                break;
            default:
                $_RAASForm_Options($row->children, $level + 1);
                break;
        }
    }
};

/**
 * Отображение опций в виде набора флажков
 * @param OptionCollection $options Набор опций для отображения
 * @param int $level Уровень вложенности опций
 */
$_RAASForm_Checkbox = function (
    OptionCollection $options,
    $level = 0
) use (
    &$_RAASForm_Checkbox,
    &$_RAASForm_Attrs
) {
    $field = $options->Parent;
    $options = (array)$options;
    $attrs = [];
    $text = '';
    $plain = !$level &&
        !array_filter($options, function ($x) {
            return (bool)(array)$x->children;
        }) &&
        (count($options) < 16);
    foreach ($options as $row) {
        $attrs = $row->attrs;
        foreach (['type', 'name', 'multiple'] as $key) {
            $attrs[$key] = $field->$key;
        }
        if (in_array($row->value, (array)($field->Form->DATA[$field->name] ?? []))) {
            $attrs['checked'] = 'checked';
        }
        if ($plain) {
            $text .= '<label class="' . $field->type . ' inline">
                        <input' . $_RAASForm_Attrs($field, $attrs) . ' /> '
                  .     htmlspecialchars($row->caption)
                  .  '</label>';
        } else {
            $text .= '<li>
                        <label>
                          <input' . $_RAASForm_Attrs($field, $attrs) . ' /> '
                  .       htmlspecialchars($row->caption)
                  .  '  </label>'
                  .     $_RAASForm_Checkbox($row->children, $level + 1)
                  .  '</li>';
        }
    }
    return ($text && !$plain) ?
           (
               '<ul' . (!$level ? ' class="tree" data-raas-role="tree"' : '') . '>' .
                  $text .
               '</ul>'
           ) :
           $text;
};

/**
 * Отображение элемента управления
 * @param RAASField $field Поле для отображения
 * @param bool $confirm Добавить подтверждение пароля для элемента пароля
 */
$_RAASForm_Control = function (
    RAASField $field,
    $confirm = true
) use (
    &$_RAASForm_Attrs,
    &$_RAASForm_Options,
    &$_RAASForm_Checkbox
) {
    $attrs = [];
    switch ($field->type) {
        case 'material':
            if ($field->multiple) { ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php foreach ((array)($field->Form->DATA[$field->name] ?? []) as $key => $val) {
                        $attrs = [
                            'datatype' => 'material',
                            'type' => 'hidden',
                            'data-field-id' => (int)($field->Form->Item->fields[$field->name]->id ?? 0),
                            'data-material-id' => $val->id ?? 0,
                            'data-material-pid' => $val->parents[0]->id ?? 0,
                            'data-material-name' => $val->name ?? ''
                        ]; ?>
                        <div data-role="raas-repo-element">
                          <input<?php echo $_RAASForm_Attrs($field, array_merge($attrs, ['value' => $val->id ?? 0]))?> />
                        </div>
                    <?php } ?>
                  </div>
                  <div data-role="raas-repo">
                    <?php $attrs = [
                        'datatype' => 'material',
                        'type' => 'hidden',
                        'data-field-id' => (int)($field->Form->Item->fields[$field->name]->id ?? 0),
                        'data-material-id' => '',
                        'data-material-pid' => '',
                        'data-material-name' => ''
                    ]; ?>
                    <input<?php echo $_RAASForm_Attrs($field, array_merge($attrs, ['disabled' => 'disabled', 'value' => '']))?> />
                  </div>
                </div>
            <?php } else {
                // 2015-06-08, AVS: В выражении
                // (int)$field->Form->DATA[$field->name] убрал (int),
                // т.к. $val типа материал
                $val = $field->Form->DATA[$field->name] ?? null;
                if (is_scalar($val)) {
                    $val = new Material($val);
                }
                $attrs = [
                    'datatype' => 'material',
                    'type' => 'hidden',
                    'value' => $val ? $val->id : '',
                    'data-field-id' => (int)$field->Form->Item->fields[$field->name]->id,
                    'data-material-id' => $val ? $val->id : '',
                    'data-material-pid' => isset($val->parents[0]) ? $val->parents[0]->id : 0,
                    'data-material-name' => $val ? $val->name : '',
                ];
                ?>
                <input<?php echo $_RAASForm_Attrs($field, array_merge($attrs, ['value' => $val ? $val->id : '']))?> />
            <?php }
            break;
        case 'image':
        case 'file':
            $attrs = ['type' => 'file'];
            if ($field->type == 'image') {
                $attrs['accept'] = 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml';
            }
            if (!$field->multiple) {
                $row = $field->Form->DATA[$field->name] ?? null;
                if ($field->Form->isPost) {
                    foreach (['name', 'attachment', 'vis', 'description'] as $key) {
                        $DATA[$key] = $field->Form->DATA[$field->name . '@' . $key];
                    }
                    $row = new Attachment($DATA['attachment'] ?? 0);
                } elseif ($row && $row->id) {
                    foreach (['name', 'attachment', 'vis', 'description'] as $key) {
                        $DATA[$key] = isset($row->$key) ? $row->$key : '';
                    }
                    $DATA['file'] = $row->id ? $row->fileURL : '';
                } else {
                    $DATA['vis'] = 1;
                } ?>
                <div class="well cms-filecard">
                  <?php if (!$field->meta['CustomField']->required && $row && $row->id) { ?>
                      <a class="close" data-role="delete-attach" href="#" data-ondelete="<?php echo $field->type == 'image' ? DELETE_IMAGE_TEXT : DELETE_FILE_TEXT?>">
                        &times;
                      </a>
                  <?php } ?>
                  <a href="<?php echo htmlspecialchars($row ? $row->fileURL : '')?>" target="_blank" data-role="file-link">
                    <?php if ($field->type == 'image') { ?>
                        <img
                          src="<?php echo htmlspecialchars((string)((string)($row ? $row->tnURL : '')))?>"
                          alt="<?php echo htmlspecialchars(basename((string)($row ? $row->filename : '')))?>"
                          title="<?php echo htmlspecialchars(basename((string)($row ? $row->filename : '')))?>"
                          class="cms-filecard__image"
                        />
                    <?php } else { ?>
                        <?php echo htmlspecialchars(basename($row ? $row->filename : ''))?>
                    <?php } ?>
                  </a>
                  <input type="hidden" name="<?php echo htmlspecialchars($field->name . '@attachment')?>" value="<?php echo (int)($DATA['attachment'] ?? 0)?>" />
                  <input<?php echo $_RAASForm_Attrs($field, $attrs)?> />
                  <label class="checkbox">
                    <input type="checkbox" name="<?php echo htmlspecialchars($field->name . '@vis')?>" value="1" <?php echo $DATA['vis'] ? 'checked="checked"' : ''?> />
                    <?php echo \CMS\VISIBLE?>
                  </label>
                  <div class="cms-filecard__fields<?php echo (($field->type == 'image' && $row && $row->id) ? ' cms-filecard__fields_image' : '')?>">
                    <input type="text" name="<?php echo htmlspecialchars($field->name . '@name')?>" value="<?php echo htmlspecialchars($DATA['name'] ?? '')?>" placeholder="<?php echo $field->type == 'image' ? \CMS\IMG_NAME_ALT_TITLE : NAME?>" />
                    <textarea v-pre name="<?php echo htmlspecialchars($field->name . '@description')?>" placeholder="<?php echo DESCRIPTION?>"><?php echo htmlspecialchars($DATA['description'] ?? '')?></textarea>
                  </div>
                </div>
            <?php } else { ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php if ($Set = (array)($field->Form->DATA[$field->name . ($field->Form->isPost ? '@attachment' : '')] ?? [])) {
                        for ($i = 0; $i < count($Set); $i++) {
                            $row = $Set[$i];
                            if ($field->Form->isPost) {
                                foreach ([
                                    'name',
                                    'attachment',
                                    'vis',
                                    'description'
                                ] as $key) {
                                    $DATA[$key] = $field->Form->DATA[$field->name . '@' . $key][$i];
                                }
                                $row = new Attachment($DATA['attachment']);
                            } elseif ($row->id) {
                                foreach ([
                                    'name',
                                    'attachment',
                                    'vis',
                                    'description'
                                ] as $key) {
                                    $DATA[$key] = isset($row->$key)
                                                ? $row->$key
                                                : '';
                                }
                                $DATA['file'] = $row->id ? $row->fileURL : '';
                            } else {
                                $DATA['vis'] = 1;
                            } ?>
                            <div class="well cms-filecard" data-role="raas-repo-element">
                              <a class="close" data-role="raas-repo-del" href="#">
                                &times;
                              </a>
                              <a href="#" data-role="raas-repo-move" class="cms-filecard__move">
                                <i class="icon icon-resize-vertical"></i>
                              </a>
                              <a href="<?php echo htmlspecialchars($row->fileURL)?>" target="_blank">
                                <?php if ($field->type == 'image') { ?>
                                    <img src="<?php echo htmlspecialchars($row->tnURL)?>" alt="<?php echo htmlspecialchars(basename($row->filename))?>" title="<?php echo htmlspecialchars(basename($row->filename))?>" class="cms-filecard__image" />
                                <?php } else { ?>
                                    <?php echo htmlspecialchars(basename($row->filename))?>
                                <?php } ?>
                              </a>
                              <input type="hidden" name="<?php echo htmlspecialchars($field->name . '@attachment[]')?>" value="<?php echo (int)$DATA['attachment']?>" />
                              <input<?php echo $_RAASForm_Attrs($field, $attrs)?> />
                              <label class="checkbox">
                                <input type="checkbox" name="<?php echo htmlspecialchars($field->name . '@vis[]')?>" value="1" <?php echo $DATA['vis'] ? 'checked="checked"' : ''?> />
                                <?php echo \CMS\VISIBLE?>
                              </label>
                              <input type="checkbox" style="display: none" name="<?php echo htmlspecialchars($field->name . '@vis[]')?>" value="0" data-role="checkbox-shadow" />
                              <div class="cms-filecard__fields<?php echo ($field->type == 'image' ? ' cms-filecard__fields_image' : '')?>">
                                <input type="text" name="<?php echo htmlspecialchars($field->name . '@name[]')?>" value="<?php echo htmlspecialchars($DATA['name'])?>" placeholder="<?php echo $field->type == 'image' ? \CMS\IMG_NAME_ALT_TITLE : NAME?>" />
                                <textarea v-pre name="<?php echo htmlspecialchars($field->name . '@description[]')?>" placeholder="<?php echo DESCRIPTION?>"><?php echo htmlspecialchars($DATA['description'])?></textarea>
                              </div>
                            </div>
                        <?php }
                    } ?>
                  </div>
                  <div class="well cms-filecard"<?php echo $field->multiple ? ' data-role="raas-repo"' : ''?>>
                    <a class="close" data-role="raas-repo-del" href="#">
                      &times;
                    </a>
                    <a href="#" data-role="raas-repo-move" class="cms-filecard__move">
                      <i class="icon icon-resize-vertical"></i>
                    </a>
                    <input type="hidden" name="<?php echo htmlspecialchars($field->name . '@attachment[]')?>" disabled="disabled" />
                    <input<?php echo $_RAASForm_Attrs($field, array_merge($attrs, ['disabled' => 'disabled']))?> />
                    <label class="checkbox">
                      <input type="checkbox" name="<?php echo htmlspecialchars($field->name . '@vis[]')?>" value="1" disabled="disabled" checked="checked" />
                      <?php echo \CMS\VISIBLE?>
                    </label>
                    <input type="checkbox" style="display: none" name="<?php echo htmlspecialchars($field->name . '@vis[]')?>" disabled value="0" data-role="checkbox-shadow" />
                    <div class="cms-filecard__fields">
                      <input type="text" name="<?php echo htmlspecialchars($field->name . '@name[]')?>" placeholder="<?php echo $field->type == 'image' ? \CMS\IMG_NAME_ALT_TITLE : NAME?>" disabled="disabled" />
                      <textarea v-pre name="<?php echo htmlspecialchars($field->name . '@description[]')?>" placeholder="<?php echo DESCRIPTION?>"></textarea>
                    </div>
                  </div>
                </div>
            <?php }
            break;
        case 'checkbox':
            $attrs = [];
            if ($field->multiple) {
                echo $_RAASForm_Checkbox($field->children);
            } else {
                $attrs['value'] = 1;
                if ($field->Form->DATA[$field->name] ?? '') {
                    $attrs['checked'] = 'checked';
                } ?>
                <input<?php echo $_RAASForm_Attrs($field, $attrs)?> />
            <?php }
            break;
        case 'radio':
            echo $_RAASForm_Checkbox($field->children);
            break;
        case 'select':
            $attrs['type'] = false;
            if ($field->placeholder || !$field->required) {
                for ($i = count($field->children) - 1; $i >= 0; $i--) {
                    $field->children[$i + 1] = $field->children[$i];
                }
                $field->children[0] = new Option([
                    'caption' => $field->placeholder ?: '--',
                    'value' => ''
                ]);
            }
            if ($field->multiple && !$field->{'data-raas-multiselect'}) {
                $attrs = array_merge($attrs, ['multiple' => false]); ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php foreach ((array)($field->Form->DATA[$field->name] ?? []) as $key => $val) {
                        $field->value = $val; ?>
                        <div data-role="raas-repo-element">
                          <select<?php echo $_RAASForm_Attrs($field, $attrs)?>>
                            <?php echo $_RAASForm_Options($field->children)?>
                          </select>
                        </div>
                    <?php } ?>
                  </div>
                  <div data-role="raas-repo">
                    <select<?php echo $_RAASForm_Attrs($field, array_merge($attrs, ['disabled' => 'disabled']))?>>
                      <?php echo $_RAASForm_Options($field->children)?>
                    </select>
                  </div>
                </div>
            <?php } else { ?>
                <select<?php echo $_RAASForm_Attrs($field, $attrs)?>>
                  <?php echo $_RAASForm_Options($field->children)?>
                </select>
            <?php }
            break;
        default:
            // @todo TEST!!!
            $attrs = [];
            $fieldType = $field->type ?: 'text';

            if (!$field->type) {
                $attrs['type'] = 'text';
            }
            if (($field->type == 'password') && $confirm) {
                $attrs['name'] = $field->name . '@confirm';
            }
            // $attrs['v-pre'] = 'v-pre';
            // echo 'TEST!!!';
            if ($field->multiple && !in_array($field->type, ['password'])) {
                ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php foreach ((array)($field->Form->DATA[$field->name] ?? []) as $key => $val) { ?>
                        <div data-role="raas-repo-element">
                          <raas-field-<?php echo htmlspecialchars($fieldType)?> <?php echo $_RAASForm_Attrs($field, array_merge($attrs, [':value' => json_encode($val)]))?>></raas-field-<?php echo htmlspecialchars($fieldType)?>>
                        </div>
                    <?php } ?>
                  </div>
                  <div data-role="raas-repo">
                    <raas-field-<?php echo htmlspecialchars($fieldType)?> <?php echo $_RAASForm_Attrs($field, array_merge($attrs, ['disabled' => 'disabled']))?>></raas-field-<?php echo htmlspecialchars($fieldType)?>>
                  </div>
                </div>
                <?php
            } else {
                ?>
                <raas-field-<?php echo htmlspecialchars($fieldType)?> <?php echo $_RAASForm_Attrs($field, array_merge($attrs, [':value' => json_encode($field->Form->DATA[$field->name] ?? null)]))?>></raas-field-<?php echo htmlspecialchars($fieldType)?>>
                <?php
            }
            break;
    }
};

/**
 * Отображение поля с подписью
 * @param RAASField $field Поле для отображения
 */
$_RAASForm_Field = function (RAASField $field) use (
    &$_RAASForm_Control,
    &$_RAASForm_Options
) {
    $err = (bool)array_filter(
        (array)$field->Form->localError,
        function ($x) use ($field) {
            return $x['value'] == $field->name;
        }
    );
    if (in_array($field->type, ['htmlarea', 'codearea', 'htmlcodearea'])) { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <?php if ($field->caption) { ?>
              <label class="control-label" for="<?php echo htmlspecialchars($field->name)?>">
                <?php echo htmlspecialchars($field->caption)?>:
              </label>
              <div class="controls clearfix">&nbsp;</div>
          <?php } ?>
          <div class="clearfix"><?php echo $_RAASForm_Control($field)?></div>
        </div>
    <?php } elseif (($field->type == 'password') && $field->confirm) {
        $err2 = (bool)array_filter(
            (array)$field->Form->localError,
            function ($x) use ($field) {
                return $x['value'] == $field->name . '@confirm';
            }
        );
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($field->name)?>">
            <?php echo htmlspecialchars($field->caption)?>:
          </label>
          <div class="controls">
            <?php echo $_RAASForm_Control($field, false)?>
          </div>
        </div>
        <div class="control-group<?php echo $err2 ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($field->name)?>@confirm">
            <?php echo PASSWORD_CONFIRM?>:
          </label>
          <div class="controls">
            <?php echo $_RAASForm_Control($field, true)?>
          </div>
        </div>
    <?php } elseif ($field->type == 'checkbox' && !$field->multiple) { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <div class="controls">
            <label class="checkbox"<?php echo $field->{'data-hint'} ? ' style="width: 174px;"' : ''?>>
              <?php echo $_RAASForm_Control($field, false)?>
              <?php echo htmlspecialchars($field->caption)?>
            </label>
          </div>
        </div>
    <?php } elseif ($field->type == 'hidden') {
        echo $_RAASForm_Control($field, false);
    } else { ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($field->name)?>">
            <?php echo htmlspecialchars(
                $field->caption ?
                $field->caption . ':' :
                ''
            )?>
          </label>
          <div class="controls">
            <?php echo $_RAASForm_Control($field, false)?>
          </div>
        </div>
    <?php }
};
