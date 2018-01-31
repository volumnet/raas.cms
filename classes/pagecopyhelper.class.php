<?php
namespace RAAS\CMS;

use RAAS\Application;
use SOME\Text;

class PageCopyHelper
{
    /**
     * Соответствие исходных страниц и копий по ID#
     * @var array<int => int>
     */
    protected $pageMapping = array();

    /**
     * Соответствие исходных меню и копий
     * @var array<int => int>
     */
    protected $menusMapping = array();

    /**
     * Соответствие исходных блоков и копий
     * @var array<int => int>
     */
    protected $blocksMapping = array();

    /**
     * Соответствие исходных материалов и копий
     * @var array<int => int>
     */
    protected $materialsMapping = array();

    /**
     * Соответствие блоков страницам
     * @var array<int => array<int>>
     */
    protected $blocksPagesAssoc = array();

    /**
     * Список полей страниц
     * @var array<int>
     */
    protected $pageFieldsIds = array();

    /**
     * Список полей материалов
     * @var array<$materialTypeId: int => array<int>>
     */
    protected $materialsFieldsIds = array();

    /**
     * Параметры копирования наследуемых или многостраничных текстовых блоков
     *
     * '' - ничего не делать
     * 'copy' - скопировать
     * 'spread' - распространить
     * @var string
     */
    protected $textBlocksInheritedOption = '';

    /**
     * Параметры копирования одностраничных текстовых блоков
     *
     * '' - ничего не делать
     * 'copy' - скопировать
     * 'spread' - распространить
     * @var string
     */
    protected $textBlocksSingleOption = '';

    /**
     * Параметры копирования прочих блоков
     *
     * '' - ничего не делать
     * 'copy' - скопировать
     * 'spread' - распространить
     * @var string
     */
    protected $otherBlocksOption = '';

    /**
     * Параметры копирования материалов
     *
     * '' - ничего не делать
     * 'copy' - скопировать
     * 'spread' - распространить
     * 'unglob' - разглобализировать (только для глобальных)
     * @var array<string>
     */
    protected $materialsOptions = array();


    /**
     * Список ID# глобальных типов материалов
     * @var array<int>
     */
    protected $globalMaterials = array();

    /**
     * Исходная страница
     * @var Page
     */
    protected $src;

    /**
     * Скопированная страница
     * @var Page
     */
    protected $dest;

    /**
     * Конструктор класса
     * @param array<
     *      'text_blocks_inherited' => ''|'copy'|'spread',
     *      'text_blocks_single' => ''|'copy'|'spread',
     *      'other_blocks' => ''|'copy'|'spread',
     *      'materials' => array<
     *          $materialTypeID: string => ''|'copy'|'spread'|'unglob'
     *      >
     * > $params Параметры копирования
     * @param Page $src Исходная страница
     * @param Page $dest Скопированная страница
     */
    public function __construct(array $params, Page $src, Page $dest)
    {
        $SQL_query = "SELECT id FROM " . Page_Field::_tablename() . " WHERE classname = 'RAAS\\\\CMS\\\\Material_Type' AND NOT pid";
        $this->pageFieldsIds = (array)Page_Field::_SQL()->getcol($SQL_query);

        $this->textBlocksInheritedOption = $params['text_blocks_inherited'];
        $this->textBlocksSingleOption = $params['text_blocks_single'];
        $this->otherBlocksOption = $params['other_blocks'];
        foreach ((array)$params['materials'] as $materialTypeURN => $materialTypeOption) {
            $mtype = Material_Type::importByURN($materialTypeURN);
            if ($mtype->global_type) {
                $this->globalMaterials[] = (int)$mtype->id;
            }
            $this->materialsOptions[(int)$mtype->id] = $materialTypeOption;
        }
        $this->src = $src;
        $this->dest = $dest;
        $this->pageMapping[(int)$src->id] = (int)$dest->id;
    }


    /**
     * Производит "глубокое" копирование страницы с блоками и материалами
     *
     * По сути обрабатывает событие копирования
     */
    public function oncopy()
    {
        $this->unglobMaterialTypes();
        $this->copyPagesItself($this->src->id, $this->dest->id);
        $this->copyMenus();
        $this->blocksPagesAssoc = $this->getBlocksPagesAssoc(array_merge(array_keys($this->pageMapping), array_values($this->pageMapping)));
        foreach (array_values($this->pageMapping) as $pageId) {
            $this->deleteWasteBlocks($pageId);
            $this->copyNecessaryBlocks($pageId);
        }
        foreach (array_keys($this->materialsOptions) as $materialTypeID) {
            $this->copyMaterials($materialTypeID);
        }
        // Вопрос: нужно ли обрабатывать ссылки на материалы при копировании, и как это делать?... Пока не понятно
        // Вопрос: нужно ли автоматически проставлять ссылки на страницы в блоках поиска и YML?... Пока не понятно
    }


    /**
     * Разглобализирует необходимые типы материалов
     */
    protected function unglobMaterialTypes()
    {
        $newGlobalMaterials = array();
        foreach ($this->globalMaterials as $globalMaterialTypeID) {
            if (in_array($this->materialsOptions[$globalMaterialTypeID], array('copy', 'spread', 'unglob'))) {
                $this->unglobMaterialTypeByID($globalMaterialTypeID);
            } else {
                $newGlobalMaterials[] = $globalMaterialTypeID;
            }
        }
        $this->globalMaterials = $newGlobalMaterials;
    }


    /**
     * Разглобализирует тип материалов по ID#
     * @param int $materialTypeID ID# типа материала
     */
    protected function unglobMaterialTypeByID($materialTypeID)
    {
        $mtype = new Material_Type($materialTypeID);
        $mtype->global_type = 0;
        $mtype->commit();
    }


    /**
     * Рекурсивно копирует собственно страницы
     * @param int $srcId ID# исходной страница
     * @param int $descId ID# скопированной страница
     */
    protected function copyPagesItself($srcId, $destId)
    {
        $SQL_query = "SELECT id FROM " . Page::_tablename() . " WHERE pid = " . (int)$srcId;
        $childrenIds = Page::_SQL()->getcol($SQL_query);
        foreach ($childrenIds as $childSrcId) {
            $childDestId = $this->copyPageItself($childSrcId, $destId);
            $this->pageMapping[(int)$childSrcId] = (int)$childDestId;
            $this->copyPagesItself($childSrcId, $childDestId);
        }
    }


    /**
     * Копирует страницу
     * @param int $srcId ID# страницы для копирования
     * @param int $copyToId ID# родительской страницы, куда копируем
     * @return int ID# скопированной страницы
     */
    protected function copyPageItself($srcId, $copyToId)
    {

        $SQL_query = "SELECT * FROM " . Page::_tablename() . " WHERE id = " . (int)$srcId;
        $SQL_result = Page::_SQL()->getline($SQL_query);
        unset($SQL_result['id']);
        $SQL_result['pid'] = (int)$copyToId;
        $SQL_result['post_date'] = $SQL_result['modify_date'] = date('Y-m-d H:i:s');
        $SQL_result['author_id'] = $SQL_result['editor_id'] = (int)Application::i()->user->id;
        $destId = Page::_SQL()->add(Page::_tablename(), $SQL_result);

        foreach (array('cms_access' => 'page_id', 'cms_access_pages_cache' => 'page_id') as $tablename => $fieldname) {
            $SQL_query = "SELECT * FROM " . $tablename . " WHERE " . $fieldname . " = " . (int)$srcId;
            $SQL_result = (array)Page::_SQL()->get($SQL_query);
            $arr = array();
            foreach ($SQL_result as $row) {
                $row[$fieldname] = (int)$destId;
                $arr[] = $row;
            }
            Page::_SQL()->add($tablename, $arr);
        }

        if ($this->pageFieldsIds) {
            $SQL_query = "SELECT * FROM cms_data
                           WHERE fid IN (" . implode(", ", $this->pageFieldsIds) . ")
                             AND pid = " . (int)$srcId;
            $SQL_result = (array)Page::_SQL()->get($SQL_query);
            $arr = array();
            foreach ($SQL_result as $row) {
                $row['pid'] = (int)$destId;
                $arr[] = $row;
            }
            Page::_SQL()->add("cms_data", $arr);
        }
        return $destId;
    }


    /**
     * Получает ассоциацию блоков со страницами
     * @param array<int> $pagesIds Перечень ID# страниц, для которых нужно получить ассоциации
     * @return array<$blockId: int => array<$pageId: int>>
     */
    protected function getBlocksPagesAssoc(array $pagesIds)
    {
        $result = array();
        if ($pagesIds) {
            $SQL_query = "SELECT * FROM cms_blocks_pages_assoc WHERE page_id IN (" . implode(", ", $pagesIds) . ")";
            $SQL_result = Page::_SQL()->get($SQL_query);
            foreach ($SQL_result as $row) {
                $result[(int)$row['block_id']][] = (int)$row['page_id'];
            }
        }
        return $result;
    }

    /**
     * Удаляет ненужные блоки на страница
     * @param int $pageId ID# страницы
     */
    protected function deleteWasteBlocks($pageId)
    {
        $srcId = array_search($pageId, $this->pageMapping);
        $srcBlocks = $this->getPageBlocks($srcId);
        $destBlocks = $this->getPageBlocks($pageId);
        $blocksToDelete = array_diff($destBlocks, $srcBlocks);
        if ($blocksToDelete) {
            $SQL_query = "DELETE FROM cms_blocks_pages_assoc WHERE page_id = " . (int)$pageId . " AND block_id IN (" . implode(", ", $blocksToDelete) . ")";
            Page::_SQL()->query($SQL_query);
        }
    }


    /**
     * Получает блоки, которые есть на странице
     * @param int $pageID ID# страницы
     */
    protected function getPageBlocks($pageId)
    {
        $temp = array_filter($this->blocksPagesAssoc, function ($x) use ($pageId) {
            return in_array($pageId, $x);
        });
        return array_keys($temp);
    }


    /**
     * Копирует нужные блоки на страницу
     * @param int $pageId ID# страницы
     */
    protected function copyNecessaryBlocks($pageId)
    {
        $srcId = array_search($pageId, $this->pageMapping);
        $srcBlocks = $this->getPageBlocks($srcId);
        $destBlocks = $this->getPageBlocks($pageId);
        $blocksToCopy = array_diff($srcBlocks, $destBlocks);
        foreach ($blocksToCopy as $blockId) {
            $this->copyBlock($blockId, $srcId, $pageId, $mainPage);
        }
    }


    /**
     * Копирует блок (с учетом параметров копирования)
     * @param int $blockId ID# блока для копирования
     * @param int $srcId ID# страницы, с которой копируем
     * @param int $destId ID# страницы, на которую копируем
     */
    protected function copyBlock($blockId, $srcId, $destId)
    {
        $SQL_query = "SELECT * FROM " . Block::_tablename() . " WHERE id = " . (int)$blockId;
        $blockData = Block::_SQL()->getline($SQL_query);

        if ($blockData['block_type'] == 'RAAS\CMS\Block_HTML') {
            if ($blockData['inherit'] || (isset($this->blocksPagesAssoc[$blockId]) && (count($this->blocksPagesAssoc[$blockId]) > 1))) {
                $copyOption = $this->textBlocksInheritedOption;
            } else {
                $copyOption = $this->textBlocksSingleOption;
            }
        } elseif (!$this->src->pid && in_array($blockData['block_type'], array('RAAS\CMS\Block_Menu', 'RAAS\CMS\Block_Search', 'RAAS\CMS\Shop\Block_YML'))) {
            $copyOption = 'copy';
        } else {
            $copyOption = $this->otherBlocksOption;
        }
        switch ($copyOption) {
            case 'spread':
                $this->spreadBlock((int)$blockId, (int)$destId, (int)$blockId, (int)$srcId);
                break;
            case 'copy':
                if (isset($this->blocksMapping[$blockId])) {
                    $newBlockId = $this->blocksMapping[$blockId];
                } else {
                    $newBlockId = $this->copyBlockItself((int)$blockId);
                    $this->blocksMapping[$blockId] = (int)$newBlockId;
                }
                $this->spreadBlock((int)$newBlockId, (int)$destId, (int)$blockId, (int)$srcId);
                break;
        }
    }


    /**
     * Распространить блок на страницу
     * @param int $destBlockId ID# блока
     * @param int $destId ID# страницы, на которую размещаем
     * @param int $srcBlockId ID# блока, который копируем
     * @param int $srcId ID# страницы, с которой размещаем
     */
    protected function spreadBlock($destBlockId, $destId, $srcBlockId, $srcId)
    {
        $SQL_query = "SELECT * FROM cms_blocks_pages_assoc WHERE block_id = " . (int)$srcBlockId . " AND page_id = " . (int)$srcId;
        $SQL_result = Block::_SQL()->getline($SQL_query);
        $SQL_result['block_id'] = $destBlockId;
        $SQL_result['page_id'] = (int)$destId;
        Block::_SQL()->add('cms_blocks_pages_assoc', $SQL_result);
    }


    /**
     * Копирует собственно блок
     * @param int $srcId ID# копируемого блока
     * @return int ID# скопированного блока
     */
    protected function copyBlockItself($srcId)
    {
        $SQL_query = "SELECT * FROM " . Block::_tablename() . " WHERE id = " . (int)$srcId;
        $SQL_result = Block::_SQL()->getline($SQL_query);
        $classname = $SQL_result['block_type'];
        unset($SQL_result['id']);
        $SQL_result['post_date'] = $SQL_result['modify_date'] = date('Y-m-d H:i:s');
        $SQL_result['author_id'] = $SQL_result['editor_id'] = (int)Application::i()->user->id;
        $destId = Block::_SQL()->add(Block::_tablename(), $SQL_result);

        if (class_exists($classname) && ($t2 = $classname::_tablename2())) {
            $SQL_query = "SELECT * FROM " . $t2 . " WHERE id = " . (int)$srcId;
            $SQL_result = Block::_SQL()->getline($SQL_query);
            $SQL_result['id'] = (int)$destId;
            if ($classname == 'RAAS\CMS\Block_Menu') {
                if ($destMenu = $this->menusMapping[$SQL_result['menu']]) {
                    $SQL_result['menu'] = $destMenu;
                }
            }
            Block::_SQL()->add($t2, $SQL_result);
            if ($classname == 'RAAS\CMS\Block_Search') {
                foreach (array(
                    'cms_blocks_search_languages_assoc',
                    'cms_blocks_search_material_types_assoc'
                ) as $tablename) {
                    $SQL_query = "SELECT * FROM " . $tablename . " WHERE id = " . (int)$srcId;
                    $SQL_result = Block::_SQL()->get($SQL_query);
                    $arr = array();
                    foreach ($SQL_result as $row) {
                        $row['id'] = (int)$destId;
                        $arr[] = $row;
                    }
                    if ($arr) {
                        Block::_SQL()->add($tablename, $arr);
                    }
                }

                $SQL_query = "SELECT page_id FROM cms_blocks_search_pages_assoc WHERE id = " . (int)$srcId;
                $SQL_result = Block::_SQL()->getcol($SQL_query);
                $arr = array();
                foreach ($SQL_result as $p) {
                    $arr[] = array('id' => (int)$destId, 'page_id' => $this->pageMapping[$p]);
                }
                if ($arr) {
                    Block::_SQL()->add('cms_blocks_search_pages_assoc', $arr);
                }
            }

            if ($classname == 'RAAS\CMS\Shop\Block_YML') {
                foreach (array(
                    'cms_shop_blocks_yml_currencies',
                    'cms_shop_blocks_yml_fields',
                    'cms_shop_blocks_yml_ignored_fields',
                    'cms_shop_blocks_yml_material_types_assoc',
                    'cms_shop_blocks_yml_params'
                ) as $tablename) {
                    $SQL_query = "SELECT * FROM " . $tablename . " WHERE id = " . (int)$srcId;
                    $SQL_result = Block::_SQL()->get($SQL_query);
                    $arr = array();
                    foreach ($SQL_result as $row) {
                        $row['id'] = (int)$destId;
                        $arr[] = $row;
                    }
                    if ($arr) {
                        Block::_SQL()->add($tablename, $arr);
                    }
                }

                $SQL_query = "SELECT page_id FROM cms_shop_blocks_yml_pages_assoc WHERE id = " . (int)$srcId;
                $SQL_result = Block::_SQL()->getcol($SQL_query);
                $arr = array();
                foreach ($SQL_result as $p) {
                    $arr[] = array('id' => (int)$destId, 'page_id' => $this->pageMapping[$p]);
                }
                if ($arr) {
                    Block::_SQL()->add('cms_shop_blocks_yml_pages_assoc', $arr);
                }
            }
        }

        foreach (array('cms_access' => 'block_id', 'cms_access_blocks_cache' => 'block_id') as $tablename => $fieldname) {
            $SQL_query = "SELECT * FROM " . $tablename . " WHERE " . $fieldname . " = " . (int)$srcId;
            $SQL_result = (array)Block::_SQL()->get($SQL_query);
            $arr = array();
            foreach ($SQL_result as $row) {
                $row[$fieldname] = (int)$destId;
                $arr[] = $row;
            }
            Block::_SQL()->add($tablename, $arr);
        }

        return $destId;
    }


    /**
     * Копирует материалы по ID# типа
     * @param int $materialTypeID ID# типа материалов
     */
    protected function copyMaterials($materialTypeID)
    {
        $copyOption = $this->materialsOptions[$materialTypeID];
        if (in_array($materialTypeID, $this->globalMaterials)) {
            return false;
        }
        $mtype = new Material_Type($materialTypeID);
        $this->materialsFieldsIds[$materialTypeID] = array_map(function ($x) {
            return $x->id;
        }, $mtype->fields);
        $SQL_query = "SELECT DISTINCT tM.id
                        FROM " . Material::_tablename() . " AS tM ";
        if (!$isGlobal) {
            $SQL_query .= " JOIN cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id ";
        }
        $SQL_query .= " WHERE tM.pid IN (" . implode(", ", $mtype->selfAndChildrenIds) . ") ";
        if (!$isGlobal) {
            $SQL_query .= " AND tMPA.pid IN (" . implode(", ", array_keys($this->pageMapping)) . ") ";
        }
        $materialsIds = Material::_SQL()->getcol($SQL_query);

        switch ($copyOption) {
            case 'copy':
                foreach ($materialsIds as $materialId) {
                    $destId = $this->copyMaterialItself($materialId);
                    $this->materialsMapping[$materialId] = $destId;
                    $this->spreadMaterial($destId, $materialId);
                }
                break;
            case 'spread':
                foreach ($materialsIds as $materialId) {
                    $this->spreadMaterial($materialId, $materialId);
                }
                break;
        }
    }


    /**
     * Копирует материал
     * @param int $srcId ID# исходного материала
     * @return int ID# скопированного материала
     */
    protected function copyMaterialItself($srcId)
    {
        $SQL_query = "SELECT * FROM " . Material::_tablename() . " WHERE id = " . (int)$srcId;
        $SQL_result = Material::_SQL()->getline($SQL_query);
        $materialTypeID = (int)$SQL_result['pid'];
        unset($SQL_result['id']);
        $SQL_result['post_date'] = $SQL_result['modify_date'] = date('Y-m-d H:i:s');
        $SQL_result['author_id'] = $SQL_result['editor_id'] = (int)Application::i()->user->id;
        $urn = $SQL_result['urn'];
        for ($i = 0; $this->checkForSimilar($urn); $i++) {
            $urn = Application::i()->getNewURN($urn, !$i);
        }
        $SQL_result['urn'] = $urn;

        $destId = Material::_SQL()->add(Material::_tablename(), $SQL_result);

        foreach (array('cms_access' => 'material_id', 'cms_access_materials_cache' => 'material_id') as $tablename => $fieldname) {
            $SQL_query = "SELECT * FROM " . $tablename . " WHERE " . $fieldname . " = " . (int)$srcId;
            $SQL_result = (array)Material::_SQL()->get($SQL_query);
            $arr = array();
            foreach ($SQL_result as $row) {
                $row[$fieldname] = (int)$destId;
                $arr[] = $row;
            }
            Material::_SQL()->add($tablename, $arr);
        }

        if ($this->materialsFieldsIds[$materialTypeID]) {
            $SQL_query = "SELECT * FROM cms_data
                           WHERE fid IN (" . implode(", ", $this->materialsFieldsIds[$materialTypeID]) . ")
                             AND pid = " . (int)$srcId;
            $SQL_result = (array)Material::_SQL()->get($SQL_query);
            $arr = array();
            foreach ($SQL_result as $row) {
                $row['pid'] = (int)$destId;
                $arr[] = $row;
            }
            Material::_SQL()->add("cms_data", $arr);
        }
        return $destId;
    }


    /**
     * Проверяет, нет ли материала или страницы с таким URN
     * @param string $urn URN для проверки
     * @return bool true, если присутствует, false если отсутствует
     */
    protected function checkForSimilar($urn)
    {
        $SQL_query = "SELECT COUNT(id) FROM " . Material::_tablename() . " WHERE urn = ?";
        $SQL_result = Material::_SQL()->getvalue(array($SQL_query, $urn));
        if ((int)$SQL_result) {
            return true;
        }

        $SQL_query = "SELECT COUNT(id) FROM " . Page::_tablename() . " WHERE urn = ?";
        $SQL_result = Page::_SQL()->getvalue(array($SQL_query, $urn));
        if ((int)$SQL_result) {
            return true;
        }

        return false;
    }


    /**
     * Распространить материал на новые страницы
     * @param int $destId ID# материала
     * @param int $srcId ID# материала, который копируем
     */
    protected function spreadMaterial($destId, $srcId)
    {
        $SQL_query = "SELECT pid FROM cms_materials_pages_assoc WHERE id = " . (int)$srcId;
        $SQL_result = Material::_SQL()->getcol($SQL_query);
        $arr = array();
        foreach ($SQL_result as $pid) {
            if ($newPid = $this->pageMapping[$pid]) {
                $arr[] = array('id' => $destId, 'pid' => $newPid);
            }
        }
        if ($arr) {
            Material::_SQL()->add('cms_materials_pages_assoc', $arr);
        }
    }


    /**
     * Копирование всех меню
     */
    protected function copyMenus()
    {
        $affectedMenus = $this->getAffectedMenus();
        foreach ($affectedMenus as $menuId) {
            $destId = $this->copyMenuItself($menuId, null, $this->dest->name, $this->dest->urn);
        }
    }


    /**
     * Получим задействованные меню
     * @return array<int> ID# задействованных меню
     */
    protected function getAffectedMenus()
    {
        $SQL_query = "SELECT DISTINCT tBM.menu
                        FROM " . Block_Menu::_tablename2() . " AS tBM
                        JOIN cms_blocks_pages_assoc AS tBPA ON tBPA.block_id = tBM.id
                       WHERE tBPA.page_id IN (" . implode(", ", array_keys($this->pageMapping)) . ")";
        $menuIds = Block_Menu::_SQL()->getcol($SQL_query);
        return $menuIds;
    }


    /**
     * Скопируем собственно меню (рекурсивно)
     * @param int $srcId ID# меню для копирования
     * @param int $parentId ID# родительского меню
     * @param string $pageName Наименование страницы (только для корневых)
     * @param string $pageURN URN страницы (только для корневых)
     * @return int ID# скопированного меню
     */
    protected function copyMenuItself($srcId, $parentId = null, $pageName = '', $pageURN = '')
    {
        $SQL_query = "SELECT * FROM " . Menu::_tablename() . " WHERE id = " . (int)$srcId;
        $SQL_result = Menu::_SQL()->getline($SQL_query);
        unset($SQL_result['id']);
        if ($parentId !== null) {
            $SQL_result['pid'] = (int)$parentId;
        }
        if ($pageId = $this->pageMapping[$SQL_result['page_id']]) {
            $SQL_result['page_id'] = $pageId;
        }
        if (!$parentId) {
            if ($pageName) {
                if (preg_match('/\\((.*?)\\)/umi', $SQL_result['name'], $regs)) {
                    $SQL_result['name'] = preg_replace('/\\((.*?)\\)/umi', '(' . $pageName . ')', $SQL_result['name']);
                } else {
                    $SQL_result['name'] .= ' (' . $pageName . ')';
                }
            }
            if ($pageURN) {
                $pageURN = preg_split('/(\\W|\\-|_)/', $pageURN);
                $pageURN = trim($pageURN[0]);
                $pageURN = Text::beautify($pageURN);
                if (preg_match('/_([A-Za-z0-9]+)$/umi', $SQL_result['urn'], $regs)) {
                    $SQL_result['urn'] = preg_replace('/_([A-Za-z0-9]+)$/umi', '_' . $pageURN, $SQL_result['urn']);
                } else {
                    $SQL_result['urn'] .= '_' . $pageURN;
                }
            }
        }
        $destId = Menu::_SQL()->add(Menu::_tablename(), $SQL_result);
        $SQL_query = "SELECT id FROM " . Menu::_tablename() . " WHERE pid = " . (int)$srcId;
        $childrenIds = Page::_SQL()->getcol($SQL_query);
        foreach ($childrenIds as $childSrcId) {
            $childDestId = $this->copyMenuItself($childSrcId, $destId);
        }
        $this->menusMapping[$srcId] = $destId;
        return $destId;
    }
}
