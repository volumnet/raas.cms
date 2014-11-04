<?php
namespace RAAS\CMS;
use \RAAS\Field as RAASField;
use \RAAS\FieldSet;

class EditBlockMaterialForm extends EditBlockForm
{
    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        $snippet = Snippet::importByURN('__RAAS_material_interface');
        $field->default = $snippet->id;
        return $field;
    }


    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        $row = new Material_Type();
        $this->meta['CONTENT']['material_types'] = array('Set' => $row->children);
        $tab->children[] = new RAASField(array(
            'type' => 'select', 'name' => 'material_type', 'caption' => $this->view->_('MATERIAL_TYPE'), 'children' => $this->meta['CONTENT']['material_types']
        ));
        $tab->children[] = new RAASField(array(
            'type' => 'checkbox', 
            'name' => 'nat', 
            'caption' => $this->view->_('TRANSLATE_ADDRESS'), 
            'data-hint' => $this->view->_('BLOCK_TRANSLATE_ADDRESS_DESCRIPTION')
        ));
        $tab->children[] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = parent::getServiceTab();
        $this->meta['CONTENT']['fields'] = array(
            array('value' => 'name', 'caption' => $this->view->_('NAME')),
            array('value' => 'urn', 'caption' => $this->view->_('URN')),
            array('value' => 'description', 'caption' => $this->view->_('DESCRIPTION')),
            array('value' => 'post_date', 'caption' => $this->view->_('CREATED_BY')),
            array('value' => 'modify_date', 'caption' => $this->view->_('EDITED_BY')),
        );
        if ($this->Item->id) {
            $Material_Type = $this->Item->Material_Type;
        } elseif (isset($_POST['material_type'])) {
            $Material_Type = new Material_Type($_POST['material_type']);
        } else {
            $Material_Type = $this->meta['CONTENT']['material_types']['Set'][0];
        }
        foreach ((array)$Material_Type->fields as $row) {
            if (!($row->multiple || in_array($row->datatype, array('file', 'image')))) {
                $this->meta['CONTENT']['fields'][] = array('value' => (int)$row->id, 'caption' => $row->name);
            }
        }
        foreach (Block_Material::$orderRelations as $key => $val) {
            $this->meta['CONTENT']['orders'][] = array('value' => $key, 'caption' => $this->view->_($val));
        }
        
        $tab->children[] = $this->getPagesVarField();
        $tab->children[] = $this->getRowsPerPageField();
        $tab->children[] = new FieldSet(array(
            'caption' => $this->view->_('FILTER_PARAMS'),
            'template' => 'edit_block_material.filter.php',
            'import' => function($FieldSet) {
                $DATA = array();
                if ($FieldSet->Form->Item->filter) {
                    foreach ((array)$FieldSet->Form->Item->filter as $row) {
                        $DATA['filter_var'][] = (string)$row['var'];
                        $DATA['filter_relation'][] = (string)$row['relation'];
                        $DATA['filter_field'][] = (string)$row['field'];
                    }
                }
                return $DATA;
            },
            'export' => function($FieldSet) {
                $temp = array();
                if (isset($_POST['filter_var'])) {
                    foreach ((array)$_POST['filter_var'] as $key => $val) {
                        if (isset($_POST['filter_relation'][$key], $_POST['filter_field'][$key])) {
                            $temp[] = array(
                                'var' => (string)$_POST['filter_var'][$key], 'relation' => (string)$_POST['filter_relation'][$key], 'field' => (string)$_POST['filter_field'][$key],
                            );
                        }
                    }
                }
                if ($temp) {
                    $FieldSet->Form->Item->filter = $temp;
                }
            }
        ));
        $tab->children[] = new FieldSet(array(
            'caption' => $this->view->_('SORTING_PARAMS'),
            'template' => 'edit_block_material.sort.php',
            'import' => function($FieldSet) {
                $DATA = $FieldSet->importDefault();
                if ($FieldSet->Form->Item->sort) {
                    foreach ((array)$FieldSet->Form->Item->sort as $row) {
                        $DATA['sort_var'][] = (string)$row['var'];
                        $DATA['sort_field'][] = (string)$row['field'];
                        $DATA['sort_relation'][] = (string)$row['relation'];
                    }
                }
                return $DATA;
            },
            'export' => function($FieldSet) {
                $FieldSet->exportDefault();
                $temp = array();
                if (isset($_POST['sort_var'])) {
                    foreach ((array)$_POST['sort_var'] as $key => $val) {
                        if (isset($_POST['sort_relation'][$key], $_POST['sort_field'][$key])) {
                            $temp[] = array(
                                'var' => (string)$_POST['sort_var'][$key], 'relation' => (string)$_POST['sort_relation'][$key], 'field' => (string)$_POST['sort_field'][$key],
                            );
                        }
                    }
                }
                if ($temp) {
                    $FieldSet->Form->Item->sort = $temp;
                }
            },
            'children' => array(
                'sort_var_name' => array('name' => 'sort_var_name', 'placeholder' => $this->view->_('SORTING_VAR'), 'class' => 'span2'),
                'order_var_name' => array('name' => 'order_var_name', 'placeholder' => $this->view->_('ORDER_VAR'), 'class' => 'span2'),
                'sort_field_default' => array('type' => 'select', 'name' => 'sort_field_default', 'children' => $this->meta['CONTENT']['fields'], 'class' => 'span2 jsMaterialTypeField'),
                'sort_order_default' => array('type' => 'select', 'name' => 'sort_order_default', 'children' => $this->meta['CONTENT']['orders'], 'class' => 'span2')
            )
        ));
        $tab->children[] = new RAASField(array('type' => 'checkbox', 'name' => 'legacy', 'caption' => $this->view->_('REDIRECT_LEGACY_ADDRESSES')));
        $tab->children[] = new RAASField(array('name' => 'params', 'caption' => $this->view->_('ADDITIONAL_PARAMS')));
        $tab->children[] = $this->getInterfaceField();
        return $tab;
    }
}