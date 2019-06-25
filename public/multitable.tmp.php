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
              <?php if ($Table->meta['allValue']) { ?>
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
    if ((array)$Table->Set) { ?>
        <tbody>
          <?php for ($i = 0; $i < count($Table->rows); $i++) {
              $row = $Table->rows[$i];
              include Package::i()->view->tmp('multirow.inc.php');
              if ($row->template) {
                  include Application::i()->view->context->tmp($row->template);
              }
              $_RAASTable_Row($row, $i);
          } ?>
        </tbody>
        <?php if ($Table->meta['allContextMenu']) { ?>
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
