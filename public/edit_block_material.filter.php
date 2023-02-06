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
$_RAASForm_FieldSet = function (FieldSet $fieldSet) use (
    &$_RAASForm_Form_Tabbed,
    &$_RAASForm_Form_Plain
) {
    $DATA = $fieldSet->Form->DATA;
    $CONTENT = $fieldSet->Form->meta['CONTENT'];
    ?>
    <fieldset>
      <legend><?php echo htmlspecialchars($fieldSet->caption)?></legend>
      <table class="table table-striped table-condensed" data-role="raas-repo-block">
        <thead>
          <tr>
            <th class="span4"><?php echo \CMS\GET_VARIABLE?></th>
            <th class="span3"><?php echo \CMS\RELATION?></th>
            <th class="span3"><?php echo \CMS\MATERIAL_FIELD?></th>
            <th></th>
          </tr>
        </thead>
        <tbody data-role="raas-repo-container">
          <?php foreach ((array)($DATA['filter_var'] ?? []) as $i => $temp) { ?>
              <tr data-role="raas-repo-element">
                <td>
                  <input type="text" name="filter_var[]" value="<?php echo htmlspecialchars($DATA['filter_var'][$i])?>" class="span2" />
                </td>
                <td>
                  <select name="filter_relation[]" class="span3">
                    <?php foreach (Block_Material::$filterRelations as $key => $val) { ?>
                        <option value="<?php echo htmlspecialchars($key)?>" <?php echo $DATA['filter_relation'][$i] == $key ? 'selected="selected"' : ''?>>
                          <?php echo constant('CMS\\' . $val)?>
                        </option>
                    <?php } ?>
                  </select>
                </td>
                <td>
                  <select name="filter_field[]" class="jsMaterialTypeField span2">
                    <?php foreach ((array)($CONTENT['fields'] ?? []) as $row) { ?>
                        <option value="<?php echo htmlspecialchars($row['value'])?>" <?php echo $DATA['filter_field'][$i] == $row['value'] ? 'selected="selected"' : ''?>>
                          <?php echo htmlspecialchars($row['caption'])?>
                        </option>
                    <?php } ?>
                  </select>
                </td>
                <td>
                  <a href="#" class="close" data-role="raas-repo-del">
                    &times;
                  </a>
                </td>
              </tr>
          <?php } ?>
        </tbody>
        <tbody>
          <tr data-role="raas-repo">
            <td>
              <input type="text" name="filter_var[]" value="" class="span3" disabled="disabled" />
            </td>
            <td>
              <select name="filter_relation[]" class="span2" disabled="disabled">
                <?php foreach (Block_Material::$filterRelations as $key => $val) { ?>
                    <option value="<?php echo htmlspecialchars($key)?>">
                      <?php echo constant('CMS\\' . $val)?>
                    </option>
                <?php } ?>
              </select>
            </td>
            <td>
              <select name="filter_field[]" class="jsMaterialTypeField span2" disabled="disabled">
                <?php foreach ($CONTENT['fields'] as $row) { ?>
                    <option value="<?php echo htmlspecialchars($row['value'])?>">
                      <?php echo htmlspecialchars($row['caption'])?>
                    </option>
                <?php } ?>
              </select>
            </td>
            <td>
              <a href="#" class="close" data-role="raas-repo-del">
                &times;
              </a>
            </td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>
              <input type="button" class="btn" value="<?php echo ADD?>" data-role="raas-repo-add" />
            </td>
          </tr>
        </tbody>
      </table>
    </fieldset>
<?php } ?>
