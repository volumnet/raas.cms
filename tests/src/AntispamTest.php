<?php
/**
 * Файл теста антиспама
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста антиспама
 */
class AntispamTest extends BaseTest
{
    public static $tables = [
        'cms_fields',
    ];

    /**
     * Провайдер данных для метода testCheckUserAgent
     * @return array <pre><code>array<[
     *     string Текст для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkUserAgentDataProvider()
    {
        return [
            [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 YaBrowser/23.1.3.688 (beta) Yowser/2.5 Safari/537.36',
                true,
            ],
            [
                'YandexBot',
                false,
            ],
            [
                'curl/7.54.0',
                false,
            ],
        ];
    }

    /**
     * Проверяет соответствие User-Agent
     * @dataProvider checkUserAgentDataProvider
     */
    public function testCheckUserAgent($text, $expected)
    {
        $antispam = new Antispam(
            new Form(),
            'ru',
            '',
            $text,
        );

        $result = $antispam->checkUserAgent($text);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckEmail
     * @return array <pre><code>array<[
     *     string Текст для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkEmailDataProvider()
    {
        return [
            [
                'AlexVSurnin@GMail.com',
                true,
            ],
            [
                'VolumNet@Yandex.RU',
                true,
            ],
            [
                'b.FroUdiere@LHeRMiTe-aGRI.cOm',
                false,
            ],
            [
                'COrKs@rOGErs.COm',
                false,
            ],
            [
                'v.GIlL@ROGeRs.CoM',
                false,
            ],
            [
                'LAUrAgsAAD@gMaIL.Com',
                false,
            ],
            [
                'plAStICoPera@YAhoo.COM',
                false,
            ],
            [
                'RITA.patEL0627@gMaIL.coM',
                false,
            ],
            [
                'gUrdYWolOSz@rOgErs.com',
                false,
            ],
            [
                'SkLEcher@aol.coM',
                false,
            ],
            [
                'Ron@toMbALLCPAs.com',
                false,
            ],
        ];
    }

    /**
     * Проверяет соответствие почтового адреса
     * @dataProvider checkEmailDataProvider
     */
    public function testCheckEmail($text, $expected)
    {
        $antispam = new Antispam(
            new Form(),
            'ru',
            '',
            $text,
        );

        $result = $antispam->checkEmail($text);

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка уплощения данных
     */
    public function testFlattenData()
    {
        $post = [
            'aaa' => [
                123,
                [
                    'eee' => 'fff',
                    'ggg' => [
                        'hhh',
                        'iii' => 'jjj',
                    ],
                ],
            ],
            'bbb' => 12,
            'ccc' => 'abc',
        ];
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->flattenData($post);

        $this->assertEquals([
            'aaa[0]' => 123,
            'aaa[1][eee]' => 'fff',
            'aaa[1][ggg][0]' => 'hhh',
            'aaa[1][ggg][iii]' => 'jjj',
            'bbb' => 12,
            'ccc' => 'abc',
        ], $result);
    }


    /**
     * Тестирует извлечение URN поля
     */
    public function testGetFieldURN()
    {
        $dataKey = 'phone[123][aaa]';
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->getFieldURN($dataKey);

        $this->assertEquals('phone', $result);
    }


    /**
     * Тестирует извлечение URN поля - случай без массивов
     */
    public function testGetFieldURNWithSimpleKey()
    {
        $dataKey = 'phone';
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->getFieldURN($dataKey);

        $this->assertEquals('phone', $result);
    }


    /**
     * Тест извлечения текстов из ссылок
     */
    public function testExtractURLs()
    {
        $text = 'https://yandex.ru
        ...
        некоторый-домен-123.рф
        ...
        http://xn----123-twefc7ajzghiad4a8a4n.xn--p1ai/aaa/bbb/?dsfg=df
        ...
        http://localhost
        ...
        test.org,
        google.com/?db=antispam.org,
        6.319';
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->extractURLs($text);

        $this->assertEquals([
            'yandex.ru',
            'некоторый-домен-123.рф',
            'xn----123-twefc7ajzghiad4a8a4n.xn--p1ai',
            'test.org',
            'google.com',
        ], $result);
    }


    /**
     * Провайдер данных для метода testCheckTextForeignLinks
     * @return array <pre><code>array<[
     *     string Текст для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkTextForeignLinksDataProvider()
    {
        return [
            [
                'yandex.ru ... google.com ... xn----123-twefc7ajzghiad4a8a4n.xn--p1ai',
                false,
            ],
            [
                'aaa xn----123-twefc7ajzghiad4a8a4n.xn--p1ai bbb',
                true,
            ],
        ];
    }


    /**
     * Проверка текстов на внешние ссылки
     * @param string $text Текст для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkTextForeignLinksDataProvider
     */
    public function testCheckTextForeignLinks($text, $expected)
    {
        $antispam = new Antispam(
            new Form(),
            'ru',
            'xn----123-twefc7ajzghiad4a8a4n.xn--p1ai'
        );

        $result = $antispam->checkTextForeignLinks($text);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckForeignLinks
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkForeignLinksDataProvider()
    {
        return [
            [
                [],
                true,
            ],
            [
                ['full_name' => ' xn----123-twefc7ajzghiad4a8a4n.xn--p1ai'],
                true,
            ],
            [
                [
                    'full_name' => 'yandex.ru ... google.com ... xn----123-twefc7ajzghiad4a8a4n.xn--p1ai'
                ],
                false,
            ],
            [
                ['abc' => 'www.yandex.ru'],
                false,
            ],
            [
                ['social' => 'www.yandex.ru', 'web' => 'google.com'],
                true,
            ],
            [
                [
                    'sayt' => 'yandex.ru ... google.com ... xn----123-twefc7ajzghiad4a8a4n.xn--p1ai'
                ],
                true,
            ],
            [
                [
                    '_description' => "Новая вспышка Covid-19, которую уже назвали самой серьезной после Уханя, охватила по меньшей мере 15 китайских провинций и городов. Власти проявляют все больше беспокойства по поводу уязвимости страны перед более заразным вариантом \"Дельта\".Вся информация о новом шиаме вируса доступна по ссылке :\r"
                        . "https://www.hchp.ru/phorum/viewtopic.php?t=29576\r\n\r\n"
                        . "За последние 10 дней дни зарегистрировано более 300 новых случаев заболевания, и каждый день выявляется все больше зараженных. Только 2 августа зарегистрированы 55 новых случаев, из них 40 в провинции Цзяньсу на востоке страны, остальные - в Пекине и провинциях Хуньань, Хубэй, Шандунь, Хэньань, Хайнань и Юньнань, сообщила Национальная комиссия по здравоохранению.",
                ],
                false,
            ],
            [
                [
                    '_description_' => 'Новая вспышка Covid-19, которую уже назвали самой серьезной после Уханя, охватила по меньшей мере 15 китайских провинций и городов. Власти проявляют все больше беспокойства по поводу уязвимости страны перед более заразным вариантом "Дельта".Вся информация о новом шиаме вируса доступна по ссылке :' . "\r"
                        . "https://supermamki.ru/viewtopic.php?f=35&t=1578684\r"
                        . "За последние 10 дней дни зарегистрировано более 300 новых случаев заболевания, и каждый день выявляется все больше зараженных. Только 2 августа зарегистрированы 55 новых случаев, из них 40 в провинции Цзяньсу на востоке страны, остальные - в Пекине и провинциях Хуньань, Хубэй, Шандунь, Хэньань, Хайнань и Юньнань, сообщила Национальная комиссия по здравоохранению.",
                ],
                false,
            ]
        ];
    }


    /**
     * Проверка данных на внешние ссылки
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkForeignLinksDataProvider
     */
    public function testCheckForeignLinks($flatData, $expected)
    {
        $antispam = new Antispam(
            new Form([
                'fields' => [
                    'full_name' => new Field([
                        'urn' => 'full_name',
                        'datatype' => 'text',
                        'name' => 'Ваше имя',
                    ]),
                    'phone' => new Field([
                        'urn' => 'phone',
                        'datatype' => 'tel',
                        'name' => 'Телефон',
                    ]),
                    'email' => new Field([
                        'urn' => 'email',
                        'datatype' => 'email',
                        'name' => 'Телефон',
                    ]),
                    'sayt' => new Field([ // Чтобы не распознавалось по имени
                        'urn' => 'sayt',
                        'datatype' => 'url',
                        'name' => 'Веб-сайт',
                    ]),
                    '_description_' => new Field([ // Чтобы не распознавалось по имени
                        'urn' => '_description_',
                        'datatype' => 'textarea',
                        'name' => 'Текст вопроса',
                    ]),

                ]
            ]),
            'ru',
            'www.xn----123-twefc7ajzghiad4a8a4n.xn--p1ai'
        );

        $result = $antispam->checkForeignLinks($flatData);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckTextStopWords
     * @return array <pre><code>array<[
     *     string Текст для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkTextStopWordsDataProvider()
    {
        return [
            ['Test User', true],
            ['We offer you a greatest deal', false],
        ];
    }


    /**
     * Проверка текста на стоп-слова
     * @param string $text Текст для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkTextStopWordsDataProvider
     */
    public function testCheckTextStopWords($text, $expected)
    {
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->checkTextStopWords($text);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckStopWords
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkStopWordsDataProvider()
    {
        return [
            [
                [],
                true,
            ],
            [
                ['full_name' => 'Test User'],
                true,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'We offer you a greatest deal'
                ],
                false,
            ],
        ];
    }


    /**
     * Проверка данных на стоп-слова
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkStopWordsDataProvider
     */
    public function testCheckStopWords($flatData, $expected)
    {
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->checkStopWords($flatData);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckInternational
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkInternationalDataProvider()
    {
        return [
            [
                [],
                true,
            ],
            [
                ['full_name' => 'Test User', '_description_' => 'Test question'],
                true,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'www.yandex.ru'
                ],
                false,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'We offer you a greatest deal'
                ],
                false,
            ],
        ];
    }


    /**
     * Проверка данных на международный спам-фильтр
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkInternationalDataProvider
     */
    public function testCheckInternational($flatData, $expected)
    {
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->checkInternational($flatData);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckRussianData
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkRussianData()
    {
        return [
            [
                [],
                true,
            ],
            [
                ['full_name' => 'Test User', '_description_' => 'Test question'],
                false,
            ],
            [
                ['e_mail' => 'test@test.org'],
                true,
            ],
            [
                ['email' => 'test@test.org'],
                true,
            ],
            [
                [
                    'full_name' => 'Тестовый пользователь',
                    '_description_' => 'Test question'
                ],
                true,
            ],
            [
                [
                    'form_signature' => 'b39938cd9e014cd1245fb2cd8a5a0440',
                    'phone_call' => '+7 (999) 000-00-00',
                    'agree' => '1'
                ],
                true,
            ],
            [
                [
                    'full_name' => 'KarinaOt',
                    'phone' => '88471449351',
                    'email' => '7hcm.chistyukhin2911x1su@gmail.com',
                    '_description_' => 'Новая вспышка Covid-19, которую уже назвали самой серьезной после Уханя, охватила по меньшей мере 15 китайских провинций и городов. Власти проявляют все больше беспокойства по поводу уязвимости страны перед более заразным вариантом "Дельта".Вся информация о новом шиаме вируса доступна по ссылке :
                    https://www.hchp.ru/phorum/viewtopic.php?t=29576

                    За последние 10 дней дни зарегистрировано более 300 новых случаев заболевания, и каждый день выявляется все больше зараженных. Только 2 августа зарегистрированы 55 новых случаев, из них 40 в провинции Цзяньсу на востоке страны, остальные - в Пекине и провинциях Хуньань, Хубэй, Шандунь, Хэньань, Хайнань и Юньнань, сообщила Национальная комиссия по здравоохранению.',
                    'city' => 'Москва',
                ],
                false,
            ],
            [
                [
                    'full_name' => 'MosesZipsy',
                    'phone' => '85931689399',
                    'email' => 'xgamer1def@gmail.com',
                    '_description_' => 'Автоматизирую рабочие процессы на компьюторе, сайте, в интернете.
                    Нет такого человека кому не нужны мои услуги.

                    Примеры:
                    - Наполнить интернет магазин 10 000 товаров, с картинками, ценами, описаниями, на 2-х языках (Я сделаю это за 1-2 дня, а ты ?).
                    - Бот, который отслеживает изменение цены, к примеру как только цена станет ниже 10, отправит сообщение в telegram, что цена ниже 10 пунктов.
                    - Обработать большие текстовые файлы информации от 1 GB. Составить регулярные выражения под эти базы.
                    - Написать регистратор какого-то сайта. С активацие ссылки через почту или смс. С автоматическим разгадыванием Google рекаптчи.
                    - Раскрутить интернет магазин, как минимум по Вашему региону.
                    - Клонировать магазин конкурента.

                    И много, много чего другого. Каждый заказ индивидуален. Напиши мне . . .

                    Стоимость услуг от 50$
                    Email: xgamer1cde@gmail.com
                    Telegram: @eTraffik
                    ',
                    'city' => 'Biel',
                ],
                false,
            ]
        ];
    }


    /**
     * Проверка данных на содержание в полях кириллицы помимо латиницы
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkRussianData
     */
    public function testCheckRussianData($flatData, $expected)
    {
        $antispam = new Antispam(new Form([
                'fields' => [
                    'full_name' => new Field([
                        'urn' => 'full_name',
                        'datatype' => 'text',
                        'name' => 'Ваше имя',
                    ]),
                    'phone' => new Field([
                        'urn' => 'phone',
                        'datatype' => 'tel',
                        'name' => 'Телефон',
                    ]),
                    'e_mail' => new Field([
                        'urn' => 'e_mail',
                        'datatype' => 'email',
                        'name' => 'Телефон',
                    ]),
                    'sayt' => new Field([ // Чтобы не распознавалось по имени
                        'urn' => 'sayt',
                        'datatype' => 'url',
                        'name' => 'Веб-сайт',
                    ]),
                    '_description_' => new Field([ // Чтобы не распознавалось по имени
                        'urn' => '_description_',
                        'datatype' => 'textarea',
                        'name' => 'Текст вопроса',
                    ]),

                ]
            ]), 'ru');

        $result = $antispam->checkRussianData($flatData);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckIsRussianPhone
     * @return array <pre><code>array<[
     *     string Текст для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkIsRussianPhoneDataProvider()
    {
        return [
            [' +7 999 000-00-00', true],
            [' 1234567890', true],
            [' 0123456789', false],
            [' +1 999 000-00-00', false],
            ['330-689-7666', false],
            ['We offer you a greatest deal', true],
        ];
    }


    /**
     * Проверка данных на соответствие российскому формату телефона
     * @param string $text Текст для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkIsRussianPhoneDataProvider
     */
    public function testCheckIsRussianPhone($text, $expected)
    {
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->checkIsRussianPhone($text);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckRussianPhones
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkRussianPhonesDataProvider()
    {
        return [
            [
                [],
                true,
            ],
            [
                ['full_name' => 'Test User', 'phone' => '+1 999 000-00-00'],
                false,
            ],
            [
                ['full_name' => 'Test User', 'phone' => '+7 999 000-00-00'],
                true,
            ],
            [
                ['full_name' => 'Test User', 'cell' => '+1 999 000-00-00'],
                false,
            ],
            [
                ['full_name' => 'Test User', 'cell' => '+7 999 000-00-00'],
                true,
            ],
            [
                [
                    'full_name' => 'Тестовый пользователь',
                    '_description_' => 'Test question'
                ],
                true,
            ],
        ];
    }


    /**
     * Проверка данных на соответствие полей российскому формату телефонов
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkRussianPhonesDataProvider
     */
    public function testCheckRussianPhones($flatData, $expected)
    {
        $antispam = new Antispam(new Form([
            'fields' => [
                'full_name' => new Field([
                    'urn' => 'full_name',
                    'datatype' => 'text',
                    'name' => 'Ваше имя',
                ]),
                'cell' => new Field([
                    'urn' => 'cell',
                    'datatype' => 'tel',
                    'name' => 'Телефон',
                ]),
                'email' => new Field([
                    'urn' => 'email',
                    'datatype' => 'email',
                    'name' => 'Телефон',
                ]),
                'sayt' => new Field([ // Чтобы не распознавалось по имени
                    'urn' => 'sayt',
                    'datatype' => 'url',
                    'name' => 'Веб-сайт',
                ]),
                '_description_' => new Field([ // Чтобы не распознавалось по имени
                    'urn' => '_description_',
                    'datatype' => 'textarea',
                    'name' => 'Текст вопроса',
                ]),

            ]
        ]), 'ru');

        $result = $antispam->checkRussianPhones($flatData);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckRussianTextStopWords
     * @return array <pre><code>array<[
     *     string Текст для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkRussianTextStopWordsDataProvider()
    {
        return [
            ['Test User', true],
            ['Наша компания', false],
            ['мы предлагаем', false],
            [
                'Накрутка инстаграм быстро - Накрутка Instagram, Накрутка инстаграм быстро',
                false,
            ],
            [
                '- Раскрутить интернет магазин, как минимум по Вашему региону.',
                false,
            ],
            [
                'Нет такого человека кому не нужны мои услуги.',
                false,
            ],
            [
                'Стоимость услуг от 50$',
                false,
            ]

        ];
    }


    /**
     * Проверка данных на стоп-слова для русского языка
     * @param string $text Текст для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkRussianTextStopWordsDataProvider
     */
    public function testCheckRussianTextStopWords($text, $expected)
    {
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->checkRussianTextStopWords($text);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckRussianStopWords
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkRussianStopWordsDataProvider()
    {
        return [
            [
                [],
                true,
            ],
            [
                ['full_name' => 'Test User'],
                true,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'Наша компания занимается'
                ],
                false,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'Предлагаем вам'
                ],
                false,
            ],
        ];
    }


    /**
     * Проверка данных на стоп-слова для русского языка
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkRussianStopWordsDataProvider
     */
    public function testCheckRussianStopWords($flatData, $expected)
    {
        $antispam = new Antispam(new Form(), 'ru');

        $result = $antispam->checkRussianStopWords($flatData);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckRussian
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkRussianDataProvider()
    {
        return [
            [
                [],
                true,
            ],
            [
                ['full_name' => 'Test User'],
                false,
            ],
            [
                [
                    'full_name' => 'Тестовый пользователь',
                    '_description_' => 'Наша компания занимается'
                ],
                false,
            ],
            [
                [
                    'full_name' => 'Тестовый пользователь',
                    'phone' => '333-444-5555'
                ],
                false,
            ],
            [
                [
                    'full_name' => 'Тестовый пользователь',
                    '_description_' => 'Проверка связи'
                ],
                true,
            ],
        ];
    }


    /**
     * Проверка данных для русского языка
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkRussianDataProvider
     */
    public function testCheckRussian($flatData, $expected)
    {
        $antispam = new Antispam(new Form([
            'fields' => [
                'full_name' => new Field([
                    'urn' => 'full_name',
                    'datatype' => 'text',
                    'name' => 'Ваше имя',
                ]),
                'cell' => new Field([
                    'urn' => 'cell',
                    'datatype' => 'tel',
                    'name' => 'Телефон',
                ]),
                'email' => new Field([
                    'urn' => 'email',
                    'datatype' => 'email',
                    'name' => 'Телефон',
                ]),
                '_description_' => new Field([ // Чтобы не распознавалось по имени
                    'urn' => '_description_',
                    'datatype' => 'textarea',
                    'name' => 'Текст вопроса',
                ]),

            ]
        ]), 'ru');

        $result = $antispam->checkRussian($flatData);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheckRegional
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     string Язык для проверки
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkRegionalDataProvider()
    {
        return [
            [
                [],
                'ru',
                true,
            ],
            [
                [],
                'lt',
                true,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'Наша компания занимается'
                ],
                'ru',
                false,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'Наша компания занимается'
                ],
                'lt',
                true,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'Проверка связи'
                ],
                'ru',
                true,
            ],
        ];
    }


    /**
     * Проверка данных для русского языка
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param string $lang Язык для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkRegionalDataProvider
     */
    public function testCheckRegional($flatData, $lang, $expected)
    {
        $antispam = new Antispam(new Form(), $lang);

        $result = $antispam->checkRegional($flatData);

        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testCheck
     * @return array <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     string Язык для проверки
     *     bool Ожидаемый результат
     * ]></code></pre>
     */
    public function checkDataProvider()
    {
        return [
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'Наша компания занимается'
                ],
                'ru',
                false,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'Наша компания занимается'
                ],
                'lt',
                true,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'www.yandex.ru'
                ],
                'lt',
                false,
            ],
            [
                [
                    'full_name' => 'Test User',
                    '_description_' => 'Проверка связи'
                ],
                'ru',
                true,
            ],
            [
                [
                    'form_signature' => 'b39938cd9e014cd1245fb2cd8a5a0440',
                    'phone_call' => '+7 (999) 000-00-00',
                    'agree' => '1'
                ],
                'ru',
                true,
            ],
            [
                [
                    'form_signature' => 'b39938cd9e014cd1245fb2cd8a5a0440',
                    'full_name' => 'Тестовый пользователь',
                    'phone' => '+7 999 000-00-00',
                    'email' => 'Ron@toMbALLCPAs.com',
                    'agree' => '1'
                ],
                'ru',
                false,
            ],
        ];
    }


    /**
     * Проверка данных
     * @param array $flatData <pre><code>array<[
     *     array<
     *         string[] ключ массива => int|string|null
     *     > Плоские данные для проверки,
     *     bool Ожидаемый результат
     * ]></code></pre> Плоские данные для проверки
     * @param string $lang Язык для проверки
     * @param bool $expected Ожидаемый результат
     * @dataProvider checkDataProvider
     */
    public function testCheck($flatData, $lang, $expected)
    {
        $antispam = new Antispam(new Form(), $lang);

        $result = $antispam->check($flatData);

        $this->assertEquals($expected, $result);
    }
}
