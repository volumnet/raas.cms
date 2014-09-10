<?php
namespace RAAS\CMS;

class Block_PHP extends Block
{
    public function commit()
    {
        if (!$this->name) {
            if ($this->Widget->id) {
                $this->name = $this->Widget->name;
            } elseif ($this->Interface->id) {
                $this->name = $this->Interface->name;
            }
        }
        parent::commit();
    }
}