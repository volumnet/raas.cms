<?php
namespace RAAS;

use RAAS\CMS\Page as Page;

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
                if ($val instanceof \RAAS\CMS\User) {
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
        ob_start();
        $url = parse_url($_SERVER['REQUEST_URI']);
        $url = $url['path'];
        $Page = Page::importByURL('http://' . $_SERVER['HTTP_HOST'] . $url);

        $this->exportLang($this->application, $Page->lang);
        $this->exportLang($this->model, $Page->lang);
        foreach ($this->model->modules as $mod) {
            $this->exportLang($mod, $Page->lang);
        }

        if ((trim($Page->url, '/') != trim(str_replace('\\', '/', $url), '/')) && !$Page->nat) {
            $Page = $Page->getCodePage(404);
        }
        ob_clean();
        $Page->process();
        if ($Page->cache) {
            $content = ob_get_contents();
            $headers = (array)headers_list();
        }
        ob_end_flush();
        if ($Page->cache && ($_SERVER['REQUEST_METHOD'] == 'GET')) {
            if ($content) {
                $this->saveCache($Page, $content, $headers);
            }
        }
    }

    protected function saveCache($Page, $content = '', array $headers = array())
    {
        if (!is_dir($this->model->cacheDir)) {
            @mkdir($this->model->cacheDir, 0777, true);
        }
        $id = $this->model->cachePrefix . $Page->id . '.' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
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
            $text .= '?' . ">\n";
        }
        $text .= $content;
        file_put_contents($this->model->cacheDir . '/' . $id . '.php', $text);
    }

    protected function getCache()
    {
        $id = urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        if ($f = glob($this->model->cacheDir . '/*.' . $id . '.php')) {
            include $f[0];
            exit;
        }
        return false;
    }
}