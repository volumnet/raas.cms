<?php
namespace RAAS\CMS;

$smsField = function ($field) {
    $values = $field->getValues(true);
    $arr = array();
    foreach ($values as $key => $val) {
        $val = $field->doRich($val);
        switch ($field->datatype) {
            case 'date':
                $arr[$key] = date(\DATEFORMAT, strtotime($val));
                break;
            case 'datetime-local':
                $arr[$key] = date(\DATETIMEFORMAT, strtotime($val));
                break;
            case 'file':
            case 'image':
                $arr[$key] .= $val->name;
                break;
            case 'htmlarea':
                $arr[$key] = strip_tags($val);
                break;
            default:
                if (!$field->multiple && ($field->datatype == 'checkbox')) {
                    $arr[$key] = $val ? \_YES : \_NO;
                } else {
                    $arr[$key] = $val;
                }
                break;
        }
    }
    return $field->name . ': ' . implode(', ', $arr) . "\n";
};

$emailField = function ($field) {
    $values = $field->getValues(true);
    $arr = array();
    foreach ($values as $key => $val) {
        $val = $field->doRich($val);
        switch ($field->datatype) {
            case 'date':
                $arr[$key] = date(\DATEFORMAT, strtotime($val));
                break;
            case 'datetime-local':
                $arr[$key] = date(\DATETIMEFORMAT, strtotime($val));
                break;
            case 'color':
                $arr[$key] = '<span style="display: inline-block; height: 16px; width: 16px; background-color: ' . htmlspecialchars($val) . '"></span>';
                break;
            case 'email':
                $arr[$key] .= '<a href="mailto:' . htmlspecialchars($val) . '">' . htmlspecialchars($val) . '</a>';
                break;
            case 'url':
                $arr[$key] .= '<a href="' . (!preg_match('/^http(s)?:\\/\\//umi', trim($val)) ? 'http://' : '') . htmlspecialchars($val) . '">' . htmlspecialchars($val) . '</a>';
                break;
            case 'file':
                $arr[$key] .= '<a href="http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/' . $val->fileURL . '">' . htmlspecialchars($val->name) . '</a>';
                break;
            case 'image':
                $arr[$key] .= '<a href="http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/' . $val->fileURL . '">
                                 <img src="http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/' . $val->tnURL. '" alt="' . htmlspecialchars($val->name) . '" title="' . htmlspecialchars($val->name) . '" />
                               </a>';
                break;
            case 'htmlarea':
                $arr[$key] = '<div>' . $val . '</div>';
                break;
            default:
                if (!$field->multiple && ($field->datatype == 'checkbox')) {
                    $arr[$key] = $val ? \_YES : \_NO;
                } else {
                    $arr[$key] = nl2br(htmlspecialchars($val));
                }
                break;
        }
    }
    return '<div>' . htmlspecialchars($field->name) . ': ' . implode(', ', $arr) . '</div>';
};
?>
<?php if ($SMS) {
    echo date(\DATETIMEFORMAT) . ' ' . sprintf(\FEEDBACK_STANDARD_HEADER, $Item->parent->name, $Item->page->name) . "\n";
    foreach ($Item->fields as $field) {
        echo $smsField($field);
    }
} else { ?>
    <div>
      <?php
      foreach ($Item->fields as $field) {
          echo $emailField($field);
      }
      ?>
    </div>
    <?php if ($Material && $Material->id) {
        $url = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://';
        $url .= htmlspecialchars(
            $_SERVER['HTTP_HOST'] .
            '/admin/?p=cms&sub=main&action=edit_material&id=' .
            (int)$Material->id .
            '&pid='
        );
        if (in_array(
            $Item->page->id,
            array_map(
                function ($x) {
                    return $x->id;
                },
                (array)$Item->parent->Material_Type->affectedPages
            )
        )) {
            $url .= $Item->page->id;
        } else {
            $url .= $Item->parent->Material_Type->affectedPages[0]->id;
        }
        ?>
        <p>
          <a href="<?php echo $url?>">
            <?php echo \VIEW?>
          </a>
        </p>
    <?php } elseif ($Item->parent->create_feedback) { ?>
        <p><a href="http<?php echo ($_SERVER['HTTPS'] == 'on' ? 's' : '')?>://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] . '/admin/?p=cms&sub=feedback&action=view&id=' . $Item->id)?>"><?php echo \VIEW?></a></p>
    <?php } ?>
    <p>
      <small>
        <?php echo \IP_ADDRESS?>: <?php echo htmlspecialchars($Item->ip)?><br />
        <?php echo \USER_AGENT?>: <?php echo htmlspecialchars($Item->user_agent)?><br />
        <?php echo \PAGE?>:
        <?php if ($Item->page->parents) { ?>
            <?php foreach ($Item->page->parents as $row) { ?>
                <a href="<?php echo htmlspecialchars($Item->domain . $row->url)?>"><?php echo htmlspecialchars($row->name)?></a> /
            <?php } ?>
        <?php } ?>
        <a href="<?php echo htmlspecialchars($Item->domain . $Item->page->url)?>"><?php echo htmlspecialchars($Item->page->name)?></a>
        <?php if ($Item->material->id) { ?>
            / <a href="<?php echo htmlspecialchars($Item->domain . $Item->material->url)?>"><?php echo htmlspecialchars($Item->material->name)?></a>
        <?php } ?>
        <br />
        <?php echo \FORM?>:
        <?php if ($Item->parent->create_feedback) { ?>
            <a href="<?php echo htmlspecialchars($Item->domain . '/admin/?p=cms&sub=feedback&id=' . $Item->parent->id)?>"><?php echo htmlspecialchars($Item->parent->name)?></a>
        <?php } else { ?>
            <?php echo htmlspecialchars($Item->parent->name)?>
        <?php } ?>
      </small>
    </p>
<?php } ?>
