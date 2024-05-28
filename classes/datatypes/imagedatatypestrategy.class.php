<?php
/**
 * Стратегия типа данных "Изображение"
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\DatatypeStrategy;
use RAAS\Field as RAASField;
use RAAS\ImageDatatypeStrategy as RAASImageDatatypeStrategy;

class ImageDatatypeStrategy extends RAASImageDatatypeStrategy
{
    use MediaDatatypeStrategyTrait;

    protected static $instance;
}
