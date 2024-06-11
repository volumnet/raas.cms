<?php
/**
 * Форма редактирования блока меню
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Field as RAASField;
use RAAS\FormTab;

/**
 * Класс формы редактирования блока меню
 */
class EditBlockMenuForm extends EditBlockForm
{
    const DEFAULT_BLOCK_CLASSNAME = Block_Menu::class;

    protected function getCommonTab(): FormTab
    {
        $tab = parent::getCommonTab();
        $tmp_menu = new Menu();
        $domain = $this->meta['Parent']->Domain ?? new Page();
        $this->meta['CONTENT']['menus'] = $this->getMenus((int)$domain->id);
        $this->meta['CONTENT']['menu_appearances'][] = [
            'value' => 1,
            'caption' => $this->view->_('FULL_MENU')
        ];
        $this->meta['CONTENT']['menu_appearances'][] = [
            'value' => 0,
            'caption' => $this->view->_('PAGE_SUBSECTIONS')
        ];
        $tab->children['menu'] = new RAASField([
            'type' => 'select',
            'name' => 'menu',
            'caption' => $this->view->_('MENU'),
            'children' => $this->meta['CONTENT']['menus'],
            'required' => true,
            'placeholder' => '--',
        ]);
        $tab->children['full_menu'] = new RAASField([
            'type' => 'select',
            'name' => 'full_menu',
            'caption' => $this->view->_('MENU_APPEARANCE'),
            'children' => $this->meta['CONTENT']['menu_appearances'],
            'default' => 1
        ]);
        $tab->children['widget_id'] = $this->getWidgetField();
        return $tab;
    }


    protected function getServiceTab(): FormTab
    {
        $tab = parent::getServiceTab();
        $tab->children['interface_id'] = $this->getInterfaceField();
        return $tab;
    }


    /**
     * Получает список корневых меню заданного домена,
     * либо без указанного домена
     * @param int $domainId ID# домена
     * @return array <pre><code>array<[
     *     'value' => int ID# меню,
     *     'caption' => string Наименование меню
     * ]></code></pre>
     */
    public function getMenus(int $domainId = 0): array
    {
        $cache = MenuRecursiveCache::i();
        $menusIds = $cache->getChildrenIds(0);
        $result = [];
        foreach ($menusIds as $menuId) {
            $menuData = $cache->cache[$menuId];
            if (!(int)$menuData['domain_id'] || ($menuData['domain_id'] == $domainId)) {
                $result[] = [
                    'value' => (int)$menuData['id'],
                    'caption' => $menuData['name'],
                ];
            }
        }
        return $result;
    }
}
