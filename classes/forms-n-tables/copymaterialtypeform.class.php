<?php
/**
 * Форма копирования типа материалов
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс формы копирования типа материалов
 */
class CopyMaterialTypeForm extends EditMaterialTypeForm
{
    public function __construct(array $params = [])
    {
        $params['selfUrl'] = Sub_Dev::i()->url . '&action=edit_material_type&id=%d';
        $params['newUrl'] = Sub_Dev::i()->url . '&action=edit_material_type';
        $params['caption'] = $this->view->_('COPY_MATERIAL_TYPE');
        parent::__construct($params);
        $item = isset($params['Item']) ? $params['Item'] : new Material_Type();
        foreach ($this->children as $row) {
            if ($item->{$row->name}) {
                $row->default = $item->{$row->name};
            }
        }
        $this->meta['Original'] = $original = ($params['Original'] ?? null);
        $parentOnCommit = $this->oncommit;
        $this->oncommit = function ($form) use ($original, $item, $parentOnCommit) {
            foreach ($original->fields as $field) {
                $copiedField = clone $field;
                $copiedField->pid = $item->id;
                $copiedField->commit();
            }
            $parentOnCommit($this);
        };
    }
}
