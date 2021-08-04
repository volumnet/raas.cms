<?php
/**
 * Система антиспама на основе анализа данных
 */
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
     * Конструктор класса
     * @param Form $form Форма для проверки
     * @param string $lang Язык для проверки
     * @param string|null $host Хост (null, чтобы использовать текущий)
     */
    public function __construct(Form $form, $lang = 'ru', $host = null)
    {
        $this->form = $form;
        $this->lang = $lang;
        $this->host = idn_to_ascii($host ?: $_SERVER['HTTP_HOST']);
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
        if (!$this->checkForeignLinks($flatData)) {
            return false;
        }
        if (!$this->checkStopWords($flatData)) {
            return false;
        }
        return true;
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
            $field = $this->form->fields[$fieldURN];
            if ($field && ($field->datatype == 'url')) {
                continue; // Если поле типа "Адрес сайта", то его не учитываем
            }
            if (!$this->checkTextForeignLinks($val)) {
                return false;
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
            if (!$this->checkTextStopWords($val)) {
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
    public function checkTextStopWords($text)
    {
        return !preg_match('/porn|boobs|(we offer)/umis', $text);
    }


    /**
     * Проверяет ссылки на сторонние сайты в тексте
     * @param string $text Текст для проверки
     * @return bool Пройден ли антиспам-фильтр
     */
    public function checkTextForeignLinks($text)
    {
        $urls = $this->extractURLs($text);
        if ($urls) {
            foreach ($urls as $url) {
                $url = idn_to_ascii($url);
                if ($url != $this->host) {
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
    public function extractURLs($text)
    {
        $result = [];
        $rx = '/(^| )(((http(s)?)|(ftp)):\\/\\/)?(www\\.)?[\\w\\-\\.]+\\.(([a-zA-Z0-9\\-]+)|рф|ком)/umis';
        if (preg_match_all($rx, $text, $regs)) {
            foreach ($regs[0] as $url) {
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
            $fieldURN = $this->getFieldURN($key);
            if (preg_match('/email/umis', $fieldURN)) {
                continue;
            }
            $field = $this->form->fields[$fieldURN];
            if ($field && ($field->datatype == 'email')) {
                continue;
            }
            if (!$hasLatinLetters && preg_match('/[A-Za-z]/umis', $val)) {
                $hasLatinLetters = true;
            }
            if (!$hasCyrillicLetters && preg_match('/[А-Яа-я]/umis', $val)) {
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
            $field = $this->form->fields[$fieldURN];
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
    public function checkRussianStopWords(array $flatData = [])
    {
        foreach ($flatData as $key => $val) {
            if (!$this->checkRussianTextStopWords($val)) {
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
    public function checkRussianTextStopWords($text)
    {
        return !preg_match('/(наша компания)|((^| )предлагаем( |$))/umis', $text);
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
