<?php
namespace RAAS\CMS;
use \RAAS\Redirector as Redirector;
use \RAAS\Attachment as Attachment;
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
            case 'edit_template': case 'edit_snippet_folder': case 'edit_snippet': case 'edit_material_type': case 'edit_form': case 'menus': case 'edit_menu': 
            case 'move_menu': case 'dictionaries': case 'edit_dictionary': case 'move_dictionary': case 'copy_snippet': 
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
            case 'material_types': 
                $this->view->material_types(array('Set' => $this->model->material_types()));
                break;
            case 'forms': 
                $this->view->forms(array('Set' => $this->model->forms()));
                break;
            case 'pages_fields':
                $this->view->pages_fields(array('Set' => $this->model->dev_pages_fields()));
                break;
            case 'chvis_dictionary': case 'move_up_dictionary': case 'move_down_dictionary': case 'delete_dictionary': 
                $Item = new Dictionary((int)$this->id);
                $f = str_replace('_dictionary', '', $this->action);
                StdSub::$f($Item, $this->url . '&action=dictionaries&id=' . (int)$Item->pid);
                break;
            case 'chvis_menu': case 'delete_menu': case 'realize_menu': 
                $Item = new Menu((int)$this->id);
                $f = str_replace('_menu', '', $this->action);
                StdSub::$f($Item, $this->url . '&action=menus&id=' . (int)$Item->pid);
                break;
            case 'delete_template_image': 
                $Item = new Template((int)$this->id);
                StdSub::deleteBackground($Item, ($_GET['back'] ? 'history:back' : $this->url . '&action=edit_template&id=' . (int)$Item->id) . '#layout', false);
                break;
            case 'delete_template': 
                $Item = new Template((int)$this->id);
                StdSub::delete($Item, $this->url . '&action=templates');
                break;
            case 'delete_snippet_folder': 
                $Item = new Snippet_Folder((int)$this->id);
                if ($Item->locked) {
                    exit;
                }
                StdSub::delete($Item, $this->url . '&action=snippets');
                break;
            case 'delete_snippet':
                $Item = new Snippet((int)$this->id);
                if ($Item->locked) {
                    exit;
                }
                StdSub::delete($Item, $this->url . '&action=snippets');
                break;
            case 'delete_form':
                $Item = new CMSForm((int)$this->id);
                StdSub::delete($Item, $this->url . '&action=forms');
                break;
            case 'move_up_material_field': case 'move_down_material_field': case 'delete_material_field': case 'show_in_table_material_field':
            case 'move_up_form_field': case 'move_down_form_field': case 'delete_form_field': case 'show_in_table_form_field':
            case 'move_up_page_field': case 'move_down_page_field': case 'delete_page_field': case 'show_in_table_page_field':
                if (strstr($this->action, 'form')) {
                    $Item = new Form_Field((int)$this->id);
                } elseif (strstr($this->action, 'material')) {
                    $Item = new Material_Field((int)$this->id);
                } else {
                    $Item = new Page_Field((int)$this->id);
                }
                $f = str_replace('_field', '', str_replace('_material', '', str_replace('_page', '', str_replace('_form', '', $this->action))));
                if (strstr($this->action, 'form')) {
                    $url2 .= '&action=edit_form&id=' . (int)$Item->parent->id;
                } elseif (strstr($this->action, 'material')) {
                    $url2 .= '&action=edit_material_type&id=' . (int)$Item->parent->id;
                } else {
                    $url2 .= '&action=pages_fields';
                }
                StdSub::$f($Item, $this->url . $url2);
                break;
            case 'delete_material_type':
                $Item = new Material_Type((int)$this->id);
                StdSub::delete($Item, $this->url . '&action=material_types');
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
            if ($Item->id) {
                $localError = array();
                if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $localError[] = array('name' => 'MISSED', 'value' => 'file', 'description' => 'ERR_NO_FILE');
                } else {
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    if (!in_array($ext, Dictionary::$availableExtensions)) {
                        $localError[] = array(
                            'name' => 'INVALID', 
                            'value' => 'file', 
                            'description' => sprintf($this->view->_('AVAILABLE_DICTIONARIES_FORMATS'), strtoupper(implode(', ', \RAAS\CMS\Dictionary::$availableExtensions)))
                        );
                    }
                }
                if (!$localError) {
                    $this->model->dev_dictionaries_loadFile($Item, $_FILES['file']);
                }
                $OUT['localError'] = $localError;
            }
        }
        $OUT['Item'] = $Item;
        $OUT = array_merge($OUT, $this->model->dev_dictionaries());
        $this->view->dictionaries($OUT);
    }
    
    
    protected function edit_dictionary()
    {
        $Item = new Dictionary((int)$this->id);
        $Parent = $Item->pid ? $Item->parent : new Dictionary(isset($_GET['pid']) ? (int)$_GET['pid'] : 0);
        $CONTENT = array();
        foreach (\RAAS\CMS\Dictionary::$ordersBy as $key => $val) {
            $CONTENT['orderBy'][] = array('value' => $key, 'caption' => $this->view->_($val));
        }
        $Form = new RAASForm(array(
            'Item' => $Item,
            'caption' => $Item->id ? $Item->name : ($Parent->id ? $this->view->_('CREATING_NOTE') : $this->view->_('CREATING_DICTIONARY')),
            'export' => function($Form) use ($Parent) { $Form->exportDefault(); $Form->Item->pid = (int)$Parent->id; },
            'parentUrl' => $this->url . '&action=dictionaries&id=' . (int)$Parent->id,
            'newUrl' => $this->url . '&action=edit_dictionary&pid=%s'
        ));
        $Form->children[] = new RAASField(array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_('VISIBLE'), 'default' => 1));
        $Form->children[] = new RAASField(array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'));
        if ($Parent->id) {
            $Form->children[] = new RAASField(array('name' => 'urn', 'caption' => $this->view->_('VALUE')));
        }
        $Form->children[] = new RAASField(array('type' => 'radio', 'name' => 'orderby', 'children' => $CONTENT['orderBy'], 'default' => 'priority'));
        $this->view->edit_dictionary(array_merge($Form->process(), array('Parent' => $Parent)));
    }
    
    
    protected function move_dictionary()
    {
        $Item = new Dictionary((int)$this->id);
        if ($Item->id) {
            if (isset($_GET['pid'])) {
                StdSub::move($Item, new Dictionary((int)$_GET['pid']), $this->url . '&action=dictionaries&id=%s');
            } else {
                $this->view->move_dictionary(array('Item' => $Item));
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
            if (isset($_POST['reorder']) && is_array($_POST['reorder'])) {
                foreach ($_POST['reorder'] as $key => $val) {
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
        $CONTENT = array();
        $CONTENT['pages'] = array(new Page(array('id' => 0, 'name' => '--')));
        
        $Form = new RAASForm(array(
            'Item' => $Item,
            'caption' => $Item->id ? $Item->name : ($Parent->id ? $this->_('CREATING_NOTE') : $this->_('CREATING_MENU')),
            'parentUrl' => $this->url . '&action=menus&id=%s',
            'export' => function($Form) use ($Parent) {
                $Form->exportDefault();
                if (!$Form->Item->id) {
                    $Form->Item->pid = (int)$Parent->id;
                }
            },
            'children' => array(
                array(
                    'type' => 'hidden', 
                    'name' => 'pid', 
                    'export' => 'is_null', 
                    'import' => function() use ($Parent) { return (int)$Parent->id; }, 
                    'default' => (int)$Parent->id
                ),
                array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_('VISIBLE'), 'default' => 1),
                array('type' => 'select', 'name' => 'page_id', 'caption' => $this->view->_('PAGE'), 'children' => array('Set' => $CONTENT['pages'])),
                array(
                    'type' => 'number', 
                    'name' => 'inherit', 
                    'caption' => $this->view->_('INHERIT_LEVEL'), 
                    'check' => function($Field) use ($Parent) {  
                        if (!$Parent->id && (int)$_POST['page_id'] && !(isset($_POST['inherit']) && (int)$_POST['inherit'])) {
                            return array('name' => 'MISSED', 'value' => $Field->name, 'description' => 'ERR_NO_MENU_INHERIT');
                        }
                    },
                ),
                array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'),
                array(
                    'name' => 'url', 
                    'caption' => $this->view->_('URL'), 
                    'check' => function($Field) use ($Parent) {
                        if ($Parent->id && !(int)$_POST['page_id'] && !trim($_POST['url'])) {
                            return array('name' => 'MISSED', 'value' => $Field->name, 'description' => 'ERR_NO_URL');
                        }
                    }
                ),
                
            )
        ));
        $this->view->edit_menu(array_merge($Form->process(), array('Parent' => $Parent)));
    }
    
    
    protected function move_menu()
    {
        $Item = new Menu((int)$this->id);
        if ($Item->id) {
            if (isset($_GET['pid'])) {
                StdSub::move($Item, new Menu((int)$_GET['pid']), $this->url . '&action=menus&id=%s');
            } else {
                $this->view->move_menu(array('Item' => $Item));
                return;
            }
        }
        new Redirector('history:back');
    }
    
    
    protected function edit_template()
    {
        $Item = new Template((int)$this->id);
        $NameField = new RAASField(array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'));
        $DescriptionField = new RAASField(array('type' => 'codearea', 'name' => 'description', 'caption' => $this->view->_('TEMPLATE_CODE'), 'required' => 'required'));
        $BackgroundField = new RAASField(array(
            'type' => 'image', 
            'name' => 'background', 
            'caption' => $this->view->_('BACKGROUND'), 
            'meta' => array('attachmentVar' => 'Background', 'deleteAttachmentPath' => $this->url . '&action=delete_template_image&id=' . (int)$Item->id)
        ));
        $Form = new RAASForm(array(
            'Item' => $Item, 'caption' => $this->view->_('EDIT_TEMPLATE'), 'parentUrl' => $this->url . '&action=templates'
        ));
        if ($Item->id) {
            $Form->children = array(
                new FormTab(array('name' => 'edit', 'caption' => $this->view->_('EDITING'), 'children' => array($NameField, $DescriptionField))),
                new FormTab(array(
                    'name' => 'layout',
                    'caption' => $this->view->_('LAYOUT'),
                    'children' => array(
                        new FieldSet(array(
                            'template' => 'dev_edit_template',
                            'export' => function($FormTab) {
                                $Item = $FormTab->Form->Item;
                                foreach (array('width', 'height') as $key) {
                                    if (isset($_POST[$key]) && (int)$_POST[$key]) {
                                        $Item->$key = (int)$_POST[$key];
                                    }
                                }
                                if (isset($_POST['location'])) {
                                    $Item->locs = new ArrayObject();
                                    foreach ($_POST['location'] as $key => $val) {
                                        $Item->locs[] = array(
                                            'urn' => isset($_POST['location'][$key]) ? (string)$_POST['location'][$key] : 'Location',
                                            'x' => isset($_POST['location-left'][$key]) ? (string)$_POST['location-left'][$key] : 0,
                                            'y' => isset($_POST['location-top'][$key]) ? (string)$_POST['location-top'][$key] : 0,
                                            'width' => isset($_POST['location-width'][$key]) ? (string)$_POST['location-width'][$key] : $Item->width,
                                            'height' => isset($_POST['location-height'][$key]) ? (string)$_POST['location-height'][$key] : Location::min_height,
                                        );
                                    }
                                }
                            }
                        )),
                        $BackgroundField
                    )
                ))
            );
        } else {
            $Form->children = array($NameField, $DescriptionField, $BackgroundField);
        }
        $this->view->edit_template($Form->process());
    }
    
    
    protected function edit_snippet_folder()
    {
        $Item = new Snippet_Folder((int)$this->id);
        if ($Item->locked) {
            exit;
        }
        $Form = new EditSnippetFolderForm(array('Item' => $Item, 'view' => $this->view));
        $this->view->edit_snippet_folder($Form->process());
    }
    
    
    protected function edit_snippet()
    {
        $Item = new Snippet((int)$this->id);
        if ($Item->locked) {
            exit;
        }
        $Form = new EditSnippetForm(array('Item' => $Item, 'view' => $this->view));
        $this->view->edit_snippet($Form->process());
    }
    
    
    protected function copy_snippet()
    {
        $Item = new Snippet((int)$this->id);
        $Item = $this->model->copyItem($Item);
        $Item->locked = 0;
        $Form = new CopySnippetForm(array('Item' => $Item, 'view' => $this->view));
        $this->view->edit_snippet($Form->process());
    }
    
    
    protected function edit_material_type()
    {
        $Item = new Material_Type((int)$this->id);
        $Form = new RAASForm(array(
            'Item' => $Item,
            'caption' => $Item->id ? $Item->name : $this->view->_('CREATING_MATERIAL_TYPE'),
            'parentUrl' => $this->url . '&action=material_types',
            'children' => array(
                array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'),
                array('name' => 'urn', 'caption' => $this->view->_('URN')),
                array('type' => 'checkbox', 'name' => 'global_type', 'caption' => $this->view->_('GLOBAL_MATERIALS'))
            )
        ));
        $this->view->edit_material_type($Form->process());
    }
    
    
    protected function edit_form()
    {
        $Item = new CMSForm((int)$this->id);
        $Form = new EditFormForm(array('Item' => $Item, 'view' => $this->view, ));
        $this->view->edit_form($Form->process());
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
        $Form = new EditFieldForm(array('Item' => $Item, 'view' => $this->view, 'meta' => array('Parent' => $Parent, 'parentUrl' => $parentUrl)));
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
}