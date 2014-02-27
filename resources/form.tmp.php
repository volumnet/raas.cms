<a name="feedback"></a>
<article class="article">
	<div class="feedback">
    <form class="form-horizontal" action="#feedback" method="post" enctype="multipart/form-data">
			<h3 class="form-title text-normal"><?php echo FEEDBACK?></h3>
      <?php if ($success[(int)$Block->id]) { ?>
          <div class="notifications">
            <div class="alert alert-success"><?php echo FEEDBACK_SUCCESSFULLY_SENT?></div>
          </div>
      <?php } else { ?>
          <?php include \RAAS\CMS\Package::i()->resourcesDir . '/form.inc.php'?>
          <?php if ($localError) { ?>
              <div class="notifications">
                <?php foreach ((array)$localError as $key => $val) { ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($val)?></div>
                <?php } ?>
              </div>
          <?php } ?>
          
          <?php if ($Form->signature) { ?>
          		<input type="hidden" name="form_signature" value="<?php echo md5('form' . (int)$Form->id . (int)$Block->id)?>" />
          <?php } ?>
          <?php if ($Form->antispam == 'hidden' && $Form->antispam_field_name) { ?>
          		<input type="text" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" value="<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>" style="position: absolute; left: -9999px" />
          <?php } ?>
          <?php foreach ($Form->fields as $row) { ?>
              <div class="form-group">
                <label for="<?php echo htmlspecialchars($row->urn)?>" class="control-label col-sm-2"><?php echo htmlspecialchars($row->name . ($row->required ? '*' : ''))?></label>
                <div class="col-sm-4"><?php $getField($row, $DATA)?></div>
              </div>
          <?php } ?>
          <?php if ($Form->antispam == 'captcha' && $Form->antispam_field_name) { ?>
              <div class="form-group">
                <label for="name" class="control-label col-sm-2"><?php echo CAPTCHA?></label>
                <div class="col-sm-4 <?php echo htmlspecialchars($Form->antispam_field_name)?>">
                  <img src="/assets/kcaptcha/?<?php echo session_name() . '=' . session_id()?>" /><br />
                  <input type="text" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" />
                </div>
              </div>
          <?php } ?>
          <div class="form-group">
            <div class="col-sm-4 col-sm-offset-2"><button class="btn" type="submit"><?php echo SEND?></button></div>
          </div>
      <?php } ?>
    </form>
  </div>
</article>