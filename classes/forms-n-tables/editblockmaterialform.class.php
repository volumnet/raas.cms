<?php
/**
 * Форма редактирования блока материалов
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Field as RAASField;
use RAAS\FieldSet;
use RAAS\FormTab;

/**
 * Класс формы редактирования блока материалов
 */
class EditBlockMaterialForm extends EditBlockForm
{
    const DEFAULT_BLOCK_CLASSNAME = Block_Material::class;

    protected function getCommonTab(): FormTab
    {
        $tab = parent::getCommonTab();
        $row = new Material_Type();
        $this->meta['CONTENT']['material_types'] = ['Set' => $row->children];
        $tab->children['material_type'] = new RAASField([
            'type' => 'select',
            'name' => 'material_type',
            'caption' => $this->view->_('MATERIAL_TYPE'),
            'children' => $this->meta['CONTENT']['material_types'],
            'required' => true,
            'placeholder' => '--',
        ]);
        $tab->children['nat'] = new RAASField([
            'type' => 'checkbox',
            'name' => 'nat',
            'caption' => $this->view->_('TRANSLATE_ADDRESS'),
            'data-hint' => $this->view->_('BLOCK_TRANSLATE_ADDRESS_DESCRIPTION')
        ]);
        $tab->children['widget_id'] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab(): FormTab
    {
        $tab = parent::getServiceTab();
        $this->meta['CONTENT']['fields'] = [
            ['value' => 'name', 'caption' => $this->view->_('NAME')],
            ['value' => 'urn', 'caption' => $this->view->_('URN')],
            ['value' => 'description', 'caption' => $this->view->_('DESCRIPTION')],
            ['value' => 'post_date', 'caption' => $this->view->_('CREATED_BY')],
            ['value' => 'modify_date', 'caption' => $this->view->_('EDITED_BY')],
        ];
        if ($this->Item && $this->Item->id) {
            $materialType = $this->Item->Material_Type;
        } elseif (isset($_POST['material_type'])) {
            $materialType = new Material_Type($_POST['material_type']);
        } else {
            $materialType = $this->meta['CONTENT']['material_types']['Set'][0];
        }
        foreach ((array)$materialType->fields as $row) {
            if (!in_array($row->datatype, ['file', 'image'])) {
                // 2019-07-30, AVS: убрали проверку на единичность полей,
                // т.к. фильтр может быть и по множественному полю
                $this->meta['CONTENT']['fields'][] = [
                    'value' => (int)$row->id,
                    'caption' => $row->name
                ];
            }
        }
        $this->meta['CONTENT']['fields'][] = [
            'value' => 'random',
            'caption' => $this->view->_('RANDOM')
        ];
        foreach (Block_Material::$orderRelations as $key => $val) {
            $this->meta['CONTENT']['orders'][] = [
                'value' => $key,
                'caption' => $this->view->_($val)
            ];
        }

        $tab->children['pages_var_name'] = $this->getPagesVarField();
        $tab->children['rows_per_page'] = $this->getRowsPerPageField();
        $tab->children['filter_params'] = new FieldSet([
            'caption' => $this->view->_('FILTER_PARAMS'),
            'template' => 'edit_block_material.filter.inc.php',
            'import' => function ($fieldSet) {
                $DATA = [];
                if ($fieldSet->Form->Item->filter) {
                    foreach ((array)$fieldSet->Form->Item->filter as $row) {
                        $DATA['filter_var'][] = (string)$row['var'];
                        $DATA['filter_relation'][] = (string)$row['relation'];
                        $DATA['filter_field'][] = (string)$row['field'];
                    }
                }
                return $DATA;
            },
            'export' => function ($fieldSet) {
                $temp = [];
                if (isset($_POST['filter_var'])) {
                    foreach ((array)$_POST['filter_var'] as $key => $val) {
                        if (isset(
                            $_POST['filter_relation'][$key],
                            $_POST['filter_field'][$key]
                        )) {
                            $temp[] = [
                                'var' => (string)$_POST['filter_var'][$key],
                                'relation' => (string)$_POST['filter_relation'][$key],
                                'field' => (string)$_POST['filter_field'][$key],
                            ];
                        }
                    }
                }
                $fieldSet->Form->Item->filter = $temp;
            }
        ]);
        $tab->children['sorting_params'] = new FieldSet([
            'caption' => $this->view->_('SORTING_PARAMS'),
            'template' => 'edit_block_material.sort.inc.php',
            'import' => function ($fieldSet) {
                $DATA = $fieldSet->importDefault();
                if ($fieldSet->Form->Item->sort) {
                    foreach ((array)$fieldSet->Form->Item->sort as $row) {
                        $DATA['sort_var'][] = (string)$row['var'];
                        $DATA['sort_field'][] = (string)$row['field'];
                        $DATA['sort_relation'][] = (string)$row['relation'];
                    }
                }
                return $DATA;
            },
            'export' => function ($fieldSet) {
                $fieldSet->exportDefault();
                $temp = [];
                if (isset($_POST['sort_var'])) {
                    foreach ((array)$_POST['sort_var'] as $key => $val) {
                        if (isset($_POST['sort_relation'][$key], $_POST['sort_field'][$key])) {
                            $temp[] = [
                                'var' => (string)$_POST['sort_var'][$key],
                                'relation' => (string)$_POST['sort_relation'][$key],
                                'field' => (string)$_POST['sort_field'][$key],
                            ];
                        }
                    }
                }
                $fieldSet->Form->Item->sort = $temp;
            },
            'children' => [
                'sort_var_name' => [
                    'name' => 'sort_var_name',
                    'placeholder' => $this->view->_('SORTING_VAR'),
                ],
                'order_var_name' => [
                    'name' => 'order_var_name',
                    'placeholder' => $this->view->_('ORDER_VAR'),
                ],
                'sort_field_default' => [
                    'type' => 'select',
                    'name' => 'sort_field_default',
                    'children' => $this->meta['CONTENT']['fields'],
                    'data-role' => 'material-type-field',
                ],
                'sort_order_default' => [
                    'type' => 'select',
                    'name' => 'sort_order_default',
                    'children' => $this->meta['CONTENT']['orders'],
                ]
            ]
        ]);
        $tab->children['interface_id'] = $this->getInterfaceField();
        return $tab;
    }
}
