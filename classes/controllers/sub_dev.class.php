<?php
namespace RAAS\CMS;
use \RAAS\Redirector as Redirector;
use \RAAS\Attachment as Attachment;
use \RAAS\Application;
use \ArrayObject as ArrayObject;
use \RAAS\Field as RAASField;
use \RAAS\FieldSet as FieldSet;
use \RAAS\FieldContainer as FieldContainer;
use \RAAS\Form as RAASForm;
use \RAAS\FormTab as FormTab;
use \RAAS\CMS\Form as CMSForm;
use \RAAS\OptGroup as OptGroup;
use \RAAS\Option as Option;
use \RAAS\StdSub as StdSub;

class Sub_Dev extends \RAAS\Abstract_Sub_Controller
{
    protected static $instance;
    
    public function run()
    {
        $this->view->submenu = $this->view->devMenu();
        switch ($this->action) {
            case 'edit_template': case 'edit_snippet_folder': case 'edit_snippet': 
            case 'edit_material_type': case 'edit_form': case 'menus': 
            case 'edit_menu': case 'move_menu': case 'dictionaries': 
            case 'edit_dictionary': case 'move_dictionary': case 'copy_snippet': 
            case 'diag': case 'pages_fields': case 'forms': case 'material_types':
                $this->{$this->action}();
                break;
            case 'edit_material_field': case 'edit_form_field': case 'edit_page_field': 
                $f = str_replace('_material', '', str_replace('_page', '', str_replace('_form', '', $this->action)));
                $this->$f();
                break;
            case 'templates': 
                $this->view->templates(array('Set' => $this->model->dev_templates()));
                break;
            case 'snippets': 
                $this->view->snippets();
                break;
            case 'chvis_dictionary': case 'vis_dictionary': case 'invis_dictionary': case 'delete_dictionary': 
                $items = array();
                $ids = (array)$_GET['id'];
                if (in_array('all', $ids, true)) {
                    $pids = (array)$_GET['pid'];
                    $pids = array_filter($pids, 'trim');
                    $pids = array_map('intval', $pids);
                    if ($pids) {
                        $items = Dictionary::getSet(array('where' => "pid IN (" . implode(", ", $pids) . ")"));
                    }
                } else {
                    $items = array_map(function($x) { return new Dictionary((int)$x); }, $ids);
                }
                $items = array_values($items);
                $Item = isset($items[0]) ? $items[0] : new Dictionary();
                $f = str_replace('_dictionary', '', $this->action);
                StdSub::$f($items, $this->url . '&action=dictionaries&id=' . (int)$Item->pid);
                break;
            case 'chvis_menu': case 'vis_menu': case 'invis_menu': case 'delete_menu': case 'realize_menu': 
                $items = array();
                $ids = (array)$_GET['id'];
                if (in_array('all', $ids, true)) {
                    $pids = (array)$_GET['pid'];
                    $pids = array_filter($pids, 'trim');
                    $pids = array_map('intval', $pids);
                    if ($pids) {
                        $items = Menu::getSet(array('where' => "pid IN (" . implode(", ", $pids) . ")"));
                    }
                } else {
                    $items = array_map(function($x) { return new Menu((int)$x); }, $ids);
                }
                $items = array_values($items);
                $Item = isset($items[0]) ? $items[0] : new Menu();
                $f = str_replace('_menu', '', $this->action);
                StdSub::$f($items, $this->url . '&action=menus&id=' . (int)$Item->id);
                break;
            case 'delete_template_image': 
                $Item = new Template((int)$this->id);
                StdSub::deleteBackground($Item, ($_GET['back'] ? 'history:back' : $this->url . '&action=edit_template&id=' . (int)$Item->id) . '#layout', false);
                break;
            case 'delete_template': 
                $ids = (array)$_GET['id'];
                $items = array_map(function($x) { return new Template((int)$x); }, $ids);
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=templates');
                break;
            case 'delete_snippet_folder': 
                $ids = (array)$_GET['id'];
                $items = array_map(function($x) { return new Snippet_Folder((int)$x); }, $ids);
                $items = array_filter($items, function($x) { return !$x->locked; });
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=snippets');
                break;
            case 'delete_snippet':
                $ids = (array)$_GET['id'];
                $items = array_map(function($x) { return new Snippet((int)$x); }, $ids);
                $items = array_filter($items, function($x) { return !$x->locked; });
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=snippets');
                break;
            case 'delete_form':
                $ids = (array)$_GET['id'];
                $items = array_map(function($x) { return new CMSForm((int)$x); }, $ids);
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=forms');
                break;
            case 'delete_diag':
                $from = (strtotime($_GET['from']) > 0) ? date('Y-m-d', strtotime($_GET['from'])) : null;
                $to = (strtotime($_GET['to']) > 0) ? date('Y-m-d', strtotime($_GET['to'])) : null;
                Diag::deleteStat($from, $to);
                new Redirector(isset($_GET['back']) ? 'history:back' : $this->url . '&action=diag');
                break;
            case 'delete_material_field': case 'show_in_table_material_field': case 'required_material_field':
            case 'delete_form_field': case 'show_in_table_form_field': case 'required_form_field':
            case 'delete_page_field': case 'show_in_table_page_field': case 'required_page_field':
                if (strstr($this->action, 'form')) {
                    $classname = 'RAAS\\CMS\\Form_Field';
                    $parentClassname = 'RAAS\\CMS\\Form';
                } elseif (strstr($this->action, 'material')) {
                    $classname = 'RAAS\\CMS\\Material_Field';
                    $parentClassname = 'RAAS\\CMS\\Material_Type';
                } else {
                    $classname = 'RAAS\\CMS\\Page_Field';
                    $parentClassname = 'RAAS\\CMS\\Material_Type';
                }
                $items = $where = array();
                $ids = (array)$_GET['id'];
                if (in_array('all', $ids, true)) {
                    $where[] = "classname = '" . Application::i()->SQL->real_escape_string($parentClassname) . "'";
                    $pids = (array)$_GET['pid'];
                    $pids = array_filter($pids, 'trim');
                    $pids = array_map('intval', $pids);
                    if ($pids) {
                        $where[] = "pid IN (" . implode(", ", $pids) . ")";
                        $items = $classname::getSet(array('where' => $where));
                    } elseif ($classname == 'RAAS\\CMS\\Page_Field') {
                        $items = $classname::getSet(array('where' => $where));
                    }
                } else {
                    $items = array_map(function($x) use ($classname) { return new $classname((int)$x); }, $ids);
                }
                $items = array_values($items);
                $Item = isset($items[0]) ? $items[0] : new $classname();
                $f = str_replace('_field', '', str_replace('_material', '', str_replace('_page', '', str_replace('_form', '', $this->action))));
                if (strstr($this->action, 'form')) {
                    $url2 .= '&action=edit_form&id=' . (int)$Item->parent->id;
                } elseif (strstr($this->action, 'material')) {
                    $url2 .= '&action=edit_material_type&id=' . (int)$Item->parent->id;
                } else {
                    $url2 .= '&action=pages_fields';
                }
                StdSub::$f($items, $this->url . $url2);
                break;
            case 'delete_material_type':
                $ids = (array)$_GET['id'];
                $items = array_map(function($x) { return new Material_Type((int)$x); }, $ids);
                $items = array_values($items);
                StdSub::delete($items, $this->url . '&action=material_types');
                break;
            case 'webmaster_faq':
                $w = new Webmaster();
                $w->createFAQ($this->view->_('FAQ'), 'faq', $this->view->_('FAQ_MAIN'));
                new Redirector(\SOME\HTTP::queryString('action='));
                break;
            case 'webmaster_reviews':
                $w = new Webmaster();
                $w->createFAQ($this->view->_('REVIEWS'), 'reviews', $this->view->_('REVIEWS_MAIN'));
                new Redirector(\SOME\HTTP::queryString('action='));
                break;
            case 'webmaster_photos':
                $w = new Webmaster();
                $w->createPhotos($this->view->_('PHOTOS'), 'photos');
                new Redirector(\SOME\HTTP::queryString('action='));
                break;
            case 'webmaster_search':
                $w = new Webmaster();
                $w->createSearch();
                new Redirector(\SOME\HTTP::queryString('action='));
                break;
            case 'clear_cache': 
                if (Package::i()->registryGet('clear_cache_manually')) {
                    $this->model->clearCache(true);
                    new Redirector(\SOME\HTTP::queryString('action=cache'));
                } else {
                    new Redirector(\SOME\HTTP::queryString('action='));
                }
                break;
            case 'cache':
                if (Package::i()->registryGet('clear_cache_manually')) {
                    // $this->model->getCacheMap();
                    $this->view->cache();
                } else {
                    new Redirector(\SOME\HTTP::queryString('action='));
                }
                break;
            default:
                $this->view->dev();
                break;
        }
    }
    
    
    protected function dictionaries()
    {
        $Item = new Dictionary((int)$this->id);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $localError = array();
            if ($Item->id) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    if (!in_array($ext, Dictionary::$availableExtensions)) {
                        $localError[] = array(
                            'name' => 'INVALID', 
                            'value' => 'file', 
                            'description' => sprintf($this->view->_('AVAILABLE_DICTIONARIES_FORMATS'), strtoupper(implode(', ', \RAAS\CMS\Dictionary::$availableExtensions)))
                        );
                    }
                    if (!$localError) {
                        $this->model->dev_dictionaries_loadFile($Item, $_FILES['file']);
                    }
                }
            }
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                $this->model->setEntitiesPriority('\RAAS\CMS\Dictionary', (array)$_POST['priority']);
            }
            $OUT['localError'] = $localError;
        }
        $OUT['Item'] = $Item;
        $OUT = array_merge($OUT, $this->model->dev_dictionaries());
        $this->view->dictionaries($OUT);
    }
    
    
    protected function edit_dictionary()
    {
        $Item = new Dictionary((int)$this->id);
        $Parent = $Item->pid ? $Item->parent : new Dictionary(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $Form = new EditDictionaryForm(array('Item' => $Item, 'Parent' => $Parent));
        $this->view->edit_dictionary(array_merge($Form->process(), array('Parent' => $Parent)));
    }
    
    
    protected function move_dictionary()
    {
        $items = array();
        $ids = (array)$_GET['id'];
        if (in_array('all', $ids, true)) {
            $pids = (array)$_GET['pid'];
            $pids = array_filter($pids, 'trim');
            $pids = array_map('intval', $pids);
            if ($pids) {
                $items = Dictionary::getSet(array('where' => "pid IN (" . implode(", ", $pids) . ")"));
            }
        } else {
            $items = array_map(function($x) { return new Dictionary((int)$x); }, $ids);
        }
        $items = array_values($items);
        $Item = isset($items[0]) ? $items[0] : new Dictionary();

        if ($items) {
            if (isset($_GET['new_pid'])) {
                StdSub::move($items, new Dictionary((int)$_GET['new_pid']), $this->url . '&action=dictionaries&id=%s');
            } else {
                $this->view->move_dictionary(array('Item' => $Item, 'items' => $items));
                return;
            }
        }
        new Redirector(isset($_GET['back']) ? 'history:back' : $this->url . '&action=dictionaries&id=' . (int)$Item->pid);
    }
    
    
    protected function menus()
    {
        $Item = new Menu((int)$this->id);
        $Parent = $Item->pid ? $Item->parent : new Menu(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $OUT = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                foreach ($_POST['priority'] as $key => $val) {
                    $row = new Menu($key);
                    if ($row->id) {
                        $row->priority = (int)$val;
                        $row->commit();
                    }
                }
            }
        }
        $OUT['DATA'] = $Item->getArrayCopy();
        if (!$Item->id) {
            $OUT['DATA']['vis'] = 1;
        }
        $OUT['Item'] = $Item;
        $OUT['Parent'] = $Parent;
        if ($Item->id || ($this->action != 'edit_menu')) {
            $OUT['Set'] = $Item->id ? $Item->subMenu : $Item->children;
        }
        $this->view->menus($OUT);
    }
    
    
    protected function edit_menu()
    {
        $Item = new Menu((int)$this->id);
        $Parent = $Item->pid ? $Item->parent : new Menu(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $Form = new EditMenuForm(array('Item' => $Item, 'Parent' => $Parent));
        $this->view->edit_menu(array_merge($Form->process(), array('Parent' => $Parent)));
    }
    
    
    protected function move_menu()
    {
        $items = array();
        $ids = (array)$_GET['id'];
        if (in_array('all', $ids, true)) {
            $pids = (array)$_GET['pid'];
            $pids = array_filter($pids, 'trim');
            $pids = array_map('intval', $pids);
            if ($pids) {
                $items = Menu::getSet(array('where' => "pid IN (" . implode(", ", $pids) . ")"));
            }
        } else {
            $items = array_map(function($x) { return new Menu((int)$x); }, $ids);
        }
        $items = array_values($items);
        $Item = isset($items[0]) ? $items[0] : new Menu();
        
        if ($items) {
            if (isset($_GET['new_pid'])) {
                StdSub::move($items, new Menu((int)$_GET['new_pid']), $this->url . '&action=menus&id=%s');
            } else {
                $this->view->move_menu(array('Item' => $Item, 'items' => $items));
                return;
            }
        }
        new Redirector('history:back');
    }
    
    
    protected function edit_template()
    {
        $Item = new Template((int)$this->id);
        $Form = new EditTemplateForm(array('Item' => $Item));
        $this->view->edit_template($Form->process());
    }
    
    
    protected function edit_snippet_folder()
    {
        $Item = new Snippet_Folder((int)$this->id);
        if ($Item->locked) {
            exit;
        }
        $Form = new EditSnippetFolderForm(array('Item' => $Item));
        $this->view->edit_snippet_folder($Form->process());
    }
    
    
    protected function edit_snippet()
    {
        $Item = new Snippet((int)$this->id);
        if ($Item->locked) {
            exit;
        }
        $Form = new EditSnippetForm(array('Item' => $Item));
        $this->view->edit_snippet($Form->process());
    }
    
    
    protected function copy_snippet()
    {
        $Item = new Snippet((int)$this->id);
        $Item = $this->model->copyItem($Item);
        $Item->locked = 0;
        $Form = new CopySnippetForm(array('Item' => $Item));
        $this->view->edit_snippet($Form->process());
    }


    protected function material_types()
    {
        $this->view->material_types();
    }
    
    
    protected function edit_material_type()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                $this->model->setEntitiesPriority('\RAAS\CMS\Material_Field', (array)$_POST['priority']);
            }
        }
        $Item = new Material_Type((int)$this->id);
        $Parent = $Item->pid ? $Item->parent : new Material_Type(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $Form = new EditMaterialTypeForm(array('Item' => $Item, 'Parent' => $Parent));
        $this->view->edit_material_type(array_merge($Form->process(), array('Parent' => $Parent)));
    }


    protected function forms()
    {
        $this->view->forms(array('Set' => $this->model->forms()));
    }
    
    
    protected function edit_form()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                $this->model->setEntitiesPriority('\RAAS\CMS\Form_Field', (array)$_POST['priority']);
            }
        }
        $Item = new CMSForm((int)$this->id);
        $Form = new EditFormForm(array('Item' => $Item));
        $this->view->edit_form($Form->process());
    }


    protected function pages_fields()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['priority']) && is_array($_POST['priority'])) {
                $this->model->setEntitiesPriority('\RAAS\CMS\Page_Field', (array)$_POST['priority']);
            }
        }
        $this->view->pages_fields(array('Set' => $this->model->dev_pages_fields()));
    }
    
    
    protected function edit_field()
    {
        if ($this->sub == 'dev' && $this->action == 'edit_form_field') {
            $Item = new Form_Field((int)$this->id);
            $Parent = $Item->pid ? $Item->parent : new CMSForm(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
            $parentUrl = $this->url . '&action=edit_form';
            if (!$Parent->id) {
                new Redirector($parentUrl);
            }
            $parentUrl .= '&id=' . (int)$Parent->id;
        } elseif (strstr($this->action, 'material')) {
            $Item = new Material_Field((int)$this->id);
            $Parent = $Item->pid ? $Item->parent : new Material_Type(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
            $parentUrl = $this->url . '&action=edit_material_type';
            if (!$Parent->id) {
                new Redirector($parentUrl);
            }
            $parentUrl .= '&id=' . (int)$Parent->id;
        } else {
            $Item = new Page_Field((int)$this->id);
            $Parent = null;
            $parentUrl = $this->url . '&action=pages_fields';
        }
        $Form = new EditFieldForm(array('Item' => $Item, 'meta' => array('Parent' => $Parent, 'parentUrl' => $parentUrl)));
        $OUT = $Form->process();
        if ($Item instanceof Material_Field) {
            $OUT['Parent'] = $Parent;
            $this->view->edit_material_field($OUT);
        } elseif ($Item instanceof Form_Field) {
            $OUT['Parent'] = $Parent;
            $this->view->edit_form_field($OUT);
        } else {
            $this->view->edit_page_field($OUT);
        }
    }


    protected function diag()
    {
        $from = date('Y-m-d', (strtotime($_GET['from']) > 0) ? strtotime($_GET['from']) : time());
        $to = date('Y-m-d', (strtotime($_GET['to']) > 0) ? strtotime($_GET['to']) : time());
        $Item = Diag::getMerged($from, $to);
        $this->view->diag(array('Item' => $Item, 'from' => $from, 'to' => $to));
    }
}