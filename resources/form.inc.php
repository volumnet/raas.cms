<?php
$getSelect = function(\RAAS\CMS\Field $Item, array $DATA, $current = 0, $placeholder = '') use (&$getSelect) {
    static $level = 0;
    $text = '';
    if (!$level && !$Item->required) {
        $text .= '<option value="" ' . (!$current ? 'selected="selected"' : '') . '>' . htmlspecialchars($placeholder ? $placeholder : '--') . '</option>';
    }
    foreach ($DATA as $key => $val) {
        $text .= '<option value="' . htmlspecialchars($key) . '" ' . ($current == $key ? 'selected="selected"' : '') . '>' 
              .     str_repeat('&nbsp;', $level * 5) . htmlspecialchars(isset($val['name']) ? $val['name'] : '') 
              . '</option>';
        if (isset($val['children']) && is_array($val['children'])) {
            $level++;
            $text .= $getSelect($Item, $val['children'], $current);
            $level--;
        }
    }
    return $text;
};

$getCheckbox = function(\RAAS\CMS\Field $Item, array $DATA, $current = 0, $placeholder = '') use (&$getCheckbox) {
    static $level = 0;
    $temp = '<input type="' . $Item->datatype . '" '
          .       ' name="' . htmlspecialchars($Item->urn) . ((($Item->datatype == 'checkbox') && $Item->multiple) ? '[]' : '') . '"' 
          .       ($Item->required ? ' required="required"' : '');
    
    $text = '';
    if (($Item->datatype == 'radio') && !$Item->required && !$level) {
        $text .= '<li>'
              .  '  <label>' . $temp . ' value="" ' . (!$current ? 'checked="checked"' : '') . ' /> ' . htmlspecialchars($placeholder ? $placeholder : '--') . '</label>'
              .  '</li>';
    }
    foreach ($DATA as $key => $val) {
        $text .= '<li>'
              .  '  <label>' 
              .       $temp . ' value="' . htmlspecialchars($key) . '" ' . (($Item->multiple ? in_array($key, (array)$current) : ($current == $key)) ? 'checked="checked"' : '') . '> '  
              .       htmlspecialchars(isset($val['name']) ? $val['name'] : '')
              .  '  </label>';
        if (isset($val['children']) && is_array($val['children'])) {
            $level++;
            $text .= $getCheckbox($Item, $val['children'], $current);
            $level--;
        }
        $text .= '</li>';
    }
    return $text ? '<ul' . (!$level ? ' class="tree jsFieldTree"' : '') . '>' . $text . '</ul>' : '';
};

$getField = function(\RAAS\CMS\Field $row, array $DATA = array()) use (&$getCheckbox, $getSelect)  {
    switch ($row->datatype) { 
        case 'image': case 'file': 
            if ($row->multiple) {
                echo '<div class="jsFieldContainer">';
                for ($i = 0; ($i < count($DATA[$row->urn])) || (($i < 1) && $row->multiple && $row->required); $i++) {
                    echo '<div class="jsField cms-file">' . 
                            '<div class="cms-file-internal">' . 
                              (
                                  isset($DATA[$row->urn . '@file'][$i]) ? 
                                  '<p class="jsFilePath cms-file-path">' . 
                                    '<a href="' . htmlspecialchars($DATA[$row->urn . '@file'][$i]) . '">' . 
                                        (
                                            $row->datatype == 'image' ? 
                                            '<img src="' . htmlspecialchars($DATA[$row->urn][$i]->tnURL) . '" alt="' . htmlspecialchars(basename($DATA[$row->urn . '@file'][$i])) . '" title="' . htmlspecialchars(basename($DATA[$row->urn . '@file'][$i])) . '" />' : 
                                            htmlspecialchars(basename($DATA[$row->urn . '@file'][$i]))
                                        ) . 
                                    '</a>' . 
                                  '</p>' : 
                                  ''
                              ) . 
                              '<input type="hidden" name="' . htmlspecialchars($row->urn . '@attachment[]') . '" ' . ($row->required ? 'required="required"' : '') . ' value="' . (int)(isset($DATA[$row->urn . '@attachment'][$i]) ? $DATA[$row->urn . '@attachment'][$i] : 0) . '" />' . 
                              '<input type="file"' .
                                 ($row->datatype == 'image' ? ' accept="image/jpeg,image/png,image/gif"' : '') .
                                 ' name="' . htmlspecialchars($row->urn) . '[]"' .
                                 ($row->placeholder ? ' placeholder="' . htmlspecialchars($row->placeholder) . '"' : '') .
                                 (!isset($DATA[$row->urn . '@file'][$i]) && $row->required ? ' required="required"' : '') . ' /><br /> ' .
                              '<label>' .
                                '<input type="hidden" class="jsRemainDisable" name="' . htmlspecialchars($row->urn . '@vis[]') . '" value="0" ' . (!isset($DATA[$row->urn . '@vis'][$i]) || $DATA[$row->urn . '@vis'][$i] ? 'disabled="disabled"' : '') . ' />' .
                                '<input type="checkbox" name="' . htmlspecialchars($row->urn . '@vis[]') . '" value="1" ' . (!isset($DATA[$row->urn . '@vis'][$i]) || $DATA[$row->urn . '@vis'][$i] ? 'checked="checked"' : '') . ' />' .
                                VISIBLE . 
                              '</label><br />' .
                              '<input type="text" name="' . htmlspecialchars($row->urn . '@name[]') . '" value="' . htmlspecialchars(isset($DATA[$row->urn . '@name'][$i]) ? $DATA[$row->urn . '@name'][$i] : '') . '" /><br />' .
                              '<textarea name="' . htmlspecialchars($row->urn . '@description[]') . '">' . htmlspecialchars(isset($DATA[$row->urn . '@description'][$i]) ? $DATA[$row->urn . '@description'][$i] : '') . '</textarea>' . 
                            '</div>' .
                            '<span class="icon cms-move" ' . ((count($DATA[$row->urn . '@attachment']) <= 1) ? 'style="display: none"' : '') . ' title="' . MOVE . '"></span>' .
                            '<a href="#" class="jsDeleteField icon system delete" ' . ($row->required && (count($DATA[$row->urn . '@attachment']) <= 1) ? 'style="display: none"' : '') . ' title="' . DELETE . '"></a>' .
                         '</div>';
                }
                echo  '<div class="jsRepo cms-field_repo cms-file">' . 
                         '<div class="cms-file-internal">' . 
                          '<input type="hidden" name="' . htmlspecialchars($row->urn . '@attachment[]') . '" disabled="disabled" value="0" />' . 
                          '<input type="file" disabled="disabled" ' .
                             ($row->datatype == 'image' ? ' accept="image/jpeg,image/png,image/gif"' : '') .
                             ' name="' . htmlspecialchars($row->urn) . '[]"' .
                             ($row->placeholder ? ' placeholder="' . htmlspecialchars($row->placeholder) . '"' : '') .
                             ($row->required ? ' required="required"' : '') . ' /><br /> ' .
                          '<label>' .
                            '<input type="hidden" class="jsRemainDisable" name="' . htmlspecialchars($row->urn . '@vis[]') . '" value="0" disabled="disabled" />' .
                            '<input type="checkbox" name="' . htmlspecialchars($row->urn . '@vis[]') . '" value="1" checked="checked" />' .
                            VISIBLE . 
                          '</label><br />' .
                          '<input type="text" name="' . htmlspecialchars($row->urn . '@name[]') . '" value="" /><br />' .
                          '<textarea name="' . htmlspecialchars($row->urn . '@name[]') . '"></textarea>' . 
                        '</div>' .
                        '<span class="icon cms-move" title="' . MOVE . '"></span>' .
                        '<a href="#" class="jsDeleteField icon system delete" title="' . DELETE . '"></a>' .
                      '</div>';
                echo '</div>';
            } else {
                echo '<div class="jsField cms-file">' .
                        '<div class="cms-file-internal">' . 
                           (
                                  isset($DATA[$row->urn . '@file']) ? 
                                  '<p class="jsFilePath cms-file-path">' . 
                                    '<a href="' . htmlspecialchars($DATA[$row->urn . '@file']) . '">' . 
                                        (
                                            $row->datatype == 'image' ? 
                                            '<img src="' . htmlspecialchars($DATA[$row->urn]->tnURL) . '" alt="' . htmlspecialchars(basename($DATA[$row->urn . '@file'])) . '" title="' . htmlspecialchars(basename($DATA[$row->urn . '@file'])) . '" />' : 
                                            htmlspecialchars(basename($DATA[$row->urn . '@file'][$i]))
                                        ) . 
                                    '</a>' . 
                                  '</p>' : 
                                  ''
                              ) . 
                          '<input type="hidden" name="' . htmlspecialchars($row->urn . '@attachment') . '" value="' . (int)(isset($DATA[$row->urn . '@attachment']) ? $DATA[$row->urn . '@attachment'] : 0) . '" />' .
                          '<input type="file"' .
                             ($row->datatype == 'image' ? ' accept="image/jpeg,image/png,image/gif"' : '') .
                             ' name="' . htmlspecialchars($row->urn) . '"' .
                             ' id="' . htmlspecialchars($row->urn) . '"' .
                             ($row->placeholder ? ' placeholder="' . htmlspecialchars($row->placeholder) . '"' : '') .
                             ($row->required ? ' required1="required"' : '') . ' /><br /> ' .
                          '<label>' .
                            '<input type="checkbox" name="' . htmlspecialchars($row->urn . '@vis') . '" value="1" ' . (isset($DATA[$row->urn . '@vis']) && $DATA[$row->urn . '@vis'] ? 'checked="checked"' : '') . ' />' .
                            VISIBLE . 
                          '</label><br />' .
                          '<input type="text" name="' . htmlspecialchars($row->urn . '@name') . '" value="' . htmlspecialchars(isset($DATA[$row->urn . '@name']) ? $DATA[$row->urn . '@name'] : '') . '" /><br />' .
                          '<textarea name="' . htmlspecialchars($row->urn . '@description') . '">' . htmlspecialchars(isset($DATA[$row->urn . '@description']) ? $DATA[$row->urn . '@description'] : '') . '</textarea>' . 
                        '</div>' .
                        (!$row->required ? '<a href="#" class="jsDeleteFile icon system delete" ' . ($row->required && !count($DATA[$row->urn]) ? 'style="display: none"' : '') . ' title="' . DELETE . '"></a>' : '') .
                     '</div>';
            }
            break;
        case 'checkbox':
            if ($row->multiple) {
                echo $getCheckbox($row, $row->stdSource, $DATA[$row->urn], $row->placeholder);
            } else {
                echo '<input type="' . $row->datatype . '" ' . 
                            'name="' . htmlspecialchars($row->urn) . '" ' . 
                            'id="' . htmlspecialchars($row->urn) . '"' .
                              ($row->required ? ' required="required"' : '') . ' ' .
                            'value="1" ' . 
                            (isset($DATA[$row->urn]) && $DATA[$row->urn] ? 'checked="checked"' : '') . ' />';
            }
            break;
        case 'radio':
            echo $getCheckbox($row, $row->stdSource, $DATA[$row->urn], $row->placeholder);
            break;
        case 'select':
            $temp = '<select '
                  .       ' name="' . htmlspecialchars($row->urn) . ($row->multiple ? '[]' : '') . '"'
                  .       (!$row->multiple ? ' id="' . htmlspecialchars($row->urn) . '"' : '')
                  .       ($row->required ? ' required="required"' : '');
            if ($row->multiple) {
                echo '<div class="jsFieldContainer">';
                for ($i = 0; ($i < count($DATA[$row->urn])) || (($i < 1) && $row->multiple && $row->required); $i++) {
                    echo '<div class="jsField">' . 
                            $temp . '>' . $getSelect($row, $row->stdSource, $DATA[$row->urn][$i], $row->placeholder) . '</select>' .
                            '<span class="icon cms-move" ' . ((count($DATA[$row->urn]) <= 1) ? 'style="display: none"' : '') . ' title="' . MOVE . '"></span>' .
                            '<a href="#" class="jsDeleteField icon system delete" ' . ($row->required && (count($DATA[$row->urn]) <= 1) ? 'style="display: none"' : '') . ' title="' . DELETE . '"></a>' .
                         '</div>';
                }
                echo  '<div class="jsRepo cms-field_repo">' . $temp . ' disabled="disabled">' . $getSelect($row, $row->stdSource, 0, $row->placeholder) . '</select>' .
                        '<span class="icon cms-move" title="' . MOVE . '"></span>' .
                        '<a href="#" class="jsDeleteField icon system delete" title="' . DELETE . '"></a>' .
                      '</div>';
                echo '</div>';
            } else {
                echo $temp . '>' . $getSelect($row, $row->stdSource, $DATA[$row->urn], $row->placeholder) . '</select>';
            }
            break;
        case 'textarea': case 'htmlarea':
            $temp = '<textarea ' . ($row->datatype == 'htmlarea' ? 'class="cms-htmlarea"' : '')
                  .       ' name="' . htmlspecialchars($row->urn) . ($row->multiple ? '[]' : '') . '"'
                  .       (!$row->multiple ? ' id="' . htmlspecialchars($row->urn) . '"' : '')
                  .       ($row->maxlength ? ' maxlength="' . (int)$row->maxlength . '"' : '')
                  .       ($row->placeholder ? ' placeholder="' . htmlspecialchars($row->placeholder) . '"' : '')
                  .       ($row->required && ($row->datatype != 'htmlarea') ? ' required="required"' : '');
            if ($row->multiple) {
                echo '<div class="jsFieldContainer">';
                for ($i = 0; ($i < count($DATA[$row->urn])) || (($i < 1) && $row->multiple && $row->required); $i++) {
                    echo '<div class="jsField">' . 
                            $temp . ' id="' . htmlspecialchars($row->urn . '@' . $i) . '">' . htmlspecialchars(isset($DATA[$row->urn][$i]) ? (string)$DATA[$row->urn][$i] : '') . '</textarea>' .
                            ($row->datatype != 'htmlarea' ? '<span class="icon cms-move" ' . ((count($DATA[$row->urn]) <= 1) ? 'style="display: none"' : '') . ' title="' . MOVE . '"></span>' : '') .
                            '<a href="#" class="jsDeleteField icon system delete" ' . ($row->required && (count($DATA[$row->urn]) <= 1) ? 'style="display: none"' : '') . ' title="' . DELETE . '"></a>' .
                         '</div>';
                }
                echo  '<div class="jsRepo cms-field_repo">' . $temp . ' id="' . htmlspecialchars($row->urn . '@' . $i) . '" disabled="disabled"></textarea>' .
                        ($row->datatype != 'htmlarea' ? '<span class="icon cms-move" title="' . MOVE . '"></span>' : '') .
                        '<a href="#" class="jsDeleteField icon system delete" title="' . DELETE . '"></a>' .
                      '</div>';
                echo '</div>';
            } else {
                echo $temp . '>' . htmlspecialchars(isset($DATA[$row->urn]) ? (string)$DATA[$row->urn] : '') . '</textarea>';
            }
            break;
        case 'password':
            $temp = '<input type="' . $row->datatype . '"'
                  .       ' name="' . htmlspecialchars($row->urn) . ($row->multiple ? '[]' : '') . '"'
                  .       (!$row->multiple ? ' id="' . htmlspecialchars($row->urn) . '"' : '')
                  .       ($row->maxlength ? ' maxlength="' . (int)$row->maxlength . '"' : '')
                  .       ($row->placeholder ? ' placeholder="' . htmlspecialchars($row->placeholder) . '"' : '')
                  .       ($row->required ? ' required="required"' : '');
            $temp2 = '<input type="' . $row->datatype . '"'
                   .       ' name="' . htmlspecialchars($row->urn) . '@confirm' . ($row->multiple ? '[]' : '') . '"'
                   .       (!$row->multiple ? ' id="' . htmlspecialchars($row->urn) . '@confirm"' : '')
                   .       ($row->maxlength ? ' maxlength="' . (int)$row->maxlength . '"' : '')
                   .       ($row->placeholder ? ' placeholder="' . htmlspecialchars($row->placeholder) . '"' : '')
                   .       ($row->required ? ' required="required"' : '');
            if ($row->multiple) {
                echo '<div class="jsFieldContainer">';
                for ($i = 0; ($i < count($DATA[$row->urn])) || (($i < 1) && $row->multiple && $row->required); $i++) {
                    echo '<div class="jsField">' . 
                            $temp . ' /> <br />' . $temp2 . ' /> ' .
                            '<span class="icon cms-move" ' . ((count($DATA[$row->urn]) <= 1) ? 'style="display: none"' : '') . ' title="' . MOVE . '"></span>' .
                            '<a href="#" class="jsDeleteField icon system delete" ' . ($row->required && (count($DATA[$row->urn]) <= 1) ? 'style="display: none"' : '') . ' title="' . DELETE . '"></a>' .
                         '</div>';
                }
                echo  '<div class="jsRepo cms-field_repo">' . $temp . ' disabled="disabled"' . ' /> <br /> ' . $temp2 . ' disabled="disabled"' . ' /> ' .
                        '<span class="icon cms-move" title="' . MOVE . '"></span>' .
                        '<a href="#" class="jsDeleteField icon system delete" title="' . DELETE . '"></a>' .
                      '</div>';
                echo '</div>';
            } else {
                echo $temp . ' /> <br />' . $temp2 . ' />';
            }
            break;
            break;
        default: 
            $temp = '<input type="' . $row->datatype . '"'
                  .       ' name="' . htmlspecialchars($row->urn) . ($row->multiple ? '[]' : '') . '"'
                  .       ($row->min_val ? ' min="' . (float)$row->min_val . '"' : '')
                  .       ($row->max_val ? ' max="' . (float)$row->max_val . '"' : '')
                  .       (!$row->multiple ? ' id="' . htmlspecialchars($row->urn) . '"' : '')
                  .       ($row->maxlength ? ' maxlength="' . (int)$row->maxlength . '"' : '')
                  .       ($row->placeholder ? ' placeholder="' . htmlspecialchars($row->placeholder) . '"' : '')
                  .       ($row->required ? ' required="required"' : '');
            if ($row->multiple) {
                echo '<div class="jsFieldContainer">';
                for ($i = 0; ($i < count($DATA[$row->urn])) || (($i < 1) && $row->multiple && $row->required); $i++) {
                    echo '<div class="jsField">' . 
                            $temp . ' value="' . htmlspecialchars(isset($DATA[$row->urn][$i]) ? (string)$DATA[$row->urn][$i] : '') . '"' . ' /> ' .
                            '<span class="icon cms-move" ' . ((count($DATA[$row->urn]) <= 1) ? 'style="display: none"' : '') . ' title="' . MOVE . '"></span>' .
                            '<a href="#" class="jsDeleteField icon system delete" ' . ($row->required && (count($DATA[$row->urn]) <= 1) ? 'style="display: none"' : '') . ' title="' . DELETE . '"></a>' .
                         '</div>';
                }
                echo  '<div class="jsRepo cms-field_repo">' . $temp . ' disabled="disabled"' . ' /> ' .
                        '<span class="icon cms-move" title="' . MOVE . '"></span>' .
                        '<a href="#" class="jsDeleteField icon system delete" title="' . DELETE . '"></a>' .
                      '</div>';
                echo '</div>';
            } else {
                echo $temp . ' value="' . htmlspecialchars(isset($DATA[$row->urn]) ? (string)$DATA[$row->urn] : '') . '"' . ' />';
            }
            break;
    }
    if ($row->multiple && !in_array($row->datatype, array('checkbox'))) {
        echo '<div><a href="#" class="jsAddField">' . ADD . '</a></div>';
    }
};