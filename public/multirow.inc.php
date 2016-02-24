<?php
$_RAASTable_Row = function (\RAAS\Row $Row, $num) use (&$_RAASTable_Attrs, &$_RAASTable_Cell) {
    $Table = $Row->Parent;
    ?>
    <tr<?php echo $_RAASTable_Attrs($Row)?>>
      <td>
        <?php if ($Row->source->id) { ?>
            <input type="checkbox" data-role="checkbox-row" value="<?php echo (int)$Row->source->id?>">
        <?php } ?>
      </td>
      <?php 
      foreach ($Table->columns as $key => $col) { 
          include \RAAS\Application::i()->view->context->tmp('/column.inc.php');
          if ($col->template) {
              include \RAAS\Application::i()->view->context->tmp($col->template);
          }
          $var = null;
          if ($f = $col->callback) {
              $var = (string)$f($Row->source, $num);
          } elseif (is_object($Row->source) && isset($Row->source->$key)) {
              $var = $Row->source->$key;
          } elseif (is_array($Row->source) && isset($Row->source[$key])) {
              $var = $Row->source[$key];
          }
          $_RAASTable_Cell($col, (string)$var);
      } 
      ?>
    </tr>
    <?php
};