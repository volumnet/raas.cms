<?php
/**
 * Форма копирования материала
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\FormTab;
use RAAS\HTMLElement;
use RAAS\Field as RAASField;

/**
 * Класс формы материала страницы
 */
class CopyMaterialForm extends EditMaterialForm
{
    public function __construct(array $params = [])
    {
        $params['selfUrl'] = Sub_Main::i()->url . '&action=edit_material&id=%d';
        $params['newUrl'] = Sub_Main::i()->url . '&action=edit_material&mtype='
                          . (int)$params['Type']->id
                          . '&pid=' . (int)$params['Parent']->id;
        parent::__construct($params);
        $this->caption = $this->view->_('COPY_MATERIAL');
        $this->meta['Original'] = $original = $params['Original'];
        $this->children['copy'] = $this->getCopyTab($original);
        // 2017-08-24, AVS: поменял $original на $item с целью
        // смены названия и URN с суффиксом 2
        // 2018-04-03, AVS: не сработало - теперь все поля пустые
        // (т.к. у $item полей пока нет). Вернул $original.
        // Смену названия буду делать в другом месте
        $this->defaultize($this, $original);
    }


    /**
     * Устанавливает значение по умолчанию для HTML-элемента
     * (равное копируемому материалу)
     * @param HTMLElement $el Элемент, для которого устанавливаем значение
     * @param Material $item Копируемый материал
     */
    protected function defaultize(HTMLElement $el, Material $item)
    {
        if (!($el instanceof RAASField) && $el->children) {
            foreach ($el->children as $row) {
                $this->defaultize($row, $item);
            }
        } else {
            $val = $item->{$el->name};
            // 2018-04-03, AVS: добавил отдельную проверку на name и urn -
            // они будут браться из нового $item'а,
            // чтобы сделать новые название и URN
            if (in_array($el->name, ['name', 'urn', 'pid'])) {
                $el->default = $this->Item->{$el->name};
            } elseif ($el->name == 'cats') {
                $el->default = $item->pages_ids;
            } elseif ($el->name == 'access_id') {
                if (count($item->access)) {
                    $el->default = array_fill(0, count($item->access), '');
                }
            } elseif ($el->name == 'access_allow') {
                $el->default = array_map(function ($x) {
                    return (int)$x->allow;
                }, (array)$item->access);
            } elseif ($el->name == 'access_to_type') {
                $el->default = array_map(function ($x) {
                    return (int)$x->to_type;
                }, (array)$item->access);
            } elseif ($el->name == 'access_uid') {
                $el->default = array_map(function ($x) {
                    return (int)$x->uid;
                }, (array)$item->access);
            } elseif ($el->name == 'access_gid') {
                $el->default = array_map(function ($x) {
                    return (int)$x->gid;
                }, (array)$item->access);
            } elseif (in_array($el->type, ['datetime', 'date', 'time']) &&
                (strtotime($val) <= 0)
            ) {
            } elseif ($val) {
                $el->default = $item->{$el->name};
            }
        }
    }


    /**
     * Получает вкладку параметров копирования
     * @param Material $original Копируемый материал
     * @return FormTab
     */
    protected function getCopyTab(Material $original)
    {
        $copyTab = new FormTab([
            'name' => 'copy',
            'caption' => $this->view->_('COPY_PARAMS')
        ]);
        $copyTab->children['copy_links'] = new RAASField([
            'type' => 'checkbox',
            'name' => 'copy_links',
            'caption' => $this->view->_('COPY_MATERIAL_LINKS'),
        ]);
        $copyTab->oncommit = function ($FormTab) use ($original) {
            $item = $FormTab->Form->Item;
            if ($_POST['copy_links']) {
                // Получим все родительские типы материалов
                $mtypes = (array)$item->material_type->selfAndParentsIds;
                // Получим всевозможные множественные поля типа материалов,
                // которые могут ссылаться на данный материал
                $sqlQuery = "SELECT id
                                FROM " . Field::_tablename() . "
                               WHERE datatype = 'material'
                                 AND multiple
                                 AND (
                                        (NOT source)
                                     OR (source IN (" . implode(", ", $mtypes) . "))
                                 )";
                $fieldsIds = Application::i()->SQL->getcol($sqlQuery);
                if ($fieldsIds) {
                    // Скопируем значения для данного материала
                    $sqlQuery = "SELECT *
                                    FROM cms_data
                                   WHERE fid IN (" . implode(", ", $fieldsIds) . ")
                                     AND value = " . (int)$original->id;
                    $sqlResult = Application::i()->SQL->get($sqlQuery);
                    $ai = $arr = [];
                    foreach ($sqlResult as $row) {
                        $sqlQuery = "SELECT MAX(fii)
                                       FROM cms_data
                                      WHERE pid = ?
                                        AND fid = ?";
                        $ai[(int)$row['pid'] . '.' . (int)$row['fid']] = (int)Application::i()->SQL->getvalue([
                            $sqlQuery,
                            (int)$row['pid'],
                            (int)$row['fid']
                        ]);
                        $arr[] = [
                            'pid' => (int)$row['pid'],
                            'fid' => (int)$row['fid'],
                            'fii' => ++$ai[(int)$row['pid'] . '.' . (int)$row['fid']],
                            'value' => (int)$item->id
                        ];
                    }
                    Application::i()->SQL->add('cms_data', $arr);
                }
            }
        };
        return $copyTab;
    }
}
