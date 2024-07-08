<?php
/**
 * Таблица сниппетов
 */
namespace RAAS\CMS;

use RAAS\Table;
use RAAS\Row;

/**
 * Класс таблицы сниппетов
 * @property-read ViewSub_Dev $view Представление
 */
class SnippetsTable extends Table
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
                'id' => [
                    'caption' => $this->view->_('ID'),
                    'callback' => function ($row) use ($view) {
                        if ($row->locked) {
                            $text = (int)$row->id;
                        } else {
                            $text = '<a href="' . $this->getEditURL($row) . '">'
                                  .    (int)$row->id
                                  . '</a>';
                        }
                        return $text;
                    }
                ],
                'name' => [
                    'caption' => $this->view->_('NAME'),
                    'callback' => function ($row) use ($view) {
                        if ($row->locked) {
                            $text = '<span style="padding-left: ' . ($row->level * 30) . 'px">'
                                  .    htmlspecialchars($row->name)
                                  . '</span>';
                        } else {
                            $text = '<a style="padding-left: ' . ($row->level * 30) . 'px" href="' . $this->getEditURL($row) . '">'
                                  .    htmlspecialchars($row->name)
                                  . '</a>';
                        }
                        return $text;
                    }
                ],
                'urn' => [
                    'caption' => $this->view->_('URN'),
                    'callback' => function ($row) use ($view) {
                        return htmlspecialchars($row->urn);
                    }
                ],
                'usage' => [
                    'caption' => $this->view->_('USAGE'),
                    'callback' => function ($row) {
                        if ($row instanceof Snippet) {
                            $textArr = [];
                            $sum = 0;
                            if ($c = count($row->usingBlocks)) {
                                $sum += $c;
                                $textArr[] = $this->view->_('BLOCKS') . ': ' . $c;
                            }
                            if ($c = count($row->usingForms)) {
                                $sum += $c;
                                $textArr[] = $this->view->_('FORMS') . ': ' . $c;
                            }
                            if ($c = count($row->usingSnippets)) {
                                $sum += $c;
                                $textArr[] = $this->view->_('SNIPPETS') . ': ' . $c;
                            }
                            if ($c = count($row->usingFields)) {
                                $sum += $c;
                                $textArr[] = $this->view->_('FIELDS') . ': ' . $c;
                            }
                            if ($c = count($row->usingPriceloaders)) {
                                $sum += $c;
                                $textArr[] = $this->view->_('PRICELOADERS') . ': ' . $c;
                            }
                            if ($textArr) {
                                return '<span title="' . htmlspecialchars(implode('; ', $textArr)) . '">' .
                                          $sum .
                                       '</span>';
                            }
                        }
                        return '';
                    },
                ],
                ' ' => [
                    'callback' => function ($row) use ($view) {
                        return rowContextMenu(
                            ($row instanceof Snippet_Folder) ?
                            $view->getSnippetFolderContextMenu($row) :
                            $view->getSnippetContextMenu($row)
                        );
                    }
                ],
            ],
            'callback' => function (Row $tableRow) {
                if ($tableRow->source instanceof Snippet_Folder) {
                    $tableRow->class = 'info';
                }
                if (($tableRow->source instanceof Snippet_Folder) ||
                    $tableRow->source->locked
                ) {
                    $tableRow->disableMulti = true;
                }
            },
            'emptyString' => $this->view->_('NO_SNIPPETS_FOUND'),
            'Set' => $this->buildSnippetTree(new Snippet_Folder(), 0),
            'template' => 'cms/multitable.tmp.php',
            'data-role' => 'multitable',
            'meta' => [
                'allContextMenu' => $this->view->getAllSnippetsContextMenu(),
            ],
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Построить дерево сниппетов и папок
     * @param Snippet_Folder $node Текущий узел
     * @param int $level Уровень вложенности
     * @return array<Snippet|Snippet_Folder> Массив сниппетов и папок
     *                                       с указанием параметра $level -
     *                                       уровень вложенности
     */
    public function buildSnippetTree(Snippet_Folder $node, $level = 0)
    {
        $set = [];
        foreach ($node->children as $row) {
            $row->level = $level;
            $set[] = $row;
            $level++;
            $set = array_merge($set, $this->buildSnippetTree($row, $level));
            $level--;
        }
        foreach ($node->snippets as $row) {
            $row->level = $level;
            $set[] = $row;
        }
        return $set;
    }


    /**
     * Получает URL редактирования сниппета или папки
     * @param Snippet|Snippet_Folder $row Папка или сниппет для редактирования
     * @return string
     */
    public function getEditURL($row)
    {
        $url = $this->view->url . '&action=edit_snippet'
             . (
                    $row instanceof Snippet_Folder ?
                    '_folder' :
                    ''
               )
             . '&id=' . (int)$row->id;
        return $url;
    }
}
