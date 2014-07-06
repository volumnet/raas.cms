<?php
namespace RAAS\CMS;

class Block_HTML extends Block
{
    protected static $tablename2 = 'cms_blocks_html';

    public function commit()
    {
        if (!$this->name) {
            $this->name = htmlspecialchars($this->name ? $this->name : trim(\SOME\Text::cuttext(html_entity_decode(strip_tags($this->description), ENT_QUOTES, mb_internal_encoding()))));
        }
        parent::commit();
    }


    public function process(Page $Page)
    {
        echo $this->description;
    }


    protected function getAddData()
    {
        return array(
            'id' => (int)$this->id, 
            'description' => $this->description,
            'wysiwyg' => (int)$this->wysiwyg
        );
    }
}