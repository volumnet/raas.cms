<?php 
$_RAASForm_FormTab = function(\RAAS\FormTab $FormTab) use (&$_RAASForm_Form_Tabbed, &$_RAASForm_Form_Plain, &$_RAASForm_Attrs) { 
    $DATA = $FormTab->Form->DATA;
    $showGroups = function($node, $value) use (&$showGroups) {
        static $level = 0;
        $text = '';
        foreach ($node->children as $row) {
            $text .= '<option value="' . (int)$row->id . '" ' . ($row->id == $value ? 'selected="selected"' : '') . '>' . 
                        str_repeat('&nbsp;', 3 * $level) . htmlspecialchars($row->name) . 
                     '</option>';
            $level++;
            $text .= $showGroups($row, $value);
            $level--;
        }
        return $text;
    };
    include \RAAS\Application::i()->view->context->tmp('/field.inc.php');
    ?>
    <div data-role="raas-cms-access">
      <table class="table table-striped table-condensed" data-role="raas-repo-block">
        <tbody data-role="raas-repo-container">
          <?php foreach ((array)$DATA['access_id'] as $i => $temp) { ?>
              <tr data-role="raas-repo-element">
                <td>
                  <input type="hidden" name="access_id[]" value="<?php echo (int)$DATA['access_id'][$i]?>" />
                  <select name="access_allow[]">
                    <?php foreach($FormTab->children['access_allow']->children as $row) { ?>
                        <option value="<?php echo (int)$row->value?>" <?php echo $DATA['access_allow'][$i] == $row->value ? 'selected="selected"' : ''?>>
                          <?php echo htmlspecialchars($row->caption)?>
                        </option>
                    <?php } ?>
                  </select>
                </td>
                <td>
                  <select name="access_to_type[]">
                    <?php foreach($FormTab->children['access_to_type']->children as $row) { ?>
                        <option value="<?php echo (int)$row->value?>" <?php echo ($DATA['access_to_type'][$i] == $row->value ? 'selected="selected"' : '') . ' ' . ($row->{'data-show'} ? 'data-show="' . htmlspecialchars($row->{'data-show'}) . '"' : '')?>>
                          <?php echo htmlspecialchars($row->caption)?>
                        </option>
                    <?php } ?>
                  </select>
                </td>
                <td>
                  <div data-role="access-uid" <?php echo $DATA['access_to_type'][$i] == \RAAS\CMS\CMSAccess::TO_USER ? '' : 'style="display: none"'?>>
                    <?php
                    $u = new \RAAS\CMS\User($DATA['access_uid'][$i]);
                    $name = $u->login;
                    if ($u->full_name) {
                        $name = $u->full_name . ' (' . $u->login . ')';
                    } elseif ($u->name) {
                        $name = $u->name . ' (' . $u->login . ')';
                    } elseif ($u->last_name || $u->first_name || $u->second_name) {
                        $name = trim($u->last_name . ' ' . $u->first_name . ' ' . $u->second_name . ' (' . $u->login . ')');
                    }
                    ?>
                    <input type="hidden" name="access_uid[]" value="<?php echo $DATA['access_uid'][$i]?>" <?php echo $u->id ? 'data-user-name="' . htmlspecialchars($name) . '" data-user-id="' . (int)$u->id . '"' : ''?> />
                  </div>
                  <div data-role="access-gid" <?php echo $DATA['access_to_type'][$i] == \RAAS\CMS\CMSAccess::TO_GROUP ? '' : 'style="display: none"'?>>
                    <select name="access_gid[]"><?php echo $showGroups(new \RAAS\CMS\Group(), $DATA['access_gid'][$i])?></select>
                  </div>
                </td>
                <td><a href="#" class="close" data-role="raas-repo-del">&times;</a></td>
              </tr>
          <?php } ?>
        </tbody>
        <tbody>
          <tr data-role="raas-repo">
            <td>
              <input type="hidden" name="access_id[]" disabled="disabled" />
              <select name="access_allow[]" disabled="disabled">
                <?php foreach($FormTab->children['access_allow']->children as $row) { ?>
                    <option value="<?php echo (int)$row->value?>">
                      <?php echo htmlspecialchars($row->caption)?>
                    </option>
                <?php } ?>
              </select>
            </td>
            <td>
              <select name="access_to_type[]" disabled="disabled">
                <?php foreach($FormTab->children['access_to_type']->children as $row) { ?>
                    <option value="<?php echo (int)$row->value?>" <?php echo $row->{'data-show'} ? 'data-show="' . htmlspecialchars($row->{'data-show'}) . '"' : ''?>>
                      <?php echo htmlspecialchars($row->caption)?>
                    </option>
                <?php } ?>
              </select>
            </td>
            <td>
              <div data-role="access-uid" style="display: none"><input type="hidden" name="access_uid[]" disabled="disabled" /></div>
              <div data-role="access-gid" style="display: none">
                <select name="access_gid[]"><?php echo $showGroups(new \RAAS\CMS\Group())?></select>
              </div>
            </td>
            <td><a href="#" class="close" data-role="raas-repo-del">&times;</a></td>
          </tr>
          <tr>
            <td class="span3"></td>
            <td class="span3"></td>
            <td class="span3"></td>
            <td class="span3"><input type="button" class="btn" value="<?php echo ADD?>" data-role="raas-repo-add" /></td>
          </tr>
        </tbody>
      </table>
    </div>
    <script src="<?php echo \RAAS\Application::i()->view->context->publicURL?>/cmsaccess.js"></script>
<?php } ?>