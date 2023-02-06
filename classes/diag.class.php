<?php
/**
 * Лог диагностики
 */
namespace RAAS\CMS;

use RAAS\Application;

/**
 * Класс лога диагностики
 * @property-read string $logDir Директория, где хранится лог
 * @property string $logFile Файл, где хранится лог
 * @property-read int $queriesCounter Счетчик запросов
 * @property-read float $queriesTime Общее время запросов
 * @property-read int $timersCounter Счетчик таймеров
 * @property-read float $timersTime Общее время таймеров
 * @property-read int $blocksCounter Счетчик блоков
 * @property-read float $blocksTime Общее время блоков
 * @property-read int $snippetsCounter Счетчик сниппетов
 * @property-read float $snippetsTime Общее время сниппетов
 * @property-read int $templatesCounter Счетчик шаблонов
 * @property-read float $templatesTime Общее время шаблонов
 * @property-read int $pagesCounter Счетчик страниц
 * @property-read float $pagesTime Общее время страниц
 * @property-read array<string[] Тип данных => [
 *                    'long' Долгие сущности |
 *                    'freq' Частые сущности |
 *                    'main' Тяжелые сущности => array<[
 *                        'id' => string Внутренний ID сущности,
 *                        'key' => string Заданный ID сущности,
 *                        'counter' => int Количество обработанных сущностей,
 *                        'time' => float Общее время в секундах,
 *                        'interfaceTime' => float Общее время интерфейса
 *                                                 (только для блоков)
 *                        'widgetTime' => float Общее время виджета
 *                                              (только для блоков)
 *                    ]>
 *                ]> $stat Статистика диагностики
 */
class Diag
{
    /**
     * Путь к папке (относительно корня сайта), где хранятся логи диагностики
     */
    const logDir = '/logs';

    /**
     * Имя файла
     * @var string
     */
    protected $filename;

    /**
     * Данные диагностики
     * @var array<string[] Тип данных => array<
     *          string[] ID сущности => [
     *              'counter' => int Количество обработанных сущностей,
     *              'time' => float Общее время в секундах,
     *              'interfaceTime' => float Общее время интерфейса
     *                                       (только для блоков)
     *              'widgetTime' => float Общее время виджета
     *                                    (только для блоков)
     *          ]
     *      >>
     */
    protected $data = [
        'queries' => [],
        'timers' => [],
        'blocks' => [],
        'snippets' => [],
        'templates' => [],
        'pages' => [],
    ];

    /**
     * Критическое время для записи в диагностику
     * @var array<string[] Тип данных => float Время в секундах>
     */
    protected static $criticalTime = [
        'queries' => 0.1,
        'timers' => 0,
        'blocks' => 0.1,
        'snippets' => 0.1,
        'templates' => 0.1,
        'pages' => 1,
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
            case 'timersCounter':
            case 'timersTime':
            case 'snippetsCounter':
            case 'snippetsTime':
            case 'templatesCounter':
            case 'templatesTime':
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
                        $sum += (float)$row[$val];
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


    /**
     * Загружает данные из лога
     * @param string $logFile Файл, из которого загружаем
     * @return array<string[] Тип данных => array<
     *             string[] ID сущности => [
     *                 'counter' => int Количество обработанных сущностей,
     *                 'time' => float Общее время в секундах,
     *                 'interfaceTime' => float Общее время интерфейса
     *                                          (только для блоков)
     *                 'widgetTime' => float Общее время виджета
     *                                       (только для блоков)
     *             ]
     *         >> Загруженные данные
     */
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
        }
        return $this->data;
    }


    /**
     * Записывает данные о сущности
     * @param string $entityName Тип данных (сущности)
     * @param string $entityId ID сущности
     * @param float $microtime Время сущности в секундах
     * @param string $counterKey Поле с количеством сущностей
     * @param string $timeKey Поле с общим временем сущности
     */
    public function handle(
        $entityName,
        $entityId,
        $microtime = 0,
        $counterKey = 'counter',
        $timeKey = 'time'
    ) {
        if ($counterKey) {
            if (!isset($this->data[$entityName][$entityId][$counterKey])) {
                $this->data[$entityName][$entityId][$counterKey] = 0;
            }
            $this->data[$entityName][$entityId][$counterKey]++;
        }
        if ($timeKey) {
            if (!isset($this->data[$entityName][$entityId][$timeKey])) {
                $this->data[$entityName][$entityId][$timeKey] = 0;
            }
            $this->data[$entityName][$entityId][$timeKey] += (float)$microtime;
        }
    }


    /**
     * Обработчик SQL-запросов для SOME\DB
     * @param string $query Шаблон SQL-запроса
     * @param array $bind Привязки SQL-запроса
     * @param float $microtime Время выполнения запроса
     */
    public function queryHandler($query = "", $bind = null, $microtime = 0)
    {
        $this->handle('queries', $this->beautifyQuery($query), $microtime);
    }


    /**
     * Сохраняет данные в лог
     * @param string $logFile Файл, в который сохраняем
     */
    public function save($logFile = null)
    {
        if ($logFile) {
            $this->logFile = $logFile;
        }
        if ($this->logFile) {
            file_put_contents($this->logFile, serialize($this->data));
        }
    }


    /**
     * Бьютифицирует SQL-запрос
     * (заменяет конкретные значения на "?")
     * @param string $sql Входной запрос
     * @return string
     */
    protected function beautifyQuery($sql)
    {
        $sql = preg_replace('/\'(.*?[\\w\\%\\$])?\'/ims', '?', $sql);
        $sql = preg_replace('/\\b\\d+\\b/ims', '?', $sql);
        $sql = preg_replace('/\\([\\? ,]+?\\)/ims', '?', $sql);
        $sql = preg_replace('/\\s+/ims', ' ', $sql);
        $sql = trim($sql);
        return $sql;
    }


    /**
     * Создает при необходимости и возвращает папку логов
     * @return string|null null, если невозможно создать папку
     */
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


    /**
     * Получает объект диагностики для заданного файла
     * @param string|null $filename Файл для открытия, либо null
     *                              для файла по умолчанию на текущую дату
     * @return self|null Объект диагностики, либо null, если не удалось
     *                          открыть/создать объект с заданным файлом
     */
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


    /**
     * Объединяет несколько объектов диагностики
     * @param array<Diag|array<Diag>> ...$args Объекты для объединения
     * @return Diag|null null, если не удалось объединить
     */
    public static function merge(...$args)
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
                        if (!isset($diag->data[$key][$k]['counter'])) {
                            $diag->data[$key][$k]['counter'] = 0;
                        }
                        if (!isset($diag->data[$key][$k]['time'])) {
                            $diag->data[$key][$k]['time'] = 0;
                        }
                        $diag->data[$key][$k]['counter'] += (int)($arr['counter'] ?? 0);
                        $diag->data[$key][$k]['time'] += (float)($arr['time'] ?? 0);
                        if ($key == 'blocks') {
                            if (!isset($diag->data[$key][$k]['widgetTime'])) {
                                $diag->data[$key][$k]['widgetTime'] = 0;
                            }
                            if (!isset($diag->data[$key][$k]['interfaceTime'])) {
                                $diag->data[$key][$k]['interfaceTime'] = 0;
                            }
                            $diag->data[$key][$k]['widgetTime'] += (float)($arr['widgetTime'] ?? 0);
                            $diag->data[$key][$k]['interfaceTime'] += (float)($arr['interfaceTime'] ?? 0);
                        }
                    }
                }
            }
            return $diag;
        }
    }


    /**
     * Получает список файлов с заданными границами дат
     * @param string|null $dateFrom Дата, от (в формате ГГГГ-ММ-ДД)
     * @param string|null $dateTo Дата, до (в формате ГГГГ-ММ-ДД)
     * @return array<string>
     */
    protected static function getFiles($dateFrom = null, $dateTo = null)
    {
        $temp = [];
        $dir = scandir(static::getLogDir());
        foreach ($dir as $f) {
            if (preg_match('/diag(\\d{4}-\\d{2}-\\d{2}).dat/i', $f, $regs)) {
                if (($d = strtotime($regs[1])) > 0) {
                    // Учитываем только валидные файлы
                    if ($dateFrom &&
                        (($fromtime = strtotime($dateFrom)) > 0)
                    ) {
                        // Только в этом случае работает дата от
                        if ($d < $fromtime) {
                            continue; // Файл датирован ранее даты от
                        }
                    }
                    if ($dateTo && (($totime = strtotime($dateTo)) > 0)) {
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


    /**
     * Получает статистику по диагностике
     * @return array<string[] Тип данных => [
     *             'long' Долгие сущности |
     *             'freq' Частые сущности |
     *             'main' Тяжелые сущности => array<[
     *                 'id' => string Внутренний ID сущности,
     *                 'key' => string Заданный ID сущности,
     *                 'counter' => int Количество обработанных сущностей,
     *                 'time' => float Общее время в секундах,
     *                 'interfaceTime' => float Общее время интерфейса
     *                                          (только для блоков)
     *                 'widgetTime' => float Общее время виджета
     *                                       (только для блоков)
     *             ]>
     *         ]>
     */
    protected function getStat()
    {
        $stat = [];
        foreach ($this->data as $entityName => $entityData) {
            if (is_numeric($entityName)) {
                continue;
                // 2019-04-29, AVS: Почему-то появляется индекс [0],
                // пока не знаю почему
            }
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


    /**
     * Очищает диагностику с заданными границами дат
     * @param string|null $dateFrom Дата, от (в формате ГГГГ-ММ-ДД)
     * @param string|null $dateTo Дата, до (в формате ГГГГ-ММ-ДД)
     */
    public static function deleteStat($dateFrom = null, $dateTo = null)
    {
        $temp = static::getFiles($dateFrom, $dateTo);
        foreach ($temp as $f) {
            unlink($f);
        }
    }


    /**
     * Возвращает объединенную диагностику с заданными границами дат
     * @param string|null $dateFrom Дата, от (в формате ГГГГ-ММ-ДД)
     * @param string|null $dateTo Дата, до (в формате ГГГГ-ММ-ДД)
     * @return Diag
     */
    public static function getMerged($dateFrom = null, $dateTo = null)
    {
        $temp = static::getFiles($dateFrom, $dateTo);
        $temp = array_map(function ($x) {
            return Diag::getInstance($x);
        }, $temp);
        $diag = static::merge($temp);
        return $diag;
    }
}
