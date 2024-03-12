<?php
/**
 * Файл теста рендерера поля уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера поля уведомления
 * @covers RAAS\CMS\NotificationFieldRenderer
 */
class NotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    /**
     * Провайдер данных для метода testSpawn()
     * @return array <pre>array<[
     *     string Тип данных поля,
     *     string Класс рендерера
     * ]></pre>
     */
    public function spawnDataProvider()
    {
        return [
            ['date', DateNotificationFieldRenderer::class],
            ['datetime-local', DateTimeNotificationFieldRenderer::class],
            ['color', ColorNotificationFieldRenderer::class],
            ['email', EmailNotificationFieldRenderer::class],
            ['tel', TelNotificationFieldRenderer::class],
            ['url', URLNotificationFieldRenderer::class],
            ['file', FileNotificationFieldRenderer::class],
            ['image', ImageNotificationFieldRenderer::class],
            ['htmlarea', HtmlAreaNotificationFieldRenderer::class],
            ['material', MaterialNotificationFieldRenderer::class],
            ['checkbox', CheckboxNotificationFieldRenderer::class],
            ['text', NotificationFieldRenderer::class],
        ];
    }

    /**
     * Тест рендера поля подписи - случай с текстовым полем
     * @dataProvider spawnDataProvider
     * @param string $datatype Тип данных поля
     * @param string $rendererClassName Класс рендерера
     */
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
    public function filterValueDataProvider()
    {
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
     * @dataProvider filterValueDataProvider
     * @param mixed $value Значение для фильтрации
     * @param bool $isFiltered Результат фильтрации
     */
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
    public function getValueHTMLDataProvider()
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
