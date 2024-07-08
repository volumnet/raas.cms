<?php
/**
 * Тест класса Template
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use RAAS\Application;
use RAAS\Controller_Frontend as RAASControllerFrontend;
use RAAS\User as RAASUser;

/**
 * Тест класса Template
 * @covers RAAS\CMS\Template
 */
class TemplateTest extends BaseTest
{
    public static $tables = [
        'cms_pages',
        'cms_templates',
        'users',
    ];

    /**
     * Тест получения свойства style
     */
    public function testGetStyle()
    {
        $template = new Template(['width' => 720, 'height' => 1920]);

        $result = $template->style;

        $this->assertStringContainsString('width: 720px;', $result);
        $this->assertStringContainsString('height: 1920px;', $result);
    }


    /**
     * Тест получения свойства filename
     */
    public function testGetFilename()
    {
        $template = new Template();
        $template->commit();

        $result = $template->filename;
        $expected = Application::i()->baseDir . '/inc/snippets/template' . (int)$template->id . '.tmp.php';

        $this->assertEquals(realpath($expected), realpath($result));

        Template::delete($template);
    }


    /**
     * Тест получения свойства filename - случай с несохраненным шаблоном
     */
    public function testGetFilenameWithNoId()
    {
        $template = new Template();

        $result = $template->filename;

        $this->assertNull($result);

        Template::delete($template);
    }


    /**
     * Тест получения свойства post_date
     */
    public function testGetPostDate()
    {
        $template = new Template(1);
        $filename = Application::i()->baseDir . '/inc/snippets/template1.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $result = $template->post_date;

        $this->assertEquals('0000-00-00 00:00:00', $result);

        touch($filename);

        $result = $template->post_date;

        $this->assertGreaterThan('0000-00-00 00:00:00', $result);

        unlink($filename);
    }


    /**
     * Тест получения свойства modify_date
     */
    public function testGetModifyDate()
    {
        $template = new Template(1);
        $filename = Application::i()->baseDir . '/inc/snippets/template1.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $result = $template->modify_date;

        $this->assertEquals('0000-00-00 00:00:00', $result);

        touch($filename);

        $result = $template->modify_date;

        $this->assertGreaterThan('0000-00-00 00:00:00', $result);

        unlink($filename);
    }


    /**
     * Тест получения свойства locations
     */
    public function testGetLocations()
    {
        $description = '<' . '?php
            $Page->location(\'test\');
            $Page->location(\'test1\');
            $Page->location(\'test2\'); ';
        $template = new Template([
            'description' => $description,
            'width' => 720,
            'height' => 100,
            'locs' => [['urn' => 'test', 'x' => 0, 'y' => 0, 'width' => 200, 'height' => 50]]
        ]);
        $template->commit();

        $result = $template->locations;

        $this->assertCount(3, $result);
        $this->assertInstanceOf(Location::class, $result['test']);
        $this->assertEquals('test', $result['test']->urn);
        $this->assertEquals(0, $result['test']->x);
        $this->assertEquals(0, $result['test']->y);
        $this->assertEquals(200, $result['test']->width);
        $this->assertEquals(50, $result['test']->height);
        $this->assertInstanceOf(Location::class, $result['test1']);
        $this->assertEquals('test1', $result['test1']->urn);
        $this->assertEquals(0, $result['test1']->x);
        $this->assertEquals(0, $result['test1']->y);
        $this->assertEquals(Location::MIN_WIDTH, $result['test1']->width);
        $this->assertEquals(Location::MIN_HEIGHT, $result['test1']->height);
        $this->assertInstanceOf(Location::class, $result['test2']);
        $this->assertEquals('test2', $result['test2']->urn);
        $this->assertEquals(0, $result['test2']->x);
        $this->assertEquals(50, $result['test2']->y);
        $this->assertEquals(Location::MIN_WIDTH, $result['test2']->width);
        $this->assertEquals(Location::MIN_HEIGHT, $result['test2']->height);

        Template::delete($template);
    }


    /**
     * Тест методов commit() и delete()
     */
    public function testCommitDelete()
    {
        Application::i()->user = new RAASUser(1);
        $template = new Template(['description' => 'test', 'locs' => ['test' => ['x' => 0]]]);
        $template->commit();
        $templateId = $template->id;

        $template = new Template($templateId);

        $this->assertFileExists(Application::i()->baseDir . '/inc/snippets/template' . $templateId . '.tmp.php');
        $this->assertEquals(date('Y-m-d'), date('Y-m-d', strtotime($template->post_date)));
        $this->assertEquals(date('Y-m-d'), date('Y-m-d', strtotime($template->modify_date)));
        $this->assertEquals(1, $template->author_id);
        $this->assertEquals(1, $template->editor_id);
        $this->assertEquals('{"test":{"x":0}}', $template->locations_info);
        $this->assertEquals('test', $template->description);

        Template::delete($template);

        $this->assertFileDoesNotExist(Application::i()->baseDir . '/inc/snippets/template' . $templateId . '.tmp.php');
    }


    /**
     * Тест метода process()
     */
    public function testProcess()
    {
        $template = new Template(['description' => '<' . '?php return $aaa; ']);
        $template->commit();

        $result = $template->process(['aaa' => 'bbb']);

        $this->assertEquals('bbb', $result);

        Template::delete($template);
    }
}
