<?php
/**
 * Система антиспама на основе анализа данных
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс антиспама на основе анализа данных
 */
class Antispam
{
    /**
     * Форма для проверки
     * @var Form
     */
    public $form;

    /**
     * Язык проверки
     * @var string
     */
    public $lang;

    /**
     * Хост (ASCII-нотация международных доменов)
     * @var string
     */
    public $host;

    /**
     * User-Agent пользователя
     * @var string
     */
    public $userAgent;

    /**
     * Конструктор класса
     * @param Form $form Форма для проверки
     * @param string $lang Язык для проверки
     * @param string|null $host Хост (null, чтобы использовать текущий)
     * @param string|null $userAgent User-Agent пользователя (null, чтобы использовать текущий)
     */
    public function __construct(Form $form, $lang = 'ru', $host = null, $userAgent = null)
    {
        $this->form = $form;
        $this->lang = $lang;
        $host = idn_to_ascii($host ?: ($_SERVER['HTTP_HOST'] ?? ''));
        $host = str_replace('www.', '', $host);
        $this->host = $host;
        $this->userAgent = ($userAgent ?: ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }


    /**
     * Проверяет входные данные
     * @param array $post POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function check(array $post)
    {
        $flatData = $this->flattenData($post);
        if (!$this->checkInternational($flatData)) {
            return false;
        }
        if (!$this->checkRegional($flatData)) {
            return false;
        }
        return true;
    }


    /**
     * Проверяет поля независимо от языка
     * @param array $flatData <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre> плоские POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkInternational(array $flatData = [])
    {
        if (!$this->checkUserAgent($this->userAgent)) {
            return false;
        }
        if (!$this->checkForeignLinks($flatData)) {
            return false;
        }
        if (!$this->checkStopWords($flatData)) {
            return false;
        }
        return true;
    }


    /**
     * Проверяет User-Agent на ботов
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkUserAgent($userAgent)
    {
        if (stristr($userAgent, 'curl') || stristr($userAgent, 'bot')) {
            return false;
        }
        return true;
    }


    /**
     * Проверяет email на соответствие спаму
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkEmail($email)
    {
        $chunks = preg_split('/[@\\-\\.\\_]/', $email);
        $spamicity = 0;
        $c = count($chunks);
        foreach ($chunks as $i => $chunk) {
            $inDomain = ($i >= $c - 2); // Чанк в домене
            if (preg_match_all('/^[a-z][A-Z][A-Z]/u', $chunk, $regs)) { // В начале маленькая, потом две больших
                return false;
            }
            if (preg_match_all('/[a-z][A-Z][A-Z]/u', $chunk, $regs)) {
                if ($inDomain) {
                    return false;
                } else {
                    $spamicity += 2 * count($regs[0]);
                }
            }
            if (preg_match_all('/[a-z][A-Z]/u', $chunk, $regs)) {
                if ($inDomain) {
                    $spamicity += 2 * count($regs[0]);
                } else {
                    $spamicity += 1 * count($regs[0]);
                }
            }
        }
        return $spamicity <= 3;
    }


    /**
     * Проверяет ссылки на сторонние сайты
     * @param array $flatData <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre> плоские POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkForeignLinks(array $flatData = [])
    {
        foreach ($flatData as $key => $val) {
            $fieldURN = $this->getFieldURN($key);
            if (preg_match('/url|social|site|web|link|www|internet/umis', $fieldURN)) {
                continue; // Если поле предназначено для ссылки или соц. сети,
                          // то его не учитываем
            }
            // if ($fieldURN == 'email') {
            //     var_dump(mixed:value, mixed:values...)
            // }
            $field = null;
            if (isset($this->form->fields[$fieldURN])) {
                $field = $this->form->fields[$fieldURN];
            }
            if ($field) {
                if ($field->datatype == 'email') {
                    if (!$this->checkEmail($val)) {
                        return false;
                    }
                } elseif ($field->datatype == 'url') {
                    continue; // Если поле типа "Адрес сайта", то его не учитываем
                } else {
                    if (!$this->checkTextForeignLinks((string)$val)) {
                        return false;
                    }
                }
            } elseif ($fieldURN == 'email') {
                if (!$this->checkEmail($val)) {
                    return false;
                }
            } else {
                if (!$this->checkTextForeignLinks((string)$val)) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * Проверяет данные на стоп-слова
     * @param array $flatData <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre> плоские POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkStopWords(array $flatData = [])
    {
        foreach ($flatData as $key => $val) {
            if (!$this->checkTextStopWords((string)$val)) {
                return false;
            }
        }
        return true;
    }


    /**
     * Проверяет стоп-слова в тексте
     * @param string $text Текст для проверки
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkTextStopWords(string $text): bool
    {
        return !preg_match('/porn|boobs|(we offer)/umis', $text);
    }


    /**
     * Проверяет ссылки на сторонние сайты в тексте
     * @param string $text Текст для проверки
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkTextForeignLinks(string $text): bool
    {
        $urls = $this->extractURLs($text);
        if ($urls) {
            foreach ($urls as $url) {
                $idnURL = idn_to_ascii($url);
                if ($idnURL) {
                    $url = $idnURL;
                }
                if (!stristr($url, $this->host) && !stristr($this->host, $url)) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * Выбирает ссылки из текста
     * @param string $text Текст для выборки
     * @return string[] Массив адресов (только сервера)
     */
    public function extractURLs(string $text): array
    {
        $result = [];
        // 2024-01-03, AVS: добавил в явном виде поддержку Punycode-доменов 1-го уровня
        // (если добавлять просто цифры, возможно ложное срабатывание)
        // 2024-09-04, AVS: добавил ([^\\w\\-А-Яа-я]|$), поскольку срабатывало на ул.Комсомольская - ул.Ком
        $rx = '/(^|\\s)((((http(s)?)|(ftp)):\\/\\/)?(www\\.)?[\\w\\-\\.]+\\.((xn--[a-zA-Z0-9\\-]+)|([a-zA-Z]+)|рф|ком))([^\\w\\-А-Яа-я]|$)/umis';
        if (preg_match_all($rx, $text, $regs)) {
            foreach ($regs[2] as $url) {
                // 2024-04-09, AVS: сейчас необходимости в проверке нет, т.к. явным образом добавлена поддержка
                // Punycode-доменов (вариант 123.321 не пройдет)
                // if (is_numeric($url)) {
                //     continue; // 2023-02-09, AVS: убрал, т.к. в корзине вес не проходит
                // }
                $url = preg_replace('/((http(s)?)|(ftp)):\\/\\//umis', '', trim($url));
                $url = str_replace('www.', '', $url);
                $result[] = $url;
            }
        }
        return $result;
    }


    /**
     * Проверяет поля в зависимости от языка
     * @param array $flatData <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre> плоские POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkRegional(array $flatData = [])
    {
        switch ($this->lang) {
            case 'ru':
                return $this->checkRussian($flatData);
                break;
            default:
                return true;
                break;
        }
    }


    /**
     * Проверяет поля для русского языка
     * @param array $flatData <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre> плоские POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkRussian(array $flatData = [])
    {
        if (!$this->checkRussianData($flatData)) {
            return false;
        }
        if (!$this->checkRussianPhones($flatData)) {
            return false;
        }
        if (!$this->checkRussianStopWords($flatData)) {
            return false;
        }
        return true;
    }


    /**
     * Проверяет, содержится ли в полях кириллица помимо латиницы
     * @param array $flatData <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre> плоские POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkRussianData(array $flatData = [])
    {
        $hasLatinLetters = $hasCyrillicLetters = false;
        foreach ($flatData as $key => $val) {
            $fieldHasLatinLetters = preg_match('/[A-Za-z]/umis', (string)$val);
            $fieldHasCyrillicLetters = preg_match('/[А-Яа-я]/umis', (string)$val);
            $fieldURN = $this->getFieldURN($key);
            if (preg_match('/email/umis', $fieldURN)) {
                continue;
            }
            if (!isset($this->form->fields[$fieldURN])) {
                continue;
            }
            $field = $this->form->fields[$fieldURN];
            if (!$field || ($field->datatype == 'email') || ($field->datatype == 'select')) {
                continue;
            }
            if (in_array($fieldURN, [
                '_name_',
                'full_name',
                'first_name',
                'second_name',
                'last_name',
                'city'
            ]) && $fieldHasLatinLetters && !$fieldHasCyrillicLetters) {
                return false;
            }
            if (!$hasLatinLetters && $fieldHasLatinLetters) {
                $hasLatinLetters = true;
            }
            if (!$hasCyrillicLetters && $fieldHasCyrillicLetters) {
                $hasCyrillicLetters = true;
            }
        }
        if ($hasLatinLetters && !$hasCyrillicLetters) {
            return false;
        }
        return true;
    }


    /**
     * Проверяет телефоны, актуальные для России
     * @param array $flatData <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre> плоские POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkRussianPhones(array $flatData = [])
    {
        foreach ($flatData as $key => $val) {
            $fieldURN = $this->getFieldURN($key);
            $field = null;
            if (isset($this->form->fields[$fieldURN])) {
                $field = $this->form->fields[$fieldURN];
            }
            if (!preg_match('/phone|(^tel$)/umis', $fieldURN)) {
                if (!$field || ($field->datatype != 'tel')) {
                    continue; // Поле не подходит ни по названию, ни по типу
                }
            }

            if (!$this->checkIsRussianPhone($val)) {
                return false;
            }
        }
        return true;
    }


    /**
     * Проверяет, актуален ли телефон для России
     * @param string $text Текст для проверки
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkIsRussianPhone($text)
    {
        if (preg_match('/\\+1/umis', $text)) {
            return false;
        }
        $beautifiedPhone = preg_replace('/\\D/umis', '', $text);
        if (!$beautifiedPhone) {
            return true;
        }
        if ($beautifiedPhone[0] == '0') {
            return false;
        }
        if (preg_match('/^\\d{3}-\\d{3}-\\d{4}$/umis', $text)) { // Пример: 330-689-7666
            return false;
        }
        return true;
    }


    /**
     * Проверяет данные на стоп-слова для русского языка
     * @param array $flatData <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre> плоские POST-данные
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkRussianStopWords(array $flatData = []): bool
    {
        foreach ($flatData as $key => $val) {
            if (!$this->checkRussianTextStopWords((string)$val)) {
                return false;
            }
        }
        return true;
    }


    /**
     * Проверяет стоп-слова в тексте для русского языка
     * @param string $text Текст для проверки
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkRussianTextStopWords(string $text): bool
    {
        return !preg_match('/((^|\\s)((наша компания)|((мои|наши) услуги)|(стоимость услуг)|предлагаем)(\\s|\\.|,|$))|((^|\\s)(накрут|раскрут))/umis', $text);
    }


    /**
     * Возвращает "уплощенный" вариант массива данных
     * @param array $data Входные данные
     * @param string $prefix Префикс поля (для вложенных массивов),
     *     вручную не используется
     * @return array <pre><code>array<
     *     string[] Ключ поля => int|string|null Значение поля
     * ></code></pre>
     */
    public function flattenData(array $data = [], $prefix = '')
    {
        $result = [];
        foreach ($data as $key => $val) {
            $resultKey = $key;
            if ($prefix) {
                $resultKey = $prefix . '[' . $resultKey . ']';
            }
            if (is_array($val)) {
                $flatVal = $this->flattenData($val, $resultKey);
                $result = $result + $flatVal;
            } else {
                $result[$resultKey] = $val;
            }
        }
        return $result;
    }


    /**
     * Получает название поля по ключу данных уплощенного массива
     */
    public function getFieldURN($dataKey)
    {
        $nameArr = explode('[', $dataKey, 2);
        return $nameArr[0];
    }
}
