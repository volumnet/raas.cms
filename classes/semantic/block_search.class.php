<?php
/**
 * Блок поиска
 */
namespace RAAS\CMS;

use RAAS\User as RAASUser;

/**
 * Класс блока поиска
 * @property-read array<Page> $pages Страницы, на которых размещен блок
 * @property-read array<int> $mtypes Какие ID# типов материалов задействованы
 *                                   в поиске
 * @property-read array<Material_Type> $material_types Какие типы материалов
 *                                                     задействованы в поиске
 * @property-read array<string> $languages Какие языки задействованы в поиске
 * @property-read array<Page> $search_pages Какие страницы задействованы
 *                                          в поиске
 */
class Block_Search extends Block
{
    protected static $tablename2 = 'cms_blocks_search';

    protected static $links = [
        'pages' => [
            'tablename' => 'cms_blocks_pages_assoc',
            'field_from' => 'block_id',
            'field_to' => 'page_id',
            'classname' => Page::class
        ],
        'mtypes' => [
            'tablename' => 'cms_blocks_search_material_types_assoc',
            'field_from' => 'id',
            'field_to' => 'material_type'
        ],
        'material_types' => [
            'tablename' => 'cms_blocks_search_material_types_assoc',
            'field_from' => 'id',
            'field_to' => 'material_type',
            'classname' => Material_Type::class
        ],
        'languages' => [
            'tablename' => 'cms_blocks_search_languages_assoc',
            'field_from' => 'id',
            'field_to' => 'language'
        ],
        'search_pages' => [
            'tablename' => 'cms_blocks_search_pages_assoc',
            'field_from' => 'id',
            'field_to' => 'page_id',
            'classname' => Page::class
        ],
    ];


    public function commit()
    {
        if (!$this->name) {
            $this->name = Package::i()->view->_('SITE_SEARCH');
        }
        parent::commit();
        $sqlQuery = "DELETE FROM " . self::$dbprefix . "cms_blocks_search_material_types_assoc
                      WHERE id = ?";
        self::$SQL->query([$sqlQuery, (int)$this->id]);
        $arr = [];
        if ($this->meta['mtypes'] && is_array($this->meta['mtypes'])) {
            for ($i = 0; $i < count($this->meta['mtypes']); $i++) {
                $val = $this->meta['mtypes'][$i];
                $arr[] = [
                    'id' => (int)$this->id,
                    'material_type' => (int)$val
                ];
            }
        }
        if ($arr) {
            self::$SQL->add(
                self::$dbprefix . "cms_blocks_search_material_types_assoc",
                $arr
            );
        }

        $sqlQuery = "DELETE FROM " . self::$dbprefix . "cms_blocks_search_languages_assoc
                      WHERE id = ?";
        self::$SQL->query([$sqlQuery, (int)$this->id]);
        $arr = [];
        if ($this->meta['languages'] && is_array($this->meta['languages'])) {
            for ($i = 0; $i < count($this->meta['languages']); $i++) {
                if ($val = $this->meta['languages'][$i]) {
                    $arr[] = [
                        'id' => (int)$this->id,
                        'language' => (string)$val
                    ];
                }
            }
        }
        if ($arr) {
            self::$SQL->add(
                self::$dbprefix . "cms_blocks_search_languages_assoc",
                $arr
            );
        }

        $sqlQuery = "DELETE FROM " . self::$dbprefix . "cms_blocks_search_pages_assoc
                      WHERE id = ?";
        self::$SQL->query([$sqlQuery, (int)$this->id]);
        $arr = [];
        if ($this->meta['search_pages_ids'] &&
            is_array($this->meta['search_pages_ids'])
        ) {
            for ($i = 0; $i < count($this->meta['search_pages_ids']); $i++) {
                if ($val = $this->meta['search_pages_ids'][$i]) {
                    $arr[] = [
                        'id' => (int)$this->id,
                        'page_id' => (int)$val
                    ];
                }
            }
        }
        if ($arr) {
            self::$SQL->add(
                self::$dbprefix . "cms_blocks_search_pages_assoc",
                $arr
            );
        }
    }


    /**
     * Получает дополнительные данные блока
     * @return [
     *             'id' => int ID# блока,
     *             'search_var_name' => string GET-переменная поисковой строки,
     *             'min_length' => int Минимальная длина поисковой строки,
     *             'pages_var_name' => string GET-переменная постраничной
     *                                        разбивки,
     *             'rows_per_page' => int Количество записей на страницу,
     *         ]
     */
    public function getAddData()
    {
        return [
            'id' => (int)$this->id,
            'search_var_name' => (string)$this->search_var_name,
            'min_length' => (int)$this->min_length,
            'pages_var_name' => (string)$this->pages_var_name,
            'rows_per_page' => (int)$this->rows_per_page,
        ];
    }
}
