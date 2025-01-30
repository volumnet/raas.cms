<?php
/**
 * Тест класса EditTemplateForm
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\FormTab;
use RAAS\User as RAASUser;

/**
 * Тест класса EditTemplateForm
 */
#[CoversClass(EditTemplateForm::class)]
class EditTemplateFormTest extends BaseTest
{
    public static $tables = [
        'cms_pages',
        'cms_templates',
    ];


    public static function setUpBeforeClass(): void
    {
        Application::i()->initPackages();
        parent::setUpBeforeClass();
    }


    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $form = new EditTemplateForm(['Item' => new Template(1)]);

        $this->assertInstanceOf(FormTab::class, $form->children['common']);
        $this->assertInstanceOf(FormTab::class, $form->children['layout']);
        $this->assertInstanceOf(FormTab::class, $form->children['service']);
    }


    /**
     * Тест получения свойства view
     */
    public function testGetView()
    {
        $form = new EditTemplateForm();

        $this->assertInstanceOf(ViewSub_Dev::class, $form->view);
    }


    /**
     * Тест получения наследуемых свойств
     */
    public function testGetDefault()
    {
        $form = new EditTemplateForm(['Item' => new Template(1)]);

        $result = $form->Item;

        $this->assertInstanceOf(Template::class, $result);
        $this->assertEquals(1, $result->id);
    }


    /**
     * Тест метода process() - случай с сохранением
     */
    public function testProcessWithExport()
    {
        // Создадим, чтобы был макет
        $template = new Template();
        $template->commit();

        $oldPost = $_POST;
        $oldServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $description = '<' . '?php
            $Page->location(\'test\');
            $Page->location(\'test1\');
            $Page->location(\'test2\');';
        $_POST = [
            'description' => $description,
            'width' => '640', // Максимум 680
            'height' => '1920',
            'location' => [
                'test' => 'test',
                'test1' => 'test1',
                'test2' => 'test2',
            ],
            'location-left' => [
                'test' => '0',
                'test1' => '10',
                'test2' => '20',
            ],
            'location-top' => [
                'test' => '0',
                'test1' => '100',
                'test2' => '200',
            ],
            'location-width' => [
                'test' => '640',
                'test1' => '620',
                'test2' => '600',
            ],
            'location-height' => [
                'test' => '50',
                'test1' => '60',
                'test2' => '70',
            ],
        ];
        $form = new EditTemplateForm(['Item' => $template]);

        $result = $form->process();

        $template = new Template($template->id);

        $this->assertInstanceOf(Template::class, $template);
        $this->assertNotEmpty($template->id);
        $this->assertEquals($description, $template->description);
        $this->assertEquals(640, $template->width);
        $this->assertEquals(1920, $template->height);
        $this->assertEquals('test', $template->locations['test']->urn);
        $this->assertEquals(0, $template->locations['test']->x);
        $this->assertEquals(0, $template->locations['test']->y);
        $this->assertEquals(640, $template->locations['test']->width);
        $this->assertEquals(50, $template->locations['test']->height);
        $this->assertEquals('test1', $template->locations['test1']->urn);
        $this->assertEquals(10, $template->locations['test1']->x);
        $this->assertEquals(100, $template->locations['test1']->y);
        $this->assertEquals(620, $template->locations['test1']->width);
        $this->assertEquals(60, $template->locations['test1']->height);
        $this->assertEquals('test2', $template->locations['test2']->urn);
        $this->assertEquals(20, $template->locations['test2']->x);
        $this->assertEquals(200, $template->locations['test2']->y);
        $this->assertEquals(600, $template->locations['test2']->width);
        $this->assertEquals(70, $template->locations['test2']->height);

        $_POST = $oldPost;
        $_SERVER = $oldServer;
        Template::delete($template);
    }
}
