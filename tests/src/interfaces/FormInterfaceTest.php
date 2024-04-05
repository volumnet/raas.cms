<?php
/**
 * Файл теста стандартного интерфейса формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use RAAS\Application;
use RAAS\Controller_Frontend as RAASControllerFrontend;

/**
 * Класс теста стандартного интерфейса формы
 * @covers \RAAS\CMS\FormInterface
 */
class FormInterfaceTest extends BaseTest
{
    public static $tables = [
        'attachments',
        'cms_access',
        'cms_access_materials_cache',
        'cms_blocks',
        'cms_blocks_form',
        'cms_blocks_material',
        'cms_blocks_pages_assoc',
        'cms_data',
        'cms_feedback',
        'cms_fields',
        'cms_fields_form_vis',
        'cms_forms',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_pages',
        'cms_snippets',
        'cms_users', // Только для одиночного теста
        'registry',
    ];

    /**
     * Форма для проверки
     * @var Form
     */
    public $form;

    /**
     * Блок для проверки
     * @var Block_Form
     */
    public $block;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        RAASControllerFrontend::i()->exportLang(Application::i(), 'ru');
        RAASControllerFrontend::i()->exportLang(Package::i(), 'ru');
    //     $formInterface = Snippet::importByURN('__raas_form_interface');
    //     $formWidget = Snippet::importByURN('feedback');
    //     $block = new Block_Form([
    //         'name' => 'Обратная связь',
    //         'form' => 1,
    //         'location' => 'content',
    //         'vis' => true,
    //         'interface_id' => $formInterface->id,
    //         'widget_id' => $formWidget->id,
    //     ]);
    //     $block->commit();
    //     $this->block = $block;
    }

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
     * (случай прохождения проверки в множественном поле)
     */
    public function testCheckRegularFieldWithMultiple()
    {
        $field = new Form_Field([
            'pid' => 6, // Отзывы к товарам
            'name' => 'aaa',
            'datatype' => 'text',
            'multiple' => 1
        ]);
        $field->commit();
        $post = ['aaa' => ['Test User']];
        $interface = new FormInterface();

        $result = $interface->checkRegularField($field, $post);

        $this->assertNull($result);

        Form_Field::delete($field);
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
     * (случай незаполненного поля)
     */
    public function testCheckRegularFieldWithConditional()
    {
        $field = new Form_Field(5); // Поле "Ваше имя" формы "Обратная связь"
        $post = ['full_name' => ' '];
        $interface = new FormInterface();
        $interface->conditionalRequiredFields = [
            'full_name' => function ($field, $post) {
                return false;
            },
        ];

        $result = $interface->checkRegularField($field, $post);

        $this->assertNull($result);
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

        $this->assertEquals('Антиспам-система не пройдена', $result);
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

        $result = $interface->checkAntispamField($form, $post, []);

        $this->assertNull($result);
    }


    /**
     * Тест проверки на корректность антиспам-поля
     * (случай с ошибочным прохождением анализа данных)
     */
    public function testCheckAntispamFieldWithSmartInvalid()
    {
        $form = new Form(1); // Обратная связь
        $form->antispam = 'smart'; // Установим тип проверки "hidden"
        $form->antispam_field_name = '_name'; // Установим поле "_name"
        $post = ['full_name' => 'Mr.Smith', 'last_name' => 'www.yandex.com'];
        $interface = new FormInterface(null, new Page(['lang' => 'ru']));

        $result = $interface->checkAntispamField($form, $post, []);

        $this->assertEquals('Антиспам-система не пройдена', $result);
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

        $result = $interface->checkAntispamField($form, $post, []);

        $this->assertEquals('Антиспам-система не пройдена', $result);
    }


    /**
     * Тест проверки на корректность антиспам-поля
     * (случай с ошибочным прохождением скрытого поля в случае, если установлен тип "Анализ данных")
     */
    public function testCheckAntispamFieldWithHiddenSmartInvalid()
    {
        $form = new Form(1); // Обратная связь
        $form->antispam = 'smart'; // Установим тип проверки "hidden"
        $form->antispam_field_name = '_name'; // Установим поле "_name"
        $post = ['_name' => 'aaa'];
        $interface = new FormInterface(null, new Page(['lang' => 'ru']));

        $result = $interface->checkAntispamField($form, $post, []);

        $this->assertEquals('Антиспам-система не пройдена', $result);
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
        $server = [
            'HTTP_REFERER' => 'http://test/news/empiricheskiy_kreditor_v_xxi_veke-8/'
        ];
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
     * Тест подстановки данных пользователя в объект - случай с прокси
     */
    public function testProcessUserDataWithProxy()
    {
        $feedback = new Feedback(['pid' => 1]);
        $server = [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_X_FORWARDED_FOR' => '11.11.11.11, 22.22.22.22, 33.33.33.33',
        ];
        $interface = new FormInterface();

        $interface->processUserData($feedback, $server);

        $this->assertEquals('11.11.11.11', $feedback->ip);
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
     * (случай отсутствия файла)
     */
    public function testCheckFileMatchesAllowedExtensionsWithNoFile()
    {
        $interface = new FormInterface();

        $result = $interface->checkFileMatchesAllowedExtensions(
            'aaa.txt',
            '/path/to/aaa.tmp',
            $allowedExtensions = ['txt', 'jpg'],
            false
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
        $form = new Form(6);
        $interface = new FormInterface();

        $result = $interface->processMaterialHeader(
            $material,
            new Form(6),
            [
                '_name_' => 'Тестовый отзыв',
                '_description_' => 'Тестовое описание'
            ]
        );

        $this->assertEquals('Тестовый отзыв', $material->name);
        $this->assertEquals('Тестовое описание', $material->description);

        Form_Field::delete($nameField);
    }


    /**
     * Тест обработки названия и описания материала
     * (случай без поля наименования)
     */
    public function testProcessMaterialHeaderWithoutNameField()
    {
        $material = new Material(['pid' => 7]); // Отзывы к товарам
        $form = new Form(6);
        $interface = new FormInterface();

        $t = time();
        $result = $interface->processMaterialHeader(
            $material,
            $form,
            [
                '_description_' => 'Тестовое описание'
            ]
        );

        $this->assertEquals(
            'Отзывы к товарам: ' . date('d.m.Y H:i:s', $t),
            $material->name
        );
        $this->assertEquals('Тестовое описание', $material->description);
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

        $t = time();
        $interface->processObjectDates($material, []);

        $this->assertEquals(date('Y-m-d H:i:s', $t), $material->fields['date']->getRawValue());

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

        $t = time();
        $interface->processObjectDates($material, []);

        $this->assertEquals(date('Y-m-d', $t), $material->fields['date']->doRich());

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

        $t = time();
        $interface->processObjectDates($material, []);

        $this->assertEquals(date('H:i:s', $t), $material->fields['date']->getRawValue());

        $field->datatype = 'datetime-local';
        $field->commit();
        Material::delete($material);
    }


    /**
     * Тест подстановки данных пользователя в объект
     */
    public function testProcessObjectUserData()
    {
        $ipField = new Material_Field([
            'pid' => 7,
            'datatype' => 'text',
            'name' => 'ip'
        ]);
        $ipField->commit();
        $userAgentField = new Material_Field([
            'pid' => 7,
            'datatype' => 'text',
            'name' => 'user_agent'
        ]);
        $server = [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'userAgent'
        ];
        $userAgentField->commit();
        $material = new Material(['name' => 'test', 'pid' => 7]); // Отзывы к товарам
        $material->commit();
        $materialId = $material->id;

        $interface = new FormInterface();

        $interface->processObjectUserData($material, $server);
        $material = new Material($materialId);

        $this->assertEquals('127.0.0.1', $material->ip);
        $this->assertEquals('userAgent', $material->user_agent);

        Material::delete($material);
        Material_Field::delete($ipField);
        Material_Field::delete($userAgentField);
    }


    /**
     * Тест подстановки данных UTM-меток в объект
     */
    public function testProcessUTM()
    {
        $utmSourceField = new Material_Field([
            'pid' => 7,
            'datatype' => 'text',
            'name' => 'utm_source'
        ]);
        $utmSourceField->commit();
        $session = [
            'utm_source' => 'UTM source',
        ];
        $material = new Material(['name' => 'test', 'pid' => 7]); // Отзывы к товарам
        $material->commit();
        $materialId = $material->id;

        $interface = new FormInterface();

        $interface->processUTM($material, [], $session);
        $material = new Material($materialId);

        $this->assertEquals('UTM source', $material->utm_source);

        Material::delete($material);
        Material_Field::delete($utmSourceField);
    }


    /**
     * Тест обработки регулярного поля
     */
    public function testProcessRegularField()
    {
        $material = new Material(['name' => 'test', 'pid' => 7]); // Отзывы к товарам
        $material->commit();
        $post = ['answer_name' => 'Test User'];
        $interface = new FormInterface();

        $interface->processRegularField(
            $material->fields['answer_name'],
            $post
        );

        $this->assertEquals('Test User', $material->answer_name);

        Material::delete($material);
    }


    /**
     * Тест обработки файлового поля
     * (случай с загрузкой файла)
     */
    public function testProcessFileFieldWithFile()
    {
        $material = new Material(['name' => 'test', 'pid' => 3]); // Новости
        $material->commit();
        $interface = new FormInterface();

        $interface->processFileField(
            $material->fields['images'],
            [
                'images@name' => 'aaa',
                'images@description' => 'bbb',
                'images@vis' => 1,
            ],
            ['images' => [
                'tmp_name' => __DIR__ . '/../../resources/noname.gif',
                'name' => 'noname.gif',
                'type' => 'image/jpeg',
            ]],
            true
        );

        $this->assertNotEmpty($material->images[0]->id);
        $this->assertEquals(1, $material->images[0]->vis);
        $this->assertEquals('aaa', $material->images[0]->name);
        $this->assertEquals('bbb', $material->images[0]->description);
        $this->assertEquals('noname.gif', $material->images[0]->filename);
        $this->assertEquals(1, $material->images[0]->image);

        Material::delete($material);
    }


    /**
     * Тест обработки файлового поля
     * (случай с записью предыдущего значения)
     */
    public function testProcessFileFieldWithOldValue()
    {
        $material = new Material(['name' => 'test', 'pid' => 3]); // Новости
        $material->commit();
        $interface = new FormInterface();

        $interface->processFileField(
            $material->fields['images'],
            [
                'images@name' => 'aaa',
                'images@description' => 'bbb',
                'images@vis' => 1,
                'images@attachment' => 4
            ],
            [],
            true
        );

        $this->assertEquals(4, $material->images[0]->id);
        $this->assertEquals(1, $material->images[0]->vis);
        $this->assertEquals('aaa', $material->images[0]->name);
        $this->assertEquals('bbb', $material->images[0]->description);
        $this->assertEquals(1, $material->images[0]->image);

        Material::delete($material);
    }


    /**
     * Тест получения списка адресов формы
     */
    public function testParseFormAddresses()
    {
        $form = new Form([
            'email' => 'test@test.org, [79990000000@sms.test.org], [+79990000000], test1@test.org, [79991111111@sms.test.org], [79991111111]'
        ]);
        $interface = new FormInterface();

        $result = $interface->parseFormAddresses($form);

        $this->assertEquals([
            'emails' => [
                'test@test.org',
                'test1@test.org',
            ],
            'smsEmails' => [
                '79990000000@sms.test.org',
                '79991111111@sms.test.org',
            ],
            'smsPhones' => [
                '+79990000000',
                '79991111111',
            ]
        ], $result);
    }


    /**
     * Тест получения списка адресов пользователя
     */
    public function testParseUserAddresses()
    {
        $feedback = new Feedback(1);
        $interface = new FormInterface();

        $result = $interface->parseUserAddresses($feedback);

        $this->assertEquals([
            'emails' => [
                'test@test.org',
            ],
            'smsPhones' => [
                '+79990000000'
            ]
        ], $result);
    }


    /**
     * Тест получения списка адресов пользователя
     * (случай с неконвенциональными названиями полей)
     */
    public function testParseUserAddressesWithFieldsByType()
    {
        $phoneField = new Form_Field(6);
        $phoneField->urn = 'telephone';
        $phoneField->datatype = 'tel';
        $phoneField->commit();
        $emailField = new Form_Field(7);
        $emailField->urn = 'post_address';
        $emailField->datatype = 'email';
        $emailField->commit();
        $feedback = new Feedback(1);
        $interface = new FormInterface();

        $result = $interface->parseUserAddresses($feedback);

        $this->assertEquals([
            'emails' => [
                'test@test.org',
            ],
            'smsPhones' => [
                '+79990000000'
            ]
        ], $result);

        $phoneField->urn = 'phone';
        $phoneField->datatype = 'text';
        $phoneField->commit();
        $emailField->urn = 'email';
        $emailField->datatype = 'text';
        $emailField->commit();
    }


    /**
     * Тест получения заголовка e-mail сообщения
     */
    public function testGetEmailSubject()
    {
        $feedback = new Feedback(1);
        $server = ['HTTP_HOST' => 'xn--d1acufc.xn--p1ai'];
        $interface = new FormInterface(null, null, [], [], [], [], $server);

        $result = $interface->getEmailSubject($feedback);

        $this->assertStringContainsString(
            'Новое сообщение с формы «Обратная связь» на странице «Главная» сайта ДОМЕН.РФ',
            $result
        );
    }


    /**
     * Тест получения тела сообщения
     */
    public function testGetMessageBody()
    {
        $feedback = new Feedback(1);
        $interface = new FormInterface();
        $data = [
            'Item' => $feedback,
            'Material' => null,
            'SMS' => false,
            'forUser' => false,
            'ADMIN' => true,
            'formInterface' => $interface,
        ];

        $result = $interface->getMessageBody(
            $feedback->parent->Interface,
            $data
        );

        $this->assertStringContainsString('Ваше имя', $result);
        $this->assertStringContainsString('Тестовый пользователь', $result);
        $this->assertStringContainsString('Телефон', $result);
        $this->assertStringContainsString('+7 999 000-00-00', $result);
    }


    /**
     * Тест получения значения поля "От"
     */
    public function testGetFromName()
    {
        $server = ['HTTP_HOST' => 'xn--d1acufc.xn--p1ai'];
        $interface = new FormInterface(null, null, [], [], [], [], $server);

        $result = $interface->getFromName();

        $this->assertEquals('Администрация сайта домен.рф', $result);
    }


    /**
     * Тест получения значение обратного адреса
     */
    public function testGetFromEmail()
    {
        $server = ['HTTP_HOST' => 'test.org'];
        $interface = new FormInterface(null, null, [], [], [], [], $server);

        $result = $interface->getFromEmail();

        $this->assertEquals('info@test.org', $result);
    }


    /**
     * Тест проверки на корректность файлового поля
     * (случай с наличием правильного файла)
     */
    public function testCheckFileFieldWithOk()
    {
        $fileField = new Form_Field([
            'pid' => 1,
            'datatype' => 'image',
            'multiple' => 1,
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true,
            'source' => 'gif,jpg,png',
        ]); // Обратная связь
        $fileField->commit();
        $files = ['image' => [
            'tmp_name' => [__DIR__ . '/../../resources/noname.gif'],
            'name' => ['noname.gif'],
            'type' => ['image/gif'],
        ]];
        $interface = new FormInterface();

        $result = $interface->checkFileField($fileField, $files, true);

        $this->assertNull($result);

        Form_Field::delete($fileField);
    }


    /**
     * Тест проверки на корректность файлового поля
     * (случай с отсутствием файла)
     */
    public function testCheckFileFieldWithNoFile()
    {
        $fileField = new Form_Field([
            'pid' => 1,
            'datatype' => 'image',
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true
        ]); // Обратная связь
        $fileField->commit();
        $files = [];
        $interface = new FormInterface();

        $result = $interface->checkFileField($fileField, $files, true);

        $this->assertEquals('Необходимо заполнить поле «Изображение»', $result);

        Form_Field::delete($fileField);
    }


    /**
     * Тест проверки на корректность файлового поля
     * (случай с отсутствием файла)
     */
    public function testCheckFileFieldWithConditional()
    {
        $fileField = new Form_Field([
            'pid' => 1,
            'datatype' => 'image',
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true
        ]); // Обратная связь
        $fileField->commit();
        $files = [];
        $interface = new FormInterface();
        $interface->conditionalRequiredFields = [
            'image' => function ($field, $post) {
                return false;
            },
        ];

        $result = $interface->checkFileField($fileField, $files, true);

        $this->assertNull($result);

        Form_Field::delete($fileField);
    }


    /**
     * Тест проверки на корректность файлового поля
     * (случай с некорректным файлом)
     */
    public function testCheckFileFieldWithInvalidFile()
    {
        $fileField = new Form_Field([
            'pid' => 1,
            'datatype' => 'image',
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true
        ]); // Обратная связь
        $fileField->commit();
        $files = ['image' => [
            'tmp_name' => __DIR__ . '/../../resources/favicon.txt',
            'name' => 'favicon.txt',
            'type' => 'text/plain',
        ]];
        $interface = new FormInterface();

        $result = $interface->checkFileField($fileField, $files, true);

        $this->assertEquals('Некорректный формат изображения. Доступные форматы: GIF, JPG, PNG', $result);

        Form_Field::delete($fileField);
    }


    /**
     * Тест проверки на корректность файлового поля
     * (случай с файлом, несоответствующим расширение)
     */
    public function testCheckFileFieldWithInvalidExtension()
    {
        $fileField = new Form_Field([
            'pid' => 1,
            'datatype' => 'image',
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true,
            'source' => 'jpg, png',
        ]); // Обратная связь
        $fileField->commit();
        $files = ['image' => [
            'tmp_name' => __DIR__ . '/../../resources/noname.gif',
            'name' => 'noname.gif',
            'type' => 'image/jpeg',
        ]];
        $interface = new FormInterface();

        $result = $interface->checkFileField($fileField, $files, true);

        $this->assertEquals(
            'Файл «Изображение» недопустимого формата. Допустимые форматы: JPG, PNG',
            $result
        );

        Form_Field::delete($fileField);
    }


    /**
     * Тест проверки на корректность файлового поля
     * (случай с несовпадением шаблона)
     */
    public function testCheckFileFieldWithPatternMismatch()
    {
        $fileField = new Form_Field([
            'pid' => 1,
            'datatype' => 'image',
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true,
            'pattern' => 'test',
        ]); // Обратная связь
        $fileField->commit();
        $files = ['image' => [
            'tmp_name' => __DIR__ . '/../../resources/noname.gif',
            'name' => 'noname.gif',
            'type' => 'image/gif',
        ]];
        $interface = new FormInterface();

        $result = $interface->checkFileField($fileField, $files, true);

        $this->assertEquals('Поле «Изображение» заполнено неверно', $result);

        Form_Field::delete($fileField);
    }


    /**
     * Тест проверки правильности заполнения формы
     */
    public function testCheck()
    {
        $fileField = new Form_Field([
            'pid' => 1,
            'datatype' => 'image',
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true,
            'source' => 'jpg, png',
        ]); // Обратная связь
        $fileField->commit();
        $form = new Form(1); // Обратная связь
        $post = ['_name' => 'aaa'];
        $files = [];
        $interface = new FormInterface();

        $result = $interface->check($form, $post, [], $files);

        $this->assertEquals(
            'Необходимо заполнить поле «Ваше имя»',
            $result['full_name']
        );
        $this->assertEquals(
            'Необходимо заполнить поле «Изображение»',
            $result['image']
        );
        $this->assertEquals(
            'Антиспам-система не пройдена',
            $result['_name']
        );

        Form_Field::delete($fileField);
    }


    /**
     * Тест обработки кастомных полей объекта
     */
    public function testProcessObjectFields()
    {
        $formFileField = new Form_Field([
            'pid' => 6, // Форма "отзывы к товарам"
            'datatype' => 'image',
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true,
        ]);
        $formFileField->commit();
        $materialFileField = new Material_Field([
            'pid' => 7, // Тип материала "отзывы к товарам"
            'datatype' => 'image',
            'urn' => 'image',
            'name' => 'Изображение',
            'required' => true,
        ]);
        $materialFileField->commit();
        $form = new Form(6);
        $post = [
            'full_name' => 'Test User',
            'email' => 'test@test.org',
            '_description_' => 'Test comment',
            'material' => 10,
            'rating' => 5,
        ];
        $files = ['image' => [
            'tmp_name' => __DIR__ . '/../../resources/noname.gif',
            'name' => 'noname.gif',
            'type' => 'image/jpeg',
        ]];
        $material = new Material(['pid' => 7]);
        $material->commit();
        $interface = new FormInterface();

        $interface->processObjectFields($material, $form, $post, $files, true);

        $this->assertEquals(5, $material->rating);
        $this->assertEquals('noname.gif', $material->image->filename);
        $this->assertEquals(10, $material->material->id);

        Material::delete($material);
        Form_Field::delete($formFileField);
        Material_Field::delete($materialFileField);
    }


    /**
     * Тест обработки объекта, порождаемого формой (материал или уведомление)
     */
    public function testProcessObject()
    {
        $interface = new FormInterface();
        $ipField = new Material_Field([
            'pid' => 7,
            'datatype' => 'text',
            'name' => 'ip'
        ]);
        $ipField->commit();
        $form = new Form(6);

        $post = [
            'full_name' => 'Test User',
            'email' => 'test@test.org',
            '_description_' => 'Test comment',
            'material' => 10,
            'rating' => 5,
        ];
        $server = [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'userAgent'
        ];
        $material = new Material(['pid' => 7]);
        $interface = new FormInterface();

        $interface->processObject($material, $form, $post, $server, []);

        $this->assertStringContainsString('Отзывы к товарам: ', $material->name);
        $this->assertStringContainsString(date('Y-m-d'), $material->date);
        $this->assertEquals(5, $material->rating);
        $this->assertEquals(10, $material->material->id);
        $this->assertEquals('127.0.0.1', $material->ip);

        Material::delete($material);
        Material_Field::delete($ipField);
    }


    /**
     * Тест уведомления администратора о заполненной форме
     */
    public function testNotify()
    {
        $form = new Form(1);
        $form->email = 'test@test.org, [79990000000@sms.test.org], [+79990000000]';
        $form->commit();
        $feedback = new Feedback(1);
        Package::i()->registrySet(
            'sms_gate',
            'http://smsgate/{{PHONE}}/{{TEXT}}/'
        );
        $interface = new FormInterface();

        $result = $interface->notify($feedback, null, true, true);

        $this->assertEquals(['test@test.org'], $result['emails']['emails']);
        $this->assertStringContainsString(
            'Новое сообщение с формы «Обратная связь»',
            $result['emails']['subject']
        );
        $this->assertStringContainsString('<div>', $result['emails']['message']);
        $this->assertStringContainsString(
            'Телефон: +7 999 000-00-00',
            $result['emails']['message']
        );
        $this->assertStringContainsString('/admin/', $result['emails']['message']);
        $this->assertStringContainsString('Администрация сайта', $result['emails']['from']);
        $this->assertStringContainsString('info@', $result['emails']['fromEmail']);
        $this->assertEquals(
            ['79990000000@sms.test.org'],
            $result['smsEmails']['emails']
        );
        $this->assertStringContainsString(
            'Новое сообщение с формы «Обратная связь»',
            $result['smsEmails']['subject']
        );
        $this->assertStringNotContainsString('<div>', $result['smsEmails']['message']);
        $this->assertStringContainsString(
            'Администрация сайта',
            $result['smsEmails']['from']
        );
        $this->assertStringContainsString('info@', $result['smsEmails']['fromEmail']);
        $this->assertStringContainsString(
            'Телефон: +7 999 000-00-00',
            $result['smsEmails']['message']
        );
        $this->assertStringContainsString(
            'smsgate/%2B79990000000/',
            $result['smsPhones'][0] ?? ''
        );
        $this->assertStringContainsString(
            urlencode('Телефон: +7 999 000-00-00'),
            $result['smsPhones'][0] ?? ''
        );

        $form->email = '';
        $form->commit();
        Package::i()->registrySet('sms_gate', '');
    }


    /**
     * Тест уведомления пользователя о заполненной форме
     */
    public function testNotifyWithForUser()
    {
        $form = new Form(1);
        $feedback = new Feedback(1);
        Package::i()->registrySet(
            'sms_gate',
            'http://smsgate/{{PHONE}}/{{TEXT}}/'
        );
        $interface = new FormInterface();

        $result = $interface->notify($feedback, null, false, true);

        $this->assertEquals(['test@test.org'], $result['emails']['emails']);
        $this->assertStringContainsString(
            'Новое сообщение с формы «Обратная связь»',
            $result['emails']['subject']
        );
        $this->assertStringContainsString('<div>', $result['emails']['message']);
        $this->assertStringContainsString(
            'Телефон: +7 999 000-00-00',
            $result['emails']['message']
        );
        $this->assertStringContainsString('Администрация сайта', $result['emails']['from']);
        $this->assertStringContainsString('info@', $result['emails']['fromEmail']);
        $this->assertEmpty($result['smsEmails'] ?? null);
        $this->assertStringContainsString(
            'smsgate/%2B79990000000/',
            $result['smsPhones'][0] ?? ''
        );
        $this->assertStringContainsString(
            urlencode('Телефон: +7 999 000-00-00'),
            $result['smsPhones'][0] ?? ''
        );

        Package::i()->registrySet('sms_gate', '');
    }


    /**
     * Тест уведомления администратора о заполненной форме
     * (случай с формой без интерфейса)
     */
    public function testNotifyWithNoInterface()
    {
        $form = new Form(1);
        $interfaceId = (int)$form->interface_id;
        $form->interface_id = 0;
        $form->commit();
        $feedback = new Feedback(1);
        $interface = new FormInterface();

        $result = $interface->notify($feedback, null, true, true);

        $this->assertNull($result);

        $form->interface_id = $interfaceId;
        $form->commit();
    }


    /**
     * Тест отработки формы
     * (случай без создания материала)
     */
    public function testProcessFormWithoutMaterial()
    {
        $form = new Form(1); // Обратная связь
        $form->email = 'test@test.org';
        $page = new Page(1); // Главная
        $post = [
            'full_name' => 'Test User',
            'phone' => '+7 999 000-00-00',
            'email' => 'test@test.org',
            '_description_' => 'Test message',
            'agree' => 1,
        ];
        $interface = new FormInterface();

        $result = $interface->processForm($form, $page, $post, [], []);

        $this->assertInstanceof(Feedback::class, $result['Item']);
        $feedback = $result['Item'];
        $this->assertEquals(1, $feedback->page_id);
        $this->assertEquals('Test User', $feedback->full_name);
    }


    /**
     * Тест отработки формы
     * (случай с созданием материала)
     */
    public function testProcessFormWithMaterial()
    {
        $form = new Form(6); // Отзывы к товарам
        $form->create_feedback = 0;
        $form->commit();
        $page = new Page(24); // Категория 3 (каталог товара)
        $post = [
            'full_name' => 'Test User',
            'email' => 'test@test.org',
            '_description_' => 'Test message',
            'material' => 10,
            'rating' => 5,
        ];
        $interface = new FormInterface();

        $result = $interface->processForm($form, $page, $post, [], []);

        $this->assertNull($result['Item'] ?? null);
        $this->assertInstanceof(Material::class, $result['Material']);
        $material = $result['Material'];
        $this->assertEquals(10, $material->material->id);

        $form->create_feedback = 1;
        $form->commit();
    }


    /**
     * Тестирует отработку интерфейса
     * (случай с процессом формы)
     */
    public function testProcessWithOk()
    {
        $post = [
            'full_name' => 'Test User',
            '_description_' => 'Test message',
            'agree' => 1,
            'form_signature' => md5('form127')
        ];
        $interface = new FormInterface(
            Block::spawn(27), // Обратная связь на странице "Контакты"
            new Page(8), // Страница "Контакты"
            [],
            $post
        );

        $result = $interface->process();

        $this->assertEquals([], $result['localError']);
        $this->assertTrue($result['success'][27]);
        $this->assertEquals($post, $result['DATA']);
        $this->assertInstanceof(Form::class, $result['Form']);
        $this->assertEquals(1, $result['Form']->id);
    }


    /**
     * Тестирует отработку интерфейса
     * (случай без процесса формы)
     */
    public function testProcessWithoutPost()
    {
        $phoneField = new Form_Field(6); // Поле "телефон"
        $phoneField->defval = '+7 999 000-00-00';
        $phoneField->commit();
        $interface = new FormInterface(
            Block::spawn(27), // Обратная связь на странице "Контакты"
            new Page(8), // Страница "Контакты"
            [],
            [],
            [],
            [],
            ['REQUEST_METHOD' => 'GET']
        );

        $result = $interface->process();

        $this->assertEquals([], $result['localError']);
        $this->assertFalse(isset($result['success']));
        $this->assertEquals(['phone' => '+7 999 000-00-00'], $result['DATA']);
        $this->assertInstanceof(Form::class, $result['Form']);
        $this->assertEquals(1, $result['Form']->id);

        $phoneField->defval = '+7 999 000-00-00';
        $phoneField->commit();
    }


    /**
     * Проверка метода getBasename
     */
    public function testGetBasename()
    {
        $interface = new FormInterface();

        $result = $interface->getBasename('https://test.org/test.jpg');

        $this->assertEquals('3f48dd5b-test.jpg', $result);
    }


    /**
     * Проверка метода processEmbedded
     */
    public function testProcessEmbedded()
    {
        $interface = new FormInterface();
        $text = '<img src="http://test/files/cms/common/image/nophoto.jpg">';

        $result = $interface->processEmbedded($text);

        $this->assertEquals('<img src="cid:d9249709-nophoto.jpeg">', $result['message']);
        $this->assertIsArray($result['embedded']);
        $this->assertCount(1, $result['embedded']);
        $this->assertEquals('d9249709-nophoto.jpeg', $result['embedded'][0]['name']);
        $this->assertEquals('image/jpeg', $result['embedded'][0]['type']);
    }


    /**
     * Проверка метода getEmbedded
     */
    public function testGetEmbedded()
    {
        $interface = new FormInterface();

        $result = $interface->getEmbedded('//test/files/cms/common/image/nophoto.jpg');

        $this->assertEquals('8ed4670b-nophoto.jpg', $result['name']);
    }


    /**
     * Проверка метода getEmbedded - случай с локальным адресом
     */
    public function testGetEmbeddedWithLocalAddress()
    {
        $interface = new FormInterface();

        $result = $interface->getEmbedded('/files/cms/common/image/nophoto.jpg');

        $this->assertEquals('ccbf470b-nophoto.jpeg', $result['name']);
        $this->assertEquals('image/jpeg', $result['type']);
    }


    /**
     * Проверка метода getEmbedded - случай с локальным адресом, большая картинка
     */
    public function testGetEmbeddedWithLocalBig()
    {
        $interface = new FormInterface();

        $result = $interface->getEmbedded('/files/cms/common/image/arboretum_tree_rings.jpg');

        $this->assertNotEmpty($result['tmp_name']);
        $this->assertFileExists($result['tmp_name']);
        $size = getimagesize($result['tmp_name']);
        $this->assertEquals(600, $size[0]);
    }


    /**
     * Проверка метода getEmbedded - случай с Data URL
     */
    public function testGetEmbeddedWithDataURL()
    {
        $interface = new FormInterface();

        $result = $interface->getEmbedded('data:text/plain;base64,dGVzdA==');

        $this->assertStringContainsString('raa', $result['name']);
        $this->assertEquals('text/plain', $result['type']);
    }
}
