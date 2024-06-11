<?php
/**
 * Блок формы
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\User as RAASUser;

/**
 * Класс блока формы
 * @property-read RAASUser $author Автор блока
 * @property-read RAASUser $editor Редактор блока
 * @property-read Form $Form Форма, привязанная к блоку
 */
class Block_Form extends Block
{
    const ALLOWED_INTERFACE_CLASSNAME = FormInterface::class;

    protected static $tablename2 = 'cms_blocks_form';

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
        'Form' => [
            'FK' => 'form',
            'classname' => Form::class,
            'cascade' => false
        ],
    ];

    public function commit()
    {
        if (!$this->name && $this->Form->id) {
            $this->name = $this->Form->name;
        }
        parent::commit();
    }


    /**
     * Получает дополнительные данные блока
     * @return [
     *             'id' => int ID# блока,
     *             'form' => int ID# формы,
     *         ]
     */
    public function getAddData(): array
    {
        return [
            'id' => (int)$this->id,
            'form' => (int)$this->form,
        ];
    }
}
