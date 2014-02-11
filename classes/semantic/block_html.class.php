<?php
namespace RAAS\CMS;

class Block_HTML extends Block
{
    public function commit()
    {
        if (!$this->name) {
            $this->name = htmlspecialchars($this->name ? $this->name : trim(\SOME\Text::cuttext(html_entity_decode(strip_tags($this->widget), ENT_QUOTES, mb_internal_encoding()))));
        }
        parent::commit();
    }


    public function process(Page $Page)
    {
        echo $this->widget;
    }
}