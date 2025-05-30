<?php
/**
 * Файл теста рендерера поля уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера поля уведомления
 */
#[CoversClass(NotificationFieldRenderer::class)]
class NotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    public static $tables = [
        'cms_access', // Не используется в полном тесте
        'cms_blocks', // Не используется в полном тесте
        'cms_fields',
        'cms_groups', // Не используется в полном тесте
        'cms_material_types', // Не используется в полном тесте
        'cms_materials', // Не используется в полном тесте
        'cms_pages', // Не используется в полном тесте
        'cms_templates', // Не используется в полном тесте
        'cms_users', // Не используется в полном тесте
        'users', // Не используется в полном тесте
    ];

    /**
     * Тест рендера поля подписи - случай с текстовым полем
     * @param string $datatype Тип данных поля
     * @param string $rendererClassName Класс рендерера
     */
    #[TestWith(['date', DateNotificationFieldRenderer::class])]
    #[TestWith(['datetime-local', DateTimeNotificationFieldRenderer::class])]
    #[TestWith(['color', ColorNotificationFieldRenderer::class])]
    #[TestWith(['email', EmailNotificationFieldRenderer::class])]
    #[TestWith(['tel', TelNotificationFieldRenderer::class])]
    #[TestWith(['url', URLNotificationFieldRenderer::class])]
    #[TestWith(['file', FileNotificationFieldRenderer::class])]
    #[TestWith(['image', ImageNotificationFieldRenderer::class])]
    #[TestWith(['htmlarea', HtmlAreaNotificationFieldRenderer::class])]
    #[TestWith(['material', MaterialNotificationFieldRenderer::class])]
    #[TestWith(['checkbox', CheckboxNotificationFieldRenderer::class])]
    #[TestWith(['text', NotificationFieldRenderer::class])]
    public function testSpawn($datatype, $rendererClassName)
    {
        $field = new Form_Field(['datatype' => $datatype]);

        $result = NotificationFieldRenderer::spawn($field);

        $this->assertInstanceOf($rendererClassName, $result);
    }


    /**
     * Провайдер данных для метода testFilterValue
     * @return array <pre><code>array<[
     *     mixed Значение для фильтрации
     *     bool Результат фильтрации
     * ]></code></pre>
     */
    public static function filterValueDataProvider()
    {
        static::installTables();
        return [
            [new Page(), false],
            [new Page(['id' => 1]), true],
            ['aaa', true],
            ['', false],
            [['aaa'], true],
            [[], false],
        ];
    }


    /**
     * Тест фильтрации значений
     * @param mixed $value Значение для фильтрации
     * @param bool $isFiltered Результат фильтрации
     */
    #[DataProvider('filterValueDataProvider')]
    public function testFilterValue($value, $isFiltered)
    {
        $renderer = new NotificationFieldRenderer(new Form_Field([
            'type' => 'text',
        ]));

        $result = $renderer->filterValue($value);

        $this->assertEquals($isFiltered, $result);
    }


    /**
     * Провайдер данных для метода testGetValueHTML
     * @return array <pre><code>array<[
     *     mixed Значение
     *     bool Рендеринг для администратора
     *     bool Рендеринг для SMS
     *     string Искомый результат
     * ]></code></pre>
     */
    public static function getValueHTMLDataProvider()
    {
        return [
            ['"тестовая строка"', false, false, '&quot;тестовая строка&quot;'],
            ['"тестовая строка"', false, true, '"тестовая строка"'],
        ];
    }


    /**
     * Тест получения массива HTML-значений
     */
    public function testGetValuesHTMLArray()
    {
        $field = new FormFieldMock([
            'datatype' => 'text',
            'name' => 'Название',
        ]);
        $renderer = new NotificationFieldRenderer($field);

        $result = $renderer->getValuesHTMLArray();

        $this->assertEquals(['aaa', 'bbb', '&quot;ccc'], $result);
    }


    /**
     * Тест получения массива HTML-значений - случай с пустыми значениями
     */
    public function testGetValuesHTMLArrayWithEmptyValues()
    {
        $field = new FormFieldMock([
            'datatype' => 'text',
            'name' => 'Название',
            'urn' => 'aaa',
        ]);
        $owner = new Page(1);
        $renderer = new NotificationFieldRenderer($field, $owner);

        $result = $renderer->getValuesHTMLArray();

        $this->assertEmpty($result);
    }


    /**
     * Тест получения массива HTML-значений - случай с самостоятельным полем
     */
    public function testGetValuesHTMLArrayWithOrphanField()
    {
        $field = new Material_Field(14);
        $renderer = new NotificationFieldRenderer($field);

        $result = $renderer->getValuesHTMLArray();

        $this->assertEmpty($result);
    }


    /**
     * Тест получения массива HTML-значений (случай с кастомным владельцем)
     */
    public function testGetValuesHTMLArrayWithCustomOwner()
    {
        $field = new FormFieldMock([
            'urn' => 'testfield',
            'datatype' => 'text',
            'name' => 'Название',
        ]);
        $owner = new OwnerMock();
        $renderer = new NotificationFieldRenderer($field, $owner);

        $result = $renderer->getValuesHTMLArray();

        $this->assertEquals(['aaa', 'bbb', '&quot;ccc'], $result);
    }


    /**
     * Тест получения массива HTML-значений
     * (случай с кастомным владельцем - если у владельца нет такого поля)
     */
    public function testGetValuesHTMLArrayWithCustomOwnerWithNoOwnerField()
    {
        $field = new FormFieldMock([
            'urn' => 'login',
            'datatype' => 'text',
            'name' => 'Название',
        ]);
        $owner = new OwnerMock();
        $renderer = new NotificationFieldRenderer($field, $owner);

        $result = $renderer->getValuesHTMLArray();

        $this->assertEquals(['testuser'], $result);
    }


    /**
     * Тест получения массива HTML-значений
     * (случай с кастомным владельцем - по POST-данным)
     */
    public function testGetValuesHTMLArrayWithCustomOwnerWithPostData()
    {
        $field = new FormFieldMock([
            'urn' => 'password',
            'datatype' => 'text',
            'name' => 'Название',
        ]);
        $owner = new OwnerMock();
        $_POST['password'] = 'pass';
        $renderer = new NotificationFieldRenderer($field, $owner);

        $result = $renderer->getValuesHTMLArray();

        $this->assertEquals(['pass'], $result);
    }


    /**
     * Тест рендера
     */
    public function testRender()
    {
        $field = new FormFieldMock([
            'datatype' => 'text',
            'name' => 'Название',
        ]);
        $renderer = new NotificationFieldRenderer($field);

        $result = $renderer->render();

        $this->assertEquals('<div>Название: aaa, bbb, &quot;ccc</div>', $result);
    }


    /**
     * Тест рендера - случай с SMS
     */
    public function testRenderWithSMS()
    {
        $field = new FormFieldMock([
            'datatype' => 'text',
            'name' => 'Название',
        ]);
        $renderer = new NotificationFieldRenderer($field);

        $result = $renderer->render(['sms' => true]);

        $this->assertEquals('Название: aaa, bbb, "ccc' . "\n", $result);
    }


    /**
     * Тест рендера - случай с пустым значением
     */
    public function testRenderWithEmpty()
    {
        $field = new FormFieldMock([
            'datatype' => 'text',
            'name' => 'Название',
            'isEmpty' => true,
        ]);
        $renderer = new NotificationFieldRenderer($field);

        $result = $renderer->render();

        $this->assertEquals('', $result);
    }
}
