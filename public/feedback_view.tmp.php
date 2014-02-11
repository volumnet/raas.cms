<form class="form-horizontal">
  <div class="control-group">
    <label class="control-label"><?php echo CMS\POST_DATE?>:</label> <div class="controls"><?php echo date(DATETIMEFORMAT, strtotime($Item->post_date))?></div>
  </div>
  <?php 
  foreach ($Item->fields as $field) {
      $values = $field->getValues(true);
      $arr = array();
      foreach ($values as $key => $val) {
          $val = $field->doRich($val);
          switch ($field->datatype) {
              case 'date':
                  $arr[$key] = date(DATEFORMAT, strtotime($val));
                  break;
              case 'datetime-local':
                  $arr[$key] = date(DATETIMEFORMAT, strtotime($val));
                  break;
              case 'color':
                  $arr[$key] = '<span style="display: inline-block; height: 16px; width: 16px; background-color: ' . htmlspecialchars($val) . '"></span>';
                  break;
              case 'email':
                  $arr[$key] .= '<a href="mailto:' . htmlspecialchars($val) . '">' . htmlspecialchars($val) . '</a>';
                  break;
              case 'url':
                  $arr[$key] .= '<a href="http://' . htmlspecialchars(str_replace('http://', '', $val)) . '">' . htmlspecialchars($val) . '</a>';
                  break;
              case 'file':
                  $arr[$key] .= '<a href="/' . $val->fileURL . '">' . htmlspecialchars($val->name) . '</a>';
                  break;
              case 'image':
                  $arr[$key] .= '<a href="/' . $val->fileURL . '"><img src="/' . $val->tnURL. '" alt="' . htmlspecialchars($val->name) . '" title="' . htmlspecialchars($val->name) . '" /></a>';
                  break;
              case 'htmlarea':
                  $arr[$key] = '<div>' . $val . '</div>';
                  break;
              default:
                  if (!$field->multiple && ($field->datatype == 'checkbox')) {
                      $arr[$key] = $val ? _YES : _NO;
                  } else {
                      $arr[$key] = nl2br(htmlspecialchars($val));
                  }
                  break;
          }
      }
      ?>
      <div class="control-group">
        <label class="control-label"><?php echo htmlspecialchars($field->name)?>:</label>
        <div class="controls"><?php echo implode(', ', $arr)?></div>
      </div>
  <?php } ?>
  <?php if ($Item->uid) { ?>
      <div class="control-group">
        <label class="control-label"><?php echo CMS\USER?>:</label>
        <div class="controls">
          <a href="?p=<?php echo $VIEW->packageName?>&m=users&action=edit&id=<?php echo (int)$Item->uid?>">
            <?php $User = new \RAAS\CMS\User($Item->uid); echo htmlspecialchars($User->full_name)?>
          </a>
        </div>
      </div>
  <?php } ?>
  <div class="control-group">
    <label class="control-label"><?php echo CMS\FORM?>:</label>
    <div class="controls">
      <?php if ($APPLICATION->user->root) { ?>
          <a href="?p=<?php echo $VIEW->packageName?>&sub=dev&action=edit_form&id=<?php echo (int)$Item->pid?>"><?php echo htmlspecialchars($Item->parent->name)?></a>
      <?php } else { ?>
          <a href="?p=<?php echo $VIEW->packageName?>&sub=feedback&id=<?php echo (int)$Item->pid?>"><?php echo htmlspecialchars($Item->parent->name)?></a>
      <?php } ?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label"><?php echo CMS\PAGE?>:</label>
    <div class="controls">
      <?php if ($Item->page->parents) { ?>
          <?php foreach ($Item->page->parents as $row) { ?>
              <a href="?p=<?php echo $VIEW->packageName?>&sub=main&id=<?php echo (int)$row->id?>"><?php echo htmlspecialchars($row->name)?></a> / 
          <?php } ?>
      <?php } ?>
      <a href="?p=<?php echo $VIEW->packageName?>&sub=main&id=<?php echo (int)$Item->page_id?>"><?php echo htmlspecialchars($Item->page->name)?></a>
    </div>
  </div>
  <?php if ($Item->viewer->id) { ?>
      <div class="control-group">
        <label class="control-label"><?php echo CMS\VIEWED_BY?>:</label> 
        <div class="controls">
          <?php if ($Item->viewer->id) { ?>
              <?php if ($Item->viewer->email) { ?>
                  <a href="mailto:<?php echo htmlspecialchars($Item->viewer->email)?>">
                    <?php echo htmlspecialchars($Item->viewer->full_name ? $Item->viewer->full_name : $Item->viewer->login)?>
                  </a>
              <?php } else { ?>
                  <?php echo htmlspecialchars($Item->viewer->full_name ? $Item->viewer->full_name : $Item->viewer->login)?>
              <?php } ?>
          <?php } ?>
        </div>
      </div>
  <?php } ?>
  <div class="control-group">
    <label class="control-label"><?php echo CMS\IP_ADDRESS?>:</label>
    <div class="controls"><a href="http://whois.net/ip-address-lookup/<?php echo htmlspecialchars($Item->ip)?>"><?php echo htmlspecialchars($Item->ip)?></a></div>
  </div>
  <div class="control-group">
    <label class="control-label"><?php echo CMS\USER_AGENT?>:</label> <div class="controls"><?php echo htmlspecialchars($Item->user_agent)?></div>
  </div>
</form>