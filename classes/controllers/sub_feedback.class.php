<?php
/**
 * Подмодуль "Обратная связь"
 */
namespace RAAS\CMS;

use PhpOffice\PhpSpreadsheet\Cell\Datatype;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SOME\CSV;
use RAAS\Redirector;
use RAAS\StdSub;

/**
 * Класс подмодуля "Обратная связь"
 */
class Sub_Feedback extends \RAAS\Abstract_Sub_Controller
{
    protected static $instance;

    public function run()
    {
        switch ($this->action) {
            case 'view':
            case 'export':
                $this->{$this->action}();
                break;
            case 'chvis':
            case 'delete':
            case 'vis':
            case 'invis':
                $ids = (array)$_GET['id'];
                if (in_array('all', $ids, true)) {
                    $pids = (array)$_GET['pid'];
                    $pids = array_filter($pids, 'trim');
                    $pids = array_map('intval', $pids);
                    if ($pids) {
                        $items = Feedback::getSet([
                            'where' => "pid IN (" . implode(", ", $pids) . ")",
                            'orderBy' => "id"
                        ]);
                    }
                } else {
                    $items = array_map(function ($x) {
                        return new Feedback((int)$x);
                    }, $ids);
                }
                $items = array_values($items);
                $f = $this->action;
                StdSub::$f($items, $this->url);
                break;
            default:
                $this->feedback();
                break;
        }
    }


    /**
     * Экспорт в Excel
     */
    protected function export()
    {
        $IN = $this->model->feedback(false);
        $Set = $IN['Set'];
        $columns = $IN['columns'];
        $Item = $IN['Parent'];
        $table = new FeedbackExportTable([
            'Item' => $Item,
            'Set' => $Set,
            'columns' => $columns,
        ]);
        $data = [];
        $row = [];
        foreach ($table->columns as $col) {
            $row[] = $col->caption;
        }
        $data[] = $row;
        foreach ($Set as $item) {
            $row = [];
            foreach ($table->columns as $key => $col) {
                if ($f = $col->callback) {
                    $var = (string)$f($item);
                } else {
                    $var = $item->$key;
                }
                $row[] = $var;
            }
            $data[] = $row;
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        $filename = date('Y-m-d') . ' - ' . $table->caption;
        $type = $_GET['format'];
        switch ($type) {
            case 'xls':
            case 'xlsx':
                $filename .= '.' . $type;
                $workbook = new Spreadsheet();
                $sheet = $workbook->setActiveSheetIndex(0);
                $sheet->setTitle($table->caption);
                $maxcol = 0;
                for ($i = 0; $i < count($data); $i++) {
                    $maxcol = max($maxcol, count($data[$i]));
                    for ($j = 0; $j < count($data[$i]); $j++) {
                        $cell = $sheet->getCellByColumnAndRow($j + 1, $i + 1);
                        $cell->setValueExplicit($data[$i][$j], DataType::TYPE_STRING);
                    }
                }
                // 2024-06-03, AVS: переделал на цикл по ячейкам для совместимости со старыми версиями PhpOffice
                for ($j = 0; $j <= $maxcol; $j++) {
                    $cell = $sheet->getCellByColumnAndRow($j, 1);
                    $cell->getStyle()->getFont()->setBold(true);
                }
                // $range = [1, 1, $maxcol + 1, 1];
                // $sheet->getStyle($range)->getFont()->setBold(true);
                switch ($type) {
                    case 'xlsx':
                        $writerName = 'Xlsx';
                        $header = 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; name="'
                                . $filename
                                . '"';
                        header($header);
                        break;
                    default:
                        $writerName = 'Xls';
                        $header = 'Content-Type: application/excel; name="'
                                . $filename
                                . '"';
                        header($header);
                        break;
                }
                $objWriter = IOFactory::createWriter($workbook, $writerName);
                $tmpfile = tempnam(sys_get_temp_dir(), '');
                $objWriter->save($tmpfile);
                $text = file_get_contents($tmpfile);
                unlink($tmpfile);
                break;
            default:
                $filename .= '.csv';
                $csv = new CSV($data);
                unset($DATA);
                $text = $csv->csv;
                unset($csv);
                if ($type == 'csv1251') {
                    $text = iconv('UTF-8', 'Windows-1251//IGNORE', $text);
                }
                header('Content-Type: text/csv; name="' . $filename . '"');
                break;
        }

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $text;
        exit;
    }


    /**
     * Список сообщений
     */
    protected function feedback()
    {
        $IN = $this->model->feedback();
        $Set = $IN['Set'];
        $Pages = $IN['Pages'];
        $Item = $IN['Parent'];
        $Forms = $this->model->forms();

        $OUT['Item'] = $Item;
        $OUT['columns'] = $IN['columns'];
        $OUT['Set'] = $Set;
        $OUT['Pages'] = $Pages;
        $OUT['Forms'] = $Forms;
        $OUT['search_string'] = isset($_GET['search_string'])
                              ? (string)$_GET['search_string']
                              : '';
        $this->view->feedback($OUT);
    }


    /**
     * Просмотр сообщения
     */
    protected function view()
    {
        $Item = new Feedback($this->id);
        $Forms = $this->model->forms();
        if (!$Item->id) {
            new Redirector(\SOME\HTTP::queryString('id=&action='));
        }
        $Item->vis = (int)$this->application->user->id;
        $Item->commit();
        $OUT['Item'] = $Item;
        $OUT['Forms'] = $Forms;
        $OUT['Form'] = new ViewFeedbackForm(['Item' => $Item]);
        $this->view->view($OUT);
    }
}
