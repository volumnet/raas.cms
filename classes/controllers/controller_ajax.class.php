<?php
/**
 * Контроллер AJAX
 */
namespace RAAS\CMS;

use SOME\Text;
use RAAS\Controller_Frontend;

/**
 * Класс контроллера AJAX
 */
class Controller_Ajax extends Abstract_Controller
{
    protected static $instance;

    protected function execute()
    {
        switch ($this->action) {
            case 'material_fields':
            case 'get_materials_by_field':
            case 'rebuild_page_cache':
            case 'clear_cache':
            case 'clear_blocks_cache':
            case 'get_cache_map':
                $this->{$this->action}();
                break;
            case 'get_menu_domain_pages':
                $this->getMenuDomainPages();
                break;
            case 'debug_page':
                $this->debugPage();
                break;
            case 'pages_menu':
                $this->getPagesMenu();
                break;
        }
    }


    /**
     * Получает меню страниц, начиная от текущей
     */
    public function getPagesMenu()
    {
        $rootId = (int)($this->nav['id'] ?? 0);
        $result = ViewSub_Main::i()->pagesMenu($rootId);
        $out = [
            'menu' => $result,
        ];
        $this->view->get_cache_map($out);
    }


    /**
     * Получает карту необходимых кэшей
     */
    protected function get_cache_map()
    {
        $OUT['Set'] = array_values($this->model->getCacheMap());
        $this->view->get_cache_map($OUT);
    }


    /**
     * Очищает кэши страниц
     */
    protected function clear_cache()
    {
        $this->model->clearCache(true);
        $this->view->clear_cache([]);
    }


    /**
     * Очищает кэши блоков
     */
    protected function clear_blocks_cache()
    {
        $this->model->clearBlocksCache(true);
        $this->view->clear_blocks_cache([]);
    }


    /**
     * Перестраивает кэши страниц
     */
    protected function rebuild_page_cache()
    {
        $Page = new Page($this->nav['id']);
        $Material = isset($this->nav['mid'])
                  ? new Material($this->nav['mid'])
                  : null;
        if ($Material->id) {
            $Page->Material = $Material;
        }
        $Page->rebuildCache();
        $this->view->rebuild_page_cache([]);
    }


    /**
     * Отображает поля типов материалов
     * @return ['Set' => array<[
     *             'id' => string|int URN нативного поля или ID# кастомного,
     *             'name' => string Наименование поля
     *         ]>]
     */
    protected function material_fields()
    {
        $Material_Type = new Material_Type((int)$this->id);
        $Set = [
            (object)[
                'id' => 'name',
                'name' => $this->view->_('NAME')
            ],
            (object)[
                'id' => 'urn',
                'name' => $this->view->_('URN')
            ],
            (object)[
                'id' => 'description',
                'name' => $this->view->_('DESCRIPTION')
            ],
            (object)[
                'id' => 'post_date',
                'name' => $this->view->_('CREATED_BY')
            ],
            (object)[
                'id' => 'modify_date',
                'name' => $this->view->_('EDITED_BY')
            ],
        ];
        $Set = array_merge(
            $Set,
            array_values(array_filter($Material_Type->fields, function ($x) {
                return !(
                    // 2022-12-19, AVS: (2019-07-30, AVS:) убрали проверку на единичность полей,
                    // т.к. фильтр может быть и по множественному полю
                    // $x->multiple ||
                    in_array($x->datatype, ['file', 'image'])
                );
            }))
        );
        $Set[] = (object)[
            'id' => 'random',
            'name' => $this->view->_('RANDOM')
        ];
        $OUT['Set'] = array_map(
            function ($x) {
                return ['val' => $x->id, 'text' => $x->name];
            },
            $Set
        );
        $this->view->show_page($OUT);
    }


    /**
     * Получает материалы по поиску
     * @return ['Set' => array<[
     *             'id' => int ID# материала,
     *             'name' => string Наименование материала,
     *             'description' => string Краткое описание материала,
     *             'pid' =>? int ID# первой родительской страницы для материала,
     *             'img' =>? string URL картинки материала
     *         ]>]
     */
    protected function get_materials_by_field()
    {
        if ((int)$this->id) {
            $Field = new Material_Field((int)$this->id);
            $Set = [];
            if ($Field->datatype == 'material') {
                $mtype = (int)$Field->source;
            }
        } elseif ((int)$this->nav['mtype']) {
            $mtype = (int)$this->nav['mtype'];
        }
        $onlyByName = false;
        if ((int)$this->nav['only_by_name']) {
            $onlyByName = true;
        }
        $Set = $this->model->getMaterialsBySearch(
            (
                isset($_GET['search_string']) ?
                $_GET['search_string'] :
                ''
            ),
            $mtype,
            10,
            $onlyByName
        );
        $OUT['Set'] = array_map(
            function ($x) {
                $y = [
                    'id' => (int)$x->id,
                    'name' => $x->name,
                    'description' => Text::cuttext(
                        html_entity_decode(
                            strip_tags($x->description),
                            ENT_COMPAT | ENT_HTML5,
                            'UTF-8'
                        ),
                        256,
                        '...'
                    )
                ];
                if ($x->parents) {
                    $y['pid'] = (int)$x->parents_ids[0];
                }
                foreach ($x->fields as $row) {
                    if ($row->datatype == 'image') {
                        if ($val = $row->getValue()) {
                            if ($val->id) {
                                $y['img'] = '/' . $val->fileURL;
                            }
                        }
                    }
                }
                return $y;
            },
            $Set
        );
        $this->view->show_page($OUT);
    }


    /**
     * Получает список страниц домена для меню
     * @return ['Set' => array<[
     *             'val' => int ID# страницы,
     *             'text' => string Наименование страницы
     *             'src' => string URL страницы
     *         ]>]
     */
    protected function getMenuDomainPages()
    {
        $domainId = (int)$_GET['domain_id'];
        $pages = $this->getPages(null, $domainId);
        $this->view->show_page(['Set' => $pages]);
    }


    /**
     * Получает список страниц домена для меню
     * @param int|null $pid ID# родительской страницы или null,
     *                      если нужно отобразить корневую страницу
     * @param int|null $domainId ID# домена (только для фильтрации
     *                               корневых страниц)
     * @param int $level Уровень вложенности
     * @return array<[
     *             'val' => int ID# страницы,
     *             'text' => string Наименование страницы
     *             'src' => string URL страницы
     *         ]>
     */
    public function getPages($pid = null, $domainId = null, $level = 0)
    {
        $pageCache = PageRecursiveCache::i();
        $result = [];
        if ($pid === null) {
            $result = array_merge([[
                'val' => '',
                'text' => '--',
            ]], $this->getPages(0, $domainId, $level + 1));
        } else {
            if (!$pid && $domainId) {
                $pagesIds = [$domainId];
            } else {
                $pagesIds = $pageCache->getChildrenIds($pid);
            }
            $pagesData = [];
            foreach ($pagesIds as $pageId) {
                $pageData = $pageCache->cache[$pageId];
                $pagesData[] = [
                    'val' => (int)$pageData['id'],
                    'text' => (str_repeat('&nbsp;', 3 * $level))
                           .  ($pageData['menu_name'] ?: $pageData['name']),
                    'src' => $pageData['cache_url'],
                ];
            }
            foreach ($pagesData as $pageData) {
                $result = array_merge(
                    $result,
                    [$pageData],
                    $this->getPages((int)$pageData['val'], null, $level + 1)
                );
            }
        }
        return $result;
    }


    /**
     * Возвращает скрипт вывода отладочной информации в консоль
     */
    public function debugPage()
    {
        header('Content-Type: text/javascript');
        if (!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']) {
            exit;
        }
        $url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
        $url = str_replace('\\', '/', $url);
        $host = Controller_Frontend::i()->scheme . '://' .
                Controller_Frontend::i()->host;
        $page = Page::importByURL($host . $url);
        $page->initialURL = $url;
        $additionalURLArray = $page->additionalURLArray;
        $material = Material::importByURN(isset($additionalURLArray[0]) ? $additionalURLArray[0] : null);
        if ($page->id) {
            echo "console.log('Страница ID# " . (int)$page->id . " " . $page->name . "');";
            echo "console.log('" . $host . addslashes($page->url) . "');";
            echo "console.log('" . $host . "/admin/?p=cms&id=" . (int)$page->id . "');";
        }
        if ($material && $material->id) {
            echo "console.log('Материал ID# " . (int)$material->id . " " . $material->name . "');";
            echo "console.log('" . $host . addslashes($material->url) . "');";
            echo "console.log('" . $host . "/admin/?p=cms&action=edit_material&id=" . (int)$material->id . "');";
            if ($material->url == $url) {
                echo "console.info('Адрес материала совпадает с текущим адресом');";
            } else {
                echo "console.error('Адрес материала не совпадает с текущим адресом');";
            }
        }
        exit;
    }
}
