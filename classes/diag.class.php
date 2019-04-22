<?php
/**
 * Лог диагностики
 */
namespace RAAS\CMS;

/**
 * Класс лога диагностики
 * @property-read string $logDir Директория, где хранится лог
 * @property-read string $logFile Файл, где хранится лог
 * @property-read int $queriesCounter Счетчик запросов
 * @property-read float $queriesTime Общее время запросов
 * @property-read int $blocksCounter Счетчик блоков
 * @property-read float $blocksTime Общее время блоков
 * @property-read int $pagesCounter Счетчик страниц
 * @property-read float $pagesTime Общее время страниц
 * @property-read array $stat Статистика диагностики
 */
class Diag
{
    const logDir = 'logs';

    protected $filename;
    protected $data = [];
    protected static $criticalTime = [
        'queries' => 0.1,
        'blocks' => 0.1,
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
                $temp = 0;
                foreach ($this->data['queries'] as $row) {
                    if (isset($row['counter'])) {
                        $temp += (int)$row['counter'];
                    }
                }
                return $temp;
                break;
            case 'queriesTime':
                $temp = 0;
                foreach ($this->data['queries'] as $row) {
                    if (isset($row['time'])) {
                        $temp += (float)$row['time'];
                    }
                }
                return $temp;
                break;
            case 'blocksCounter':
                $temp = 0;
                foreach ($this->data['blocks'] as $row) {
                    if (isset($row['counter'])) {
                        $temp += (int)$row['counter'];
                    }
                }
                return $temp;
                break;
            case 'blocksTime':
                $temp = 0;
                foreach ($this->data['blocks'] as $row) {
                    if (isset($row['time'])) {
                        $temp += (float)$row['time'];
                    }
                }
                return $temp;
                break;
            case 'pagesCounter':
                $temp = 0;
                foreach ($this->data['pages'] as $row) {
                    if (isset($row['counter'])) {
                        $temp += (int)$row['counter'];
                    }
                }
                return $temp;
                break;
            case 'pagesTime':
                $temp = 0;
                foreach ($this->data['pages'] as $row) {
                    if (isset($row['time'])) {
                        $temp += (float)$row['time'];
                    }
                }
                return $temp;
                break;
            case 'stat':
                $temp = [];
                foreach (['queries', 'blocks', 'pages'] as $statKey) {
                    if (isset($this->data[$statKey])) {
                        $ct = static::$criticalTime[$statKey];
                        $all = [];
                        foreach ($this->data[$statKey] as $key => $val) {
                            $all[] = array_merge(
                                [
                                    'key' => $key,
                                    'id' => abs(crc32($key) % 1000)
                                ],
                                (array)$val
                            );
                        }
                        $L = $all;
                        $L = array_filter($L, function ($x) use ($ct) {
                            return ($x['time'] / $x['counter']) > $ct;
                        });
                        usort($L, function ($a, $b) {
                            return (
                                ((float)$b['time'] / $b['counter']) -
                                ((float)$a['time'] / $a['counter'])
                            ) * 1000;
                        });
                        $L = array_slice($L, 0, 10);
                        $Lids = array_map(function ($x) {
                            return $x['id'];
                        }, $L);

                        $F = $all;
                        $F = array_filter($F,
                            function ($x) use ($ct, $statKey) {
                                return (
                                    $statKey != 'queries' ?
                                    ($x['time'] / $x['counter']) :
                                    $x['time']
                                ) > $ct;
                            }
                        );
                        usort($F, function ($a, $b) {
                            return ($b['counter'] - $a['counter']) * 1000;
                        });
                        $F = array_slice($F, 0, 10);
                        $Fids = array_map(function ($x) {
                            return $x['id'];
                        }, $F);

                        $M = $all;
                        $M = array_filter(
                            $M,
                            function ($x) use ($ct, $statKey) {
                                return (
                                    $statKey != 'queries' ?
                                    ($x['time'] / $x['counter']) :
                                    $x['time']
                                ) > $ct;
                            }
                        );
                        usort($M, function ($a, $b) {
                            return (
                                (float)$b['time'] - (float)$a['time']
                            ) * 1000;
                        });
                        $M = array_slice($M, 0, 10);
                        $Mids = array_map(function ($x) {
                            return $x['id'];
                        }, $M);

                        $ids = array_merge(
                            array_intersect($Lids, $Mids),
                            array_intersect($Lids, $Fids),
                            array_intersect($Fids, $Mids)
                        );
                        $ids = array_unique($ids);
                        $ids = array_values($ids);
                        foreach ([
                            'long' => 'L',
                            'freq' => 'F',
                            'main' => 'M'
                        ] as $key => $key2) {
                            foreach ($$key2 as $row) {
                                $row['type'] = $statKey;
                                if (in_array($row['id'], $ids)) {
                                    if ($row['time'] > static::$criticalTime[$statKey]) {
                                        $row['danger'] = true;
                                    } else {
                                        $row['alert'] = true;
                                    }
                                }
                                $temp[$statKey][$key][] = $row;
                            }
                        }
                        unset($L, $Lids, $F, $Fids, $M, $Mids, $ids, $ct);
                    }
                }
                return $temp;
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


    public function __construct()
    {
        $this->data = ['queries' => [], 'blocks' => [], 'pages' => []];
    }


    public function load($logFile = null)
    {
        if ($logFile) {
            $this->logFile = $logFile;
        }
        if ($this->logFile) {
            $data = (array)@unserialize(file_get_contents($this->logFile));
            if ($data && is_array($data)) {
                $this->data = $data;
            }
            return $data;
        }
    }


    public function queryHandler($query = "", $bind = null, $microtime = 0)
    {
        $this->data['queries'][$this->beautifyQuery($query)]['counter']++;
        $this->data['queries'][$this->beautifyQuery($query)]['time'] += (float)$microtime;
    }


    public function blockHandler(Block $block, $microtime = 0)
    {
        $this->data['blocks'][(int)$block->id]['counter']++;
        $this->data['blocks'][(int)$block->id]['time'] += (float)$microtime;
    }


    public function blockInterfaceHandler(Block $block, $microtime = 0)
    {
        $this->data['blocks'][(int)$block->id]['interfaceTime'] += (float)$microtime;
    }


    public function blockWidgetHandler(Block $block, $microtime = 0)
    {
        $this->data['blocks'][(int)$block->id]['widgetTime'] += (float)$microtime;
    }


    public function pageHandler(Page $Page, $microtime = 0)
    {
        // $u = $this->beautifyURL($_SERVER['REQUEST_URI']);
        $u = $Page->id;
        $this->data['pages'][$u]['counter']++;
        $this->data['pages'][$u]['time'] += $microtime;
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


    protected function beautifyURL($url)
    {
        $get = parse_url($url, PHP_URL_QUERY);
        parse_str($get, $getarr);
        foreach ($getarr as $key => $val) {
            $getarr[$key] = $this->beautifyValue($val);
        }
        $get = http_build_query($getarr);
        $url = str_replace(parse_url($url, PHP_URL_QUERY), $get, $url);
        return $url;
    }


    protected function beautifyValue($val)
    {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = $this->beautifyValue($v);
            }
        } elseif (is_numeric($val)) {
            if (!in_array($val, [-1, 0, 1])) {
                $val = 'D';
            }
        } elseif ($val) {
            $val = 'S';
        }
        return $val;
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
        $dir = __DIR__ . '/../../../../' . self::logDir;
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
                foreach (['queries', 'blocks', 'pages'] as $key) {
                    foreach ($row->data[$key] as $k => $arr) {
                        $diag->data[$key][$k]['counter'] += (int)$arr['counter'];
                        $diag->data[$key][$k]['time'] += (float)$arr['time'];
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
