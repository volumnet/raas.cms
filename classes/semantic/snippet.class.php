<?php
/**
 * Сниппет
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс сниппета
 * @property-read Snippet_Folder $parent Папка, содержащая сниппет
 */
class Snippet extends SOME
{
    use ImportByURNTrait;

    protected static $tablename = 'cms_snippets';

    protected static $defaultOrderBy = "urn";

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Snippet_Folder::class,
            'cascade' => true
        ],
    ];

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
        $result = eval('?' . '>' . $this->description);
        if ($diag = Controller_Frontend::i()->diag) {
            $diag->handle('snippets', $this->id, microtime(true) - $st);
        }
        return $result;
    }
}
