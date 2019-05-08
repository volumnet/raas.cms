<?php
/**
 * HTML-блок
 */
namespace RAAS\CMS;

use SOME\Text;

/**
 * Класс HTML-блока
 */
class Block_HTML extends Block
{
    protected static $tablename2 = 'cms_blocks_html';

    public function commit()
    {
        if (!$this->name) {
            $this->name = trim(Text::cuttext(
                html_entity_decode(
                    strip_tags($this->description),
                    ENT_QUOTES,
                    mb_internal_encoding()
                ),
                32,
                '...'
            ));
        }
        parent::commit();
    }


    public function process(Page $Page, $nocache = false)
    {
        if (!$this->currentUserHasAccess()) {
            return null;
        }
        if ($this->Interface->id || $this->Widget->id) {
            return parent::process($Page);
        } else {
            echo $this->description;
        }
    }


    /**
     * Получает дополнительные данные блока
     * @return [
     *             'id' => int ID# блока,
     *             'description' => string текст блока,
     *             'wysiwyg' => 0|1 включен ли визуальный редактор,
     *         ]
     */
    public function getAddData()
    {
        return [
            'id' => (int)$this->id,
            'description' => $this->description,
            'wysiwyg' => (int)$this->wysiwyg
        ];
    }
}
