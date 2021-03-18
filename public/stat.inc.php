<?php
/**
 * Отображает статистические поля (даты создания/модификации, автора, редактора)
 */
namespace RAAS\CMS;

use RAAS\Field as RAASField;

/**
 * Отображает поле
 * @param RAASField $field Поле для отображения
 * @param bool $confirm Добавлять поле подтверждения для пароля
 *                      (не используется)
 */
$_RAASForm_Control = function (
    RAASField $field,
    $confirm = true
) use (
    &$_RAASForm_Attrs,
    &$_RAASForm_Options,
    &$_RAASForm_Checkbox
) {
    $Item = $field->Form->Item;
    if ($field->name == 'post_date') {
        $dateN = 'post_date';
        $userN = 'author';
    } elseif ($field->name == 'modify_date') {
        $dateN = 'modify_date';
        $userN = 'editor';
    } elseif ($field->name == 'last_modified') {
        $dateN = 'last_modified';
    }
    $t = strtotime($Item->$dateN);
    if ($t > 0) {
        echo date(DATETIMEFORMAT, $t) . ($userN ? ', ' : '');
    }
    if ($Item->$userN->id) {
        $fullName = $Item->$userN->full_name ?: $Item->$userN->login;
        if ($Item->$userN->email) { ?>
            <a href="mailto:<?php echo htmlspecialchars($Item->$userN->email)?>">
              <?php echo htmlspecialchars($fullName)?>
            </a>
        <?php } elseif ($userN) {
            echo htmlspecialchars($fullName);
        }
    }
};
