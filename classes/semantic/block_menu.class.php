<?php
/**
 * Блок меню
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\User as RAASUser;

/**
 * Класс блока меню
 * @property-read RAASUser $author Автор блока
 * @property-read RAASUser $editor Редактор блока
 * @property-read Menu $Menu Меню, привязанное к блоку
 */
class Block_Menu extends Block
{
    protected static $tablename2 = 'cms_blocks_menu';

    protected static $references = [
        'author' => [
            'FK' => 'author_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
        'editor' => [
            'FK' => 'editor_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
        'Menu' => [
            'FK' => 'menu',
            'classname' => Menu::class,
            'cascade' => false
        ],
    ];

    public function commit()
    {
        if (!$this->name && $this->Menu->id) {
            $this->name = $this->Menu->name;
        }
        parent::commit();
    }


    /**
     * Получает дополнительные данные блока
     * @return [
     *             'id' => int ID# блока,
     *             'menu' => int ID# меню,
     *             'full_menu' => 0|1 полное меню (либо только подразделы)
     *         ]
     */
    public function getAddData(): array
    {
        return [
            'id' => (int)$this->id,
            'menu' => (int)$this->menu,
            'full_menu' => (int)$this->full_menu,
        ];
    }
}
