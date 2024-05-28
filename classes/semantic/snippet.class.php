<?php
/**
 * Сниппет
 */
declare(strict_types=1);

namespace RAAS\CMS;

use Error;
use Exception;
use phpDocumentor\Reflection\DocBlockFactory;
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
 *                                     (как виджет, интерфейс или
 *                                     интерфейс кэширования)
 * @property-read Form[] $usingForms Формы, использующие этот сниппет
 * @property-read Field[] $usingFields Поля, использующие этот сниппет
 *                                     в качестве пре- или пост-процессора
 * @property-read \RAAS\CMS\Shop\PriceLoader[] $usingPriceloaders Загрузчики прайсов,
 *                                             использующие этот сниппет
 * @property-read \RAAS\CMS\Shop\ImageLoader[] $usingImageloaders Загрузчики изображений,
 *                                             использующие этот сниппет
 * @property-read string $filename Имя файла кэша для сохранения
 * @property-read string $name Наименование сниппета
 */
class Snippet extends SOME
{
    use ImportByURNTrait;
    use CodeTrait;

    protected static $tablename = 'cms_snippets';

    protected static $defaultOrderBy = "urn";

    protected static $cognizableVars = [
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
            case 'filename':
                // Здесь именно ...properties... , поскольку при сохранении
                // нужно удалять старый файл
                // Обращение к новому файлу идёт только в случае
                // реального commit'а
                // Шунтирование ...updates... идёт на случай, когда сниппет
                // генерируется динамически
                $filename = Package::i()->cacheDir . '/system/snippets/'
                    . (($this->properties['urn'] ?? null) ?: ($this->updates['urn'] ?? null))
                    . '.tmp.php';
                return $filename;
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
        $datetime = date('Y-m-d H:i:s');
        $uid = (int)(Application::i()->user->id ?? 0);
        if (!$this->id) {
            $this->post_date = $datetime;
            $this->author_id = $uid;
        }
        $this->modify_date = $datetime;
        $this->editor_id = $uid;
        if ($this->id &&
            ($this->updates['urn'] ?? false) &&
            ($this->properties['urn'] ?? false) &&
            ($this->updates['urn'] != $this->properties['urn'])
        ) {
            $this->deleteFile();
        }
        parent::commit();
        static::$snippetsSet = [];
        $this->saveFile();
    }


    /**
     * Отрабатывает сниппет
     * @param array $data Данные, передаваемые в сниппет
     */
    public function process(array $data = [])
    {
        if (!is_file($this->filename)) {
            $this->saveFile();
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
            $diag->handle(
                'snippets',
                $diagId,
                microtime(true) - $snippetST
            );
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
     * Возвращает наименование сниппета
     * @return string
     */
    protected function _name()
    {
        if ($description = $this->description) {
            $tokens = token_get_all($description);
            $docBlockTexts = array_values(array_filter($tokens, function ($item) {
                return $item[0] == T_DOC_COMMENT;
            }));
            if ($docBlockTexts) {
                $docBlockText = $docBlockTexts[0][1];
                $docBlockFactory  = DocBlockFactory::createInstance();
                try {
                    $docBlock = $docBlockFactory->create($docBlockText);
                    $result = $docBlock->getSummary();
                    if (trim($result)) {
                        return trim($result);
                    }
                } catch (Exception $e) {
                }
            }
        }
        if ($this->urn) {
            return $this->urn;
        }
        return '';
    }


    public static function delete(SOME $item)
    {
        $item->deleteFile();
        parent::delete($item);
    }
}
