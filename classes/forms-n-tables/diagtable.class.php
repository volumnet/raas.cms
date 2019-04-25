<?php
namespace RAAS\CMS;

class DiagTable extends \RAAS\Table
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
                        switch ($row['type']) {
                            case 'blocks':
                                $block = Block::spawn($row['key']);
                                return '<a href="' . $view->parent->url . '&action=edit_block&id=' . (int)$block->id . '">'
                                     .    htmlspecialchars($block->name)
                                     . '</a>';
                                break;
                            case 'pages':
                                $page = new Page($row['key']);
                                return '<a href="' . $view->parent->url . '&action=edit_page&id=' . (int)$page->id . '">'
                                     .    htmlspecialchars($page->name)
                                     . '</a>';
                                break;
                            case 'snippets':
                                $snippet = new Snippet($row['key']);
                                return '<a href="' . $view->parent->url . '&sub=dev&action=edit_snippet&id=' . (int)$snippet->id . '">'
                                     .    htmlspecialchars($snippet->name)
                                     . '</a>';
                                break;
                            default:
                                return htmlspecialchars($row['key']);
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
                if ($Row->source['danger']) {
                    $Row->class = 'error';
                } elseif ($Row->source['alert']) {
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
