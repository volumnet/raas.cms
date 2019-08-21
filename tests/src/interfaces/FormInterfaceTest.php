<?php
/**
 * Файл теста стандартного интерфейса формы
 */
namespace RAAS\CMS;

/**
 * Класс теста стандартного интерфейса формы
 */
class MaterialInterfaceTest extends BaseDBTest
{
    /**
     * Тест проверки, действительно ли форма отправлена
     * (случай нормальной отправки формы)
     */
    public function testIsFormProceedWithOk()
    {
        $block = Block::spawn(27);
        $interface = new FormInterface();

        $result = $interface->isFormProceed(
            $block,
            $block->Form,
            'POST',
            ['form_signature' => md5('form127')]
        );

        $this->assertTrue($result);
    }


    /**
     * Тест проверки, действительно ли форма отправлена
     * (случай без проверки подписи)
     */
    public function testIsFormProceedWithoutSignature()
    {
        $block = Block::spawn(27);
        $block->Form->signature = 0;
        $interface = new FormInterface();

        $result = $interface->isFormProceed(
            $block,
            $block->Form,
            'POST'
        );

        $this->assertTrue($result);
    }


    /**
     * Тест проверки, действительно ли форма отправлена
     * (случай с неправильной подписью)
     */
    public function testIsFormProceedWithInvalidSignature()
    {
        $block = Block::spawn(27);
        $interface = new FormInterface();

        $result = $interface->isFormProceed(
            $block,
            $block->Form,
            'POST'
        );

        $this->assertFalse($result);
    }


    /**
     * Тест проверки, действительно ли форма отправлена
     * (случай неотправленной формы)
     */
    public function testIsFormProceedWithGetRequest()
    {
        $block = Block::spawn(27);
        $block->Form->signature = 0;
        $interface = new FormInterface();

        $result = $interface->isFormProceed(
            $block,
            $block->Form,
            'GET'
        );

        $this->assertFalse($result);
    }


    /**
     * Тест проверки на корректность регулярного поля
     * (случай прохождения проверки)
     */
    public function testCheckRegularFieldWithOk()
    {
        $field = new Form_Field(5); // Поле "Ваше имя" формы "Обратная связь"
        $post = ['full_name' => 'Test User'];
        $interface = new FormInterface();

        $result = $interface->checkRegularField($field, $post);

        $this->assertNull($result);
    }


    /**
     * Тест проверки на корректность регулярного поля
     * (случай незаполненного поля)
     */
    public function testCheckRegularFieldWithEmptyField()
    {
        $field = new Form_Field(5); // Поле "Ваше имя" формы "Обратная связь"
        $post = ['full_name' => ' '];
        $interface = new FormInterface();

        $result = $interface->checkRegularField($field, $post);

        $this->assertEquals('Необходимо заполнить поле «Ваше имя»', $result);
    }


    /**
     * Тест проверки на корректность регулярного поля
     * (случай неправильного подтверждения пароля)
     */
    public function testCheckRegularFieldWithInvalidPasswordConfirmation()
    {
        $field = new Form_Field(39); // Поле "Пароль" формы "Форма регистрации"
        $post = ['password' => 'aaa', 'password@confirm' => 'bbb'];
        $interface = new FormInterface();

        $result = $interface->checkRegularField($field, $post);

        $this->assertEquals('Пароль не совпадает с подтверждением', $result);
    }


    /**
     * Тест проверки на корректность регулярного поля
     * (случай неправильного значения)
     */
    public function testCheckRegularFieldWithInvalidValue()
    {
        $field = new Form_Field(7); // Поле "E-mail" формы "Обратная связь"
        $field->datatype = 'email'; // Установим явно, т.к. в базе стоит тип "текст"
        $post = ['email' => 'invalidemail'];
        $interface = new FormInterface();

        $result = $interface->checkRegularField($field, $post);

        $this->assertEquals('Поле «E-mail» заполнено неверно', $result);
    }


    /**
     * Тест проверки на корректность антиспам-поля
     * (случай с корректным прохождением captcha)
     */
    public function testCheckAntispamFieldWithCaptchaOk()
    {
        $form = new Form(1); // Обратная связь
        $form->antispam = 'captcha'; // Установим тип проверки "captcha"
        $form->antispam_field_name = 'captcha'; // Установим поле "captcha"
        $post = ['captcha' => '12345'];
        $session = ['captcha_keystring' => '12345'];
        $interface = new FormInterface();

        $result = $interface->checkAntispamField($form, $post, $session);

        $this->assertNull($result);
    }


    /**
     * Тест проверки на корректность антиспам-поля
     * (случай с ошибочным прохождением captcha)
     */
    public function testCheckAntispamFieldWithCaptchaInvalid()
    {
        $form = new Form(1); // Обратная связь
        $form->antispam = 'captcha'; // Установим тип проверки "captcha"
        $form->antispam_field_name = 'captcha'; // Установим поле "captcha"
        $post = ['captcha' => '12345'];
        $session = ['captcha_keystring' => '54321'];
        $interface = new FormInterface();

        $result = $interface->checkAntispamField($form, $post, $session);

        $this->assertEquals('Код с картинки указан неверно', $result);
    }


    /**
     * Тест проверки на корректность антиспам-поля
     * (случай с корректным прохождением скрытого поля)
     */
    public function testCheckAntispamFieldWithHiddenOk()
    {
        $form = new Form(1); // Обратная связь
        $form->antispam = 'hidden'; // Установим тип проверки "hidden"
        $form->antispam_field_name = '_name'; // Установим поле "_name"
        $post = ['_name' => ''];
        $interface = new FormInterface();

        $result = $interface->checkAntispamField($form, $post, $session);

        $this->assertNull($result);
    }


    /**
     * Тест проверки на корректность антиспам-поля
     * (случай с ошибочным прохождением скрытого поля)
     */
    public function testCheckAntispamFieldWithHiddenInvalid()
    {
        $form = new Form(1); // Обратная связь
        $form->antispam = 'hidden'; // Установим тип проверки "hidden"
        $form->antispam_field_name = '_name'; // Установим поле "_name"
        $post = ['_name' => 'aaa'];
        $interface = new FormInterface();

        $result = $interface->checkAntispamField($form, $post, $session);

        $this->assertEquals('Код с картинки указан неверно', $result);
    }


    /**
     * Тест получения "сырого" созданного уведомление
     * (без commit'а и заполненных полей)
     */
    public function testGetRawFeedback()
    {
        $form = new Form(1); // Обратная связь
        $interface = new FormInterface();

        $result = $interface->getRawFeedback($form);

        $this->assertInstanceOf(Feedback::class, $result);
        $this->assertEquals(1, $result->pid);
    }


    /**
     * Тест установки страницы и материала уведомлению обратной связи
     * (случай с явным указанием реферера)
     */
    public function testProcessFeedbackRefererWithReferer()
    {
        $feedback = new Feedback(['pid' => 1]);
        $page = new Page(8); // Страница "Контакты"
        $server = ['HTTP_REFERER' => 'http://test/news/empiricheskiy_kreditor_v_xxi_veke-8/'];
        $interface = new FormInterface();

        $interface->processFeedbackReferer(
            $feedback,
            $page,
            $server
        );

        $this->assertEquals(7, $feedback->page_id); // Новости
        $this->assertEquals(8, $feedback->material_id);
    }


    /**
     * Тест установки страницы и материала уведомлению обратной связи
     * (случай с неявным указанием реферера)
     */
    public function testProcessFeedbackRefererWithoutReferer()
    {
        $feedback = new Feedback(['pid' => 1]);
        $page = new Page(7); // Страница "Контакты"
        $page->Material = new Material(8); // Материал новостей
        $server = [];
        $interface = new FormInterface();

        $interface->processFeedbackReferer(
            $feedback,
            $page,
            $server
        );

        $this->assertEquals(7, $feedback->page_id); // Новости
        $this->assertEquals(8, $feedback->material_id);
    }


    /**
     * Тест подстановки данных пользователя в объект
     */
    public function testProcessUserData()
    {
        $feedback = new Feedback(['pid' => 1]);
        $server = [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'userAgent'
        ];
        $interface = new FormInterface();

        $interface->processUserData($feedback, $server);

        $this->assertEquals('127.0.0.1', $feedback->ip);
        $this->assertEquals('userAgent', $feedback->user_agent);
    }


    /**
     * Тест получения "сырого" созданного материала
     * (без commit'а и заполненных полей),
     * если форма поддерживает создание материала
     */
    public function testGetRawMaterial()
    {
        $form = new Form(6); // Отзывы к товарам
        $interface = new FormInterface();

        $result = $interface->getRawMaterial($form);

        $this->assertInstanceOf(Material::class, $result);
        $this->assertEquals(7, $result->pid); // Тип "Отзывы к товарам"
        $this->assertEquals(0, $result->vis);
    }


    /**
     * Тест проверки, соответствует ли имя файла допустимым расширениям
     * (случай успешной проверки)
     */
    public function testCheckFileMatchesAllowedExtensionsWithOk()
    {
        $interface = new FormInterface();

        $result = $interface->checkFileMatchesAllowedExtensions(
            'aaa.txt',
            '/path/to/aaa.tmp',
            $allowedExtensions = ['txt', 'jpg'],
            true
        );

        $this->assertTrue($result);
    }


    /**
     * Тест проверки, соответствует ли имя файла допустимым расширениям
     * (случай неуспешной проверки)
     */
    public function testCheckFileMatchesAllowedExtensionsWithError()
    {
        $interface = new FormInterface();

        $result = $interface->checkFileMatchesAllowedExtensions(
            'aaa.pdf',
            '/path/to/aaa.tmp',
            $allowedExtensions = ['txt', 'jpg'],
            true
        );

        $this->assertFalse($result);
    }


    /**
     * Тест обработки названия и описания материала
     * (случай с полем наименования)
     */
    public function testProcessMaterialHeaderWithNameField()
    {
        $nameField = new Form_Field([
            'pid' => 6, // Отзывы к товарам
            'name' => '_name_',
            'datatype' => 'text'
        ]);
        $nameField->commit();

        $material = new Material(['pid' => 7]); // Отзывы к товарам
        $feedback = new Feedback(['pid' => 6]); // Отзывы к товарам
        $feedback->commit();
        $feedback->fields['_name_']->addValue('Тестовый отзыв');
        $feedback->fields['_description_']->addValue('Тестовое описание');
        $interface = new FormInterface();

        $result = $interface->processMaterialHeader($material, $feedback);

        $this->assertEquals('Тестовый отзыв', $material->name);
        $this->assertEquals('Тестовое описание', $material->description);

        Feedback::delete($feedback);
        Form_Field::delete($nameField);
    }


    /**
     * Тест обработки названия и описания материала
     * (случай без поля наименования)
     */
    public function testProcessMaterialHeaderWithoutNameField()
    {
        $material = new Material(['pid' => 7]); // Отзывы к товарам
        $feedback = new Feedback(['pid' => 6]); // Отзывы к товарам
        $feedback->commit();
        $feedback->fields['_description_']->addValue('Тестовое описание');
        $interface = new FormInterface();

        $result = $interface->processMaterialHeader($material, $feedback);

        $this->assertEquals('Отзывы к товарам: ' . date('d.m.Y H:i:s'), $material->name);
        $this->assertEquals('Тестовое описание', $material->description);

        Feedback::delete($feedback);
    }


    /**
     * Тест подстановки даты в объект
     * (случай с полем даты и времени)
     */
    public function testProcessObjectDatesWithDatetimeField()
    {
        $material = new Material(['pid' => 7]); // Отзывы к товарам
        $material->commit();
        $interface = new FormInterface();

        $interface->processObjectDates($material, []);

        $this->assertEquals(date('Y-m-d H:i:s'), $material->fields['date']->doRich());

        Material::delete($material);
    }


    /**
     * Тест подстановки даты в объект
     * (случай с полем даты)
     */
    public function testProcessObjectDatesWithDateField()
    {
        $material = new Material(['pid' => 3]); // Новости
        $material->commit();
        $interface = new FormInterface();

        $interface->processObjectDates($material, []);

        $this->assertEquals(date('Y-m-d'), $material->fields['date']->doRich());

        Material::delete($material);
    }


    /**
     * Тест подстановки даты в объект
     * (случай с полем времени)
     */
    public function testProcessObjectDatesWithTimeField()
    {
        $material = new Material(['pid' => 7]); // Отзывы к товарам
        $field = new Material_Field(55);
        $field->datatype = 'time';
        $field->commit();
        $material->commit();
        $interface = new FormInterface();

        $interface->processObjectDates($material, []);

        $this->assertEquals(date('H:i:s'), $material->fields['date']->doRich());

        $field->datatype = 'datetime';
        $field->commit();
        Material::delete($material);
    }


    /**
     * Тест подстановки данных пользователя в объект
     */
    public function testProcessObjectUserData()
    {

    }


    /**
     * Тест обработки регулярного поля
     */
    public function testProcessRegularField()
    {

    }


    /**
     * Тест обработки файлового поля
     */
    public function testProcessFileField()
    {

    }


    /**
     * Тест получения списка адресов формы
     */
    public function testParseFormAddresses()
    {

    }


    /**
     * Тест получения заголовка e-mail сообщения
     */
    public function testGetEmailSubject()
    {

    }


    /**
     * Тест получения тела сообщения
     */
    public function testGetMessageBody()
    {

    }


    /**
     * Тест получения значения поля "От"
     */
    public function testGetFromName()
    {

    }


    /**
     * Тест получения значение обратного адреса
     */
    public function testgetFromEmail()
    {

    }


    /**
     * Тест проверки на корректность файлового поля
     */
    public function testCheckFileField()
    {

    }


    /**
     * Тест проверки правильности заполнения формы
     */
    public function testCheck()
    {

    }


    /**
     * Тест обработки кастомных полей объекта
     */
    public function testProcessObjectFields()
    {

    }


    /**
     * Тест обработки объекта, порождаемого формой (материал или уведомление)
     */
    public function testProcessObject()
    {

    }


    /**
     * Тест уведомления администратора о заполненной форме
     */
    public function testNotify()
    {

    }


    /**
     * Тест отработки формы
     */
    public function testProcessForm()
    {

    }


    /**
     * Тестирует отработку интерфейса
     */
    public function testProcess()
    {

    }
}
