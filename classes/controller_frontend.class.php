<?php
namespace RAAS;

use \RAAS\CMS\Page;
use \RAAS\CMS\Material;
use \RAAS\CMS\User AS CMSUser;
use \RAAS\CMS\Auth;
use \RAAS\CMS\Diag;

class Controller_Frontend extends Abstract_Controller
{
    private $user;
    protected $diag = null;
    
    public function __get($var)
    {
        switch ($var) {
            case 'model':
                return \RAAS\CMS\Package::i();
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
    
    protected static $instance;
    
    protected function init()
    {
    }
    
    public function run()
    {
        if (!$this->getCache()) {
            $p = pathinfo($_SERVER['REQUEST_URI']);
            if (preg_match('/(\\.(\\d+|auto)x(\\d+|auto)(_(\\w+))?)(\\.|$)/i', $p['basename'], $regs)) {
                $this->parseThumbnail($regs);
            }
            if ($this->checkCompatibility()) {
                if ($this->checkDB()) {
                    if ($this->checkSOME()) {
                        if ((int)$this->model->registryGet('clear_cache_by_time')) {
                            $this->model->clearCache(false);
                        }
                        if (\RAAS\CMS\Package::i()->registryGet('diag')) {
                            $this->diag = Diag::getInstance();
                            if ($this->diag) {
                                $this->application->SQL->query_handler = array($this->diag, 'queryHandler');
                            }
                            $pst = microtime(true);
                        }
                        $Page = $this->fork();
                    }
                }
                if ($this->diag) {
                    if ($Page) { 
                        $this->diag->pageHandler($Page, microtime(true) - $pst);
                    }
                    $this->diag->save();
                }
            }
        }
    }
    

    public function exportLang(IContext $Context, $language)
    {
        $filename = $Context->systemDir . '/languages/' . $language . '.ini';
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
        return ($this->application->phpVersionCompatible && !$this->application->missedExt);
    }
    

    protected function checkDB()
    {
        if ($this->application->DSN) {
            $ok = $this->application->initDB();
            return $ok;
        }
        return false;
    }
    

    protected function checkSOME()
    {
        return $this->application->initSOME();
    }
        

    protected function configureDB()
    {
    }
    

    protected function fork()
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $url = $url['path'];
        $url = str_replace('\\', '/', $url);
        $Page = Page::importByURL('http://' . $_SERVER['HTTP_HOST'] . $url);
        $doCache = (bool)(int)$Page->cache;
        $Page->initialURL = $url;

        $Page = $this->checkPageRights($Page, $doCache);
        $Page = $this->checkMaterial($Page, $doCache);

        $this->exportLang($this->application, $Page->lang);
        $this->exportLang($this->model, $Page->lang);
        foreach ($this->model->modules as $mod) {
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
        if ($Page->cache && ($_SERVER['REQUEST_METHOD'] == 'GET') && $content) {
            $headers = (array)headers_list();
            if (($status1 = array_filter($headers, function($x) { return stristr($x, 'Status:'); })) && !($status2 = array_filter($headers, function($x) { return stristr($x, 'HTTP/1.'); }))) {
                $status2 = array_map(function($x) { return str_ireplace('Status:', 'HTTP/1.0', $x); }, $status1);
                $headers = array_merge($headers, $status2);
            }
            $this->saveCache($content, $headers);
        }
        return $Page;
    }


    /**
     * Проверяет права доступа страницы
     * @param Page $Page исходная страница
     * @param bool $doCache есть ли кэширование
     * @return Page страница, которая должна быть
     */
    private function checkPageRights(Page $Page, $doCache = false)
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
    private function checkMaterial(Page $Page, $doCache = false)
    {
        if (count($Page->additionalURLArray) == 1) {
            $Material = Material::importByURN($Page->additionalURLArray[0]);
            // 2016-02-24, AVS: Добавил проверку in_array(...), т.к. странице присваивались материалы, которых на ней в принципе быть не может
            if ($Material && $Material->id && in_array($Page->id, array_map(function($x) { return $x->id; }, $Material->affectedPages))) {
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


    protected function saveCache($content = '', array $headers = array(), $prefix = '')
    {
        if (!is_dir($this->model->cacheDir)) {
            @mkdir($this->model->cacheDir, 0777, true);
        }
        $filename = $this->model->cachePrefix . $prefix . '.' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $replace = array();
        // 2015-11-23, AVS: заменил, т.к. в кэше меню <?php так же заменяется и глючит
        $content = preg_replace('/\\<\\?xml (.*?)\\?\\>/umi', '<?php echo \'<\' . \'?xml $1?\' . ">\\n"?' . '>', $content);
        $text = '';
        if ($headers) {
            $text .= '<' . "?php\n";
            foreach ($headers as $header) {
                if (!stristr($header, 'cookie')) {
                    $text .= 'header("' . addslashes($header) . '");' . "\n";
                }
            }
            $text .= '?' . ">";
        }
        $text .= $content;
        file_put_contents($this->model->cacheDir . '/' . $filename . '.php', $text);
        chmod($this->model->cacheDir . '/' . $filename . '.php', 0777);
    }


    /**
     * Разбор эскиза из адреса
     * @param array $regs обработка регулярного выражения из адреса: '/(\\.(\\d+|auto)x(\\d+|auto)(_(\\w+))?)(\\.|$)/i'
     */
    private function parseThumbnail($regs)
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


    protected function getThumbnail($filename, $w = null, $h = null, $mode = null)
    {
        $temp = pathinfo($filename);
        $outputFile = \RAAS\CMS\Package::tn($filename, $w, $h, $mode);
        $mime = \SOME\Graphics::extension_to_mime_type(strtolower($temp['extension']));
        if (!is_file($outputFile)) {
            if (defined('SOME\Thumbnail::THUMBNAIL_' . strtoupper($mode))) {
                $mode = constant('SOME\Thumbnail::THUMBNAIL_' . strtoupper($mode));
            } else {
                $mode = \SOME\Thumbnail::THUMBNAIL_CROP;
            }
            \SOME\Thumbnail::make($filename, $outputFile, $w ?: INF, $h ?: INF, $mode, true, true, 90);
            chmod($outputFile, 0777);
        }
        header('Content-Type: ' . $mime);
        readfile($outputFile);
    }


    protected function getCache()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if ($f = glob($this->model->cacheDir . '/' . $this->model->cachePrefix . '.' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '.php')) {
                include $f[0];
                exit;
            }
        }
        return false;
    }
}