<?php
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
    <div class="order-call-modal">
      <div id="<?php echo htmlspecialchars($Widget->urn)?>" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade">
        <div class="modal-dialog">
          <div class="modal-content">
            <form data-role="raas-ajaxform" action="#feedback" method="post" enctype="multipart/form-data">
              <div class="modal-header">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close">×</button>
                <div class="h4 modal-title"><?php echo ORDER_CALL?></div>
              </div>
              <div class="modal-body">
                <div class="form-horizontal">
                  <?php include Package::i()->resourcesDir . '/form.inc.php'?>
                  <div data-role="notifications" <?php echo ($success[(int)$Block->id] || $localError) ? '' : 'style="display: none"'?>>
                    <div class="alert alert-success" <?php echo ($success[(int)$Block->id]) ? '' : 'style="display: none"'?>><?php echo FEEDBACK_SUCCESSFULLY_SENT?></div>
                    <div class="alert alert-danger" <?php echo ($localError) ? '' : 'style="display: none"'?>>
                      <ul>
                        <?php foreach ((array)$localError as $key => $val) { ?>
                            <li><?php echo htmlspecialchars($val)?></li>
                        <?php } ?>
                      </ul>
                    </div>
                  </div>

                  <div data-role="feedback-form" <?php echo $success[(int)$Block->id] ? 'style="display: none"' : ''?>>
                    <?php if ($Form->signature) { ?>
                          <input type="hidden" name="form_signature" value="<?php echo md5('form' . (int)$Form->id . (int)$Block->id)?>" />
                    <?php } ?>
                    <?php if ($Form->antispam == 'hidden' && $Form->antispam_field_name) { ?>
                          <input type="text" autocomplete="off" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" value="<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>" style="position: absolute; left: -9999px" />
                    <?php } ?>
                    <?php $row = $Form->fields['phone_call']; $row->placeholder = $row->name; ?>
                    <div class="form-group">
                      <div class="col-xs-12 order-call__phone">
                        <?php $getField($row, $DATA)?>
                        <button class="btn btn-primary" type="submit"><span class="fa fa-phone"></span></button>
                      </div>
                    </div>
                    <?php $row = $Form->fields['agree'] ?>
                    <div class="form-group">
                      <div class="col-xs-12">
                        <label class="checkbox">
                          <?php $getField($row, $DATA);?>
                          <a href="/privacy/" target="_blank"><?php echo htmlspecialchars($row->name)?></a>
                        </label>
                      </div>
                    </div>
                    <?php if ($Form->antispam == 'captcha' && $Form->antispam_field_name) { ?>
                        <div class="form-group">
                          <label for="<?php echo htmlspecialchars($Form->antispam_field_name)?>" class="control-label col-sm-3"><?php echo CAPTCHA?></label>
                          <div class="col-sm-9">
                            <img src="/assets/kcaptcha/?<?php echo session_name() . '=' . session_id()?>" /><br />
                            <input type="text" autocomplete="off" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" />
                          </div>
                        </div>
                    <?php } ?>
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
