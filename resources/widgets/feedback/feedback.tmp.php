<?php
/**
 * Виджет формы обратной связи (размещаемой в тексте)
 * @param Page $Page Текущая страница
 * @param Block_Form $Block Текущий блок
 * @param Feedback $Item Уведомление формы
 * @param Form $Form Форма
 */
namespace RAAS\CMS;

if ($_POST['AJAX'] && ($Item instanceof Feedback)) {
    $result = array();
    if ($success[(int)$Block->id]) {
        $result['success'] = 1;
    }
    if ($localError) {
        $result['localError'] = $localError;
    }
    ob_clean();
    echo json_encode($result);
    exit;
} else { ?>
    <div class="feedback">
      <?php if ($Block->name[0] != '.') { ?>
          <div class="feedback__title">
            <?php echo htmlspecialchars($Block->name)?>
          </div>
      <?php } ?>
      <form class="form-horizontal" data-role="raas-ajaxform" action="#feedback" method="post" enctype="multipart/form-data">
        <?php include Package::i()->resourcesDir . '/form2.inc.php'?>
        <div data-role="notifications" <?php echo ($success[(int)$Block->id] || $localError) ? '' : 'style="display: none"'?>>
          <div class="alert alert-success" <?php echo ($success[(int)$Block->id]) ? '' : 'style="display: none"'?>>
            <?php echo FEEDBACK_SUCCESSFULLY_SENT?>
          </div>
          <div class="alert alert-danger" <?php echo ($localError) ? '' : 'style="display: none"'?>>
            <ul>
              <?php foreach ((array)$localError as $key => $val) { ?>
                  <li>
                    <?php echo htmlspecialchars($val)?>
                  </li>
              <?php } ?>
            </ul>
          </div>
        </div>

        <div data-role="feedback-form" <?php echo $success[(int)$Block->id] ? 'style="display: none"' : ''?>>
          <p>
            <small class="text-muted">
              <?php echo ASTERISK_MARKED_FIELDS_ARE_REQUIRED?>
            </small>
          </p>
          <?php if ($Form->signature) { ?>
              <input type="hidden" name="form_signature" value="<?php echo htmlspecialchars($Form->getSignature($Block))?>" />
          <?php } ?>
          <?php if ($Form->antispam == 'hidden' && $Form->antispam_field_name) { ?>
              <textarea autocomplete="off" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" style="position: absolute; left: -9999px"><?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?></textarea>
          <?php } ?>
          <?php foreach ($Form->fields as $field) { ?>
              <?php if ($field->urn == 'agree') { ?>
                  <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-md-offset-2">
                      <label>
                        <?php $getField($field, $DATA);?>
                        <a href="/privacy/" target="_blank">
                          <?php echo htmlspecialchars($field->name)?>
                        </a>
                      </label>
                    </div>
                  </div>
              <?php } elseif ($field->datatype == 'checkbox') { ?>
                  <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-md-offset-2">
                      <label>
                        <?php $getField($field, $DATA);?>
                        <?php echo htmlspecialchars($field->name . ($field->required ? '*' : ''))?>
                      </label>
                    </div>
                  </div>
              <?php } else { ?>
                  <div class="form-group">
                    <label<?php echo !$field->multiple ? ' for="' . htmlspecialchars($field->getHTMLId($Block)) . '"' : ''?> class="control-label col-sm-3 col-md-2">
                      <?php echo htmlspecialchars($field->name . ($field->required ? '*' : ''))?>
                    </label>
                    <div class="col-sm-9 col-md-4">
                      <?php $getField($field, $DATA);?>
                    </div>
                  </div>
              <?php } ?>
          <?php } ?>
          <?php if ($Form->antispam == 'captcha' && $Form->antispam_field_name) { ?>
              <div class="form-group">
                <label for="<?php echo htmlspecialchars($Form->antispam_field_name . '_' . $Block->id)?>" class="control-label col-sm-3 col-md-2">
                  <?php echo CAPTCHA?>
                </label>
                <div class="col-sm-9 col-md-4">
                  <img src="/assets/kcaptcha/?<?php echo session_name() . '=' . session_id()?>" /><br />
                  <input type="text" autocomplete="off" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" id="<?php echo htmlspecialchars($Form->antispam_field_name . '_' . $Block->id)?>" />
                </div>
              </div>
          <?php } ?>
          <div class="feedback__controls col-sm-offset-3 col-md-offset-2">
            <button class="feedback__submit btn btn-primary" type="submit">
              <?php echo SEND?>
            </button>
          </div>
        </div>
      </form>
    </div>
<?php } ?>
