<?php
namespace RAAS\CMS;
use \RAAS\StdSub;
use \RAAS\Application;
use \RAAS\Redirector;

class Sub_Main extends \RAAS\Abstract_Sub_Controller
{
    protected static $instance;
    
    public function run()
    {
        switch ($this->action) {
            case 'edit': case 'move':
                $this->{$this->action . '_page'}();
                break;
            case 'edit_block': case 'edit_material': 
                $this->{$this->action}();
                break;
            case 'chvis': case 'delete':
                $Item = new Page((int)$this->id);
                $f = $this->action;
                StdSub::$f($Item, (isset($_GET['back']) ? 'history:back' : $this->url . '&id=' . (int)$Item->pid) . '#subsections', false);
                break;
            case 'chvis_block': case 'delete_block':
                $Item = Block::spawn((int)$this->id);
                $Page = new Page((int)(isset($_GET['pid']) ? $_GET['pid'] : 0));
                $f = str_replace('_block', '', $this->action);
                StdSub::$f($Item, $this->url . '&id=' . (int)$Item->pid, true, true, $Page);
                break;
            case 'move_up_block': case 'move_down_block':
                $Item = Block::spawn((int)$this->id);
                $Page = new Page((int)(isset($_GET['pid']) ? $_GET['pid'] : 0));
                $step = (isset($_GET['step']) && (int)$_GET['step']) ? abs((int)$_GET['step']) : 1;
                if ($this->action == 'move_up_block') {
                    $step *= -1;
                }
                StdSub::swap($Item, $this->url . '&id=' . (int)$Item->pid, true, true, $step, $Page);
                break;
            case 'unassoc_block':
                $Item = Block::spawn((int)$this->id);
                $pid = (isset($_GET['pid']) ? $_GET['pid'] : $Item->pid);
                $Page = new Page(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
                StdSub::unassoc($Item, $this->url . '&id=' . (int)$pid, true, isset($_GET['pid']) && $Page->id, $Page);
                break;
            case 'delete_material': case 'chvis_material':
                $Item = new Material((int)$this->id);
                $f = str_replace('_material', '', $this->action);
                $pid = ($mtype->global_type || (int)$_GET['pid'] ? (int)$_GET['pid'] : (int)$Item->pages_ids[0]);
                StdSub::$f($Item, (isset($_GET['back']) ? 'history:back' : $this->url . '&id=' . (int)$pid) . '#_' . $Item->material_type->urn, false);
                break;
            default:
                $this->show_page();
                break;
        }
    }


    protected function show_page()
    {
        if (isset($_POST['priority']) && is_array($_POST['priority'])) {
            $this->model->setEntitiesPriority('\RAAS\CMS\Material', (array)$_POST['priority']);
        }
        if (isset($_POST['page_priority']) && is_array($_POST['page_priority'])) {
            $this->model->setEntitiesPriority('\RAAS\CMS\Page', (array)$_POST['page_priority']);
        }
        $Page = new Page($this->id);
        $OUT = array();
        $OUT = array_merge($OUT, $this->model->show_page());
        $OUT['Item'] = $Page;
        $MSet = array();
        $MPages = array();
        foreach ($Page->affectedMaterialTypes as $mtype) {
            foreach (array('sort', 'order') as $v) {
                $var = 'm' . $mtype->id . $v;
                if (isset($_GET[$var])) {
                    $_COOKIE[$var] = $_GET[$var];
                    setcookie($var, $_COOKIE[$var], time() + Application::i()->registryGet('cookieLifetime') * 86400, '/');
                }
            }

            $temp = $this->model->getPageMaterials(
                $Page, 
                $mtype, 
                isset($_GET['m' . $mtype->id . 'search_string']) ? $_GET['m' . $mtype->id . 'search_string'] : '', 
                isset($_COOKIE['m' . $mtype->id . 'sort']) ? $_COOKIE['m' . $mtype->id . 'sort'] : 'post_date',
                isset($_COOKIE['m' . $mtype->id . 'order']) ? $_COOKIE['m' . $mtype->id . 'order'] : 'asc',
                isset($_GET['m' . $mtype->id . 'page']) ? (int)$_GET['m' . $mtype->id . 'page'] : 1
            );
            $MSet[$mtype->urn] = $temp['Set'];
            $MPages[$mtype->urn] = $temp['Pages'];
            $OUT['Morder'][$mtype->urn] = $temp['order'];
            $OUT['Msort'][$mtype->urn] = $temp['sort'];
        }
        $OUT['MSet'] = $MSet;
        $OUT['MPages'] = $MPages;
        $this->view->show_page($OUT);
    }
    
    
    protected function edit_page()
    {
        $Item = new Page((int)$this->id);
        $Parent = $Item->pid ? $Item->parent : new Page(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $Form = new EditPageForm(array('Item' => $Item, 'Parent' => $Parent));
        $this->view->edit_page(array_merge($Form->process(), array('Parent' => $Parent)));
    }
    
    
    protected function edit_block()
    {
        if ($this->id) {
            $Item = Block::spawn($this->id);
            $classname = $Item->block_type;
        } else {
            $classname = 'RAAS\\CMS\\' . str_replace('.', '\\', $_GET['type']);
        }
        if (!($blockType = Block_Type::getType($classname)) || !class_exists($classname)) {
            $classname = 'RAAS\\CMS\\Block_HTML';
            $blockType = Block_Type::getType($classname);
        }
        if (!$this->id) {
            $Item = new $classname();
        }
        $Parent = isset($_GET['pid']) ? new Page((int)$_GET['pid']) : ($Item->pid ? $Item->parent : new Page());
        $t = $this;
        if (!$Parent->id) {
            new Redirector($this->url);
        }
        $arr = array('Item' => $Item, 'meta' => array('Parent' => $Parent));
        
        $Form = $blockType->getForm($arr);
        $this->view->edit_block(array_merge($Form->process(), array('Parent' => $Parent)));
    }
    
    
    protected function move_page()
    {
        $Item = new Page((int)$this->id);
        if ($Item->id && $Item->pid) {
            if (isset($_GET['pid'])) {
                $Parent = new Page((int)$_GET['pid']);
                StdSub::move($Item, $Parent, $this->url . '&id=%s#subsections');
            } else {
                $this->view->move_page(array('Item' => $Item));
                return;
            }
        }
        new Redirector('history:back#subsections');
    }
    
    
    protected function edit_material()
    {
        $Item = new Material($this->id);
        $Type = (isset($Item->pid) && $Item->pid) ? $Item->material_type : new Material_Type(isset($_GET['mtype']) ? (int)$_GET['mtype'] : 0);
        if (!$Type->id) {
            new Redirector($this->url . '&id=' . (int)$Parent->id);
        }
        if (!$Item->id) {
            $Item->pid = (int)$Type->id;
        }
        $Parent = new Page();
        $OUT = array();
        $MSet = $MPages = $Morder = $Msort = array();
        if ($Item->id) {
            if (isset($_GET['pid']) && in_array((int)$_GET['pid'], $Item->pages_ids)) {
                $Parent = new Page((int)$_GET['pid']);
            } elseif ($Item->pages) {
                $Parent = new Page($Item->pages_ids[0]);
            } else {
                $Parent = new Page((int)$_GET['pid']);
            }
        } elseif (isset($_GET['pid'])) {
            $Parent = new Page((int)$_GET['pid']);
        }
        foreach ($Item->relatedMaterialTypes as $mtype) {
            foreach (array('sort', 'order') as $v) {
                $var = 'm' . $mtype->id . $v;
                if (isset($_GET[$var])) {
                    $_COOKIE[$var] = $_GET[$var];
                    setcookie($var, $_COOKIE[$var], time() + Application::i()->registryGet('cookieLifetime') * 86400, '/');
                }
            }
            $temp = $this->model->getRelatedMaterials(
                $Item, 
                $mtype, 
                isset($_GET['m' . $mtype->id . 'search_string']) ? $_GET['m' . $mtype->id . 'search_string'] : '', 
                isset($_COOKIE['m' . $mtype->id . 'sort']) ? $_COOKIE['m' . $mtype->id . 'sort'] : 'post_date',
                isset($_COOKIE['m' . $mtype->id . 'order']) ? $_COOKIE['m' . $mtype->id . 'order'] : 'asc',
                isset($_GET['m' . $mtype->id . 'page']) ? (int)$_GET['m' . $mtype->id . 'page'] : 1
            );
            $MSet[$mtype->urn] = $temp['Set'];
            $MPages[$mtype->urn] = $temp['Pages'];
            $Morder[$mtype->urn] = $temp['order'];
            $Msort[$mtype->urn] = $temp['sort'];
        }
        $OUT['Morder'] = $Morder;
        $OUT['Msort'] = $Msort;
        $OUT['MSet'] = $MSet;
        $OUT['MPages'] = $MPages;
        $OUT['Parent'] = $Parent;
        $OUT['Type'] = $Type;
        $Form = new EditMaterialForm(array(
            'Item' => $Item, 'Parent' => $Parent, 'Type' => $Type, 'MSet' => $MSet, 'MPages' => $MPages, 'Msort' => $Msort, 'Morder' => $Morder
        ));
        $OUT = array_merge($OUT, (array)$Form->process());
        $this->view->edit_material($OUT);
    }

}