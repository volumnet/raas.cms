<?php
/**
 * Форма редактирования материального поля
 */
namespace RAAS\CMS;

use RAAS\Option;
use RAAS\FieldSet;
use RAAS\FormTab;

/**
 * Класс формы редактирования материального поля
 * @property-read ViewSub_Dev $view Представление
 */
class EditMaterialFieldForm extends EditFieldForm
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Dev::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $parent = $params['meta']['Parent'];
        $this->children['formvis'] = $this->getFormVisTab($parent);
    }


    /**
     * Возвращает вкладку видимости поля в формах по типам
     * @param Material_Type $materialType Тип материалов
     */
    public function getFormVisTab(Material_Type $materialType)
    {

        $tab = new FormTab([
            'name' => 'formvis',
            'caption' => $this->view->_('SHOW_IN_FORM'),
            'children' => [
                'formvis' => [
                    'type' => 'checkbox',
                    'name' => 'formvis',
                    'caption' => $this->view->_('SHOW_IN_FORM_BY_TYPES'),
                    'multiple' => 'multiple',
                    'default' => $materialType->selfAndChildrenIds,
                    'children' => $this->getMetaCats($materialType->id, true),
                    'import' => function ($field) {
                        $item = $field->Form->Item;
                        $sqlQuery = "SELECT pid
                                       FROM " . Field::_dbprefix() . "cms_fields_form_vis
                                      WHERE fid = ?";
                        $sqlBind = [$item->id];
                        $sqlResult = Field::_SQL()->getcol([$sqlQuery, $sqlBind]);
                        return $sqlResult;
                    },
                    'export' => 'is_null',
                    'oncommit' => function ($field) use ($materialType) {
                        $item = $field->Form->Item;
                        $materialTypeCache = MaterialTypeRecursiveCache::i();
                        $selfAndChildrenIds = $materialTypeCache->getSelfAndChildrenIds($materialType->id);
                        foreach ($selfAndChildrenIds as $subtypeId) {
                            $visArr = [];
                            $visArr[$item->id] = [
                                'vis' => in_array(
                                    $subtypeId,
                                    (array)$_POST[$field->name]
                                ),
                                'inherit' => false,
                            ];
                            $subType = new Material_Type($subtypeId);
                            $subType->setFormFieldsIds($visArr);
                        }
                    },
                ]
            ],
        ]);
        return $tab;
    }

    /**
     * Получает список типов материалов для отображения в поле видимости в форме
     * @param int $pid ID# родительской страницы
     * @param bool $isRoot Корневая категория для дерева
     * @return array <pre><code>array<[
     *     'value' => int ID# типа,
     *     'caption' => string Наименование типа,
     *     'children' => *рекурсивно*
     * ]></code></pre>
     */
    public function getMetaCats($pid = 0, $isRoot = false)
    {
        $materialTypeCache = MaterialTypeRecursiveCache::i();
        $result = [];
        if ($pid && $isRoot) {
            $materialTypesIds = [$pid];
        } else {
            $materialTypesIds = $materialTypeCache->getChildrenIds($pid);
        }
        $materialTypesData = [];
        foreach ($materialTypesIds as $materialTypeId) {
            $materialTypeData = $materialTypeCache->cache[$materialTypeId];
            $materialTypesData[] = [
                'value' => (int)$materialTypeData['id'],
                'caption' => $materialTypeData['name'],
                'data-group' => $materialTypeData['template'],
            ];
        }
        foreach ($materialTypesData as $materialTypeData) {
            if ($children = $this->getMetaCats((int)$materialTypeData['value'])) {
                $materialTypeData['children'] = $children;
            }
            $result[] = $materialTypeData;
        }
        return $result;
    }
}
