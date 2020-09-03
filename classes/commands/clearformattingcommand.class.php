<?php
/**
 * Команда очистки HTML-форматирования
 */
namespace RAAS\CMS;

use RAAS\Command;

/**
 * Команда очистки HTML-форматирования
 */
class ClearFormattingCommand extends Command
{
    /**
     * Отрабатывает команду
     * @param string $action Действие
     * @param string $datatype Тип данных
     */
    public function process($action = '', $datatype = '')
    {
        if ($action == 'clear') {
            $ids = array_slice(func_get_args(), 2);
            if (!$ids) {
                $this->showUsage();
                return;
            }
            switch ($datatype) {
                case 'blocks':
                    $blocksData = $this->findBlocks(false);
                    $blocksData = array_filter(
                        $blocksData,
                        function ($x) use ($ids) {
                            return in_array($x['id'], $ids);
                        }
                    );
                    $blocksData = array_values($blocksData);
                    $this->clearBlocks($blocksData);
                    break;
                case 'materials':
                    $materialsData = $this->findMaterials();
                    $materialsData = array_filter(
                        $materialsData,
                        function ($x) use ($ids) {
                            return in_array($x['id'], $ids);
                        }
                    );
                    $materialsData = array_values($materialsData);
                    $this->clearMaterials($materialsData);
                    break;
                case 'material_types':
                    $pids = MaterialTypeRecursiveCache::i()->getSelfAndChildrenIds($ids);
                    $materialsData = $this->findMaterials();
                    $materialsData = array_filter(
                        $materialsData,
                        function ($x) use ($pids) {
                            return in_array($x['pid'], $pids);
                        }
                    );
                    $materialsData = array_values($materialsData);
                    $this->clearMaterials($materialsData);
                    break;
                case 'fields':
                    $fieldsData = $this->findFields(false);
                    $fieldsData = array_filter(
                        $fieldsData,
                        function ($x) use ($ids) {
                            return in_array($x['fid'], $ids);
                        }
                    );
                    $fieldsData = array_values($fieldsData);
                    $this->clearFields($fieldsData);
                    break;
                default:
                    $this->showUsage();
                    break;
            }
        } elseif ($action == 'find') {
            switch ($datatype) {
                case 'blocks':
                    $blocksData = $this->findBlocks(false);
                    foreach ($blocksData as $blockData) {
                        $logMessage = 'Found block #' . (int)$blockData['id']
                                    . ' "' . $blockData['name'] . '"';
                        $this->controller->doLog($logMessage);
                    }
                    break;
                case 'materials':
                    $materialsData = $this->findMaterials();
                    foreach ($materialsData as $materialData) {
                        $logMessage = 'Found material #' . (int)$materialData['id']
                                    . ' "' . $materialData['name'] . '"'
                                    . ' (type #' . (int)$materialData['pid']
                                    . ' "' . $materialData['material_type_name']
                                    . '")';
                        $this->controller->doLog($logMessage);
                    }
                    break;
                case 'fields':
                    $fieldsData = $this->findFields(true);
                    foreach ($fieldsData as $fieldData) {
                        $logMessage = 'Found field #' . (int)$fieldData['fid']
                                    . ' "' . $fieldData['name'] . '"';
                        $this->controller->doLog($logMessage);
                    }
                    break;
                default:
                    $this->showUsage();
                    break;
            }
        } else {
            $this->showUsage();
        }
    }


    /**
     * Отображает пример использования команды
     */
    public function showUsage()
    {
        $logMessage = 'Usage: find|clear blocks|materials|fields ids...';
        $this->controller->doLog($logMessage);
    }


    /**
     * Находит данные полей для очистки форматирования (HTML-поля)
     * @param bool $groupByFieldId Группировать по ID# поля
     * @return array
     */
    public function findFields($groupByFieldId = true)
    {
        $sqlQuery = "SELECT tD.*, tF.name AS name
                       FROM cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                      WHERE tF.datatype = 'htmlarea'
                        AND tD.value LIKE '% style=%' ";
        if ($groupByFieldId) {
            $sqlQuery .= " GROUP BY tF.id ";
        }
        $sqlQuery .= " ORDER BY tF.id";
        $sqlResult = Field::_SQL()->get($sqlQuery);
        return $sqlResult;
    }


    /**
     * Находит данные блоков для очистки форматирования (HTML-блоки)
     * @param bool $wysiwygOnly Только с визуальным редактором
     * @return array
     */
    public function findBlocks($wysiwygOnly = true)
    {
        $sqlQuery = "SELECT tBH.*, tB.name
                       FROM cms_blocks_html AS tBH
                  LEFT JOIN " . Block::_tablename() . " AS tB ON tB.id = tBH.id
                      WHERE tBH.description LIKE '% style=%' ";
        if ($wysiwygOnly) {
            $sqlQuery .= " AND wysiwyg ";
        }
        $sqlQuery .= " ORDER BY tBH.id";
        $sqlResult = Block::_SQL()->get($sqlQuery);
        return $sqlResult;
    }


    /**
     * Находит данные блоков для очистки форматирования (HTML-блоки)
     * @return array
     */
    public function findMaterials()
    {
        $sqlQuery = "SELECT tM.id, tM.pid, tM.name, tM.description, tMT.name
                         AS material_type_name
                       FROM " . Material::_tablename() . " AS tM
                       JOIN " . Material_Type::_tablename() . " AS tMT ON tMT.id = tM.pid
                      WHERE tM.description LIKE '% style=%'
                   ORDER BY tM.id";
        $sqlResult = Material::_SQL()->get($sqlQuery);
        return $sqlResult;
    }


    /**
     * Очищает форматирование в HTML-блоках
     * @param array $blocksData Данные блоков
     */
    public function clearBlocks(array $blocksData)
    {
        foreach ($blocksData as $blockData) {
            $oldDescription = $blockData['description'];
            $newDescription = $this->clearFormatting($oldDescription);
            Block::_SQL()->update(
                "cms_blocks_html",
                "id = " . (int)$blockData['id'],
                ['description' => $newDescription]
            );
            $logMessage = 'Cleared formatting in block #'
                        . (int)$blockData['id']
                        . ' "' . $blockData['name'] . '"';
            $this->controller->doLog($logMessage);
        }
    }


    /**
     * Очищает форматирование в материалах
     * @param array $materialsData Данные блоков
     */
    public function clearMaterials(array $materialsData)
    {
        foreach ($materialsData as $materialData) {
            $oldDescription = $materialData['description'];
            $newDescription = $this->clearFormatting($oldDescription);
            Material::_SQL()->update(
                Material::_tablename(),
                "id = " . (int)$materialData['id'],
                ['description' => $newDescription]
            );
            $logMessage = 'Cleared formatting in material description #'
                        . (int)$materialData['id']
                        . ' "' . $materialData['name'] . '"'
                        . ' (type #' . (int)$materialData['pid']
                        . ' "' . $materialData['material_type_name']
                        . '")';
            $this->controller->doLog($logMessage);
        }
    }


    /**
     * Очищает форматирование в материалах
     * @param array $fieldsData Данные блоков
     */
    public function clearFields(array $fieldsData)
    {
        foreach ($fieldsData as $fieldData) {
            $oldDescription = $fieldData['value'];
            $newDescription = $this->clearFormatting($oldDescription);
            $sqlQuery = "fid = " . (int)$fieldData['fid']
                      . " AND pid = " . (int)$fieldData['pid']
                      . " AND fii = " . (int)$fieldData['fii'];
            Field::_SQL()->update(
                "cms_data",
                $sqlQuery,
                ['value' => $newDescription]
            );
            $logMessage = 'Cleared formatting in field #'
                        . (int)$fieldData['fid']
                        . ' "' . $fieldData['name'] . '", object #'
                        . (int)$fieldData['pid'] . ", fii="
                        . (int)$fieldData['fii'];
            $this->controller->doLog($logMessage);
        }
    }


    /**
     * Очищает форматирование текста
     * @param string $text Входной текст
     * @return string
     */
    public function clearFormatting($text)
    {
        $newText = $text;
        if (preg_match_all('/ style=".*?"/umis', $newText, $regs)) {
            for ($i = 0; $i < count($regs[0]); $i++) {
                $newText = str_replace($regs[0][$i], '', $newText);
            }
        }
        return $newText;
    }
}
