<?php
/**
 * Поле виджетов
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Поле виджетов
 */
class WidgetField extends SnippetField
{
    public function __construct(array $params = [])
    {
        $arr = $params;
        $arr['meta']['ignoredSnippetFoldersURNs'] = ['__raas_interfaces']; // Здесь, чтобы не было перекрытия
        parent::__construct($arr);
    }
}
