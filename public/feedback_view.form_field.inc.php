<?php
/**
 * Поле "Форма" в просмотре сообщения обратной связи
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Field as RAASField;

/**
 * Отображает поле
 * @param RAASField $field Поле для отображения
 */
$_RAASForm_Control = function (RAASField $field) {
    $Item = $field->Form->Item;
    if (Application::i()->user->root) {
        echo '<a href="' . Sub_Dev::i()->url . '&action=edit_form&id=' . (int)$Item->pid . '">' .
                htmlspecialchars($Item->parent->name) .
              '</a>';
    } else {
        echo '<a href="' . Sub_Feedback::i()->url . '&id=' . (int)$Item->pid . '">' .
                htmlspecialchars($Item->parent->name) .
              '</a>';
    }
};
