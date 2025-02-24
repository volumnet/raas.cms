<?php
/**
 * Контроллер сайта
 */
declare(strict_types=1);

namespace RAAS;

use Error;
use SOME\Graphics;
use SOME\Namespaces;
use SOME\Thumbnail;
use RAAS\Application;
use RAAS\IContext;
use RAAS\Process;
use RAAS\View_Web as RAASViewWeb;
use RAAS\CMS\Block;
use RAAS\CMS\Page;
use RAAS\CMS\Material;
use RAAS\CMS\Material_Type;
use RAAS\CMS\User as CMSUser;
use RAAS\CMS\Auth;
use RAAS\CMS\Diag;
use RAAS\CMS\Package as CMSPackage;
use RAAS\CMS\Redirect;

/**
 * Класс контроллера сайта
 * @property-read CMSPackage $model Модель контроллера
 * @property CMSUser $user Текущий пользователь
 * @property-read Diag $diag Объект диагностики
 * @property-read bool $isHTTPS Подключение осуществляется по HTTPS-протоколу
 * @property-read string $scheme Протокол подключения (http или https)
 * @property-read string $host Имя сервера (Punycode)
 * @property-read string $idnHost Имя сервера с учетом IDN
 * @property-read string $schemeHost Имя сервера со схемой
 * @property-read string $idnSchemeHost Имя сервера с учетом IDN со схемой
 * @property-read string $requestMethod Метод запроса (строчными буквами)
 * @property-read string $requestUri Запрос к серверу (относительный URL запроса)
 * @property-read string $url Абсолютный URL запроса
 * @property-read string $idnUrl Абсолютный URL запроса с учетом IDN
 * @property-read string $path Путь запроса
 * @property-read string $query Параметры запроса
 */
class Controller_Frontend extends Abstract_Controller
{
    protected static $instance;

    protected $user;

    /**
     * Текущая страница
     * @var Page|null
     */
    protected $page = null;

    /**
     * Компания
     * @var Material|null
     */
    protected $company = null;

    protected $diag = null;

    public function __get($var)
    {
        switch ($var) {
            case 'model':
                return CMSPackage::i();
                break;
            case 'user':
                if (!$this->user) {
                    $a = new Auth(new CMSUser());
                    $this->user = $a->auth();
                }
                return $this->user;
                break;
            case 'diag':
                return $this->diag;
                break;
            case 'isHTTPS':
                return (
                    isset($_SERVER['HTTPS']) &&
                    (mb_strtolower($_SERVER['HTTPS']) == 'on')
                ) || (
                    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                    (mb_strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')
                );
                break;
            case 'scheme':
                return 'http' . ($this->isHTTPS ? 's' : '');
                break;
            case 'host':
                return $_SERVER['HTTP_HOST'] ?? '';
                break;
            case 'idnHost':
                return idn_to_utf8($this->host);
                break;
            case 'schemeHost':
                return $this->scheme . '://' . $this->host;
                break;
            case 'idnSchemeHost':
                return $this->scheme . '://' . $this->idnHost;
                break;
            case 'requestMethod':
                return mb_strtolower($_SERVER['REQUEST_METHOD'] ?? '');
                break;
            case 'requestUri':
                return $_SERVER['REQUEST_URI'] ?? '';
                break;
            case 'url':
                return $this->schemeHost . $this->requestUri;
                break;
            case 'idnUrl':
                return $this->idnSchemeHost . $this->requestUri;
                break;
            case 'path':
                return parse_url($this->url, PHP_URL_PATH);
                break;
            case 'query':
                return parse_url($this->url, PHP_URL_QUERY);
                break;
            case 'page':
                return $this->page;
                break;
            case 'company':
                return $this->company;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }

    public function __set($var, $val)
    {
        switch ($var) {
            case 'user':
                if ($val instanceof CMSUser) {
                    $this->user = $val;
                }
                break;
            default:
                return parent::__set($var, $val);
                break;
        }
    }


    protected function init()
    {
        $this->view = View_Web::i();
    }


    /**
     * Обрабатывает системные редиректы
     */
    public function checkSystemRedirects()
    {
        if ($this->requestUri == '/robots.txt') {
            $_SERVER['REQUEST_URI'] = '/robots/';
        } elseif ($this->requestUri == '/custom.css') {
            $_SERVER['REQUEST_URI'] = '/custom_css/';
        } elseif ($this->requestUri == '/sitemap.xml') {
            $_SERVER['REQUEST_URI'] = '/sitemaps/';
        } elseif ($this->requestUri == '/favicon.ico') {
            $_SERVER['REQUEST_URI'] = '/favicon/?type=ico';
            $_GET['type'] = 'ico';
        } elseif ($this->requestUri == '/favicon.svg') {
            $_SERVER['REQUEST_URI'] = '/favicon/?type=svg';
            $_GET['type'] = 'svg';
        } elseif ($this->requestUri == '/apple-touch-icon.png') {
            $_SERVER['REQUEST_URI'] = '/favicon/?type=apple';
            $_GET['type'] = 'apple';
        } elseif ($this->requestUri == '/manifest-logo.png') {
            $_SERVER['REQUEST_URI'] = '/favicon/?type=manifest';
            $_GET['type'] = 'apple';
        } elseif ($this->requestUri == '/manifest.json') {
            $_SERVER['REQUEST_URI'] = '/manifest/';
        }
    }


    /**
     * Обрабатывает стандартные редиректы
     */
    public function checkStdRedirects()
    {
        $oldUrl = $this->url;
        $newUrl = Redirect::processAll($oldUrl);
        if ($newUrl != $oldUrl) {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: ' . $newUrl);
            exit;
        }
    }


    public function run()
    {
        $this->checkSystemRedirects();
        $this->processUTM();
        ob_start(function ($text) {
            $result = $text;
            $result = $this->checkMedia($result);
            $result = $this->checkTeleport($result);
            return $result;
        });
        if (!$this->getCache()) {
            $p = pathinfo($this->requestUri);
            if (preg_match('/(\\.(\\d+|auto)x(\\d+|auto)(_(\\w+))?)(\\.|$)/i', $p['basename'], $regs)) {
                $this->parseThumbnail($regs);
            }
            if ($this->checkCompatibility()) {
                if ($this->checkDB()) {
                    if ($this->checkSOME()) {
                        Process::checkIn();
                        $this->checkStdRedirects();
                        if ((int)$this->model->registryGet('clear_cache_by_time')) {
                            $this->model->clearCache(false);
                        }
                        if (CMSPackage::i()->registryGet('diag')) {
                            $diag = Diag::getInstance();
                            $this->setDiag($diag);
                            if ($this->diag) {
                                Application::i()->SQL->query_handler = [$this->diag, 'queryHandler'];
                            }
                            $pst = microtime(true);
                        }
                        $Page = $this->fork();
                    }
                }
                if ($this->diag) {
                    if ($Page) {
                        $diagId = $Page->id;
                        if (($Page->Material && $Page->Material->id) || ($Page->Item && $Page->Item->id)) {
                            $diagId .= '@m';
                        }
                        $this->diag->handle('pages', $diagId, microtime(true) - $pst);
                    }
                    $this->diag->save();
                }
            }
        }
        $text = ob_end_flush();
    }


    /**
     * Устанавливает модуль диагностики
     * @param ?Diag Объект модуля диагностики
     */
    public function setDiag(?Diag $diag = null)
    {
        $this->diag = $diag;
    }


    /**
     * Проверяет медиа-запросы, сопоставляя их с комментариями вида
     * <!--nomobile-->...<!--/nomobile-->
     * <!--nophone-->...<!--/nophone-->
     * <!--notablet-->...<!--/notablet-->
     * <!--nodesktop-->...<!--/nodesktop-->
     * При необходимости исключает блоки
     * @param string $text Входной текст
     * @return string
     */
    public function checkMedia($text)
    {
        $tagsToExclude = [];
        if (CMSPackage::i()->isMobile) {
            $tagsToExclude[] = 'nomobile';
            if (CMSPackage::i()->isPhone) {
                $tagsToExclude[] = 'nophone';
            }
            if (CMSPackage::i()->isTablet) {
                $tagsToExclude[] = 'notablet';
            }
        } else {
            $tagsToExclude[] = 'nodesktop';
        }

        $tags = [];
        foreach ($tagsToExclude as $tagToExclude) {
            $tagData = [];
            foreach (['open', 'close'] as $openCloseIndex => $openCloseTag) {
                $i = 0;
                $realTag = '<!--' . ($openCloseIndex ? '/' : '') . $tagToExclude . '-->';
                $realTagLength = mb_strlen($realTag);
                while ((mb_strlen($text) >= ($i + $realTagLength)) &&
                    (($i = mb_strpos($text, $realTag, $i + $realTagLength)) !== false)
                ) {
                    $tagData[] = [
                        'pos' => $i + ($openCloseIndex ? $realTagLength : 0),
                        'type' => $openCloseTag,
                        'tag' => $tagToExclude
                    ];
                }
            }

            // Отсортируем теги по вхождению
            usort($tagData, function ($a, $b) {
                return $a['pos'] - $b['pos'];
            });

            // Снабдим теги уровнем вложенности
            $level = 0;
            for ($i = 0; $i < count($tagData); $i++) {
                if ($tagData[$i]['type'] == 'close') {
                    $level--;
                }
                $tagData[$i]['level'] = $level;
                if ($tagData[$i]['type'] == 'open') {
                    $level++;
                }
            }

            // Уберем вложенные теги
            $tagData = array_values(array_filter(
                $tagData,
                function ($x) {
                    return !$x['level'];
                }
            ));

            // Совместим теги
            $combinedTag = [];
            for ($i = 0; $i < count($tagData); $i++) {
                if ($tagData[$i]['type'] == 'open') {
                    $combinedTag = $tagData[$i];
                    $combinedTag['open'] = $combinedTag['pos'];
                    unset(
                        $combinedTag['type'],
                        $combinedTag['pos'],
                        $combinedTag['level']
                    );
                } elseif ($tagData[$i]['type'] == 'close') {
                    $combinedTag['close'] = $tagData[$i]['pos'];
                    $tags[] = $combinedTag;
                    unset($combinedTag);
                }
            }
        }

        // Комбинируем теги
        usort($tags, function ($a, $b) {
            return $a['open'] - $b['open'];
        });

        $newTags = [];
        $tag = null;
        for ($i = 0; $i < count($tags); $i++) {
            if (!$i) {
                $tag = $tags[$i];
            } else {
                if ($tags[$i]['open'] < $tag['close']) {
                    if ($tags[$i]['close'] > $tag['close']) {
                        $tag['close'] = $tags[$i]['close'];
                    }
                } else {
                    $newTags[] = $tag;
                    $tag = $tags[$i];
                }
            }
        }
        if ($tag) {
            $newTags[] = $tag;
        }
        $tags = $newTags;

        $result = '';
        if (count($tags)) {
            for ($i = 0; $i < count($tags); $i++) {
                $result .= mb_substr(
                    $text,
                    $i ? $tags[$i - 1]['close'] : 0,
                    $tags[$i]['open'] - ($i ? $tags[$i - 1]['close'] : 0)
                );
            }
            $result .= mb_substr($text, $tags[count($tags) - 1]['close']);
        } else {
            $result .= $text;
        }
        return $result;
    }


    /**
     * Проверяет телепорт-запросы вида
     * <!--raas-teleport-from#[ID]-->...<!--/raas-teleport-from#[ID]-->
     * <!--raas-teleport-to#[ID]-->
     * При необходимости исключает блоки
     * @param string $text Входной текст
     * @return string
     */
    public function checkTeleport($text)
    {
        $st = microtime(true);
        $startTagPrefix = '<!--raas-teleport-from#';
        $endTagPrefix = '<!--/raas-teleport-from#';
        $toTagPrefix = '<!--raas-teleport-to#';
        $tagSuffix = '-->';
        do {
            $changed = false;
            $startTagPos = strpos($text, $startTagPrefix);
            if ($startTagPos !== false) {
                $startTagPos2 = strpos($text, $tagSuffix, $startTagPos);
                if ($startTagPos2 !== false) {
                    $startTagLength = $startTagPos2 + strlen($tagSuffix) - $startTagPos;
                    $idPos = $startTagPos + strlen($startTagPrefix);
                    $idLength = $startTagPos2 - $idPos;
                    $id = substr($text, $idPos, $idLength);
                    $endTag = $endTagPrefix . $id . $tagSuffix;
                    $endTagPos = strpos($text, $endTag);
                    if ($endTagPos !== false) {
                        $endTagLength = strlen($endTag);
                        $outerText = substr($text, $startTagPos, $endTagPos + $endTagLength - $startTagPos);
                        $outerTextLength = strlen($outerText);
                        $innerText = substr($text, $startTagPos + $startTagLength, $endTagPos - $startTagPos - $startTagLength);
                        $toTag = $toTagPrefix . $id . $tagSuffix;
                        $toTagPos = strpos($text, $toTag);
                        if ($toTagPos !== false) {
                            $toTagLength = strlen($toTag);
                            if ($toTagPos > $startTagPos + $outerTextLength) { // Текст телепортируется дальше
                                $text = substr($text, 0, $startTagPos)
                                    . substr($text, $startTagPos + $outerTextLength, $toTagPos - $startTagPos - $outerTextLength)
                                    . $innerText
                                    . substr($text, $toTagPos + $toTagLength);
                                $changed = true;
                            } elseif ($toTagPos < $startTagPos) { // Текст телепортируется ближе
                                $text = substr($text, 0, $toTagPos)
                                    . $innerText
                                    . substr($text, $toTagPos + $toTagLength, $startTagPos - $toTagPos - $toTagLength)
                                    . substr($text, $startTagPos + $outerTextLength);
                                $changed = true;
                            } else {
                                $text = substr($text, 0, $startTagPos)
                                    . $innerText
                                    . substr($text, $endTagPos + $endTagLength);
                                $changed = true;
                            }
                        } else {
                            $text = substr($text, 0, $startTagPos)
                                    . $innerText
                                    . substr($text, $endTagPos + $endTagLength);
                            $changed = true;
                        }
                    }
                }
            }
        } while ($changed);
        // Почистим висящие теги
        do {
            $changed = false;
            $toTagPos = strpos($text, $toTagPrefix);
            if ($toTagPos !== false) {
                $toTagPos2 = strpos($text, $tagSuffix, $toTagPos);
                if ($toTagPos2 !== false) {
                    $toTagLength = $toTagPos2 + strlen($tagSuffix) - $toTagPos;
                    $text = substr($text, 0, $toTagPos)
                        . substr($text, $toTagPos + $toTagLength);
                    $changed = true;
                }
            }
        } while ($changed);
        // $text .= '<!--teleport: ' . (microtime(true) - $st) . '-->';
        return $text;
    }


    /**
     * Сохраняет UTM-метки в сессию
     */
    public function processUTM()
    {
        foreach ($_GET as $key => $val) {
            if (stristr((string)$key, 'utm_')) {
                $_SESSION[$key] = $val;
            }
        }
    }


    /**
     * Экспортирует переводы в константы
     * @param IContext $context Контекст переводов
     * @param string $language Код языка
     */
    public function exportLang(IContext $context, $language)
    {
        $filename = $context->systemDir . '/languages/' . $language . '.ini';
        $translations = [];
        if (is_file($filename)) {
            $translations = (array)parse_ini_file($filename);
        }
        foreach ((array)$translations as $key => $val) {
            $name = $key;
            if (!defined($name)) {
                define($name, $val);
            }
        }
    }


    protected function checkCompatibility()
    {
        return Application::i()->phpVersionCompatible &&
               !Application::i()->missedExt;
    }


    protected function checkDB()
    {
        if (Application::i()->DSN) {
            $ok = Application::i()->initDB();
            return $ok;
        }
        return false;
    }


    protected function checkSOME()
    {
        return Application::i()->initSOME();
    }


    protected function configureDB()
    {
    }


    protected function fork()
    {
        $this->company = $this->getCompany();
        $content = '';
        $blockId = (int)($_SERVER['HTTP_X_RAAS_BLOCK_ID'] ?? 0);
        $url = parse_url($this->requestUri);
        $url = $url['path'];
        $url = str_replace('\\', '/', $url);
        $page = $originalPage = Page::importByURL(
            $this->scheme . '://' . $this->host . $url
        );
        // 2021-11-18, AVS: оставляем кэширование 404-х их собственными настройками
        // $doCache = (bool)(int)$page->cache;
        $page->initialURL = $url;

        // 2021-11-18, AVS: оставляем кэширование 404-х их собственными настройками
        $cprPage = $this->checkPageRights($page/*, $doCache*/);
        $page = $cprPage;
        // 2021-11-18, AVS: оставляем кэширование 404-х их собственными настройками
        $cmPage = $this->checkMaterial($page/*, $doCache*/);
        if ($cmPage->Material) {
            $originalPage->Material = $cmPage->Material;
        }
        $page = $cmPage;
        $this->page = $page;
        $page->company = $this->company;

        RAASViewWeb::i()->loadLanguage($page->lang);
        $this->exportLang(Application::i(), $page->lang);
        $this->exportLang($this->model, $page->lang);
        foreach ($this->model->modules as $mod) {
            $classname = Namespaces::getNS($mod) . '\\View_Web';
            $this->exportLang($mod, $page->lang);
        }

        if ($blockId) {
            $block = Block::spawn($blockId);
            if ($block->vis && $block->tuneWithMaterial($page)) {
                ob_start();
                $block->process($page);
                $content = ob_get_contents();
                ob_end_clean();
            }
        } else {
            $content = $page->process();
        }

        if ($page->Material && !$page->Material->proceed) {
            // Материал заявлен, но не обработан
            $page = $page->getCodePage(404);
            $this->page = $page;
            $page->company = $this->company;
            // 2021-11-18, AVS: оставляем кэширование 404-х их собственными настройками
            // $page->cache = (bool)(int)$page->cache || $doCache;
            $content = $page->process();
        }

        $page->visit();
        if ($page->Material && $page->Material->proceed) {
            $page->Material->visit();
        }
        $content = CMSPackage::processInternalLinks($content, $page);
        echo $content;

        if (!$blockId &&
            $page->cache &&
            ($this->requestMethod == 'get') &&
            !(Application::i()->debug && ($_GET['nocache'] ?? null))
        ) {
            $headers = (array)headers_list();
            if (($status1 = array_filter($headers, function ($x) {
                return stristr($x, 'Status:');
            })) && !($status2 = array_filter($headers, function ($x) {
                return stristr($x, 'HTTP/1.');
            }))) {
                $status2 = array_map(function ($x) {
                    return str_ireplace('Status:', 'HTTP/1.0', $x);
                }, $status1);
                $headers = array_merge($headers, $status2);
            }
            $this->saveCache($content, $headers, '', $originalPage);
        }
        $this->outputDebug();
        return $page;
    }


    /**
     * Получает материал компании
     * @return Material|null
     */
    public function getCompany()
    {
        $materialType = Material_Type::importByURN('company');
        if ($materialType && $materialType->id) {
            $companies = Material::getSet([
                'where' => ["pid = " . $materialType->id],
                'orderBy' => "NOT priority, priority",
                'limit' => 1,
            ]);
            if ($companies) {
                return $companies[0];
            }
        }
        return null;
    }


    /**
     * Проверяет права доступа страницы
     * @param Page $Page исходная страница
     * @param bool $doCache есть ли кэширование
     * @return Page страница, которая должна быть
     */
    protected function checkPageRights(Page $Page, $doCache = false)
    {
        if ($Page->currentUserHasAccess()) {
            return $Page;
        }
        $cp = $Page->getCodePage(403);
        if (!$cp->id) {
            $cp = $Page->getCodePage(404);
        }
        $cp->cache = (bool)(int)$cp->cache || $doCache;
        return $cp;
    }


    /**
     * Выводит отладочную информацию в консоль
     */
    protected function outputDebug()
    {
        if (isset($_SESSION['login']) && $_SESSION['login']) {
            $nonHtmlContentTypeHeaders = array_values(
                array_filter(
                    (array)headers_list(),
                    function ($x) {
                        return stristr($x, 'Content-Type') &&
                               !stristr($x, 'text/html');
                    }
                )
            );
            if (!$nonHtmlContentTypeHeaders && !($_GET['AJAX'] ?? null) && !($_SERVER['HTTP_X_RAAS_BLOCK_ID'] ?? 0)) {
                echo '<script>
                var f = function (d) {
                    var s = d.createElement("script");
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = "/admin/ajax.php?p=cms&action=debug_page&v=' . date('Y-m-d-H-i-s') . '";
                    d.body.append(s);
                };
                document.addEventListener("DOMContentLoaded", function() {
                    f(document);;
                });</script>';
            }
        }
    }


    /**
     * Проверяет наличие и права доступа к материалу
     * @param Page $Page исходная страница
     * @param bool $doCache есть ли кэширование
     * @return Page страница, которая должна быть
     */
    protected function checkMaterial(Page $Page, $doCache = false)
    {
        if (count($Page->additionalURLArray) == 1) {
            $Material = Material::importByURN($Page->additionalURLArray[0]);
            // 2016-02-24, AVS: Добавил проверку in_array(...),
            // т.к. странице присваивались материалы, которых на ней
            // в принципе быть не может
            if ($Material
                && $Material->id
                && in_array($Page->id, array_map(function ($x) {
                    return $x->id;
                }, $Material->affectedPages))) {
                $Page->Material = $Material;
            }
        }
        if (!$Page->Material && $Page->additionalURL && !$Page->nat) {
            // Нет материала, но есть добавочный URL без трансляции адресов
            $cp = $Page->getCodePage(404);
            $cp->cache = (bool)(int)$cp->cache || $doCache;
            return $cp;
        }
        if ($Page->Material && !$Page->Material->currentUserHasAccess()) {
            $cp = $Page->getCodePage(403);
            if (!$cp->id) {
                $cp = $Page->getCodePage(404);
            }
            $cp->cache = (bool)(int)$cp->cache || $doCache;
            return $cp;
        }
        return $Page;
    }


    /**
     * Сохраняет кэш страницы
     * @param string $content Текст страницы
     * @param array<string> $headers HTTP-заголовки страницы
     * @param string $prefix Префикс файла кэша
     * @param ?Page $originalPage Оригинальная страница
     */
    protected function saveCache(
        $content = '',
        array $headers = [],
        $prefix = '',
        ?Page $originalPage = null
    ) {
        if (!is_dir($this->model->cacheDir)) {
            @mkdir($this->model->cacheDir, 0777, true);
        }
        $filename = $this->model->cachePrefix . $prefix . '.' . urlencode($this->url);
        // 2015-11-23, AVS: заменил, т.к. в кэше меню <?php так же заменяется
        // и глючит
        // 2024-07-04, AVS: заменил на str_replace, т.к. preg_replace глючит с бинарными данными
        $replacements = [];
        $replacements['<' . '?'] = "<" . "?php echo '<' . '?'?" . ">";
        $replacements['?' . '>'] = "<" . "?php echo '?' . '>'?" . ">";
        $content = strtr($content, $replacements);
        // $content = str_replace('?' . '>', "<?php echo '?' . '>'? >", $content);
        $text = '<' . "?php\n"
              . "/**\n"
              . " * Файл кэша страницы\n"
              . " * " . $this->url . "\n"
              . " * Создан: " . date('Y-m-d H:i:s') . "\n"
              . " * IP-адрес: " . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n"
              . " * User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? '') . "\n";
        if ($originalPage && $originalPage->id) {
            $text .= " * Страница ID#: " . (int)$originalPage->id . "\n";
        }
        if ($originalPage->Material->id ?? null) {
            $text .= " * Материал ID#: " . (int)$originalPage->Material->id . "\n"
                  .  " * Материал обработан: " . ($originalPage->Material->proceed ? 'да' : 'нет') . "\n";
        }
        $text .= " */\n";
        if ($headers) {
            foreach ($headers as $header) {
                if (!stristr($header, 'cookie')) {
                    $text .= 'header("' . addslashes($header) . '");' . "\n";
                }
            }
        }
        $text .= '?' . ">";
        $text .= $content;
        $cacheLeaveFreeSpace = (int)CMSPackage::i()->registryGet('cache_leave_free_space')
                             * (1024 * 1024);
        $diskFreeSpace = disk_free_space(Application::i()->baseDir);
        $availableCacheSpace = $diskFreeSpace - $cacheLeaveFreeSpace - strlen($text);
        if ($availableCacheSpace > 0) {
            $filepath = $this->model->cacheDir . '/' . $filename . '.php';
            @file_put_contents($filepath, $text);
            if (is_file($filepath)) {
                chmod($filepath, 0777);
            }
        }
    }


    /**
     * Разбор эскиза из адреса
     * @param array $regs обработка регулярного выражения из адреса:
     *                    '/(\\.(\\d+|auto)x(\\d+|auto)(_(\\w+))?)(\\.|$)/i'
     */
    protected function parseThumbnail($regs)
    {
        $width = ($regs[2] != 'auto') ? (int)$regs[2] : null;
        $height = ($regs[3] != 'auto') ? (int)$regs[3] : null;
        $mode = $regs[5];
        $originalFile = str_replace($regs[1], '', $this->requestUri);
        $originalFile = ltrim($originalFile, '/');
        $originalFile = rtrim($originalFile, '.');
        if (is_file($originalFile)) {
            if ($s = getimagesize($originalFile)) {
                $this->getThumbnail($originalFile, $width, $height, $mode);
                exit;
            }
        }
    }


    /**
     * Выводит в stdout содержимое эскиза
     * @param string $filename Имя файла, для которого нужно построить эскиз
     * @param int|null $width Ширина эскиза, либо null, если не ограничена
     * @param int|null $height Высота эскиза, либо null, если не ограничена
     * @param 'inline'|'frame'|'crop'|null $mode Режим создания эскиза
     */
    protected function getThumbnail(
        $filename,
        $width = null,
        $height = null,
        $mode = null
    ) {
        $temp = pathinfo($filename);
        $outputFile = CMSPackage::tn($filename, $width, $height, $mode);
        $ext = strtolower($temp['extension']);
        $mime = Graphics::extension_to_mime_type($ext);
        if (!is_file($outputFile)) {
            if (defined('SOME\Thumbnail::THUMBNAIL_' . strtoupper($mode))) {
                $mode = constant(
                    'SOME\Thumbnail::THUMBNAIL_' . strtoupper($mode)
                );
            } else {
                $mode = Thumbnail::THUMBNAIL_CROP;
            }
            Thumbnail::make(
                $filename,
                $outputFile,
                $width ?: INF,
                $height ?: INF,
                $mode,
                true,
                true,
                90
            );
            chmod($outputFile, 0777);
        }
        // 2023-03-02, AVS: убрал заголовки, отвечающие за предотвращение кэширования (устанавливаются с session_start())
        header_remove('Cache-Control');
        header_remove('Expires');
        header_remove('Pragma');
        header('Content-Type: ' . $mime);
        readfile($outputFile);
    }


    /**
     * Получает кэш текущей страницы
     * @return false Если не удалось получить
     */
    protected function getCache()
    {
        if (($this->requestMethod == 'get') && !(Application::i()->debug && ($_GET['nocache'] ?? null))) {
            $filename = $this->model->cacheDir . '/' . $this->model->cachePrefix
                      . '.' . urlencode($this->url) . '.php';
            if (strlen($filename) < 256) {
                if ($f = glob($filename)) {
                    try {
                        include $f[0];
                    } catch (Error $e) {
                    }
                    $this->outputDebug();
                    exit;
                }
            }
        }
        return false;
    }
}
