<?php
/**
 * Диагностический таймер
 */
namespace RAAS\CMS;

use RAAS\Timer;

/**
 * Класс диагностического таймера
 */
class DiagTimer extends Timer
{
    /**
     * Наименование таймера
     * @var string
     */
    protected $name = '';

    /**
     * Имя файла таймера
     * @var string
     */
    protected $filename = '';

    /**
     * Строка старта
     * @var int
     */
    protected $startLine = 0;

    /**
     * Строка остановки
     * @var int
     */
    protected $stopLine = 0;

    /**
     * Конструктор класса
     * @param string|null $name Наименование таймера
     * @param string|null $filename Имя файла
     * @param bool $autoStart Стартовать автоматически
     */
    public function __construct(
        $name = null,
        $filename = null,
        $autoStart = true
    ) {
        $debugBacktrace = debug_backtrace(0, 3);
        if ($name) {
            $this->name = $name;
        } else {
            $this->name = $debugBacktrace[1]['function'];
        }
        if ($filename) {
            $this->filename = $filename;
        } else {
            $filepath = $debugBacktrace[0]['file'];
            if (!stristr($filepath, 'eval()')) {
                $filename = basename($filepath);
                $this->filename = $filename;
            }
        }
        if ($autoStart) {
            $this->start();
        }
    }


    public function start()
    {
        if (!($diag = Controller_Frontend::i()->diag)) {
            return;
        } elseif ($this->startLine) {
            return;
        }
        if (!$this->startLine) {
            $debugBacktrace = debug_backtrace(0, 3);
            foreach ($debugBacktrace as $dbRow) {
                if ($dbRow['file'] != __FILE__) {
                    $this->startLine = $dbRow['line'];
                    break;
                }
            }
            parent::start();
        }
    }


    public function stop()
    {
        if (!($diag = Controller_Frontend::i()->diag)) {
            return;
        }
        if (!$this->startLine) {
            return;
        }
        $debugBacktrace = debug_backtrace(0, 3);
        $this->stopLine = $debugBacktrace[0]['line'];
        parent::stop();

        $nameArr = [];
        if ($this->name) {
            $nameArr[] = $this->name;
        }
        $fileData = '';
        if ($this->filename) {
            $fileData .= $this->filename . ':';
        }
        $fileData .= $this->startLine . '-' . $this->stopLine;
        if ($fileData) {
            $nameArr[] = '(' . $fileData . ')';
        }
        $name = implode(' ', $nameArr);
        $diag->handle('timers', $name, $this->time);
    }
}
