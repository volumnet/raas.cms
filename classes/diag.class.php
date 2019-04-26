<?php
/**
 * Лог диагностики
 */
namespace RAAS\CMS;

use RAAS\Application;

/**
 * Класс лога диагностики
 * @property-read string $logDir Директория, где хранится лог
 * @property-read string $logFile Файл, где хранится лог
 * @property-read int $queriesCounter Счетчик запросов
 * @property-read float $queriesTime Общее время запросов
 * @property-read int $timersCounter Счетчик таймеров
 * @property-read float $timersTime Общее время таймеров
 * @property-read int $blocksCounter Счетчик блоков
 * @property-read float $blocksTime Общее время блоков
 * @property-read int $snippetsCounter Счетчик сниппетов
 * @property-read float $snippetsTime Общее время сниппетов
 * @property-read int $pagesCounter Счетчик страниц
 * @property-read float $pagesTime Общее время страниц
 * @property-read array $stat Статистика диагностики
 */
class Diag
{
    const logDir = '/logs';

    protected $filename;

    protected $data = [
        'queries' => [],
        'timers' => [],
        'blocks' => [],
        'pages' => [],
        'snippets' => [],
    ];

    protected static $criticalTime = [
        'queries' => 0.1,
        'timers' => 0,
        'blocks' => 0.1,
        'snippets' => 0.1,
        'pages' => 1
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'logDir':
                return static::getLogDir();
                break;
            case 'logFile':
                if ($this->filename &&
                    stristr($this->filename, $this->logDir)
                ) {
                    if (!is_file($this->filename)) {
                        touch($this->filename);
                    }
                    return $this->filename;
                }
                return null;
                break;
            case 'queriesCounter':
            case 'queriesTime':
            case 'snippetsCounter':
            case 'snippetsTime':
            case 'blocksCounter':
            case 'blocksTime':
            case 'pagesCounter':
            case 'pagesTime':
                preg_match('/^(.*?)(Time|Counter)$/umi', $var, $regs);
                $key = $regs[1];
                $val = mb_strtolower($regs[2]);
                $sum = 0;
                foreach ((array)$this->data[$key] as $row) {
                    if (isset($row[$val])) {
                        $sum += (int)$row[$val];
                    }
                }
                return $sum;
                break;
            case 'stat':
                return $this->getStat();
                break;
        }
    }


    public function __set($var, $val)
    {
        switch ($var) {
            case 'logFile':
                if ($val && stristr($val, $this->logDir)) {
                    $this->filename = $val;
                }
                break;
        }
    }


    public function load($logFile = null)
    {
        if ($logFile) {
            $this->logFile = $logFile;
        }
        if ($this->logFile) {
            $text = @file_get_contents($this->logFile);
            if ($text) {
                $data = @unserialize($text);
                if (is_array($data)) {
                    $this->data = $data;
                }
            }
            return $this->data;
        }
    }


    public function handle($entityName, $entityId, $microtime = 0, $counterKey = 'counter', $timeKey = 'time')
    {
        if ($counterKey) {
            $this->data[$entityName][$entityId][$counterKey]++;
        }
        if ($timeKey) {
            $this->data[$entityName][$entityId][$timeKey] += (float)$microtime;
        }
    }


    public function queryHandler($query = "", $bind = null, $microtime = 0)
    {
        $this->handle('queries', $this->beautifyQuery($query), $microtime);
    }


    public function save($logFile = null)
    {
        if ($logFile) {
            $this->logFile = $logFile;
        }
        if ($this->logFile) {
            file_put_contents($this->logFile, serialize($this->data));
        }
    }


    protected function beautifyQuery($sql)
    {
        $sql = preg_replace('/\'(.*?[\\w\\%\\$])?\'/ims', '?', $sql);
        $sql = preg_replace('/\\b\\d+\\b/ims', '?', $sql);
        $sql = preg_replace('/\\([\\? ,]+?\\)/ims', '?', $sql);
        $sql = preg_replace('/\\s+/ims', ' ', $sql);
        $sql = trim($sql);
        return $sql;
    }


    public static function getLogDir()
    {
        $dir = Application::i()->baseDir . self::logDir;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (is_dir($dir)) {
            return $dir;
        }
        return null;
    }


    public static function getInstance($filename = null)
    {
        if (!$filename) {
            $filename = static::getLogDir() . '/diag' . date('Y-m-d') . '.dat';
        }
        $Item = new self();
        $Item->logFile = $filename;
        if ($Item->logFile) {
            $Item->load();
            return $Item;
        }
        return null;
    }


    public static function merge()
    {
        $args = func_get_args();
        $temp = [];
        foreach ($args as $row) {
            if ($row instanceof Diag) {
                $temp[] = $row;
            } elseif (is_array($row)) {
                foreach ($row as $val) {
                    if ($val instanceof Diag) {
                        $temp[] = $val;
                    }
                }
            }
        }
        unset($args);
        if ($temp) {
            $diag = new self();
            foreach ($temp as $row) {
                foreach ($row->data as $key => $keyData) {
                    foreach ((array)$keyData as $k => $arr) {
                        $diag->data[$key][$k]['counter'] += (int)$arr['counter'];
                        $diag->data[$key][$k]['time'] += (float)$arr['time'];
                        if ($key == 'blocks') {
                            $diag->data[$key][$k]['widgetTime'] += (float)$arr['widgetTime'];
                            $diag->data[$key][$k]['interfaceTime'] += (float)$arr['interfaceTime'];
                        }
                    }
                }
            }
            return $diag;
        }
    }


    protected static function getFiles($date_from = null, $date_to = null)
    {
        $temp = [];
        $dir = scandir(static::getLogDir());
        foreach ($dir as $f) {
            if (preg_match('/diag(\\d{4}-\\d{2}-\\d{2}).dat/i', $f, $regs)) {
                if (($d = strtotime($regs[1])) > 0) {
                    // Учитываем только валидные файлы
                    if ($date_from &&
                        (($fromtime = strtotime($date_from)) > 0)
                    ) {
                        // Только в этом случае работает дата от
                        if ($d < $fromtime) {
                            continue; // Файл датирован ранее даты от
                        }
                    }
                    if ($date_to && (($totime = strtotime($date_to)) > 0)) {
                        // Только в этом случае работает дата до
                        if ($d > $totime) {
                            continue; // Файл датирован позднее даты до
                        }
                    }
                    $temp[] = static::getLogDir() . '/' . $f;
                }
            }
        }
        return $temp;
    }


    protected function getStat()
    {
        $stat = [];
        foreach ($this->data as $entityName => $entityData) {
            $criticalTime = static::$criticalTime[$entityName];
            $all = [];
            foreach ((array)$entityData as $id => $val) {
                $all[] = array_merge(
                    [
                        'key' => $id,
                        'id' => abs(crc32($id) % 1000)
                    ],
                    (array)$val
                );
            }

            $long = array_filter(
                $all,
                function ($x) use ($criticalTime) {
                    return ($x['time'] / $x['counter']) > $criticalTime;
                }
            );
            usort($long, function ($a, $b) {
                return (
                    ((float)$b['time'] / $b['counter']) -
                    ((float)$a['time'] / $a['counter'])
                ) * 1000;
            });
            $long = array_slice($long, 0, 10);
            $longIds = array_map(function ($x) {
                return $x['id'];
            }, $long);

            $frequent = array_filter(
                $all,
                function ($x) use ($criticalTime, $entityName) {
                    return (
                        $entityName != 'queries' ?
                        ($x['time'] / $x['counter']) :
                        $x['time']
                    ) > $criticalTime;
                }
            );
            usort($frequent, function ($a, $b) {
                return ($b['counter'] - $a['counter']);
            });
            $frequent = array_slice($frequent, 0, 10);
            $frequentIds = array_map(function ($x) {
                return $x['id'];
            }, $frequent);

            $main = array_filter(
                $all,
                function ($x) use ($criticalTime, $entityName) {
                    return (
                        $entityName != 'queries' ?
                        ($x['time'] / $x['counter']) :
                        $x['time']
                    ) > $criticalTime;
                }
            );
            usort($main, function ($a, $b) {
                return (
                    (float)$b['time'] - (float)$a['time']
                ) * 1000;
            });
            $main = array_slice($main, 0, 10);
            $mainIds = array_map(function ($x) {
                return $x['id'];
            }, $main);

            $ids = array_values(array_unique(array_merge(
                array_intersect($longIds, $mainIds),
                array_intersect($longIds, $frequentIds),
                array_intersect($frequentIds, $mainIds)
            )));
            foreach ([
                'long' => $long,
                'freq' => $frequent,
                'main' => $main
            ] as $key => $subset) {
                foreach ($subset as $row) {
                    $row['type'] = $entityName;
                    if (in_array($row['id'], $ids)) {
                        if ($row['time'] > static::$criticalTime[$entityName]) {
                            $row['danger'] = true;
                        } else {
                            $row['alert'] = true;
                        }
                    }
                    $stat[$entityName][$key][] = $row;
                }
            }
        }
        return $stat;
    }


    public static function deleteStat($date_from = null, $date_to = null)
    {
        $temp = static::getFiles($date_from, $date_to);
        foreach ($temp as $f) {
            unlink($f);
        }
    }


    public static function getMerged($date_from = null, $date_to = null)
    {
        $temp = static::getFiles($date_from, $date_to);
        $temp = array_map(function ($x) {
            return Diag::getInstance($x);
        }, $temp);
        $diag = static::merge($temp);
        return $diag;
    }
}
