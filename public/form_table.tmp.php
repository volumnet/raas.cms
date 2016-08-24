<?php include $VIEW->tmp('/form.inc.php'); ?>
<form<?php echo $_RAASForm_Attrs($Form)?>>
  <?php
  if (array_filter((array)$Form->children, function($x) { return $x instanceof \RAAS\FormTab; })) {
      $_RAASForm_Form_Tabbed($Form->children);
  } else {
      $_RAASForm_Form_Plain($Form->children);
  }
  ?>
  <div class="form-horizontal">
    <div class="control-group">
      <div class="controls">
          <input type="submit" class="btn btn-primary" value="<?php echo $Form->submitCaption ? htmlspecialchars($Form->submitCaption) : SAVE?>" />
        <?php if ($Form->Item && $Form->actionMenu) { ?>
            <input type="submit" name="@cancel" class="btn" value="<?php echo $Form->resetCaption ? htmlspecialchars($Form->resetCaption) : RESET?>" /> <?php echo _AND?>
            <select name="@oncommit">
              <?php
              $_RAASForm_Actions = array();
              $_RAASForm_Actions[\RAAS\Form::ONCOMMIT_EDIT] = ONCOMMIT_EDIT;
              $_RAASForm_Actions[\RAAS\Form::ONCOMMIT_RETURN] = ONCOMMIT_RETURN;
              if (!$Form->Item->id) {
                  $_RAASForm_Actions[\RAAS\Form::ONCOMMIT_NEW] = ONCOMMIT_NEW;
              }
              foreach ($_RAASForm_Actions as $key => $val) {
                  ?>
                  <option value="<?php echo (int)$key?>" <?php echo (isset($Form->DATA['@oncommit']) && $Form->DATA['@oncommit'] == $key) ? 'selected="selected"' : ''?>>
                    <?php echo htmlspecialchars($val)?>
                  </option>
              <?php } ?>
            </select>
        <?php } else { ?>
            <input type="reset" class="btn" value="<?php echo $Form->resetCaption ? htmlspecialchars($Form->resetCaption) : RESET?>" />
        <?php } ?>
      </div>
    </div>
  </div>
  <?php
  if ($Item->id) {
      include \RAAS\CMS\ViewSub_Main::i()->tmp('/table.inc.php');
      if ((array)$Table->Set || ($Table->emptyHeader && $Table->header)) {
          ?>
          <table<?php echo $_RAASTable_Attrs($Table)?>>
            <?php if ($Table->header) { ?>
                <thead>
                  <tr>
                    <th>
                      <?php if ($Table->meta['allValue']) { ?>
                          <input type="checkbox" data-role="checkbox-all" value="<?php echo htmlspecialchars($Table->meta['allValue'])?>">
                      <?php } ?>
                    </th>
                    <?php
                    foreach ($Table->columns as $key => $col) {
                        include \RAAS\Application::i()->view->context->tmp('/column.inc.php');
                        if ($col->template) {
                            include \RAAS\Application::i()->view->context->tmp($col->template);
                        }
                        $_RAASTable_Header($col, $key);
                    }
                    ?>
                  </tr>
                </thead>
            <?php } ?>
            <?php if ((array)$Table->Set) { ?>
                <tbody>
                  <?php
                  for ($i = 0; $i < count($Table->rows); $i++) {
                      $row = $Table->rows[$i];
                      include \RAAS\CMS\Package::i()->view->context->tmp('multirow.inc.php');
                      if ($row->template) {
                          include \RAAS\Application::i()->view->context->tmp($row->template);
                      }
                      $_RAASTable_Row($row, $i);
                      ?>
                  <?php } ?>
                </tbody>
            <?php } ?>
            <tfoot>
              <tr>
                <td colspan="2"><?php echo rowContextMenu($Table->meta['allContextMenu'], \RAAS\Application::i()->view->context->_('WITH_SELECTED'), '', 'btn-mini')?></td>
                <td colspan="<?php echo (count($Table->columns) - 3)?>">&nbsp;</td>
                <td><input type="submit" class="btn btn-small btn-default" style="width: 70px; padding: 2px 0;" value="<?php echo DO_UPDATE?>" /></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
          <?php
      }
  }
  ?>
</form>
