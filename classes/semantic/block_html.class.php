<?php
namespace RAAS\CMS;

class Block_HTML extends Block
{
    protected static $tablename2 = 'cms_blocks_html';

    public function commit()
    {
        if (!$this->name) {
            $this->name = trim(\SOME\Text::cuttext(html_entity_decode(strip_tags($this->description), ENT_QUOTES, mb_internal_encoding()), 32, '...'));
        }
        parent::commit();
    }


    public function process(Page $Page)
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


    public function getAddData()
    {
        return array(
            'id' => (int)$this->id,
            'description' => $this->description,
            'wysiwyg' => (int)$this->wysiwyg
        );
    }
}
