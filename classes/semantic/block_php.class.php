<?php
/**
 * PHP-блок
 */
declare(strict_types=1);

namespace RAAS\CMS;

use Exception;
use ReflectionClass;
use phpDocumentor\Reflection\DocBlockFactory;

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
            } elseif ($this->interface_classname) {
                try {
                    $reflectionClass = new ReflectionClass($this->interface_classname);
                    $docBlockFactory  = DocBlockFactory::createInstance();
                    $docBlock = $docBlockFactory->create($reflectionClass->getDocComment());
                    $caption = $docBlock->getSummary();
                } catch (Exception $e) {
                    $caption = $this->interface_classname;
                }
                $this->name = $caption;
            } elseif ($this->Interface->id) {
                $this->name = $this->Interface->name;
            }
        }
        parent::commit();
    }
}
