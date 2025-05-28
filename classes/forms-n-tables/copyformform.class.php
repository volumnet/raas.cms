<?php
/**
 * Форма дублирования формы
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\Form as RAASForm;
use RAAS\Option;

/**
 * Класс формы дублирования формы
 * @property-read ViewSub_Dev $view Представление
 */
class CopyFormForm extends EditFormForm
{
    public function __construct(array $params = [])
    {
        $params['selfUrl'] = Sub_Dev::i()->url . '&action=edit_form&id=%d';
        $params['newUrl'] = Sub_Dev::i()->url . '&action=edit_form';
        $params['caption'] = $this->view->_('COPY_FORM');
        parent::__construct($params);
        $item = isset($params['Item']) ? $params['Item'] : null;
        foreach ($this->children as $row) {
            if ($item->{$row->name}) {
                $row->default = $item->{$row->name};
            }
        }
        $this->meta['Original'] = $original = $params['Original'];
        $this->oncommit = function ($form) use ($original, $item) {
            $fieldGroupsMapping = [];
            foreach ($original->fieldGroups as $fieldGroupURN => $fieldGroup) {
                if (!$fieldGroup->id) {
                    continue;
                }
                $copiedFieldGroup = clone $fieldGroup;
                $copiedFieldGroup->pid = $item->id;
                $copiedFieldGroup->commit();
                $fieldGroupsMapping[(string)$fieldGroup->id] = $copiedFieldGroup->id;
            }
            foreach ($original->fields as $field) {
                $copiedField = clone $field;
                $copiedField->pid = $item->id;
                if ($copiedField->gid) {
                    $copiedField->gid = $fieldGroupsMapping[$copiedField->gid] ?? 0;
                }
                $copiedField->commit();
            }
        };
    }
}
