<?php
/**
 * Редактирование шаблона
 */
namespace RAAS\CMS;

use RAAS\FieldSet;

/**
 * Отображение группы полей
 * @param FieldSet $fieldSet Группа полей для отображения
 */
$_RAASForm_FieldSet = function(FieldSet $fieldSet) {
    $template = $Item = $fieldSet->Form->Item;
    $templateJSON = $template->getArrayCopy();
    unset($templateJSON['description'], $templateJSON['background']);
    $templateJSON['locations_info'] = array_map(function ($location) {
        $result = [];
        foreach (['urn', 'x', 'y', 'width', 'height'] as $key) {
            $result[$key] = $location->$key;
        }
        return $result;
    }, $template->locations);
    ?>
    <cms-template-editor :initial-data="<?php echo htmlspecialchars(json_encode($templateJSON))?>"></cms-template-editor>
    <?php
};
