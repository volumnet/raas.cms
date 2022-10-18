<?php
/**
 * Команда поиска и замены текста
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Command;

/**
 * Команда поиска и замены текста
 */
class TextFindReplaceCommand extends Command
{
    /**
     * Данные полей
     * @var array <pre>array<string[] ID# поля => [
     *     'id' => int ID# поля,
     *     'classname' => string Класс поля,
     *     'urn' => string URN поля,
     *     'name' => string Наименование поля
     * ]></pre>
     */
    protected $fieldsData = [];

    public function process()
    {
        $args = func_get_args();
        $action = $args[0];
        $args = array_slice($args, 1);
        switch ($action) {
            case 'find':
                if (!$args[0]) {
                    $this->showUsage();
                    return;
                }
                call_user_func_array([$this, 'find'], $args);
                break;
            case 'replace':
                if (!$args[0] || ($args[1] === null)) {
                    $this->showUsage();
                    return;
                }
                call_user_func_array([$this, 'replace'], $args);
                break;
            default:
                $this->showUsage();
                break;
        }
    }


    /**
     * Отображает пример использования команды
     */
    public function showUsage()
    {
        $logMessage = 'Usage: find "Text or regexp" $isRegexp';
        $this->controller->doLog($logMessage);
        $logMessage = 'Usage: replace "Text or regexp" "Replacement text" $isRegexp';
        $this->controller->doLog($logMessage);
    }


    /**
     * Поиск текста
     * @param string $text Искомый текст или регулярное выражение в формате MySQL
     * @param bool $isRegexp Является ли искомый текст регулярным выражением
     */
    public function find($text, $isRegexp = false)
    {
        if (!$this->fieldsData) {
            $this->getFieldsData();
        }
        $textBlocksData = $this->findTextBlocks($text, $isRegexp);
        foreach ($textBlocksData as $sqlRow) {
            $logMessage = 'Block #' . (int)$sqlRow['id']
                        . ' — ' . $sqlRow['name'];
            if ($context = $this->getContext(
                $sqlRow['description'],
                $text,
                $isRegexp
            )) {
                $logMessage .= "\n" . 'Context: "' . $context . '"';
            }
            $this->controller->doLog($logMessage);
        }
        $materialsData = $this->findMaterials($text, $isRegexp);
        foreach ($materialsData as $sqlRow) {
            $logMessage = 'Material #' . (int)$sqlRow['id']
                        . ' — ' . $sqlRow['name'];
            if ($context = $this->getContext(
                $sqlRow['description'],
                $text,
                $isRegexp
            )) {
                $logMessage .= "\n" . 'Context: "' . $context . '"';
            }
            $this->controller->doLog($logMessage);
        }
        $dataData = $this->findData($text, $isRegexp);
        foreach ($dataData as $sqlRow) {
            switch ($this->fieldsData[$sqlRow['fid']]['classname']) {
                case Page::class:
                    $classname = 'Page';
                    break;
                case Material_Type::class:
                    $classname = 'Material';
                    break;
                default:
                    $classname = $this->fieldsData[$sqlRow['fid']]['classname'];
                    break;
            }
            $logMessage = 'Data: ' . $classname . ' #' . (int)$sqlRow['pid']
                        . ' — ' . $sqlRow['name'] . ' : Field #'
                        . $sqlRow['fid']
                        . ' — ' . $this->fieldsData[$sqlRow['fid']]['urn'];
            if ($context = $this->getContext(
                $sqlRow['value'],
                $text,
                $isRegexp
            )) {
                $logMessage .= "\n" . 'Context: "' . $context . '"';
            }
            $this->controller->doLog($logMessage);
        }
    }


    public function replace($search, $replace, $isRegexp = false)
    {
        $sqlArr = [];
        $textBlocksData = $this->findTextBlocks($search, $isRegexp);
        $materialsData = $this->findMaterials($search, $isRegexp);
        $dataData = $this->findData($search, $isRegexp);
        if ($isRegexp) {
            $rx = '/' . $search . '/umis';
        }
        $sqlTemplate = "UPDATE {tablename} SET {field} = REPLACE({field}, '{from}', '{to}') WHERE {where};";
        $sqlBlocksTemplate = strtr($sqlTemplate, [
            '{tablename}' => "cms_blocks_html",
            '{field}' => "description",
            '{where}' => "id = {id}",
        ]);
        $sqlMaterialsTemplate = strtr($sqlTemplate, [
            '{tablename}' => Material::_tablename(),
            '{field}' => "description",
            '{where}' => "id = {id}",
        ]);
        $sqlDataTemplate = strtr($sqlTemplate, [
            '{tablename}' => "cms_data",
            '{field}' => "value",
            '{where}' => "pid = {pid} AND fid = {fid} AND fii = {fii}",
        ]);
        foreach ($textBlocksData as $sqlRow) {
            $sqlLocalTemplate = strtr($sqlBlocksTemplate, [
                '{id}' => (int)$sqlRow['id']
            ]);
            if ($isRegexp) {
                preg_match_all($rx, $sqlRow['description'], $regs);
                foreach ($regs[0] as $reg) {
                    $regReplace = preg_replace($rx, $replace, $reg);
                    $sqlArr[] = strtr($sqlLocalTemplate, [
                        '{from}' => $reg,
                        '{to}' => preg_replace($rx, $replace, $reg)
                    ]);
                }
            } else {
                $sqlArr[] = strtr($sqlLocalTemplate, [
                    '{from}' => $search,
                    '{to}' => $replace
                ]);
            }
        }
        foreach ($materialsData as $sqlRow) {
            $sqlLocalTemplate = strtr($sqlMaterialsTemplate, [
                '{id}' => (int)$sqlRow['id']
            ]);
            if ($isRegexp) {
                preg_match_all($rx, $sqlRow['description'], $regs);
                foreach ($regs[0] as $reg) {
                    $regReplace = preg_replace($rx, $replace, $reg);
                    $sqlArr[] = strtr($sqlLocalTemplate, [
                        '{from}' => $reg,
                        '{to}' => preg_replace($rx, $replace, $reg)
                    ]);
                }
            } else {
                $sqlArr[] = strtr($sqlLocalTemplate, [
                    '{from}' => $search,
                    '{to}' => $replace
                ]);
            }
        }
        foreach ($dataData as $sqlRow) {
            $sqlLocalTemplate = strtr($sqlDataTemplate, [
                '{pid}' => (int)$sqlRow['pid'],
                '{fid}' => (int)$sqlRow['fid'],
                '{fii}' => (int)$sqlRow['fii'],
            ]);
            if ($isRegexp) {
                preg_match_all($rx, $sqlRow['value'], $regs);
                foreach ($regs[0] as $reg) {
                    $regReplace = preg_replace($rx, $replace, $reg);
                    $sqlArr[] = strtr($sqlLocalTemplate, [
                        '{from}' => $reg,
                        '{to}' => preg_replace($rx, $replace, $reg)
                    ]);
                }
            } else {
                $sqlArr[] = strtr($sqlLocalTemplate, [
                    '{from}' => $search,
                    '{to}' => $replace
                ]);
            }
        }
        echo implode("\n", $sqlArr);
    }


    /**
     * Поиск текста по SQL-запросу
     * @param string $sqlQuery Шаблон запроса без WHERE
     * @param string $var Переменная для поиска
     * @param string $text Искомый текст или регулярное выражение в формате MySQL
     * @param bool $isRegexp Является ли искомый текст регулярным выражением
     * @return array Данные из MySQL, отфильтрованные по реальному соответствию
     */
    public function findSQL($sqlQuery, $var, $text, $isRegexp = false)
    {
        if ($isRegexp) {
            // $sqlQuery .= " WHERE " . $var . " REGEXP ?";
            // $sqlBind = [$text];
            $sqlBind = [];
        } else {
            $sqlQuery .= " WHERE " . $var . " LIKE ?";
            // var_dump($sqlQuery); exit;
            $sqlBind = ['%' . $text . '%'];
        }
        $sqlResult = Material::_SQL()->get([$sqlQuery, $sqlBind]);
        $result = [];
        $rx = '';
        if ($isRegexp) {
            $rx = '/' . $text . '/umis';
        }
        foreach ($sqlResult as $sqlRow) {
            if ($isRegexp) {
                if (preg_match($rx, $sqlRow[$var])) {
                    $result[] = $sqlRow;
                }
            } else {
                if (mb_stristr($sqlRow[$var], $text)) {
                    $result[] = $sqlRow;
                }
            }
        }
        return $result;
    }


    /**
     * Поиск текста по текстовым блокам
     * @param string $text Искомый текст или регулярное выражение в формате MySQL
     * @param bool $isRegexp Является ли искомый текст регулярным выражением
     * @return array Данные из MySQL, отфильтрованные по реальному соответствию
     */
    public function findTextBlocks($text, $isRegexp = false)
    {
        $sqlQuery = "SELECT tBH.*, tB.name
                       FROM " . Block::_tablename() . " AS tB
                       JOIN cms_blocks_html AS tBH ON tBH.id = tB.id";
        $result = $this->findSQL($sqlQuery, 'description', $text, $isRegexp);
        return $result;
    }


    /**
     * Поиск текста по материалам
     * @param string $text Искомый текст или регулярное выражение в формате MySQL
     * @param bool $isRegexp Является ли искомый текст регулярным выражением
     * @return array Данные из MySQL, отфильтрованные по реальному соответствию
     */
    public function findMaterials($text, $isRegexp = false)
    {
        $sqlQuery = "SELECT * FROM " . Material::_tablename();
        $result = $this->findSQL($sqlQuery, 'description', $text, $isRegexp);
        return $result;
    }


    /**
     * Поиск текста по данным
     * @param string $text Искомый текст или регулярное выражение в формате MySQL
     * @param bool $isRegexp Является ли искомый текст регулярным выражением
     * @return array Данные из MySQL, отфильтрованные по реальному соответствию
     */
    public function findData($text, $isRegexp = false)
    {
        if (!$this->fieldsData) {
            $this->getFieldsData();
        }
        $sqlQuery = "SELECT * FROM cms_data";
        $result = $this->findSQL($sqlQuery, 'value', $text, $isRegexp);
        $result = array_values(array_filter($result, function ($x) {
            return isset($this->fieldsData[$x['fid']]);
        }));
        return $result;
    }


    /**
     * Получает данные по полям
     */
    public function getFieldsData()
    {
        $sqlQuery = "SELECT * FROM " . Field::_tablename()
                  . " WHERE classname IN (?, ?) ";
        $sqlBind = [Page::class, Material_Type::class];
        $sqlResult = Field::_SQL()->get([$sqlQuery, $sqlBind]);
        foreach ($sqlResult as $sqlRow) {
            $this->fieldsData[trim($sqlRow['id'])] = [
                'id' => (int)$sqlRow['id'],
                'urn' => trim($sqlRow['urn']),
                'classname' => trim($sqlRow['classname']),
                'name' => trim($sqlRow['name'])
            ];
        }
    }


    /**
     * Получает отрезок текста, содержащего искомую строку
     * @param string $haystack Текст для поиска
     * @param string $needle Искомый текст
     * @param bool $isRegexp Является ли искомый текст регулярным выражением
     * @param int $length Сколько символов возвращать
     * @return string|null null, если не найдено
     */
    public function getContext($haystack, $needle, $isRegexp = false, $length = 80)
    {
        if ($isRegexp) {
            $rx = '/' . $needle . '/umis';
            if (!preg_match($rx, $haystack, $regs)) {
                return null;
            }
            $needle = $regs[0];
        }
        $pos = mb_stripos($haystack, $needle);
        $length = max($length, mb_strlen($needle));
        if ($pos === false) {
            return null;
        }
        $pos = max(0, $pos - (($length - mb_strlen($needle)) / 2));
        return mb_substr($haystack, $pos, $length);
    }
}
