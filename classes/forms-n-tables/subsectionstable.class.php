<?php
/**
 * Таблица подразделов
 */
namespace RAAS\CMS;

use RAAS\Column;

/**
 * Класс таблицы подразделов
 * @property-read ViewSub_Main $view Представление
 */
class SubsectionsTable extends \RAAS\Table
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Main::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $columns = [];
        if ($params['Item']->id) {
            $i = 0;
            $columns['id'] = [
                'caption' => $this->view->_('ID'),
                'callback' => function ($row) use ($view) {
                    if ($row->locked) {
                        $text = (int)$row->id;
                    } else {
                        $text = '<a href="' . $view->url . '&id=' . (int)$row->id . '" class="' . (!$row->vis ? 'muted' : ($row->response_code ? ' text-error' : '')) . ($row->pvis ? '' : ' cms-inpvis') . '">'
                              .    (int)$row->id
                              . '</a>';
                    }
                    return $text;
                }
            ];
            foreach (array_filter(
                Page_Field::getSet(),
                function ($x) {
                    return ($x->datatype == 'image') && $x->show_in_table;
                }
            ) as $key => $col) {
                if ($i < 3) {
                    $columns[$col->urn] = [
                        'caption' => $col->name,
                        'callback' => function ($row) use (
                            $col,
                            $view,
                            $params
                        ) {
                            $f = $row->fields[$col->urn];
                            $v = $f->getValue();
                            if ($v && $v->id) {
                                return '<a href="' . $view->url . '&id=' . (int)$row->id . '" ' . (!$row->vis ? 'class="muted"' : '') . '>
                                          <img src="/' . $v->tnURL . '" style="max-width: 48px;" /></a>';
                            }
                        }
                    ];
                    $i++;
                }
            }
            $columns['name'] = [
                'caption' => $this->view->_('NAME'),
                'callback' => function ($row) use ($view) {
                    return '<a href="' . $view->url . '&id=' . (int)$row->id . '" class="' . (!$row->vis ? 'muted' : ($row->response_code ? ' text-error' : '')) . ($row->pvis ? '' : ' cms-inpvis') . '">'
                         .    htmlspecialchars($row->name)
                         . '</a>';
                }
            ];
            $columns['urn'] = [
                'caption' => $this->view->_('URN'),
                'callback' => function ($row) use ($view) {
                    $urnArr = explode(' ', $row->urn);
                    $firstURN = array_shift($urnArr);
                    $name = preg_replace('/^http(s)?:\\/\\//umi', '', $firstURN);
                    return '<a href="' . $view->url . '&id=' . (int)$row->id . '" class="' . (!$row->vis ? 'muted' : ($row->response_code ? ' text-error' : '')) . ($row->pvis ? '' : ' cms-inpvis') . '">'
                         .    htmlspecialchars($name)
                         . '</a>';
                }
            ];
            foreach (array_filter(
                Page_Field::getSet(),
                function ($x) {
                    return ($x->datatype != 'image') && $x->show_in_table;
                }
            ) as $key => $col) {
                if ($i < 3) {
                    $columns[$col->urn] = [
                        'caption' => $col->name,
                        'callback' => function ($row) use ($col, $view) {
                            $f = $row->fields[$col->urn];
                            switch ($f->datatype) {
                                case 'htmlarea':
                                    return strip_tags($f->doRich());
                                    break;
                                case 'file':
                                    $v = $f->getValue();
                                    return '<a href="/' . $view->fileURL . '" ' . (!$row->vis ? 'class="muted"' : '') . '>' .
                                              htmlspecialchars($row->name) .
                                           '</a>';
                                    break;
                                case 'material':
                                    $v = $f->getValue();
                                    $m = new Material($v);
                                    if ($m->id) {
                                        return '<a href="' . $view->url . '&action=edit_material&id=' . (int)$m->id . '" ' . (!$m->vis ? 'class="muted"' : '') . '>'
                                             .    htmlspecialchars($m->name)
                                             . '</a>';
                                    }
                                    break;
                                case 'checkbox':
                                    if ($f->multiple) {
                                        return $f->doRich();
                                    } else {
                                        if ((int)$f->getValue()) {
                                            return '<span class="icon icon-ok"></span>';
                                        }
                                    }
                                    break;
                                default:
                                    return $f->doRich();
                                    break;
                            }
                        }
                    ];
                    $i++;
                }
            }
            $columns['priority'] = [
                'caption' => $this->view->_('PRIORITY'),
                'callback' => function ($row, $i) {
                    return '<input type="number" name="page_priority[' . (int)$row->id . ']" value="' . (($i + 1) * 10) . '" class="span1" min="0" />';
                }
            ];
            $columns[' '] = [
                'callback' => function ($row, $i) use ($view, $params) {
                    return rowContextMenu($view->getPageContextMenu(
                        $row,
                        $i,
                        count($params['Set'])
                    ));
                }
            ];
        } else {
            $columns['name'] = [
                'caption' => $this->view->_('NAME'),
                'sortable' => Column::SORTABLE_REVERSABLE,
                'callback' => function ($row) use ($view) {
                    return '<a href="' . $view->url . '&id=' . (int)$row->id . '" ' . (!$row->vis ? ' class="muted"' : '') . '>'
                         .    htmlspecialchars($row->name)
                         . '</a>';
                }
            ];
            $columns['urn'] = [
                'caption' => $this->view->_('DOMAIN'),
                'sortable' => Column::SORTABLE_REVERSABLE,
                'callback' => function ($row) use ($view) {
                    $domains = explode(' ', $row->urn);
                    $firstDomain = array_shift($domains);
                    $name = preg_replace('/^http(s)?:\\/\\//umi', '', $firstDomain);
                    return '<a href="http' . (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ? 's' : '') . '://' . htmlspecialchars(preg_replace('/^http(s)?:\\/\\//umi', '', $firstDomain)) . '"' . (!$row->vis ? ' class="muted"' : '') . '>'
                         .    htmlspecialchars($name)
                         . '</a>';
                }
            ];
            $columns[' '] = [
                'callback' => function ($row, $i) use ($view, $params) {
                    return rowContextMenu($view->getPageContextMenu(
                        $row,
                        $i,
                        count($params['Set'])
                    ));
                }
            ];
        }
        $arr = $params;
        $arr['data-role'] = 'multitable';
        $arr['meta']['allContextMenu'] = $view->getAllPagesContextMenu();
        $arr['meta']['allValue'] = 'all&pid=' . (int)$params['Item']->id;
        $arr['columns'] = $columns;
        parent::__construct($arr);
        if ($params['Item']->id) {
            $this->class = 'table-condensed';
        } else {
            $this->sort = $params['sort'];
            $this->order = (strtolower($params['order']) == 'desc')
                         ? Column::SORT_DESC
                         : Column::SORT_ASC;
            $this->emptyString = $this->view->_('NO_SITES_FOUND');
        }
    }
}
