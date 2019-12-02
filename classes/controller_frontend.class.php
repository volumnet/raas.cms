<?php
/**
 * Контроллер сайта
 */
namespace RAAS;

use SOME\Graphics;
use SOME\Namespaces;
use SOME\Thumbnail;
use RAAS\Application;
use RAAS\View_Web as RAASViewWeb;
use RAAS\CMS\Page;
use RAAS\CMS\Material;
use RAAS\CMS\User as CMSUser;
use RAAS\CMS\Auth;
use RAAS\CMS\Diag;
use RAAS\CMS\Package as CMSPackage;

/**
 * Класс контроллера сайта
 * @property-read CMSPackage $model Модель контроллера
 * @property CMSUser $user Текущий пользователь
 * @property-read Diag $diag Объект диагностики
 */
class Controller_Frontend extends Abstract_Controller
{
    protected static $instance;

    protected $user;

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
     * Обрабатывает стандартные редиректы
     */
    public function checkStdRedirects()
    {
        if ($_SERVER['REQUEST_URI'] == '/robots.txt') {
            $_SERVER['REQUEST_URI'] = '/robots/';
        } elseif ($_SERVER['REQUEST_URI'] == '/custom.css') {
            $_SERVER['REQUEST_URI'] = '/custom_css/';
        } elseif ($_SERVER['REQUEST_URI'] == '/sitemap.xml') {
            $_SERVER['REQUEST_URI'] = '/sitemaps/';
        } elseif ($_SERVER['REQUEST_URI'] == '/sitemaps.xml') {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/sitemap.xml');
            exit;
        } else {
            $temp = parse_url($_SERVER['REQUEST_URI']);
            if (preg_match('/[^\\/]$/i', $temp['path']) &&
                !stristr(basename($temp['path']), '.')
            ) {
                $newUrl = 'http://' . $_SERVER['HTTP_HOST']
                        . str_replace(
                            $temp['path'],
                            $temp['path'] . '/',
                            $_SERVER['REQUEST_URI']
                        );
                header("HTTP/1.1 301 Moved Permanently");
                header('Location: ' . $newUrl);
                exit;
            }
        }
    }


    public function run()
    {
        $this->checkStdRedirects();
        if (!$this->getCache()) {
            $p = pathinfo($_SERVER['REQUEST_URI']);
            if (preg_match(
                '/(\\.(\\d+|auto)x(\\d+|auto)(_(\\w+))?)(\\.|$)/i',
                $p['basename'],
                $regs
            )) {
                $this->parseThumbnail($regs);
            }
            if ($this->checkCompatibility()) {
                if ($this->checkDB()) {
                    if ($this->checkSOME()) {
                        if ((int)$this->model->registryGet('clear_cache_by_time')) {
                            $this->model->clearCache(false);
                        }
                        if (CMSPackage::i()->registryGet('diag')) {
                            $this->diag = Diag::getInstance();
                            if ($this->diag) {
                                Application::i()->SQL->query_handler = [
                                    $this->diag,
                                    'queryHandler'
                                ];
                            }
                            $pst = microtime(true);
                        }
                        $Page = $this->fork();
                    }
                }
                if ($this->diag) {
                    if ($Page) {
                        $this->diag->handle(
                            'pages',
                            $Page->id,
                            microtime(true) - $pst
                        );
                    }
                    $this->diag->save();
                }
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
        if (is_file($filename)) {
            $translations = parse_ini_file($filename);
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
        $url = parse_url($_SERVER['REQUEST_URI']);
        $url = $url['path'];
        $url = str_replace('\\', '/', $url);
        $Page = $originalPage = Page::importByURL(
            'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' .
            $_SERVER['HTTP_HOST'] . $url
        );
        $doCache = (bool)(int)$Page->cache;
        $Page->initialURL = $url;

        $cprPage = $this->checkPageRights($Page, $doCache);
        $Page = $cprPage;
        $cmPage = $this->checkMaterial($Page, $doCache);
        if ($cmPage->Material) {
            $originalPage->Material = $cmPage->Material;
        }
        $Page = $cmPage;

        RAASViewWeb::i()->loadLanguage($Page->lang);
        $this->exportLang(Application::i(), $Page->lang);
        $this->exportLang($this->model, $Page->lang);
        foreach ($this->model->modules as $mod) {
            $classname = Namespaces::getNS($mod) . '\\View_Web';
            $this->exportLang($mod, $Page->lang);
        }

        $content = $Page->process();

        if ($Page->Material && !$Page->Material->proceed) {
            // Материал заявлен, но не обработан
            $Page = $Page->getCodePage(404);
            $Page->cache = (bool)(int)$Page->cache || $doCache;
            $content = $Page->process();
        }

        $Page->visit();
        if ($Page->Material && $Page->Material->proceed) {
            $Page->Material->visit();
        }
        echo $content;

        if ($Page->cache && ($_SERVER['REQUEST_METHOD'] == 'GET')) {
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
        return $Page;
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
     * @param Page $originalPage Оригинальная страница
     */
    protected function saveCache(
        $content = '',
        array $headers = [],
        $prefix = '',
        Page $originalPage = null
    ) {
        if (!is_dir($this->model->cacheDir)) {
            @mkdir($this->model->cacheDir, 0777, true);
        }
        $filename = $this->model->cachePrefix . $prefix . '.'
                  . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $replace = [];
        // 2015-11-23, AVS: заменил, т.к. в кэше меню <?php так же заменяется
        // и глючит
        $content = preg_replace(
            '/\\<\\?xml (.*?)\\?\\>/umi',
            '<' . '?php echo \'<\' . \'?xml $1?\' . ">\\n"?' . '>',
            $content
        );
        $text = '<' . "?php\n"
              . "/**\n"
              . " * Файл кэша страницы\n"
              . " * " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n"
              . " * Создан: " . date('Y-m-d H:i:s') . "\n"
              . " * IP-адрес: " . $_SERVER['REMOTE_ADDR'] . "\n"
              . " * User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
        if ($originalPage->id) {
            $text .= " * Страница ID#: " . (int)$originalPage->id . "\n";
        }
        if ($originalPage->Material->id) {
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
        file_put_contents(
            $this->model->cacheDir . '/' . $filename . '.php',
            $text
        );
        chmod($this->model->cacheDir . '/' . $filename . '.php', 0777);
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
        $originalFile = str_replace($regs[1], '', $_SERVER['REQUEST_URI']);
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
        header('Content-Type: ' . $mime);
        readfile($outputFile);
    }


    /**
     * Получает кэш текущей страницы
     * @return false Если не удалось получить
     */
    protected function getCache()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if ($f = glob(
                $this->model->cacheDir . '/' . $this->model->cachePrefix . '.' .
                urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) .
                '.php'
            )) {
                include $f[0];
                exit;
            }
        }
        return false;
    }
}
