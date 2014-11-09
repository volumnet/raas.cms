<?php
$_RAASForm_Control = function(\RAAS\Field $Field, $confirm = true) use (&$_RAASForm_Attrs, &$_RAASForm_Options, &$_RAASForm_Checkbox) {
    $Item = $Field->Form->Item;
    if ($Field->name == 'post_date') {
        $dateN = 'post_date';
        $userN = 'author';
    } elseif ($Field->name == 'modify_date') {
        $dateN = 'modify_date';
        $userN = 'editor';
    } elseif ($Field->name == 'last_modified') {
        $dateN = 'last_modified';
    }
    ?>
    <?php echo strtotime($Item->$dateN) ? date(DATETIMEFORMAT, strtotime($Item->$dateN)) . ($userN ? ', ' : '') : ''?>
    <?php if ($Item->$userN->id) { ?>
        <?php if ($Item->$userN->email) { ?>
            <a href="mailto:<?php echo htmlspecialchars($Item->$userN->email)?>">
              <?php echo htmlspecialchars($Item->$userN->full_name ? $Item->$userN->full_name : $Item->$userN->login)?>
            </a>
        <?php } elseif ($userN) { ?>
            <?php echo htmlspecialchars($Item->$userN->full_name ? $Item->$userN->full_name : $Item->$userN->login)?>
        <?php } ?>
    <?php } ?>
    <?php
};