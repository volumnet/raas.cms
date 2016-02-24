<?php include \RAAS\CMS\ViewSub_Main::i()->tmp('/table.inc.php'); ?>
<?php if ((array)$Table->Set || ($Table->emptyHeader && $Table->header)) { ?>
    <form action="" method="post">
      <table<?php echo $_RAASTable_Attrs($Table)?>>
        <?php if ($Table->header) { ?>
            <thead>
              <tr>
                <?php if ($Item->id) { ?>
                    <th>
                      <?php if ($Table->meta['allValue']) { ?>
                          <input type="checkbox" data-role="checkbox-all" value="<?php echo htmlspecialchars($Table->meta['allValue'])?>">
                      <?php } ?>
                    </th>
                <?php } ?>
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
                  include \RAAS\CMS\Package::i()->view->context->tmp($Item->id ? 'multirow.inc.php' : '/row.inc.php');
                  if ($row->template) {
                      include \RAAS\Application::i()->view->context->tmp($row->template);
                  }
                  $_RAASTable_Row($row, $i);
                  ?>
              <?php } ?>
            </tbody>
        <?php } ?>
        <tfoot>
          <?php if ($Table->meta['realizedCounter']) { ?>
              <tr>
                <?php if ($Item->id) { ?>
                    <td colspan="2"><?php echo rowContextMenu($Table->meta['allContextMenu'], \RAAS\Application::i()->view->context->_('WITH_SELECTED'), '', 'btn-mini')?></td>
                <?php } ?>
                <td colspan="<?php echo 1 + 2 * (int)(!$Item->id)?>">&nbsp;</td>
                <td><input type="submit" class="btn" value="<?php echo DO_UPDATE?>" /></td>
                <td>&nbsp;</td>
              </tr>
          <?php } else { ?>
              <tr>
                <td><?php echo rowContextMenu($Table->meta['allContextMenu'], \RAAS\Application::i()->view->context->_('WITH_SELECTED'), '', 'btn-mini')?></td>
              </tr>
          <?php } ?>
        </tfoot>
      </table>
    </form>
<?php } ?>
<?php if (!(array)$Table->Set && $Table->emptyString) { ?>
  <p><?php echo htmlspecialchars($Table->emptyString)?></p>
<?php } ?>
