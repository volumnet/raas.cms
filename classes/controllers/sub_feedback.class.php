<?php
namespace RAAS\CMS;

use \RAAS\Redirector as Redirector;
use \RAAS\Attachment as Attachment;
use \ArrayObject as ArrayObject;
use \RAAS\Field as Field;
use \RAAS\FieldSet as FieldSet;
use \RAAS\FieldContainer as FieldContainer;
use \RAAS\FormTab as FormTab;
use \RAAS\CMS\Form as CMSForm;
use \RAAS\OptGroup as OptGroup;
use \RAAS\Option as Option;
use \RAAS\StdSub as StdSub;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Cell_DataType;

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
                        $items = Feedback::getSet(array('where' => "pid IN (" . implode(", ", $pids) . ")", 'orderBy' => "id"));
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


    protected function export()
    {
        $IN = $this->model->feedback(false);
        $Set = $IN['Set'];
        $columns = $IN['columns'];
        $Item = $IN['Parent'];
        $table = new FeedbackExportTable(array(
            'Item' => $Item,
            'Set' => $Set,
            'columns' => $columns,
        ));
        $data = array();
        $row = array();
        foreach ($table->columns as $col) {
            $row[] = $col->caption;
        }
        $data[] = $row;
        foreach ($Set as $item) {
            $row = array();
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
                $x = new PHPExcel();
                $x->setActiveSheetIndex(0)->setTitle($table->caption);
                $maxcol = 0;
                for ($i = 0; $i < count($data); $i++) {
                    $maxcol = max($maxcol, count($data[$i]));
                    for ($j = 0; $j < count($data[$i]); $j++) {
                        $cell = $x->getActiveSheet()->getCellByColumnAndRow($j, $i + 1);
                        $cell->setValueExplicit($data[$i][$j], PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                }
                $range = 'A1:' . PHPExcel_Cell::stringFromColumnIndex($maxcol) . '1';
                $x->getActiveSheet()->getStyle($range)->getFont()->setBold(true);
                switch ($type) {
                    case 'xlsx':
                        $writerName = 'Excel2007';
                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; name="' . $filename . '"');
                        break;
                    default:
                        $writerName = 'Excel5';
                        header('Content-Type: application/excel; name="' . $filename . '"');
                        break;
                }
                $objWriter = PHPExcel_IOFactory::createWriter($x, $writerName);
                $temp_file = tempnam(sys_get_temp_dir(), '');
                $objWriter->save($temp_file);
                $text = file_get_contents($temp_file);
                break;
            default:
                $filename .= '.csv';
                $csv = new \SOME\CSV($data);
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
        $OUT['search_string'] = isset($_GET['search_string']) ? (string)$_GET['search_string'] : '';
        $this->view->feedback($OUT);
    }


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
        $OUT['Form'] = new ViewFeedbackForm(array('Item' => $Item));
        $this->view->view($OUT);
    }
}
