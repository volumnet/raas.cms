<?php
/**
 * Поле сниппетов
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Option;

/**
 * Поле сниппетов
 */
class SnippetField extends \RAAS\Field
{
    public function __construct(array $params = [])
    {
        $ignoredSnippetFoldersURNs = (array)($params['meta']['ignoredSnippetFoldersURNs'] ?? []);
        $defaultParams = [
            'type' => 'select',
            'class' => 'input-xxlarge',
            'placeholder' => Application::i()->view->_('_NONE'),
            'children' => $this->getChildrenArr($ignoredSnippetFoldersURNs)
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает набор параметров дочерних элементов в текущей папке
     * @param string[] $ignoredSnippetFoldersURNs Список URN игнорируемых папок
     * @param ?Snippet_Folder $currentFolder Текущая папка (для рекурсии)
     * @return array
     */
    public function getChildrenArr(array $ignoredSnippetFoldersURNs = [], ?Snippet_Folder $currentFolder = null): array
    {
        if (!$currentFolder) {
            $currentFolder = new Snippet_Folder();
        }
        $result = [];
        foreach ($currentFolder->children as $childFolder) {
            if (!in_array($childFolder->urn, $ignoredSnippetFoldersURNs)) {
                $option = ['value' => '', 'caption' => $childFolder->name, 'disabled' => 'disabled'];
                if ($ch = $this->getChildrenArr($ignoredSnippetFoldersURNs, $childFolder)) {
                    $option['children'] = $ch;
                }
                $result[] = $option;
            }
        }
        foreach ($currentFolder->snippets as $snippet) {
            $caption = $snippet->urn;
            if ($snippet->name && ($snippet->name != $snippet->urn)) {
                $caption .= ': ' . $snippet->name;
            }
            $result[] = ['value' => $snippet->id, 'caption' => $caption];
        }
        return $result;
    }
}
