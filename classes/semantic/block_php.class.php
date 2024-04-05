<?php
/**
 * PHP-блок
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Класс PHP-блока
 */
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
