<?php
/**
 * Таблица с формой (редактирование типа материалов или формы)
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Form as RAASForm;
use RAAS\FormTab;

include $VIEW->tmp('/form.inc.php'); ?>
<form<?php echo $_RAASForm_Attrs($Form)?>>
  <?php
  if (array_filter(
      (array)$Form->children,
      function ($x) {
          return $x instanceof FormTab;
      }
  )) {
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
            <input type="submit" name="@cancel" class="btn" value="<?php echo $Form->resetCaption ? htmlspecialchars($Form->resetCaption) : RESET?>" />
            <?php echo _AND?>
            <select name="@oncommit">
              <?php
              $_RAASForm_Actions = [];
              $_RAASForm_Actions[RAASForm::ONCOMMIT_EDIT] = ONCOMMIT_EDIT;
              $_RAASForm_Actions[RAASForm::ONCOMMIT_RETURN] = ONCOMMIT_RETURN;
              if (!$Form->Item->id) {
                  $_RAASForm_Actions[RAASForm::ONCOMMIT_NEW] = ONCOMMIT_NEW;
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
  if ($Item->id) { ?>
      <ul class="nav nav-tabs" id="myTab">
        <li class="active">
          <a href="#common" data-toggle="tab">
            <?php echo Application::i()->view->context->_('FIELDS')?>
          </a>
        </li>
        <?php if ($Item->children) { ?>
            <li>
              <a href="#subtypes" data-toggle="tab">
                <?php echo Application::i()->view->context->_('CHILD_TYPES')?>
              </a>
            </li>
        <?php } ?>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="common">
          <?php
          include ViewSub_Main::i()->tmp('/table.inc.php');
          if ((array)$Table->Set || ($Table->emptyHeader && $Table->header)) { ?>
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
                            include Application::i()->view->context->tmp('/column.inc.php');
                            if ($col->template) {
                                include Application::i()->view->context->tmp($col->template);
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
                          include Package::i()->view->tmp('multirow.inc.php');
                          if ($row->template) {
                              include Application::i()->view->context->tmp($row->template);
                          }
                          $_RAASTable_Row($row, $i);
                          ?>
                      <?php } ?>
                    </tbody>
                <?php } ?>
                <tfoot>
                  <tr>
                    <td colspan="2">
                      <?php echo rowContextMenu(
                          $Table->meta['allContextMenu'],
                          Application::i()->view->context->_('WITH_SELECTED'),
                          '',
                          'btn-mini'
                      )?>
                    </td>
                    <td colspan="<?php echo (count($Table->columns) - 3)?>">
                      &nbsp;
                    </td>
                    <td>
                      <input type="submit" class="btn btn-small btn-default" style="width: 70px; padding: 2px 0;" value="<?php echo DO_UPDATE?>" />
                    </td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
          <?php } ?>
        </div>
        <?php if ($Item->children) {
            $Table = $childrenTable; ?>
            <div class="tab-pane" id="subtypes">
              <?php include ViewSub_Main::i()->tmp('/table.tmp.php'); ?>
            </div>
        <?php } ?>
      </div>
  <?php } ?>
</form>
