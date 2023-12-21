<?php
/**
 * Стратегия типа данных "Изображение"
 */
namespace RAAS\CMS;

use RAAS\DatatypeStrategy;
use RAAS\Field as RAASField;
use RAAS\ImageDatatypeStrategy as RAASImageDatatypeStrategy;

class ImageDatatypeStrategy extends RAASImageDatatypeStrategy
{
    use MediaDatatypeStrategyTrait;

    protected static $instance;
}
