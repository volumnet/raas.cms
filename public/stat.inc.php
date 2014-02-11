<?php
$_RAASForm_Control = function(\RAAS\Field $Field, $confirm = true) use (&$_RAASForm_Attrs, &$_RAASForm_Options, &$_RAASForm_Checkbox) {
    $Item = $Field->Form->Item;
    if ($Field->name == 'post_date') {
        $dateN = 'post_date';
        $userN = 'author';
    } else {
        $dateN = 'modify_date';
        $userN = 'editor';
    }
    ?>
    <?php echo strtotime($Item->$dateN) ? date(DATETIMEFORMAT, strtotime($Item->$dateN)) . ', ' : ''?>
    <?php if ($Item->$userN->id) { ?>
        <?php if ($Item->$userN->email) { ?>
            <a href="mailto:<?php echo htmlspecialchars($Item->$userN->email)?>">
              <?php echo htmlspecialchars($Item->$userN->full_name ? $Item->$userN->full_name : $Item->$userN->login)?>
            </a>
        <?php } else { ?>
            <?php echo htmlspecialchars($Item->$userN->full_name ? $Item->$userN->full_name : $Item->$userN->login)?>
        <?php } ?>
    <?php } ?>
    <?php
};