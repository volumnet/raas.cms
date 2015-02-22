<?php
$_RAASForm_Control = function(\RAAS\Field $Field) use (&$_RAASForm_Attrs, &$_RAASForm_Options, &$_RAASForm_Checkbox, &$_RAASForm_Control) {
    $Item = $Field->Form->Item;
    if (\RAAS\Application::i()->user->root) { 
        echo '<a href="' . \RAAS\CMS\Sub_Dev::i()->url . '&action=edit_form&id=' . (int)$Item->pid . '">' . htmlspecialchars($Item->parent->name) . '</a>';
    } else {
        echo '<a href="' . \RAAS\CMS\Sub_Feedback::i()->url . '&id=' . (int)$Item->pid . '">' . htmlspecialchars($Item->parent->name) . '</a>';
    }
};