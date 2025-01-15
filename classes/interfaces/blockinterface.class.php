<?php
/**
 * Интерфейс блока
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Интерфейс блока
 */
abstract class BlockInterface extends AbstractInterface
{
    public function __construct(
        ?Block $block = null,
        ?Page $page = null,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        parent::__construct($block, $page, $get, $post, $cookie, $session, $server, $files);
    }

    /**
     * Выполнить интерфейс
     * @return array Выходные данные для виджета
     */
    abstract public function process(): array;
}
