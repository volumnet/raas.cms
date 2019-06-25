<?php
/**
 * Строка таблицы с возможностью отметки флажком (выбор множества строк)
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Row;

/**
 * Отображает строку
 * @param Row $row Строка для отображения
 * @param int $num Позиция строки в списке
 */
$_RAASTable_Row = function (
    Row $row,
    $num
) use (
    &$_RAASTable_Attrs,
    &$_RAASTable_Cell
) {
    $Table = $row->Parent;
    ?>
    <tr<?php echo $_RAASTable_Attrs($row)?>>
      <td>
        <?php if ($row->source->id) { ?>
            <input type="checkbox" data-role="checkbox-row" value="<?php echo (int)$row->source->id?>">
        <?php } ?>
      </td>
      <?php foreach ($Table->columns as $key => $col) {
          include Application::i()->view->context->tmp('/column.inc.php');
          if ($col->template) {
              include Application::i()->view->context->tmp($col->template);
          }
          $var = null;
          if ($f = $col->callback) {
              $var = (string)$f($row->source, $num);
          } elseif (is_object($row->source) && isset($row->source->$key)) {
              $var = $row->source->$key;
          } elseif (is_array($row->source) && isset($row->source[$key])) {
              $var = $row->source[$key];
          }
          $_RAASTable_Cell($col, (string)$var);
      } ?>
    </tr>
<?php };
