<?php
/**
 * Форма редактирования блока меню
 */
namespace RAAS\CMS;

use RAAS\Field as RAASField;

/**
 * Класс формы редактирования блока меню
 */
class EditBlockMenuForm extends EditBlockForm
{
    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        $snippet = Snippet::importByURN('__raas_menu_interface');
        $field->default = $snippet->id;
        return $field;
    }


    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        $tmp_menu = new Menu();
        $domain = $this->meta['Parent']->Domain;
        $this->meta['CONTENT']['menus'] = $this->getMenus($domain->id);
        $this->meta['CONTENT']['menu_appearances'][] = [
            'value' => 1,
            'caption' => $this->view->_('FULL_MENU')
        ];
        $this->meta['CONTENT']['menu_appearances'][] = [
            'value' => 0,
            'caption' => $this->view->_('PAGE_SUBSECTIONS')
        ];
        $tab->children[] = new RAASField([
            'type' => 'select',
            'name' => 'menu',
            'caption' => $this->view->_('MENU'),
            'children' => $this->meta['CONTENT']['menus']
        ]);
        $tab->children[] = new RAASField([
            'type' => 'select',
            'name' => 'full_menu',
            'caption' => $this->view->_('MENU_APPEARANCE'),
            'children' => $this->meta['CONTENT']['menu_appearances'],
            'default' => 1
        ]);
        $tab->children[] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = parent::getServiceTab();
        $tab->children[] = $this->getInterfaceField();
        return $tab;
    }


    /**
     * Получает список корневых меню заданного домена,
     * либо без указанного домена
     * @param int $domainId ID# домена
     * @return array<[
     *             'value' => int ID# меню,
     *             'caption' => string Наименование меню
     *         ]>
     */
    public function getMenus($domainId = 0)
    {
        $cache = MenuRecursiveCache::i();
        $menusIds = $cache->getChildrenIds(0);
        $result = [];
        foreach ($menusIds as $menuId) {
            $menuData = $cache->cache[$menuId];
            if ((int)$menuData['domain_id'] &&
                ($menuData['domain_id'] != $domainId)
            ) {
                continue;
            }
            $result[] = [
                'value' => (int)$menuData['id'],
                'caption' => $menuData['name'],
            ];
        }
        return $result;
    }
}
