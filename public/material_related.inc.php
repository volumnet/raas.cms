<?php
/**
 * Вкладка "Связанные материалы" в редактировании материалов
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FormTab;

/**
 * Отображает вкладку типа материалов
 * @param FormTab $formTab Вкладка для отображения
 */
$_RAASForm_FormTab = function (FormTab $formTab) use (
    &$_RAASForm_Form_Tabbed,
    &$_RAASForm_Form_Plain,
    &$_RAASForm_Attrs
) {
    $Table = $formTab->meta['Table'];
    $mtype = $formTab->meta['mtype'];
    $pagesHash = '_' . $mtype->urn;
    include ViewSub_Main::i()->tmp('/table.inc.php');
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
                    include Package::i()->view->context->tmp('multirow.inc.php');
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
            </tr>
          </tfoot>
        </table>
    <?php } ?>
    <?php if (!(array)$Table->Set && $Table->emptyString) { ?>
      <p><?php echo htmlspecialchars($Table->emptyString)?></p>
    <?php } ?>
    <?php
    if ($Table->Set &&
        ($Pages = $Table->Pages) &&
        ($pagesVar = $Table->pagesVar)
    ) {
        include ViewSub_Main::i()->tmp('/pages.tmp.php');
    }
};
