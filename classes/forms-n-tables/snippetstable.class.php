<?php
namespace RAAS\CMS;

class SnippetsTable extends \RAAS\Table
{
    protected $view;

    public function __construct(array $params = array())
    {
        $this->view = $view = isset($params['view']) ? $params['view'] : null;
        unset($params['view']);
        $f = function(Snippet_Folder $node) use (&$f)
        {
            static $level = 0;
            $Set = array();
            foreach ($node->children as $row) {
                $row->level = $level;
                $Set[] = $row;
                $level++;
                $Set = array_merge($Set, $f($row));
                $level--;
            }
            foreach ($node->snippets as $row) {
                $row->level = $level;
                $Set[] = $row;
            }
            return $Set;
        };
        $defaultParams = array(
            'columns' => array(
                'name' => array(
                    'caption' => $this->view->_('NAME'), 
                    'callback' => function($row) use ($view) { 
                        if ($row->locked) {
                            return '<span style="padding-left: ' . ($row->level * 30) . 'px">' 
                                 .    htmlspecialchars($row->name) 
                                 . '</span>'; 
                        } else {
                            return '<a style="padding-left: ' . ($row->level * 30) . 'px" href="' . $view->url . '&action=' . ($row instanceof Snippet_Folder ? 'edit_snippet_folder' : 'edit_snippet') . '&id=' . (int)$row->id . '">' 
                                 .    htmlspecialchars($row->name) 
                                 . '</a>'; 
                        }
                    }
                ),
                'urn' => array('caption' => $this->view->_('URN'), 'callback' => function($row) use ($view) { return htmlspecialchars($row->urn); }),
                ' ' => array(
                    'callback' => function ($row) use ($view) { 
                        return rowContextMenu(($row instanceof Snippet_Folder) ? $view->getSnippetFolderContextMenu($row) : $view->getSnippetContextMenu($row));
                    }
                )
            ),
            'callback' => function($Row) { if ($Row->source instanceof Snippet_Folder) { $Row->class = 'info'; } },
            'emptyString' => $this->view->_('NO_SNIPPETS_FOUND'),
            'Set' => $f(new Snippet_Folder())
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}