<?php
/**
 * Форма редактирования блока материалов
 */
namespace RAAS\CMS;

use RAAS\Field as RAASField;
use RAAS\FieldSet;

/**
 * Класс формы редактирования блока материалов
 */
class EditBlockMaterialForm extends EditBlockForm
{
    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        $snippet = Snippet::importByURN('__raas_material_interface');
        $field->default = $snippet->id;
        return $field;
    }


    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        $row = new Material_Type();
        $this->meta['CONTENT']['material_types'] = ['Set' => $row->children];
        $tab->children[] = new RAASField([
            'type' => 'select',
            'name' => 'material_type',
            'caption' => $this->view->_('MATERIAL_TYPE'),
            'children' => $this->meta['CONTENT']['material_types']
        ]);
        $tab->children[] = new RAASField([
            'type' => 'checkbox',
            'name' => 'nat',
            'caption' => $this->view->_('TRANSLATE_ADDRESS'),
            'data-hint' => $this->view->_('BLOCK_TRANSLATE_ADDRESS_DESCRIPTION')
        ]);
        $tab->children[] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = parent::getServiceTab();
        $this->meta['CONTENT']['fields'] = [
            [
                'value' => 'name',
                'caption' => $this->view->_('NAME')
            ],
            [
                'value' => 'urn',
                'caption' => $this->view->_('URN')
            ],
            [
                'value' => 'description',
                'caption' => $this->view->_('DESCRIPTION')
            ],
            [
                'value' => 'post_date',
                'caption' => $this->view->_('CREATED_BY')
            ],
            [
                'value' => 'modify_date',
                'caption' => $this->view->_('EDITED_BY')
            ],
        ];
        if ($this->Item->id) {
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
        foreach (Block_Material::$orderRelations as $key => $val) {
            $this->meta['CONTENT']['orders'][] = [
                'value' => $key,
                'caption' => $this->view->_($val)
            ];
        }

        $tab->children[] = $this->getPagesVarField();
        $tab->children[] = $this->getRowsPerPageField();
        $tab->children[] = new FieldSet([
            'caption' => $this->view->_('FILTER_PARAMS'),
            'template' => 'edit_block_material.filter.php',
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
                if ($temp) {
                    $fieldSet->Form->Item->filter = $temp;
                }
            }
        ]);
        $tab->children[] = new FieldSet([
            'caption' => $this->view->_('SORTING_PARAMS'),
            'template' => 'edit_block_material.sort.php',
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
                        if (isset(
                            $_POST['sort_relation'][$key],
                            $_POST['sort_field'][$key]
                        )) {
                            $temp[] = [
                                'var' => (string)$_POST['sort_var'][$key],
                                'relation' => (string)$_POST['sort_relation'][$key],
                                'field' => (string)$_POST['sort_field'][$key],
                            ];
                        }
                    }
                }
                if ($temp) {
                    $fieldSet->Form->Item->sort = $temp;
                }
            },
            'children' => [
                'sort_var_name' => [
                    'name' => 'sort_var_name',
                    'placeholder' => $this->view->_('SORTING_VAR'),
                    'class' => 'span2'
                ],
                'order_var_name' => [
                    'name' => 'order_var_name',
                    'placeholder' => $this->view->_('ORDER_VAR'),
                    'class' => 'span2'
                ],
                'sort_field_default' => [
                    'type' => 'select',
                    'name' => 'sort_field_default',
                    'children' => $this->meta['CONTENT']['fields'],
                    'class' => 'span2 jsMaterialTypeField'
                ],
                'sort_order_default' => [
                    'type' => 'select',
                    'name' => 'sort_order_default',
                    'children' => $this->meta['CONTENT']['orders'],
                    'class' => 'span2'
                ]
            ]
        ]);
        $tab->children[] = new RAASField([
            'type' => 'checkbox',
            'name' => 'legacy',
            'caption' => $this->view->_('REDIRECT_LEGACY_ADDRESSES')
        ]);
        $tab->children[] = new RAASField([
            'name' => 'params',
            'caption' => $this->view->_('ADDITIONAL_PARAMS')
        ]);
        $tab->children[] = $this->getInterfaceField();
        return $tab;
    }
}
