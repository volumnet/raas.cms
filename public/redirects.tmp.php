<?php
/**
 * Группа полей - таблица редиректов
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FieldSet;

/**
 * Отображает группу полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function(FieldSet $fieldSet) use (&$_RAASForm_Options) {

    $DATA = $fieldSet->Form->DATA;
    include Application::i()->view->context->tmp('/field.inc.php');
    ?>
    <table class="table table-striped table-condensed" data-role="raas-repo-block">
      <thead>
        <tr>
          <?php foreach ($fieldSet->children as $field) {
              if ($field->type != 'hidden') { ?>
                  <th>
                    <?php
                    echo htmlspecialchars($field->caption);
                    if ($field->name == 'redirect_rx') { ?>
                        <a class="btn" href="#" rel="popover" data-html="true" data-content="<?php echo ViewSub_Dev::i()->_('REGULAR_EXPRESSION')?>">
                          <i class="fa fa-question-circle"></i>
                        </a>
                    <?php } ?>
                  </th>
              <?php }
          } ?>
        </tr>
      </thead>
      <tbody data-role="raas-repo-container">
        <?php foreach ((array)$DATA['redirect_id'] as $i => $temp) { ?>
            <tr data-role="raas-repo-element">
              <?php
              $j = 0;
              foreach ($fieldSet->children as $field) {
                  if ($field->type != 'hidden') { ?>
                      <td>
                        <?php if (!$j++) {
                            $idField = $fieldSet->children['redirect_id']; ?>
                            <input type="<?php echo htmlspecialchars($idField->type)?>" name="<?php echo htmlspecialchars($idField->name)?>[]" value="<?php echo (int)$DATA[$idField->name][$i]?>" />
                        <?php } ?>
                        <input
                            type="<?php echo htmlspecialchars($field->type ?: 'text')?>"
                            <?php echo $field->classname ? ' class="' . htmlspecialchars($field->classname) . '"' : ''?>
                            name="<?php echo htmlspecialchars($field->name)?>[]"
                            <?php if ($field->type == 'checkbox') { ?>
                                value="1"
                                <?php echo (int)$DATA[$field->name][$i] ? 'checked="checked"' : ''?>
                            <?php } else { ?>
                                value="<?php echo htmlspecialchars($DATA[$field->name][$i])?>"
                            <?php } ?>
                        />
                      </td>
                  <?php }
              }
              ?>
              <td>
                <a href="#" class="close" data-role="raas-repo-del">&times;</a>
              </td>
            </tr>
        <?php } ?>
      </tbody>
      <tbody>
        <tr data-role="raas-repo">
          <?php
          $j = 0;
          foreach ($fieldSet->children as $field) {
              if ($field->type != 'hidden') { ?>
                  <td>
                    <?php if (!$j++) {
                        $idField = $fieldSet->children['redirect_id']; ?>
                        <input type="<?php echo htmlspecialchars($idField->type)?>" name="<?php echo htmlspecialchars($idField->name)?>[]" disabled="disabled" />
                    <?php } ?>
                    <input
                        type="<?php echo htmlspecialchars($field->type ?: 'text')?>"
                        <?php echo $field->classname ? ' class="' . htmlspecialchars($field->classname) . '"' : ''?>
                        name="<?php echo htmlspecialchars($field->name)?>[]"
                        <?php if ($field->type == 'checkbox') { ?>
                            value="1"
                        <?php } ?>
                        disabled="disabled"
                    />
                  </td>
              <?php }
          }
          ?>
          <td>
            <a href="#" class="close" data-role="raas-repo-del">&times;</a>
          </td>
        </tr>
        <tr>
          <td colspan="3"></td>
          <td>
            <input type="button" class="btn" value="<?php echo ADD?>" data-role="raas-repo-add" />
          </td>
        </tr>
      </tbody>
    </table>
    <?php
};
