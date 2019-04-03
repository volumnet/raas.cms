<?php
namespace RAAS\CMS;

use \RAAS\Redirector as Redirector;
use \RAAS\Attachment as Attachment;

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
        }
    }


    protected function get_cache_map()
    {
        $OUT['Set'] = array_values($this->model->getCacheMap());
        $this->view->get_cache_map($OUT);
    }


    protected function clear_cache()
    {
        $this->model->clearCache(true);
        $this->view->clear_cache(array());
    }


    protected function clear_blocks_cache()
    {
        $this->model->clearBlocksCache(true);
        $this->view->clear_blocks_cache(array());
    }


    protected function rebuild_page_cache()
    {
        $Page = new Page($this->nav['id']);
        $Material = isset($this->nav['mid']) ? new Material($this->nav['mid']) : null;
        if ($Material->id) {
            $Page->Material = $Material;
        }
        $Page->rebuildCache();
        $this->view->rebuild_page_cache(array());
    }


    protected function material_fields()
    {
        $Material_Type = new Material_Type((int)$this->id);
        $Set = array(
            (object)array('id' => 'name', 'name' => $this->view->_('NAME')),
            (object)array('id' => 'urn', 'name' => $this->view->_('URN')),
            (object)array('id' => 'description', 'name' => $this->view->_('DESCRIPTION')),
            (object)array('id' => 'post_date', 'name' => $this->view->_('CREATED_BY')),
            (object)array('id' => 'modify_date', 'name' => $this->view->_('EDITED_BY'))
        );
        $Set = array_merge(
            $Set,
            array_values(
                array_filter(
                    $Material_Type->fields,
                    function ($x) {
                        return !($x->multiple || in_array($x->datatype, array('file', 'image')));
                    }
                )
            )
        );
        $OUT['Set'] = array_map(
            function ($x) {
                return array('val' => $x->id, 'text' => $x->name);
            },
            $Set
        );
        $this->view->show_page($OUT);
    }


    protected function get_materials_by_field()
    {
        if ((int)$this->id) {
            $Field = new Material_Field((int)$this->id);
            $Set = array();
            if ($Field->datatype == 'material') {
                $mtype = (int)$Field->source;
            }
        } elseif ((int)$this->nav['mtype']) {
            $mtype = (int)$this->nav['mtype'];
        }
        $Set = $this->model->getMaterialsBySearch(isset($_GET['search_string']) ? $_GET['search_string'] : '', $mtype);
        $OUT['Set'] = array_map(
            function ($x) {
                $y = array(
                    'id' => (int)$x->id,
                    'name' => $x->name,
                    'description' => \SOME\Text::cuttext(html_entity_decode(strip_tags($x->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...')
                );
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
     * @return array<[
     *             'val' => int ID# страницы,
     *             'text' => string Наименование страницы
     *             'src' => string URL страницы
     *         ]>
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
     * @param int|null $domainId ID# домена (только для фильтрации корневых страниц)
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
}
