<?php
/**
 * Таблица диагностики
 */
namespace RAAS\CMS;

use RAAS\Table;

/**
 * Класс таблицы диагностики
 * @property-read ViewSub_Dev $view Представление
 */
class DiagTable extends Table
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Dev::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $defaultParams = [
            'columns' => [
                'name' => [
                    'caption' => '',
                    'callback' => function ($row) use ($view) {
                        $rowId = $row['key'];
                        $withMaterial = false;
                        $activeMaterialSuffix = '';
                        if (stristr($row['key'], '@m')) {
                            $withMaterial = true;
                            $rowId = str_replace('@m', '', $rowId);
                            $activeMaterialSuffix = ' (' . $this->view->_('WITH_ACTIVE_MATERIAL') . ')';
                        }
                        switch ($row['type']) {
                            case 'blocks':
                                $block = Block::spawn($rowId);
                                return '<a href="' . $view->parent->url . '&action=edit_block&id=' . (int)$block->id . '">'
                                     .    htmlspecialchars($block->name)
                                     .    $activeMaterialSuffix
                                     . '</a>';
                                break;
                            case 'pages':
                                $page = new Page($rowId);
                                return '<a href="' . $view->parent->url . '&action=edit_page&id=' . (int)$page->id . '">'
                                     .    htmlspecialchars($page->name)
                                     .    $activeMaterialSuffix
                                     . '</a>';
                                break;
                            case 'snippets':
                                $snippet = new Snippet($rowId);
                                return '<a href="' . $view->parent->url . '&sub=dev&action=edit_snippet&id=' . (int)$snippet->id . '">'
                                     .    htmlspecialchars($snippet->name)
                                     .    $activeMaterialSuffix
                                     . '</a>';
                                break;
                            case 'templates':
                                $template = new Template($rowId);
                                return '<a href="' . $view->parent->url . '&sub=dev&action=edit_template&id=' . (int)$template->id . '">'
                                     .    htmlspecialchars($template->name)
                                     .    $activeMaterialSuffix
                                     . '</a>';
                                break;
                            default:
                                return htmlspecialchars($rowId)
                                    . $activeMaterialSuffix;
                                break;

                        }
                    }
                ],
                'total_time' => [
                    'caption' => $this->view->_('DIAGNOSTICS_TOTAL_TIME'),
                    'callback' => function ($row) use ($view) {
                        return number_format($row['time'], 3, '.', ' ');
                    }
                ],
                'counter' => [
                    'caption' => $this->view->_('DIAGNOSTICS_COUNTER'),
                    'callback' => function ($row) use ($view) {
                        return $row['counter'];
                    }
                ],
                'average_time' => [
                    'caption' => $this->view->_('DIAGNOSTICS_AVERAGE_TIME'),
                    'callback' => function ($row) use ($view) {
                        return number_format(
                            (float)$row['time'] / $row['counter'],
                            3,
                            '.',
                            ' '
                        );
                    }
                ],
            ],
            'callback' => function ($Row) {
                if ($Row->source['danger'] ?? false) {
                    $Row->class = 'error';
                } elseif ($Row->source['alert'] ?? false) {
                    $Row->class = 'warning';
                }
            },
            'Set' => $params['Set']
        ];
        if ($params['meta']['type'] == 'blocks') {
            $defaultParams['columns']['interfaceTime'] = [
                'caption' => $this->view->_('DIAGNOSTICS_INTERFACE_TIME'),
                'callback' => function ($row) use ($view) {
                    return number_format(
                        (float)$row['interfaceTime'] / $row['counter'],
                        3,
                        '.',
                        ' '
                    );
                }
            ];
            $defaultParams['columns']['widgetTime'] = [
                'caption' => $this->view->_('DIAGNOSTICS_WIDGET_TIME'),
                'callback' => function ($row) use ($view) {
                    return number_format(
                        (float)$row['widgetTime'] / $row['counter'],
                        3,
                        '.',
                        ' '
                    );
                }
            ];
        }
        $arr = $defaultParams;
        parent::__construct($arr);
    }
}
