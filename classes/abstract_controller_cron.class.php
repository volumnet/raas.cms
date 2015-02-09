<?php
namespace RAAS;

abstract class Abstract_Controller_Cron extends Abstract_Controller
{
    const max_time = 3500;
    const timeFile = 'time.log';

    protected $st;
    protected $encoding = 'UTF-8';
    protected static $instance;
    
    public function __get($var)
    {
        switch ($var) {
            case 'model':
                return Application::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }
    
    public function __set($var, $val)
    {
        switch ($var) {
            default:
                return parent::__set($var, $val);
                break;
        }
    }
    
    protected function init()
    {
    }
    

    public function run()
    {
        $this->st = time();
        if ($this->checkCompatibility()) {
            if ($this->checkDB()) {
                if ($this->checkSOME()) {
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    $this->fork();
                }
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
    {}
    

    protected function fork()
    {}


    protected function doLog($text)
    {
        if ($this->encoding) {
            $text = iconv('UTF-8', $this->encoding . '//IGNORE', $text);
        }
        echo number_format(microtime(true) - $this->st, 3, '.', ' ') . ': ' . $text . "\n";
    }
}