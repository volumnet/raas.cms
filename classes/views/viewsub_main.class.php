<?php
namespace RAAS\CMS;
use \RAAS\Column as Column;

class ViewSub_Main extends \RAAS\Abstract_Sub_View
{
    protected static $instance;
    
    public function show_page(array $IN = array())
    {
        $view = $this;
        $IN['Table'] = new SubsectionsTable($IN);
        
        if ($IN['Item']->id) {
            $IN['MTable'] = array();
            foreach ($IN['Item']->affectedMaterialTypes as $mtype) {
                $IN['MTable'][$mtype->urn] = new MaterialsTable(array(
                    'Item' => $IN['Item'],
                    'mtype' => $mtype,
                    'hashTag' => $mtype->urn,
                    'Set' => $IN['MSet'][$mtype->urn],
                    'Pages' => $IN['MPages'][$mtype->urn], 
                    'sortVar' => 'm' . $mtype->id . 'sort',
                    'orderVar' => 'm' . $mtype->id . 'order',
                    'pagesVar' => 'm' . $mtype->id . 'page',
                    'sort' => $IN['Msort'][$mtype->urn], 
                    'order' => ((strtolower($IN['Morder'][$mtype->urn]) == 'desc') ? Column::SORT_DESC : Column::SORT_ASC)
                ));
            }
        }
        
        $this->assignVars($IN);
        $this->title = $IN['Item']->id ? $IN['Item']->name : $this->_('SITES');
        if ($IN['Item']->id) {
            $this->path[] = array('href' => $this->url, 'name' => $this->_('PAGES'));
            if ($IN['Item']->parents) {
                foreach ($IN['Item']->parents as $row) {
                    $this->path[] = array('href' => $this->url . '&id=' . (int)$row->id . '#subsections', 'name' => $row->name);
                }
            }
        }
        $this->submenu = $this->pagesMenu(new Page(), $IN['Item']);
        if ($IN['Item']->id) {
            $this->contextmenu = $this->getPageContextMenu($IN['Item']);
        } else {
            $this->contextmenu = array(array('href' => $this->url . '&action=edit', 'name' => $this->_('CREATE_SITE')));
        }
        $this->template = $IN['Item']->id ? 'pages' : $IN['Table']->template;
    }
    
    
    public function edit_page(array $IN = array())
    {
        $this->path[] = array('href' => $this->url, 'name' => $this->_('PAGES'));
        if ($IN['Parent']->id) {
            if ($IN['Parent']->parents) {
                foreach ($IN['Parent']->parents as $row) {
                    $this->path[] = array('href' => $this->url . '&id=' . (int)$row->id . '#subsections', 'name' => $row->name);
                }
            }
            $this->path[] = array('href' => $this->url . '&id=' . (int)$IN['Parent']->id . '#subsections', 'name' => $IN['Parent']->name);
        }
        if ($IN['Item']->id) {
            $this->path[] = array('href' => $this->url . '&id=' . (int)$IN['Item']->id, 'name' => $IN['Item']->name);
        }
        $this->submenu = $this->pagesMenu(new Page(), $IN['Item']->id ? $IN['Item'] : $IN['Parent']);
        $this->js[] = $this->publicURL . '/field.inc.js';
        $this->js[] = $this->publicURL . '/edit_meta.inc.js';
        $this->stdView->stdEdit($IN, 'getPageContextMenu');
    }
    
    
    public function move_page(array $IN = array())
    {
        $this->assignVars($IN);
        $this->path[] = array('href' => $this->url, 'name' => $this->_('PAGES'));
        if ($IN['Item']->parents) {
            foreach ($IN['Item']->parents as $row) {
                $this->path[] = array('href' => $this->url . '&id=' . (int)$row->id . '#subsections', 'name' => $row->name);
            }
        }
        $this->path[] = array('href' => $this->url . '&id=' . (int)$IN['Item']->id, 'name' => $IN['Item']->name);
        $this->submenu = $this->pagesMenu(new Page(), $IN['Item']);
        $this->title = $this->_('MOVING_PAGE');
        $this->contextmenu = $this->getPageContextMenu($IN['Item']);
        $this->template = 'move_page';
    }
    
    
    public function edit_block(array $IN = array())
    {
        $this->js[] = $this->publicURL . '/edit_block.js';
        $this->path[] = array('href' => $this->url, 'name' => $this->_('PAGES'));
        if ($IN['Parent']->id) {
            if ($IN['Parent']->parents) {
                foreach ($IN['Parent']->parents as $row) {
                    $this->path[] = array('href' => $this->url . '&id=' . (int)$row->id, 'name' => $row->name);
                }
            }
            $this->path[] = array('href' => $this->url . '&id=' . (int)$IN['Parent']->id, 'name' => $IN['Parent']->name);
        }
        $this->submenu = $this->pagesMenu(new Page(), $IN['Parent']);
        $this->contextmenu = $this->getBlockContextMenu($IN['Item'], $IN['Parent']);
        $this->stdView->stdEdit($IN);
    }
    
    
    public function edit_material(array $IN = array())
    {
        $this->path[] = array('href' => $this->url, 'name' => $this->_('PAGES'));
        if ($IN['Parent']->id) {
            if ($IN['Parent']->parents) {
                foreach ($IN['Parent']->parents as $row) {
                    $this->path[] = array('href' => $this->url . '&id=' . (int)$row->id . '#_' . $IN['Type']->urn, 'name' => $row->name/* . ': ' . $IN['Type']->name*/);
                }
            }
            $this->path[] = array('href' => $this->url . '&id=' . (int)$IN['Parent']->id . '#_' . $IN['Type']->urn, 'name' => $IN['Parent']->name/* . ': ' . $IN['Type']->name*/);
        }
        $this->submenu = $this->pagesMenu(new Page(), $IN['Parent']);
        $this->js[] = $this->publicURL . '/field.inc.js';
        $this->js[] = $this->publicURL . '/edit_material.js';
        $this->js[] = $this->publicURL . '/edit_meta.inc.js';
        $this->stdView->stdEdit($IN, 'getMaterialContextMenu');
    }
    
    
    public function getPageContextMenu(Page $Item, $i = 0, $c = 0)
    {
        $arr = array();
        if ($Item->id) {
            $edit = ($this->action == 'edit');
            $arr[] = array(
                'name' => $Item->vis ? $this->_('VISIBLE') : '<span class="muted">' . $this->_('INVISIBLE') . '</span>', 
                'href' => $this->url . '&action=chvis&id=' . (int)$Item->id . '&back=1', 
                'icon' => $Item->vis ? 'ok' : '',
                'title' => $this->_($Item->vis ? 'HIDE' : 'SHOW')
            );
            if ($Item->pid && ($this->action != 'move')) {
                $arr[] = array('href' => $this->url . '&action=move&id=' . (int)$Item->id, 'name' => $this->_('MOVE'), 'icon' => 'share-alt');
            }


            $edit = ($this->action == 'edit');
            $showlist = (($this->action == '') && ($this->id != $Item->id));
            if (!$edit) {
                $arr[] = array('href' => $this->url . '&action=edit&id=' . (int)$Item->id, 'name' => $this->_('EDIT'), 'icon' => 'edit');
            }
            if ($Item->_defaultOrderBy() == 'priority') {
                if ($i && 'move_up') {
                    $arr[] = array(
                        'href' => $this->url . '&action=move_up&id=' . (int)$Item->id . ($edit || $showlist ? '' : '&back=1'), 'name' => $this->_('MOVE_UP'), 'icon' => 'arrow-up'
                    );
                }
                if (($i < $c - 1) && 'move_down') {
                    $arr[] = array(
                        'href' => $this->url . '&action=move_down&id=' . (int)$Item->id . ($edit || $showlist ? '' : '&back=1'), 'name' => $this->_('MOVE_DOWN'), 'icon' => 'arrow-down'
                    );
                }
            }
            $arr[] = array(
                'href' => $this->url . '&action=delete&id=' . (int)$Item->id . ($showlist ? '&back=1' : ''), 
                'name' => $this->_('DELETE'), 
                'icon' => 'remove',
                'onclick' => 'return confirm(\'' . $this->_('DELETE_TEXT') . '\')'
            );
        }
        return $arr;
    }
    
    
    public function getMaterialContextMenu(Material $Item)
    {
        $arr = array();
        if ($Item->id) {
            $edit = (($this->action == 'edit_material') && ($this->id == $Item->id));
            if (!$edit) {
                $arr[] = array('href' => $this->url . '&action=edit_material&id=' . (int)$Item->id . '&pid=' . (int)$this->id, 'name' => $this->_('EDIT'), 'icon' => 'edit');
            }
            if ($Item->vis) {
                $arr[] = array(
                    'name' => $this->_('VISIBLE'), 
                    'href' => $this->url . '&action=chvis_material&id=' . (int)$Item->id . '&back=1', 
                    'icon' => 'ok',
                    'title' => $this->_('HIDE')
                );
            } else {
                $arr[] = array(
                    'name' => '<span class="muted">' . $this->_('INVISIBLE') . '</span>', 
                    'href' => $this->url . '&action=chvis_material&id=' . (int)$Item->id . '&back=1', 
                    'icon' => '',
                    'title' => $this->_('SHOW')
                );
            }
            $arr[] = array(
                'href' => $this->url . '&action=delete_material&id=' . (int)$Item->id . (!$edit ? '&back=1' : (isset($_GET['pid']) ? '&pid=' . (int)$_GET['pid'] : '')), 
                'name' => $this->_('DELETE'), 
                'icon' => 'remove',
                'onclick' => 'return confirm(\'' . $this->_('DELETE_TEXT') . '\')'
            );
        }
        return $arr;
    }
    
    
    public function getLocationContextMenu(Location $Item, Page $Page)
    {
        $arr = array();
        foreach (Block_Type::getTypes() as $key => $row) {
            $arr2 = $row->viewer->locationContextMenu($Page, $Item);
            $arr = array_merge($arr, $arr2);
        }
        return $arr;
    }
    
    
    public function getBlockContextMenu(Block $Item, Page $Page = null, $i = 0, $c = 0)
    {
        $arr = array();
        if ($Item->id) {
            $edit = ($this->action == 'edit_block');
            if (!$edit) {
                $arr[] = array(
                    'href' => $this->url . '&action=edit_block&id=' . (int)$Item->id . ($Page->id ? '&pid=' . (int)$Page->id : ''), 'name' => $this->_('EDIT'), 'icon' => 'edit'
                );
                $arr[] = array(
                    'name' => $Item->vis ? $this->_('VISIBLE') : '<span class="muted">' . $this->_('INVISIBLE') . '</span>', 
                    'href' => $this->url . '&action=chvis_block&id=' . (int)$Item->id . ($Page->id ? '&pid=' . (int)$Page->id : '') . '&back=1', 
                    'icon' => $Item->vis ? 'ok' : '',
                    'title' => $this->_($Item->vis ? 'HIDE' : 'SHOW')
                );
                if ($i) {
                    $arr[] = array(
                        'href' => $this->url . '&action=move_up_block&id=' . (int)$Item->id . ($Page->id ? '&pid=' . (int)$Page->id : '') . ($edit ? '' : '&back=1'), 
                        'name' => $this->_('MOVE_UP'), 
                        'icon' => 'arrow-up'
                    );
                }
                if ($i < $c - 1) {
                    $arr[] = array(
                        'href' => $this->url . '&action=move_down_block&id=' . (int)$Item->id . ($Page->id ? '&pid=' . (int)$Page->id : '') . ($edit ? '' : '&back=1'), 
                        'name' => $this->_('MOVE_DOWN'), 
                        'icon' => 'arrow-down'
                    );
                }
                $arr[] = array(
                    'href' => $this->url . '&action=delete_block&id=' . (int)$Item->id . ($edit ? '' : '&back=1'), 
                    'name' => $this->_('DELETE'), 
                    'icon' => 'remove',
                    'onclick' => 'return confirm(\'' . $this->_('DELETE_TEXT') . '\')'
                );
            } else {
                $arr[] = array(
                    'href' => $this->url . '&action=delete_block&id=' . (int)$Item->id . ($Page->id ? '&pid=' . (int)$Page->id : '') . ($edit ? '' : '&back=1'), 
                    'name' => $this->_('DELETE'), 
                    'icon' => 'remove',
                    'onclick' => 'return confirm(\'' . $this->_('DELETE_TEXT') . '\')'
                );
            }
            
        }
        return $arr;
    }
    
    
    public function pagesMenu($node, $current)
    {
        $submenu = array();
        foreach ($node->children as $row) {
            $temp = array('name' => \SOME\Text::cuttext($row->name, 64, '...'), 'href' => $this->url, 'class' => '', 'active' => false);
            if ($node instanceof Menu) {
                $temp['href'] .= '&sub=dev&action=menus';
            } elseif ($node instanceof Dictionary) {
                $temp['href'] .= '&sub=dev&action=dictionaries';
            }
            $temp['href'] .= '&id=' . (int)$row->id;
            if ($row->id == $current->id || in_array($current->id, $row->all_children_ids)) {
                $temp['active'] = true;
            }
            if ($node instanceof Page) {
                $temp['submenu'] = $this->pagesMenu($row, $current);
            }
            if (!$row->vis) {
                $temp['class'] .= ' muted';
            } elseif ($row->response_code) {
                $temp['class'] .= ' text-error';
            }
            if (!$row->pvis) {
                $temp['class'] .= ' cms-inpvis';
            }
            
            $submenu[] = $temp;
        }
        return $submenu;
    }
}