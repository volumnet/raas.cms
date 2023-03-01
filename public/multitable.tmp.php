<?php
/**
 * Таблица с множественным выбором
 */
namespace RAAS\CMS;

use RAAS\Application;

include $VIEW->tmp('/table.inc.php');

if ((array)$Table->Set || ($Table->emptyHeader && $Table->header)) { ?>
  <table<?php echo $_RAASTable_Attrs($Table)?>>
    <?php if ($Table->header) { ?>
        <thead>
          <tr>
            <th>
              <?php if ($Table->meta['allValue'] ?? false) { ?>
                  <input type="checkbox" data-role="checkbox-all" value="<?php echo htmlspecialchars($Table->meta['allValue'])?>">
              <?php } ?>
            </th>
            <?php foreach ($Table->columns as $key => $col) {
                include Application::i()->view->context->tmp('/column.inc.php');
                if ($col->template) {
                    include Application::i()->view->context->tmp($col->template);
                }
                $_RAASTable_Header($col, $key);
            } ?>
          </tr>
        </thead>
    <?php }
    if ((array)$Table->Set) { $rows = $Table->rows; ?>
        <tbody>
          <?php for ($i = 0; $i < count($rows); $i++) {
              $row = $rows[$i];
              include Package::i()->view->tmp('multirow.inc.php');
              if ($row->template) {
                  include Application::i()->view->context->tmp($row->template);
              }
              $_RAASTable_Row($row, $i);
          } ?>
        </tbody>
        <?php if ($Table->meta['allContextMenu'] ?? false) { ?>
            <tfoot>
              <tr>
                <td colspan="2">
                  <all-context-menu :menu="<?php echo htmlspecialchars(json_encode(getMenu($Table->meta['allContextMenu'])))?>"></all-context-menu>
                </td>
                <td colspan="<?php echo count($Table->columns) - 1?>"></td>
              </tr>
            </tfoot>
        <?php }
    } ?>
  </table>
<?php }
if (!(array)$Table->Set && $Table->emptyString) { ?>
  <p><?php echo htmlspecialchars($Table->emptyString)?></p>
<?php }
if ($Table->Set && ($Pages = $Table->Pages) && ($pagesVar = $Table->pagesVar)) {
    include $VIEW->tmp('/pages.tmp.php')?>
<?php } ?>
