<?php
namespace RAAS\CMS;
use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\HTMLElement;
use \RAAS\FieldSet;
use \RAAS\Field as RAASField;
use \RAAS\Column;

class CopyMaterialForm extends EditMaterialForm
{
    public function __construct(array $params = array())
    {
        $params['selfUrl'] = Sub_Main::i()->url . '&action=edit_material&id=%d';
        $params['newUrl'] = Sub_Main::i()->url . '&action=edit_material&mtype=' . (int)$params['Type']->id . '&pid=' . (int)$params['Parent']->id;
        parent::__construct($params);
        $this->caption = $this->view->_('COPY_MATERIAL');
        $this->meta['Original'] = $Original = $params['Original'];
        $this->children['copy'] = $this->getCopyTab();
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $this->defaultize($this, $Original);
    }


    protected function defaultize(HTMLElement $el, Material $Item)
    {
        if (!($el instanceof RAASField) && $el->children) {
            foreach ($el->children as $row) {
                $this->defaultize($row, $Item);
            }
        } else {
            $val = $Item->{$el->name};
            if ($el->name == 'cats') {
                $el->default = $Item->pages_ids;
            } elseif ($el->name == 'access_id') {
                $el->default = array_fill(0, count($Item->access), '');
            } elseif ($el->name == 'access_allow') {
                $el->default = array_map(function($x) { return (int)$x->allow; }, (array)$Item->access);
            } elseif ($el->name == 'access_to_type') {
                $el->default = array_map(function($x) { return (int)$x->to_type; }, (array)$Item->access);
            } elseif ($el->name == 'access_uid') {
                $el->default = array_map(function($x) { return (int)$x->uid; }, (array)$Item->access);
            } elseif ($el->name == 'access_gid') {
                $el->default = array_map(function($x) { return (int)$x->gid; }, (array)$Item->access);
            } elseif (in_array($el->type, array('datetime', 'date', 'time')) && (strtotime($val) <= 0)) {
            } elseif ($val) {
                $el->default = $Item->{$el->name};
            }
        }
    } 


    protected function getCopyTab()
    {
        $copyTab = new FormTab(array('name' => 'copy', 'caption' => $this->view->_('COPY_PARAMS')));
        $copyTab->children['copy_links'] = new RAASField(array(
            'type' => 'checkbox', 
            'name' => 'copy_links', 
            'caption' => $this->view->_('COPY_MATERIAL_LINKS'), 
        ));
        $copyTab->oncommit = function($FormTab) {
            $Item = $FormTab->Form->Item;
            if ($_POST['copy_links']) {
                // Получим все родительские типы материалов
                $mtypes = array_merge(array($Item->material_type), (array)$Item->material_type->parents);
                $mtypes = array_map(function($x) { return (int)$x->id; }, $mtypes);
                // Получим всевозможные множественные поля типа материалов, которые могут ссылаться на данный материал
                $SQL_query = "SELECT id
                                FROM " . Field::_tablename() . " 
                               WHERE datatype = 'material' 
                                 AND multiple
                                 AND ((NOT source) OR (source IN (" . implode(", ", $mtypes) . ")))";
                $fieldsIds = Application::i()->SQL->getcol($SQL_query);
                if ($fieldsIds) {
                    // Скопируем значения для данного материала
                    $SQL_query = "INSERT INTO cms_data (pid, fid, fii, value) 
                                  SELECT pid, fid, fii, " . (int)$Item->id . " AS value
                                    FROM cms_data
                                   WHERE fid IN (" . implode(", ", $fieldsIds) . ")";
                    Application::i()->SQL->query($SQL_query);
                }
            }
        };
        return $copyTab;
    }


    
}