<?php
/**
 * Сниппет
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Application;
use RAAS\User as RAASUser;

/**
 * Класс сниппета
 * @property-read Snippet_Folder $parent Папка, содержащая сниппет
 * @property-read RAASUser $author Автор страницы
 * @property-read RAASUser $editor Редактор страницы
 * @property-read Snippet[] $usingSnippets Сниппеты, использующие этот сниппет
 * @property-read Block[] $usingBlocks Блоки, использующие этот сниппет
 *     (как виджет, интерфейс или интерфейс кэширования)
 * @property-read Form[] $usingForms Формы, использующие этот сниппет
 * @property-read Field[] $usingFields Поля, использующие этот сниппет в качестве пре- или пост-процессора
 * @property-read \RAAS\CMS\Shop\PriceLoader[] $usingPriceloaders Загрузчики прайсов, использующие этот сниппет
 * @property-read \RAAS\CMS\Shop\ImageLoader[] $usingImageloaders Загрузчики изображений, использующие этот сниппет
 * @property-read string|null $lockedFilename Имя символьной ссылки заблокированного сниппета
 * @property-read string|null $filename Имя актуального файла
 * @property-read string|null $oldFilename Имя старого файла
 * @property-read string $post_date Дата создания файла
 * @property-read string $modify_date Дата обновления файла
 * @property-read string $description Код сниппета
 * @property-read string $name Наименование сниппета
 */
class Snippet extends SOME
{
    use ImportByURNTrait;
    use CodeTrait;

    protected static $tablename = 'cms_snippets';

    protected static $defaultOrderBy = "urn";

    protected static $cognizableVars = [
        'description',
        'name',
        'usingSnippets',
        'usingBlocks',
        'usingForms',
        'usingFields',
        'usingPriceloaders',
        'usingImageloaders',
    ];

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Snippet_Folder::class,
            'cascade' => true
        ],
        'author' => [
            'FK' => 'author_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
        'editor' => [
            'FK' => 'editor_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
    ];

    /**
     * Внутренний набор сниппетов (для проверки взаимосвязи)
     */
    protected static $snippetsSet = [];

    public function __get($var)
    {
        switch ($var) {
            case 'lockedFilename':
                if (!$this->locked) {
                    return null;
                }
                $nameArr = explode('/', trim((string)$this->locked, '/'));
                if (count($nameArr) > 1) {
                    $module = Package::i()->modules[$nameArr[0]] ?? null;
                    if (!$module) {
                        return null;
                    }
                    $result = $module->resourcesDir . '/interfaces/' . $nameArr[1];
                    return $result;
                } else {
                    $result = Package::i()->resourcesDir . '/interfaces/' . $nameArr[0];
                    return $result;
                }
                break;
            case 'filename':
                if ($this->locked) {
                    return $this->lockedFilename;
                }
                if (!($this->properties['urn'] ?? null) && !($this->updates['urn'] ?? null)) {
                    return null;
                }
                $filename = (($this->updates['urn'] ?? null) ?: ($this->properties['urn'] ?? null));
                $filepath = static::getDirName() . '/' . $filename . '.tmp.php';
                return $filepath;
                break;
            case 'oldFilename':
                if ($this->locked) {
                    return $this->lockedFilename;
                }
                if (!($this->properties['urn'] ?? null) && !($this->updates['urn'] ?? null)) {
                    return null;
                }
                $filename = (($this->properties['urn'] ?? null) ?: ($this->updates['urn'] ?? null));
                $filepath = static::getDirName() . '/' . $filename . '.tmp.php';
                return $filepath;
                break;
            case 'post_date':
                if (!$this->filename || !is_file($this->filename)) {
                    return '0000-00-00 00:00:00';
                }
                return date('Y-m-d H:i:s', filectime($this->filename));
                break;
            case 'modify_date':
                if (!$this->filename || !is_file($this->filename)) {
                    return '0000-00-00 00:00:00';
                }
                return date('Y-m-d H:i:s', filemtime($this->filename));
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);

        $uid = (int)(Application::i()->user->id ?? 0);
        if (!$this->id) {
            $this->author_id = $uid;
        }
        $this->editor_id = $uid;

        if (!$this->locked) {
            $oldFilename = $this->oldFilename;
            $filename = $this->filename;
            if (!$this->id) {
                // Если новый
                if (trim((string)$this->description)) {
                    // Если есть описание
                    $this->saveFile();
                } elseif ($this->filename) {
                    // Если нет описания
                    touch($this->filename);
                }
            } else {
                // Существующий
                if ($oldFilename &&
                    $filename &&
                    ($oldFilename != $filename) &&
                    is_file($oldFilename) &&
                    !is_file($filename)
                ) {
                    // Если переименовываем
                    rename($this->oldFilename, $this->filename);
                }
                $this->saveFile();
            }
        }

        parent::commit();
        static::$snippetsSet = [];
    }


    /**
     * Отрабатывает сниппет
     * @param array $data Данные, передаваемые в сниппет
     */
    public function process(array $data = [])
    {
        if (!$this->filename || !is_file($this->filename)) {
            return;
        }
        $snippetST = microtime(true);
        // 2020-12-25, убрано - в формах вместо POST-данных
        // (в отсутствие собственно POST-запроса) подставляются все параметры
        // $DATA = $data;
        extract($data);
        $result = @include $this->filename;
        if ($diag = Controller_Frontend::i()->diag) {
            $diagId = $this->id;
            $block = ($data['Block'] ?? null);
            $page = ($data['Page'] ?? null);
            $pageWithMaterial = false;
            if (($page instanceof Page)) {
                $material = $page->Material;
                $item = $page->Item;
                if (($material && $material->id) || ($item && $item->id)) {
                    $pageWithMaterial = true;
                }
            }
            if (($block instanceof Block_Material) && $block->nat && $pageWithMaterial) {
                $diagId .= '@m';
            }
            $diag->handle('snippets', $diagId, microtime(true) - $snippetST);
        }
        return $result;
    }


    /**
     * Сниппеты, использующие этот сниппет
     * @return Form[]
     */
    protected function _usingSnippets()
    {
        if (!static::$snippetsSet) {
            static::$snippetsSet = Snippet::getSet();
        }
        $result = [];
        foreach (static::$snippetsSet as $snippet) {
            if (stristr((string)$snippet->description, 'Snippet::importByURN("' . $this->urn . '")') ||
                stristr((string)$snippet->description, "Snippet::importByURN('" . $this->urn . "')") ||
                stristr((string)$snippet->description, 'new Snippet($this->id)')
            ) {
                $result[] = $snippet;
            }
        }
        return $result;
    }


    /**
     * Блоки, использующие этот сниппет (как виджет, интерфейс или
     * интерфейс кэширования)
     * @return Block[]
     */
    protected function _usingBlocks()
    {
        $sqlQuery = "SELECT id
                       FROM " . Block::_tablename()
                  . " WHERE interface_id = " . (int)$this->id
                  . "    OR widget_id = " . (int)$this->id
                  . "    OR cache_interface_id = " . (int)$this->id
                  . " ORDER BY " . Block::_idN();
        $sqlResult = Block::_SQL()->getcol($sqlQuery);
        $result = [];
        foreach ($sqlResult as $sqlVal) {
            $result[] = Block::spawn($sqlVal);
        }
        return $result;
    }


    /**
     * Формы, использующие этот сниппет
     * @return Form[]
     */
    protected function _usingForms()
    {
        $result = Form::getSet([
            'where' => "interface_id = " . (int)$this->id,
        ]);
        return $result;
    }


    /**
     * Формы, использующие этот сниппет
     * @return Form[]
     */
    protected function _usingFields()
    {
        $result = Field::getSet([
            'where' => "preprocessor_id = " . (int)$this->id
                    .  " OR postprocessor_id = " . (int)$this->id,
        ]);
        return $result;
    }


    /**
     * Загрузчики прайсов, использующие этот сниппет
     * @return \RAAS\CMS\Shop\PriceLoader[]
     */
    protected function _usingPriceloaders()
    {
        $classname = 'RAAS\\CMS\\Shop\\PriceLoader';
        $result = [];
        if (class_exists($classname)) {
            $result = $classname::getSet([
                'where' => "interface_id = " . (int)$this->id,
            ]);
        }
        return $result;
    }


    /**
     * Загрузчики прайсов, использующие этот сниппет
     * @return \RAAS\CMS\Shop\ImageLoader[]
     */
    protected function _usingImageloaders()
    {
        $classname = 'RAAS\\CMS\\Shop\\ImageLoader';
        $result = [];
        if (class_exists($classname)) {
            $result = $classname::getSet([
                'where' => "interface_id = " . (int)$this->id,
            ]);
        }
        return $result;
    }


    /**
     * Проверяет файлы на наличие новых сниппетов и добавляет их при необходимости
     */
    public static function checkSnippets()
    {
        $interfacesFolder = $widgetsFolder = null;
        $glob = glob(static::getDirName() . '/*.tmp.php');
        $sqlQuery = "SELECT urn FROM " . static::_tablename();
        $sqlResult = static::_SQL()->getcol($sqlQuery);
        $urns = [];
        foreach ($sqlResult as $urn) {
            $urns[$urn] = $urn;
        }
        foreach ($glob as $filename) {
            $urn = pathinfo($filename, PATHINFO_FILENAME);
            $urn = explode('.', $urn)[0];
            if (isset($urns[$urn])) {
                continue;
            }
            if (preg_match('/^template\\d+$/umis', $urn)) { // Шаблоны отсекаем
                continue;
            }
            if (stristr($urn, 'interface')) {
                if (!$interfacesFolder) {
                    $interfacesFolder = Snippet_Folder::importByURN('__raas_interfaces');
                }
                $folder = $interfacesFolder;
            } else {
                if (!$widgetsFolder) {
                    $widgetsFolder = Snippet_Folder::importByURN('__raas_views');
                }
                $folder = $widgetsFolder;
            }
            $snippet = new Snippet([
                'urn' => $urn,
                'pid' => ($folder && $folder->id) ? (int)$folder->id : 0,
            ]);
            $snippet->commit();
        }
    }


    public static function delete(SOME $item)
    {
        if (!$item->locked) {
            $item->deleteFile();
        }
        parent::delete($item);
    }
}
