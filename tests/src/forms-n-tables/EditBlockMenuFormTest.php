<?php
/**
 * Тест класса EditBlockMenuForm
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\FormTab;
use RAAS\User as RAASUser;

/**
 * Тест класса EditBlockMenuForm
 * @covers RAAS\CMS\EditBlockMenuForm
 */
class EditBlockMenuFormTest extends BaseTest
{
    public static $tables = [
        'cms_access_pages_cache',
        'cms_fields',
        'cms_groups',
        'cms_menus',
        'cms_pages',
        'cms_snippet_folders',
        'cms_snippets',
        'cms_users',
    ];

    /**
     * Тест метода getMenus
     */
    public function tesGetMenus()
    {
        $menu1 = new Menu(['domain_id' => 1]);
        $menu1->commit();
        $menu1Id = (int)$menu1->id;
        $menu2 = new Menu(['domain_id' => 2]);
        $menu2->commit();
        $menu2Id = (int)$menu2->id;
        $form = new EditBlockMenuForm();
        $result = $form->getMenus(1);
        $result = array_map(function ($x) {
            return (int)$x['value'];
        }, $result);

        $this->assertContains($menu1, $result);
        $this->assertContains(1, $result); // Верхнее меню (без домена)
        $this->assertNotContains($menu2, $result);

        $result = $form->getMenus(2);
        $result = array_map(function ($x) {
            return (int)$x['value'];
        }, $result);

        $this->assertNotContains($menu1Id, $result);
        $this->assertContains(1, $result); // Верхнее меню (без домена)
        $this->assertContains($menu2Id, $result);

        Menu::delete($menu1);
        Menu::delete($menu2);
    }


    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $form = new EditBlockMenuForm();
        $interfaceField = $form->children['serviceTab']->children['interface_id'];
        $widgetField = $form->children['commonTab']->children['widget_id'];

        $this->assertInstanceOf(InterfaceField::class, $interfaceField);
        $this->assertEquals(MenuInterface::class, $interfaceField->default);
        $this->assertEquals(MenuInterface::class, $interfaceField->meta['rootInterfaceClass']);
        $this->assertInstanceOf(WidgetField::class, $widgetField);
    }
}
