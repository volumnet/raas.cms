<?php include \RAAS\CMS\ViewSub_Main::i()->tmp('/table.inc.php'); ?>
<?php if ((array)$Table->Set || ($Table->emptyHeader && $Table->header)) { ?>
    <form action="#_<?php echo htmlspecialchars($mtype->urn)?>" method="post">
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
    </form>
    <?php 
} 
if (!(array)$Table->Set && $Table->emptyString) { 
    ?>
    <p><?php echo htmlspecialchars($Table->emptyString)?></p>
    <?php 
}
if ($Table->Set && ($Pages = $Table->Pages) && ($pagesVar = $Table->pagesVar)) { 
    include \RAAS\CMS\ViewSub_Main::i()->tmp('/pages.tmp.php');
}