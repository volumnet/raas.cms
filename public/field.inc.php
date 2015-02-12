<?php
$_RAASForm_Options = function(\RAAS\OptionCollection $options, $level = 0) use (&$_RAASForm_Options, &$_RAASForm_Attrs) {
    foreach ($options as $row) { 
        switch (get_class($row)) {
            case 'RAAS\OptGroup':
                include \RAAS\Application::i()->view->context->tmp('/optgroup.inc.php');
                break;
            case 'RAAS\Option':
                include \RAAS\Application::i()->view->context->tmp('/option.inc.php');
                break;
        }
        if ($row->template) {
            include \RAAS\Application::i()->view->context->tmp($row->template);
        }
        switch (get_class($row)) {
            case 'RAAS\OptGroup':
                $_RAASForm_OptGroup($row, $level);
                break;
            case 'RAAS\Option':
                $_RAASForm_Option($row, $level);
                break;
            default:
                $_RAASForm_Options($row->children, $level + 1);
                break;
        }
    } 
};

$_RAASForm_Checkbox = function (\RAAS\OptionCollection $options, $level = 0) use (&$_RAASForm_Checkbox, &$_RAASForm_Attrs) {
    $Field = $options->Parent;
    $options = (array)$options;
    $attrs = array();
    $text = '';
    $plain = !$level && !array_filter($options, function($x) { return (bool)(array)$x->children; }) && count($options) < 16;
    foreach ($options as $row) { 
        $attrs = $row->attrs;
        foreach (array('type', 'name', 'multiple') as $key) {
            $attrs[$key] = $Field->$key;
        }
        if (in_array($row->value, (array)$Field->Form->DATA[$Field->name])) {
            $attrs['checked'] = 'checked';
        }
        if ($plain) {
            $text .= '<label class="' . $Field->type . ' inline"><input' . $_RAASForm_Attrs($Field, $attrs) . ' /> ' . htmlspecialchars($row->caption) . '</label>';
        } else {
            $text .= '<li>
                        <label><input' . $_RAASForm_Attrs($Field, $attrs) . ' /> ' . htmlspecialchars($row->caption) . '</label>' 
                  .     $_RAASForm_Checkbox($row->children, $level + 1) . '
                      </li>';
        }
    }
    return $text && !$plain ? '<ul' . (!$level ? ' class="tree" data-raas-role="tree"' : '') . '>' . $text . '</ul>' : $text;
};

$_RAASForm_Control = function(\RAAS\Field $Field, $confirm = true) use (&$_RAASForm_Attrs, &$_RAASForm_Options, &$_RAASForm_Checkbox) {
    $attrs = array();
    switch ($Field->type) { 
        case 'material':
            if ($Field->multiple) { 
                ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php foreach ((array)$Field->Form->DATA[$Field->name] as $key => $val) { 
                        $attrs = array(
                          'datatype' => 'material', 
                          'type' => 'hidden', 
                          'data-field-id' => (int)$Field->Form->Item->fields[$Field->name]->id,
                          'data-material-id' => $val->id, 
                          'data-material-pid' => $val->parents[0]->id, 
                          'data-material-name' => $val->name
                        );
                        ?>
                        <div data-role="raas-repo-element"><input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('value' => $val->id)))?> /></div>
                    <?php } ?>
                  </div>
                  <div data-role="raas-repo">
                    <?php
                    $attrs = array(
                        'datatype' => 'material', 
                        'type' => 'hidden', 
                        'data-field-id' => (int)$Field->Form->Item->fields[$Field->name]->id,
                        'data-material-id' => '', 
                        'data-material-pid' => '', 
                        'data-material-name' => ''
                    );
                    ?>
                    <input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('disabled' => 'disabled', 'value' => '')))?> />
                  </div>
                </div>
            <?php 
            } else { 
                $val = (int)$Field->Form->DATA[$Field->name];
                $attrs = array(
                    'datatype' => 'material', 
                    'type' => 'hidden', 
                    'value' => $val->id,
                    'data-field-id' => (int)$Field->Form->Item->fields[$Field->name]->id,
                    'data-material-id' => $val->id, 
                    'data-material-pid' => $val->parents[0]->id, 
                    'data-material-name' => $val->name
                );
                ?>
                <input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('value' => $val->id)))?> />
                <?php
            }
            break;
        case 'image': case 'file': 
            $attrs = array('type' => 'file');
            if ($Field->type == 'image') {
                $attrs['accept'] = 'image/jpeg,image/png,image/gif';
            }
            if (!$Field->multiple) {
                $row = $Field->Form->DATA[$Field->name];
                if ($Field->Form->isPost) {
                    foreach (array('name', 'attachment', 'vis', 'description') as $key) {
                        $DATA[$key] = $Field->Form->DATA[$Field->name . '@' . $key];
                    }
                } elseif ($row->id) {
                    foreach (array('name', 'attachment', 'vis', 'description') as $key) {
                        $DATA[$key] = isset($row->$key) ? $row->$key : '';
                    }
                    $DATA['file'] = $row->id ? $row->fileURL : '';
                } else {
                    $DATA['vis'] = 1;
                }
                ?>
                <div class="well cms-filecard">
                  <?php if (!$Field->meta['CustomField']->required && $row->id) { ?>
                      <a class="close" data-role="delete-attach" href="#" onclick="return confirm('<?php echo $Field->type == 'image' ? DELETE_IMAGE_TEXT : DELETE_FILE_TEXT?>')">&times;</a>
                  <?php } ?>
                  <a href="<?php echo htmlspecialchars($row->fileURL)?>" target="_blank" data-role="file-link">
                    <?php if ($Field->type == 'image') { ?> 
                        <img src="<?php echo htmlspecialchars($row->tnURL)?>" alt="<?php echo htmlspecialchars(basename($row->filename))?>" title="<?php echo htmlspecialchars(basename($row->filename))?>" />
                    <?php } else { ?>
                        <?php echo htmlspecialchars(basename($row->filename))?>
                    <?php } ?>
                  </a>
                  <input type="hidden" name="<?php echo htmlspecialchars($Field->name . '@attachment')?>" value="<?php echo (int)$DATA['attachment']?>" />
                  <input<?php echo $_RAASForm_Attrs($Field, $attrs)?> />
                  <label class="checkbox"><input type="checkbox" name="<?php echo htmlspecialchars($Field->name . '@vis')?>" value="1" <?php echo $DATA['vis'] ? 'checked="checked"' : ''?> /> <?php echo CMS\VISIBLE?></label>
                  <input type="text" name="<?php echo htmlspecialchars($Field->name . '@name')?>" value="<?php echo htmlspecialchars($DATA['name'])?>" />
                  <textarea name="<?php echo htmlspecialchars($Field->name . '@description')?>"><?php echo htmlspecialchars($DATA['description'])?></textarea>
                </div>
                <?php
            } else {
                ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php
                    if ($Set = $Field->Form->DATA[$Field->name . ($Field->Form->isPost ? '@attachment' : '')]) {
                        for ($i = 0; $i < count($Set); $i++) {
                            $row = $Set[$i];
                            if ($Field->Form->isPost) {
                                foreach (array('name', 'attachment', 'vis', 'description') as $key) {
                                    $DATA[$key] = $Field->Form->DATA[$Field->name . '@' . $key][$i];
                                }
                                $row = new \RAAS\Attachment($DATA['attachment']);
                            } elseif ($row->id) {
                                foreach (array('name', 'attachment', 'vis', 'description') as $key) {
                                    $DATA[$key] = isset($row->$key) ? $row->$key : '';
                                }
                                $DATA['file'] = $row->id ? $row->fileURL : '';
                            } else {
                                $DATA['vis'] = 1;
                            }
                            ?>
                            <div class="well cms-filecard" data-role="raas-repo-element">
                              <a class="close" data-role="raas-repo-del" href="#">&times;</a>
                              <a href="<?php echo htmlspecialchars($row->fileURL)?>" target="_blank">
                                <?php if ($Field->type == 'image') { ?> 
                                    <img src="<?php echo htmlspecialchars($row->tnURL)?>" alt="<?php echo htmlspecialchars(basename($row->filename))?>" title="<?php echo htmlspecialchars(basename($row->filename))?>" />
                                <?php } else { ?>
                                    <?php echo htmlspecialchars(basename($row->filename))?>
                                <?php } ?>
                              </a>
                              <input type="hidden" name="<?php echo htmlspecialchars($Field->name . '@attachment[]')?>" value="<?php echo (int)$DATA['attachment']?>" />
                              <input<?php echo $_RAASForm_Attrs($Field, $attrs)?> />
                              <label class="checkbox"><input type="checkbox" name="<?php echo htmlspecialchars($Field->name . '@vis[]')?>" value="1" <?php echo $DATA['vis'] ? 'checked="checked"' : ''?> /> <?php echo CMS\VISIBLE?></label>
                              <input type="checkbox" style="display: none" name="<?php echo htmlspecialchars($Field->name . '@vis[]')?>" value="0" data-role="checkbox-shadow" />
                              <a href="#" data-role="raas-repo-move"><i class="icon icon-resize-vertical"></i></a>
                              <input type="text" name="<?php echo htmlspecialchars($Field->name . '@name[]')?>" value="<?php echo htmlspecialchars($DATA['name'])?>" />
                              <textarea name="<?php echo htmlspecialchars($Field->name . '@description[]')?>"><?php echo htmlspecialchars($DATA['description'])?></textarea>
                            </div>
                        <?php 
                        } 
                    }
                    ?>
                  </div>
                  <div class="well cms-filecard"<?php echo $Field->multiple ? ' data-role="raas-repo"' : ''?>>
                    <a class="close" data-role="raas-repo-del" href="#">&times;</a>
                    <input type="hidden" name="<?php echo htmlspecialchars($Field->name . '@attachment[]')?>" disabled="disabled" />
                    <input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('disabled' => 'disabled')))?> />
                    <label class="checkbox"><input type="checkbox" name="<?php echo htmlspecialchars($Field->name . '@vis[]')?>" value="1" disabled="disabled" checked="checked" /> <?php echo CMS\VISIBLE?></label>
                    <a href="#" data-role="raas-repo-move"><i class="icon icon-resize-vertical"></i></a>
                    <input type="text" name="<?php echo htmlspecialchars($Field->name . '@name[]')?>" disabled="disabled" />
                    <textarea name="<?php echo htmlspecialchars($Field->name . '@description[]')?>"></textarea>
                  </div>
                </div>
                <?php
            }
            break;
        case 'checkbox':
            $attrs = array();
            if ($Field->multiple) {
                echo $_RAASForm_Checkbox($Field->children);
            } else {
                $attrs['value'] = 1;
                if ($Field->Form->DATA[$Field->name]) {
                    $attrs['checked'] = 'checked';
                }
                ?>
                <input<?php echo $_RAASForm_Attrs($Field, $attrs)?> />
                <?php
            }
            break;
        case 'radio':
            echo $_RAASForm_Checkbox($Field->children);
            break;
        case 'select':
            $attrs['type'] = false;
            if ($Field->placeholder) {
                for ($i = count($Field->children) - 1; $i >= 0; $i--) {
                    $Field->children[$i + 1] = $Field->children[$i];
                }
                $Field->children[0] = new \RAAS\Option(array('caption' => $Field->placeholder, 'value' => ''));
            }
            if ($Field->multiple) { 
                $attrs = array_merge($attrs, array('disabled' => 'disabled', 'multiple' => false));
                ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php foreach ((array)$Field->Form->DATA[$Field->name] as $key => $val) { ?>
                        <div data-role="raas-repo-element">
                          <select<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('value' => $val)))?>><?php echo $_RAASForm_Options($Field->children)?></select>
                        </div>
                    <?php } ?>
                  </div>
                  <div data-role="raas-repo"><select<?php echo $_RAASForm_Attrs($Field, $attrs)?>><?php echo $_RAASForm_Options($Field->children)?></select></div>
                </div>
                <?php 
            } else { 
                ?>
                <select<?php echo $_RAASForm_Attrs($Field, $attrs)?>><?php echo $_RAASForm_Options($Field->children)?></select>
                <?php
            }
            break;
        case 'textarea': case 'htmlarea': case 'codearea':
            $attrs['type'] = false;
            if ($Field->type == 'htmlarea') {
                $attrs['class'] = 'htmlarea';
                $attrs['required'] = false;
            } elseif ($Field->type == 'codearea') {
                $attrs['class'] = 'code codearea fullscreen';
            }
            if ($Field->multiple) { 
                ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php foreach ((array)$Field->Form->DATA[$Field->name] as $key => $val) { ?>
                        <div data-role="raas-repo-element"><textarea<?php echo $_RAASForm_Attrs($Field, $attrs)?>><?php echo htmlspecialchars($val)?></textarea></div>
                    <?php } ?>
                  </div>
                  <div data-role="raas-repo"><textarea<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('disabled' => 'disabled')))?>></textarea></div>
                </div>
            <?php } else { ?>
                <textarea<?php echo $_RAASForm_Attrs($Field, $attrs)?>><?php echo htmlspecialchars($Field->Form->DATA[$Field->name])?></textarea>
            <?php
            }
            break;
        case 'password':
            $attrs = array();
            if ($confirm) {
                $attrs['name'] = $Field->name . '@confirm';
            }
            ?>
            <input<?php echo $_RAASForm_Attrs($Field, $attrs)?> />
            <?php
            break;
        default: 
            $attrs = array();
            if (!$Field->type) {
                $attrs['type'] = 'text';
            }
            if ($Field->multiple) { 
                ?>
                <div data-role="raas-repo-block">
                  <div data-role="raas-repo-container">
                    <?php foreach ((array)$Field->Form->DATA[$Field->name] as $key => $val) { ?>
                        <div data-role="raas-repo-element"><input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('value' => $val)))?> /></div>
                    <?php } ?>
                  </div>
                  <div data-role="raas-repo"><input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('disabled' => 'disabled')))?> /></div>
                </div>
                <?php 
            } else { 
                ?>
                <input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('value' => $Field->Form->DATA[$Field->name])))?> />
                <?php
            }
            break;
    }
};

$_RAASForm_Field = function(\RAAS\Field $Field) use (&$_RAASForm_Control, &$_RAASForm_Options) {
    $err = (bool)array_filter((array)$Field->Form->localError, function($x) use ($Field) { return $x['value'] == $Field->name; });
    if (in_array($Field->type, array('htmlarea', 'codearea'))) {
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <?php if ($Field->caption) { ?>
              <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>"><?php echo htmlspecialchars($Field->caption)?>:</label> 
              <div class="controls clearfix">&nbsp;</div>
          <?php } ?>
          <div class="clearfix"><?php echo $_RAASForm_Control($Field)?></div>
        </div>
        <?php
    } elseif (($Field->type == 'password') && $Field->confirm) {
        $err2 = (bool)array_filter((array)$Field->Form->localError, function($x) use ($Field) { return $x['value'] == $Field->name . '@confirm'; });
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>"><?php echo htmlspecialchars($Field->caption)?>:</label> 
          <div class="controls"><?php echo $_RAASForm_Control($Field, false)?></div>
        </div>
        <div class="control-group<?php echo $err2 ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>@confirm"><?php echo PASSWORD_CONFIRM?>:</label> 
          <div class="controls"><?php echo $_RAASForm_Control($Field, true)?></div>
        </div>
        <?php
    } elseif ($Field->type == 'checkbox' && !$Field->multiple) {
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <div class="controls"><label class="checkbox"<?php echo $Field->{'data-hint'} ? ' style="width: 174px;"' : ''?>><?php echo $_RAASForm_Control($Field, false)?> <?php echo htmlspecialchars($Field->caption)?></label></div>
        </div>
        <?php
    } elseif ($Field->type == 'hidden') {
        echo $_RAASForm_Control($Field, false);
    } else {
        ?>
        <div class="control-group<?php echo $err ? ' error' : ''?>">
          <label class="control-label" for="<?php echo htmlspecialchars($Field->name)?>">
            <?php echo htmlspecialchars($Field->caption ? $Field->caption . ':' : '')?>
          </label> 
          <div class="controls"><?php echo $_RAASForm_Control($Field, false)?></div>
        </div>
        <?php
    }
};