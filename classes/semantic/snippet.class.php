<?php
/**
 * Сниппет
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс сниппета
 * @property-read Snippet_Folder $parent Папка, содержащая сниппет
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
 */
class Snippet extends SOME
{
    use ImportByURNTrait;

    protected static $tablename = 'cms_snippets';

    protected static $defaultOrderBy = "name";

    protected static $cognizableVars = [
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
    ];

    /**
     * Внутренний набор сниппетов (для проверки взаимосвязи)
     */
    protected static $snippetsSet = [];

    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);
        parent::commit();
    }


    /**
     * Отрабатывает сниппет
     * @param array $data Данные, передаваемые в сниппет
     */
    public function process(array $data = [])
    {
        $st = microtime(true);
        $DATA = $data;
        extract($data);
        $_SESSION['RAAS_EVAL_DEBUG'] = 'Snippet::' . $this->urn;
        $result = eval('?' . '>' . $this->description);
        if ($diag = Controller_Frontend::i()->diag) {
            $diag->handle('snippets', $this->id, microtime(true) - $st);
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
            if (stristr($snippet->description, 'Snippet::importByURN("' . $this->urn . '")') ||
                stristr($snippet->description, "Snippet::importByURN('" . $this->urn . "')") ||
                stristr($snippet->description, 'new Snippet($this->id)')
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
        $snippetsIds = [(int)$this->id];
        foreach ($this->usingSnippets as $snippet) {
            $snippetsIds[] = (int)$snippet->id;
        }
        if (!$snippetsIds) {
            return [];
        }
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
}
