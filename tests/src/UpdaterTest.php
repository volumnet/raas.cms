<?php
/**
 * Файл теста менеджера обновлений
 * (поскольку мы не можем хранить все предыдущие версии, тестируем текущее состояние, без покрытия менеджера обновлений)
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;

/**
 * Класс теста обновлений
 */
class UpdaterTest extends BaseTest
{
    public static $tables = [
        'cms_blocks',
        'cms_blocks_form',
        'cms_blocks_material',
        'cms_blocks_material_filter',
        'cms_blocks_material_sort',
        'cms_blocks_menu',
        'cms_feedback',
        'cms_snippets',
        'cms_templates',
    ];


    /**
     * Тест состояния версии 4.3.92 - сниппеты и шаблоны в файлах
     */
    public function testState040392SnippetsAndTemplatesInFiles()
    {
        $snippet = Snippet::importByURN('banners');
        $snippet->description = 'banners';
        $snippet->commit();
        $template = new Template(1);
        $template->description = 'template';
        $template->commit();

        $this->assertFileExists(Application::i()->baseDir . '/inc/snippets/.htaccess');
        $this->assertEquals('banners', file_get_contents(Application::i()->baseDir . '/inc/snippets/banners.tmp.php'));
        $this->assertEquals('template', file_get_contents(Application::i()->baseDir . '/inc/snippets/template1.tmp.php'));

        $sqlQuery = "SHOW FIELDS FROM cms_snippets";
        $sqlResult = Application::i()->SQL->get($sqlQuery);
        $result = [];
        foreach ($sqlResult as $sqlRow) {
            $result[$sqlRow['Field']] = $sqlRow;
        }

        $this->assertNull($result['post_date'] ?? null);
        $this->assertNull($result['modify_date'] ?? null);
        $this->assertNull($result['description'] ?? null);
        $this->assertStringContainsString('varchar', $result['locked']['Type']);

        $sqlQuery = "SHOW FIELDS FROM cms_templates";
        $sqlResult = Application::i()->SQL->get($sqlQuery);
        $result = [];
        foreach ($sqlResult as $sqlRow) {
            $result[$sqlRow['Field']] = $sqlRow;
        }

        $this->assertNull($result['post_date'] ?? null);
        $this->assertNull($result['modify_date'] ?? null);
        $this->assertNull($result['description'] ?? null);
        $this->assertNull($result['visual'] ?? null);
        $this->assertNull($result['background'] ?? null);
        $this->assertNull($result['urn'] ?? null);

        $lockedMapping = [
            '__raas_form_notify' => 'form_notification.php',
            '__raas_shop_order_notify' => 'shop/form_notification.php',
            '__raas_users_recovery_notify' => 'users/recovery_notification.php',
            '__raas_users_register_notify' => 'users/register_notification.php',
        ];
        foreach ($lockedMapping as $urn => $symlink) {
            $snippet = Snippet::importByURN($urn);
            $this->assertEquals($symlink, $snippet->locked);
        }
    }


    /**
     * Тест состояния версии 4.3.87 - чтобы не было сниппетов интерфейсов и в блоках поменялись на классы
     */
    public function testState040387ReplaceSnippetsWithInterfacesClassnames()
    {
        $snippet = Snippet::importByURN('__raas_form_interface');
        $block = Block::spawn(6); // Блок всплывающего окна обратной связи

        $this->assertNull($snippet);
        $this->assertEmpty($block->interface_id);
        $this->assertEquals(FormInterface::class, $block->interface_classname);


        $snippet = Snippet::importByURN('__raas_material_interface');
        $block = Block::spawn(23); // Новости на главной

        $this->assertNull($snippet);
        $this->assertEmpty($block->interface_id);
        $this->assertEquals(MaterialInterface::class, $block->interface_classname);


        $snippet = Snippet::importByURN('__raas_menu_interface');
        $cacheSnippet = Snippet::importByURN('__raas_cache_interface');
        $block = Block::spawn(14); // Блок верхнего меню

        $this->assertNull($snippet);
        $this->assertNull($cacheSnippet);
        $this->assertEmpty($block->interface_id);
        $this->assertEquals(MenuInterface::class, $block->interface_classname);
        $this->assertEmpty($block->cache_interface_id);
        $this->assertEquals(CacheInterface::class, $block->cache_interface_classname);


        $snippet = Snippet::importByURN('__raas_search_interface');

        $this->assertNull($snippet);


        $snippet = Snippet::importByURN('__raas_watermark_interface');

        $this->assertNull($snippet);
    }


    /**
     * Тест состояния версии 4.3.87 - чтобы в cms_fields были поля preprocessor_classname и postprocessor_classname
     */
    public function testState040387FieldsProcessorsClassnames()
    {
        $sqlQuery = "SHOW FIELDS FROM cms_fields";
        $sqlResult = Application::i()->SQL->get($sqlQuery);
        $result = [];
        foreach ($sqlResult as $sqlRow) {
            $result[$sqlRow['Field']] = $sqlRow;
        }

        $this->assertEquals('preprocessor_classname', $result['preprocessor_classname']['Field']);
        $this->assertEquals('postprocessor_classname', $result['postprocessor_classname']['Field']);
        $this->assertNotEmpty($result['preprocessor_classname']['Key']);
        $this->assertNotEmpty($result['postprocessor_classname']['Key']);
    }


    /**
     * Тест состояния версии 4.3.87 - чтобы в cms_forms был ключ interface_id
     */
    public function testState040387FormsWithKeys()
    {
        $sqlQuery = "SHOW FIELDS FROM cms_forms";
        $sqlResult = Application::i()->SQL->get($sqlQuery);
        $result = [];
        foreach ($sqlResult as $sqlRow) {
            $result[$sqlRow['Field']] = $sqlRow;
        }

        $this->assertNotEmpty($result['interface_id']['Key']);
    }


    /**
     * Тест состояния версии 4.3.87 - чтобы в cms_blocks были поля interface_classname и cache_interface_classname,
     * а также ключи interface_id, widget_id, interface_classname и cache_interface_classname
     */
    public function testState040387BlocksInterfaceClassname()
    {
        $sqlQuery = "SHOW FIELDS FROM cms_blocks";
        $sqlResult = Application::i()->SQL->get($sqlQuery);
        $result = [];
        foreach ($sqlResult as $sqlRow) {
            $result[$sqlRow['Field']] = $sqlRow;
        }

        $this->assertEquals('interface_classname', $result['interface_classname']['Field']);
        $this->assertEquals('cache_interface_classname', $result['cache_interface_classname']['Field']);
        $this->assertNotEmpty($result['interface_id']['Key']);
        $this->assertNotEmpty($result['widget_id']['Key']);
        $this->assertNotEmpty($result['interface_classname']['Key']);
        $this->assertNotEmpty($result['cache_interface_classname']['Key']);
    }


    /**
     * Тест состояния версии 4.3.87 - чтобы в cms_feedback.user_agent по умолчанию была пустая строка
     */
    public function testState040387UserAgent()
    {
        $sqlQuery = "SELECT DEFAULT(user_agent) FROM cms_feedback";
        $sqlResult = Application::i()->SQL->getvalue($sqlQuery);

        $this->assertEquals('', $sqlResult);
    }
}
