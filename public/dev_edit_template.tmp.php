<?php
$_RAASForm_FieldSet = function(\RAAS\FieldSet $FieldSet, $level = 0) use (&$_RAASForm_Options) {
    $Item = $FieldSet->Form->Item;
    ?>
    <div class="cms-template" style="<?php echo htmlspecialchars($Item->style)?>">
      <input type="hidden" name="width" id="width" value="<?php echo (int)$Item->width?>" />
      <input type="hidden" name="height" id="height" value="<?php echo (int)$Item->height?>" />
      <?php foreach ($Item->locations as $row) { ?>
          <div class="cms-location__outer" style="<?php echo htmlspecialchars(str_replace('min-height', 'height', $row->style))?>">
            <div class="cms-location" style="width: 100%; height: 100%;">
              <h6><?php echo htmlspecialchars($row->urn)?></h6>
              <input type="hidden" class="jsLocationName" name="location[]" value="<?php echo htmlspecialchars($row->urn)?>" />
              <input type="hidden" class="jsLocationWidth" name="location-width[]" value="<?php echo (int)$row->width?>" />
              <input type="hidden" class="jsLocationHeight" name="location-height[]" value="<?php echo (int)$row->height?>" />
              <input type="hidden" class="jsLocationTop" name="location-top[]" value="<?php echo (int)$row->y?>" />
              <input type="hidden" class="jsLocationLeft" name="location-left[]" value="<?php echo (int)$row->x?>" />
            </div>
          </div>
      <?php } ?>
    </div>
    <br />
    <?php
};