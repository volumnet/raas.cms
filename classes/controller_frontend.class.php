<?php
namespace RAAS;

use \RAAS\CMS\Page;
use \RAAS\CMS\Material;
use \RAAS\CMS\User AS CMSUser;
use \RAAS\CMS\Auth;

final class Controller_Frontend extends Abstract_Controller
{
    private $user;
    
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
            if (preg_match('/(\\d+|auto)x(\\d+|auto)(_(\\w+))?$/i', $p['basename'], $regs) && is_file($f = ltrim($p['dirname'] . '/' . $p['filename'], '/'))) {
                if ($s = getimagesize($f)) {
                    $this->getThumbnail($f, ($regs[1] != 'auto') ? (int)$regs[1] : null, ($regs[2] != 'auto') ? (int)$regs[2] : null, $regs[4]);
                    exit;
                }
            }
            if ($this->checkCompatibility()) {
                if ($this->checkDB()) {
                    if ($this->checkSOME()) {
                        $this->fork();
                    }
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
        return ($this->application->DSN && $this->application->initDB());
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

        // Ищем материал, только если добавочный URN один
        if (count($Page->additionalURLArray) == 1) {
            $Material = Material::importByURN($Page->additionalURLArray[0]);
            if ($Material && $Material->id) {
                $Page->Material = $Material;
            }
        }
        if (!$Page->Material && $Page->additionalURL && !$Page->nat) {
            // Нет материала, но есть добавочный URL без трансляции адресов
            $Page = $Page->getCodePage(404);
            $Page->cache = (bool)(int)$Page->cache || $doCache;
        }
        
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

        echo $content;
        if ($Page->cache && ($_SERVER['REQUEST_METHOD'] == 'GET') && $content) {
            $this->saveCache($content, (array)headers_list());
        }
    }


    protected function saveCache($content = '', array $headers = array(), $prefix = '')
    {
        if (!is_dir($this->model->cacheDir)) {
            @mkdir($this->model->cacheDir, 0777, true);
        }
        $filename = $this->model->cachePrefix . $prefix . '.' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $replace = array();
        $replace['<' . '?'] = '<' . '?php echo "<" . "?";?' . '>';
        $replace['?' . '>'] = '<' . '?php echo "?" . ">";?' . '>';
        $content = strtr($content, $replace);
        $text = '';
        if ($headers) {
            $text .= '<' . "?php\n";
            foreach ($headers as $header) {
                $text .= 'header("' . addslashes($header) . '");' . "\n";
            }
            $text .= '?' . ">";
        }
        $text .= $content;
        file_put_contents($this->model->cacheDir . '/' . $filename . '.php', $text);
        chmod($this->model->cacheDir . '/' . $filename . '.php', 0777);
    }


    protected function getThumbnail($filename, $w = null, $h = null, $mode = null)
    {
        if (defined('SOME\Thumbnail::THUMBNAIL_' . strtoupper($mode))) {
            $mode = constant('SOME\Thumbnail::THUMBNAIL_' . strtoupper($mode));
        } else {
            $mode = \SOME\Thumbnail::THUMBNAIL_CROP;
        }
        ob_start();
        \SOME\Thumbnail::make($filename, null, $w ? $w : INF, $h ? $h : INF, $mode, true, false, 90);
        $content = ob_get_contents();
        $headers = (array)headers_list();
        ob_end_flush();
        if ($content) {
            $this->saveCache($content, $headers, '_tn');
        }
    }


    protected function getCache()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if ($f = glob($this->model->cacheDir . '/*.' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '.php')) {
                include $f[0];
                exit;
            }
        }
        return false;
    }
}