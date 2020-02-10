<?php
/**
 * Виджет формы обратной связи (всплывающее окно)
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
    <!--noindex-->
    <div class="feedback-modal">
      <div id="<?php echo htmlspecialchars($Form->urn)?>_modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade">
        <div class="modal-dialog">
          <div class="modal-content">
            <form data-role="raas-ajaxform" action="#feedback" method="post" enctype="multipart/form-data">
              <div class="modal-header">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close">&times;</button>
                <div class="h4 modal-title">
                  <?php echo htmlspecialchars($Block->name)?>
                </div>
              </div>
              <div class="modal-body">
                <div class="form-horizontal">
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
                    <p class="feedback-modal__required-fields">
                      <?php echo ASTERISK_MARKED_FIELDS_ARE_REQUIRED?>
                    </p>
                    <?php if ($Form->signature) { ?>
                        <input type="hidden" name="form_signature" value="<?php echo md5('form' . (int)$Form->id . (int)$Block->id)?>" />
                    <?php }
                    if ($Form->antispam == 'hidden' && $Form->antispam_field_name) { ?>
                        <input type="text" autocomplete="new-password" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" value="<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>" style="position: absolute; left: -9999px" />
                    <?php }
                    foreach ($Form->fields as $row) {
                        if ($row->urn == 'agree') { ?>
                            <div class="form-group">
                              <div class="col-sm-9 col-sm-offset-3">
                                <label class="checkbox">
                                  <?php $getField($row, $DATA);?>
                                  <a href="/privacy/" target="_blank">
                                    <?php echo htmlspecialchars($row->name)?>
                                  </a>
                                </label>
                              </div>
                            </div>
                        <?php } elseif ($row->datatype == 'checkbox') { ?>
                            <div class="form-group">
                              <div class="col-sm-9 col-sm-offset-3">
                                <label class="checkbox">
                                  <?php $getField($row, $DATA);?>
                                  <?php echo htmlspecialchars($row->name . ($row->required ? '*' : ''))?>
                                </label>
                              </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group">
                              <label<?php echo !$row->multiple ? ' for="' . htmlspecialchars($row->urn . $row->id . '_' . $Block->id) . '"' : ''?> class="control-label col-sm-3">
                                <?php echo htmlspecialchars($row->name . ($row->required ? '*' : ''))?>:
                              </label>
                              <div class="col-sm-9">
                                <?php $getField($row, $DATA); ?>
                              </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($Form->antispam == 'captcha' && $Form->antispam_field_name) { ?>
                        <div class="form-group">
                          <label for="<?php echo htmlspecialchars($Form->antispam_field_name . '_' . $Block->id)?>" class="control-label col-sm-3">
                            <?php echo CAPTCHA?>
                          </label>
                          <div class="col-sm-9">
                            <img src="/assets/kcaptcha/?<?php echo session_name() . '=' . session_id()?>" /><br />
                            <input type="text" autocomplete="new-password" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" id="<?php echo htmlspecialchars($Form->antispam_field_name . '_' . $Block->id)?>" />
                          </div>
                        </div>
                    <?php } ?>
                    <div class="feedback-modal__controls">
                      <button type="button" class="feedback__cancel btn btn-default" data-dismiss="modal">
                        <?php echo CANCEL?>
                      </button>
                      <button class="feedback__submit btn btn-primary" type="submit">
                        <?php echo SEND?>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!--/noindex-->
<?php } ?>
