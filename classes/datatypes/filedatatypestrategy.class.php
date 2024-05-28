<?php
/**
 * Стратегия типа данных "Файл"
 */
declare(strict_types=1);

namespace RAAS\CMS;

use InvalidArgumentException;
use RAAS\Attachment;
use RAAS\DatatypeStrategy;
use RAAS\FileDatatypeStrategy as RAASFileDatatypeStrategy;

class FileDatatypeStrategy extends RAASFileDatatypeStrategy
{
    use MediaDatatypeStrategyTrait;

    protected static $instance;
}
