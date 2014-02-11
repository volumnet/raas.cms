<?php 
$_RAASForm_FieldSet = function(\RAAS\FieldSet $FieldSet) use (&$_RAASForm_Form_Tabbed, &$_RAASForm_Form_Plain, &$_RAASForm_Control) { 
    $DATA = $FieldSet->Form->DATA;
    $CONTENT = $FieldSet->Form->meta['CONTENT'];
    ?>
    <fieldset>
      <legend><?php echo htmlspecialchars($FieldSet->caption)?></legend>
      <table class="table table-striped table-condensed" data-role="raas-repo-block">
        <thead>
          <tr>
            <th class="span4"><?php echo CMS\VARIABLE_VALUE?></th>
            <th class="span3"><?php echo CMS\MATERIAL_FIELD?></th>
            <th class="span3"><?php echo CMS\SORTING_ORDER?></th>
            <th></th>
          </tr>
          <tr>
            <th class="span4"><?php echo CMS\GET_VARIABLE?></th>
            <td><?php echo $_RAASForm_Control($FieldSet->children['sort_var_name'])?></td>
            <td><?php echo $_RAASForm_Control($FieldSet->children['order_var_name'])?></td>
            <td></td>
          </tr>
          <tr>
            <th class="span4"><?php echo CMS\DEFAULT_SORTING?></th>
            <td><?php echo $_RAASForm_Control($FieldSet->children['sort_field_default'])?></td>
            <td><?php echo $_RAASForm_Control($FieldSet->children['sort_order_default'])?></td>
            <td></td>
          </tr>
        </thead>
        <tbody data-role="raas-repo-container">
          <?php foreach ((array)$DATA['sort_var'] as $i => $temp) { ?>
              <tr data-role="raas-repo-element">
                <td><input type="text" name="sort_var[]" value="<?php echo htmlspecialchars($DATA['sort_var'][$i])?>" class="span3" /></td>
                <td>
                  <select name="sort_field[]" class="jsMaterialTypeField span2">
                    <?php foreach ($CONTENT['fields'] as $row) { ?>
                        <option value="<?php echo htmlspecialchars($row['value'])?>" <?php echo $DATA['sort_field'][$i] == $row['value'] ? 'selected="selected"' : ''?>>
                          <?php echo htmlspecialchars($row['caption'])?>
                        </option>
                    <?php } ?>
                  </select>
                </td>
                <td>
                  <select name="sort_relation[]" class="span2">
                    <?php foreach (\RAAS\CMS\Block_Material::$orderRelations as $key => $val) { ?>
                        <option value="<?php echo htmlspecialchars($key)?>" <?php echo $DATA['sort_relation'][$i] == $key ? 'selected="selected"' : ''?>>
                          <?php echo constant('CMS\\' . $val)?>
                        </option>
                    <?php } ?>
                  </select>
                </td>
                <td><a href="#" class="close" data-role="raas-repo-del">&times;</a></td>
              </tr>
          <?php } ?>
          <tbody>
            <tr data-role="raas-repo">
              <td><input type="text" name="sort_var[]" value="" class="span3" disabled="disabled" /></td>
              <td>
                <select name="sort_field[]" class="span2 jsMaterialTypeField" disabled="disabled">
                  <?php foreach ($CONTENT['fields'] as $row) { ?>
                      <option value="<?php echo htmlspecialchars($row['value'])?>" <?php echo $DATA['filter_field'][$i] == $row['value'] ? 'selected="selected"' : ''?>>
                        <?php echo htmlspecialchars($row['caption'])?>
                      </option>
                  <?php } ?>
                </select>
              </td>
              <td>
                <select name="sort_relation[]" class="span2" disabled="disabled">
                  <?php foreach (\RAAS\CMS\Block_Material::$orderRelations as $key => $val) { ?>
                      <option value="<?php echo htmlspecialchars($key)?>" <?php echo $DATA['filter_relation'][$i] == $key ? 'selected="selected"' : ''?>>
                        <?php echo constant('CMS\\' . $val)?>
                      </option>
                  <?php } ?>
                </select>
              </td>
              <td><a href="#" class="close" data-role="raas-repo-del">&times;</a></td>
            </tr>
            <tr><td></td><td></td><td></td><td><input type="button" class="btn" value="<?php echo ADD?>" data-role="raas-repo-add" /></td></tr>
          </tbody>
        </tbody>
      </table>
    </fieldset>
<?php } ?>