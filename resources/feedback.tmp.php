<?php namespace RAAS\CMS?>
<?php 
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
    <a name="feedback"></a>
    <div class="feedback">
      <form class="form-horizontal" data-role="raas-ajaxform" action="#feedback" method="post" enctype="multipart/form-data">
        <?php include \RAAS\CMS\Package::i()->resourcesDir . '/form.inc.php'?>
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
                <input type="text" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" value="<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>" style="position: absolute; left: -9999px" />
          <?php } ?>
          <?php foreach ($Form->fields as $row) { ?>
              <div class="form-group">
                <label for="<?php echo htmlspecialchars($row->urn)?>" class="control-label col-sm-3 col-md-2"><?php echo htmlspecialchars($row->name . ($row->required ? '*' : ''))?></label>
                <div class="col-sm-9 col-md-4"><?php $getField($row, $DATA)?></div>
              </div>
          <?php } ?>
          <?php if ($Form->antispam == 'captcha' && $Form->antispam_field_name) { ?>
              <div class="form-group">
                <label for="name" class="control-label col-sm-3 col-md-2"><?php echo CAPTCHA?></label>
                <div class="col-sm-9 col-md-4 <?php echo htmlspecialchars($Form->antispam_field_name)?>">
                  <img src="/assets/kcaptcha/?<?php echo session_name() . '=' . session_id()?>" /><br />
                  <input type="text" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" />
                </div>
              </div>
          <?php } ?>
          <div class="form-group">
            <div class="col-sm-9 col-md-4 col-sm-offset-3 col-md-offset-2"><button class="btn btn-default" type="submit"><?php echo SEND?></button></div>
          </div>
        </div>
      </form>
    </div>
<?php } ?>