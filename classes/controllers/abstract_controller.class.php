<?php
namespace RAAS\CMS;
use \RAAS\Application as Application;
use \RAAS\Redirector as Redirector;
use \RAAS\Attachment as Attachment;
use \ArrayObject as ArrayObject;
use \RAAS\Field as Field;
use \RAAS\FieldSet as FieldSet;
use \RAAS\FieldContainer as FieldContainer;
use \RAAS\Form as Form;
use \RAAS\FormTab as FormTab;
use \RAAS\CMS\Form as CMSForm;
use \RAAS\OptGroup as OptGroup;
use \RAAS\Option as Option;
use \RAAS\StdSub as StdSub;

abstract class Abstract_Controller extends \RAAS\Abstract_Package_Controller
{
    protected static $instance;
    
    protected function execute()
    {
        switch ($this->sub) {
            case 'dev': case 'feedback':
                parent::execute();
                break;
            default:
                switch ($this->action) {
                    case 'edit': case 'move':
                        $this->{$this->action . '_page'}();
                        break;
                    case 'edit_block': case 'edit_material': 
                        $this->{$this->action}();
                        break;
                    case 'chvis': case 'delete': case 'move_up': case 'move_down':
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
                break;
        }
        $this->model->cleanCache();
    }
    
    
    protected function show_page()
    {
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
        $t = $this;
        $CONTENT = array();
        $CONTENT['templates'] = array('Set' => array_merge(array(new Template(array('id' => 0, 'name' => $this->view->_('NOT_SELECTED')))), Template::getSet()));
        $CONTENT['languages'] = array();
        foreach ($this->view->availableLanguages as $key => $val) {
            $CONTENT['languages'][] = array('value' => $key, 'caption' => $val);
        }
        
        if ($Parent->id) {
            $title = $Item->id ? $this->view->_('EDITING_PAGE') : $this->view->_('CREATING_PAGE');
        } else {
            $title = $Item->id ? $this->view->_('EDITING_SITE') : $this->view->_('CREATING_SITE');
        }
        $Form = new Form(array(
            'Item' => $Item, 
            'parentUrl' => $this->url . '&id=%s', 
            'caption' => $title,
            'export' => function($Form) use ($t, $Parent) {
                $Form->exportDefault();
                $Form->Item->editor_id = $t->application->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->pid = $Parent->id;
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            }
        ));
        $commonTab = new FormTab(array('name' => 'common', 'caption' => $this->view->_('GENERAL'), 'children' => array(array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'))));
        $serviceTab = new FormTab(array(
            'name' => 'service', 
            'caption' => $this->view->_('SERVICE'), 
            'children' => array(
                array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_($Parent->id ? 'VISIBLE' : 'IS_ACTIVE'), 'default' => 1),
                array(
                    'name' => 'response_code', 
                    'class' => 'span1', 
                    'maxlength' => 3, 
                    'caption' => $this->view->_('SERVICE_RESPONSE_CODE'), 
                    'data-hint' => $this->view->_('SERVICE_PAGE_DESCRIPTION'), 
                    'import' => function() use ($Item) { return (int)$Item->response_code ? (int)$Item->response_code : ''; }
                ),
                array('type' => 'checkbox', 'name' => 'nat', 'caption' => $this->view->_('TRANSLATE_ADDRESS')),
                new FieldSet(array(
                    'template' => 'edit_page.inherit.php',
                    'children' => array(
                        array('type' => 'checkbox', 'name' => 'cache', 'caption' => $this->view->_('CACHE_PAGE')),
                        array('type' => 'checkbox', 'name' => 'inherit_cache', 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->inherit_cache : 1))
                    )
                )),
                new FieldSet(array(
                    'template' => 'edit_page.inherit.php',
                    'children' => array(
                        array('type' => 'select', 'name' => 'template', 'caption' => $this->view->_('TEMPLATE'), 'children' => $CONTENT['templates'], 'default' => ($Parent->id ? $Parent->template : 0)),
                        array('type' => 'checkbox', 'name' => 'inherit_template', 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->inherit_template : 1))
                    )
                )),
                new FieldSet(array(
                    'template' => 'edit_page.inherit.php',
                    'children' => array(
                        array('type' => 'select', 'name' => 'lang', 'caption' => $this->view->_('LANGUAGE'), 'children' => $CONTENT['languages'], 'default' => ($Parent->id ? $Parent->lang : $this->view->language)),
                        array('type' => 'checkbox', 'name' => 'inherit_lang', 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->inherit_lang : 1))
                    )
                )),
            )
        ));


        if ($Parent->id) {
            $commonTab->children[] = array('name' => 'urn', 'caption' => $this->view->_('URN'));
        } else {
            $commonTab->children[] = array('name' => 'urn', 'caption' => $this->view->_('DOMAIN_NAMES'), 'required' => 'required');
        }
        foreach (array('meta_title', 'meta_description', 'meta_keywords') as $key) {
            $commonTab->children[] = new FieldSet(array(
                'template' => 'edit_page.inherit.php',
                'children' => array(
                    array('name' => $key, 'caption' => $this->view->_(strtoupper($key)), 'default' => ($Parent->id ? $Parent->$key : '')), 
                    array('type' => 'checkbox', 'name' => 'inherit_' . $key, 'caption' => $this->view->_('INHERIT'), 'default' => ($Parent->id ? $Parent->{'inherit_' . $key} : 1))
                )
            ));
        }
        if ($Item->id) {
            $serviceTab->children[] = array('name' => 'post_date', 'caption' => $this->view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'modify_date', 'caption' => $this->view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
        }
        foreach ($Item->fields as $row) {
            $f = $row->Field;
            $commonTab->children[] = new FieldSet(array(
                'template' => 'edit_page.inherit.php',
                'children' => array(
                    $f,
                    array(
                        'type' => 'checkbox', 
                        'name' => 'inherit_' . $row->Field->name, 
                        'caption' => $this->view->_('INHERIT'), 
                        'default' => ($Parent->id ? $Parent->{'inherit_' . $row->Field->name} : 1), 
                        'oncommit' => function() use ($row) {
                            if ($_POST['inherit_' . $row->Field->name]) {
                                $row->inheritValues();
                            }
                        },
                        'import' => function() use ($row) { return $row->inherited; }
                    )
                ),
            ));
        }

        $Form->children = array($commonTab, $serviceTab);
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
        $arr = array('Item' => $Item, 'view' => $this->view, 'meta' => array('Parent' => $Parent));
        
        $Form = $blockType->getForm($arr);
        // switch ($Item->block_type) {
        //     case 'RAAS\\CMS\\Block_PHP':
        //         $Form = new EditBlockPHPForm($arr);
        //         break;
        //     case 'RAAS\\CMS\\Block_Material':
        //         $Form = new EditBlockMaterialForm($arr);
        //         break;
        //     case 'RAAS\\CMS\\Block_Menu':
        //         $Form = new EditBlockMenuForm($arr);
        //         break;
        //     case 'RAAS\\CMS\\Block_Form':
        //         $Form = new EditBlockFormForm($arr);
        //         break;
        //     case 'RAAS\\CMS\\Block_Search':
        //         $Form = new EditBlockSearchForm($arr);
        //         break;
        //     default:
        //         $Form = new EditBlockHTMLForm($arr);
        //         break;
        // }
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
        if (!$Item->id) {
            $Item->pid = (int)$Type->id;
        }
        $Parent = new Page();
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
        
        if (!$Type->id) {
            new Redirector($this->url . '&id=' . (int)$Parent->id);
        }
        $t = $this;

        $CONTENT = array();
        $temp = new Page();
        $CONTENT['cats'] = array('Set' => $temp->children);
        
        if ($Parent->id) {
            $title = $Item->id ? $this->view->_('EDITING_PAGE') : $this->view->_('CREATING_PAGE');
        } else {
            $title = $Item->id ? $this->view->_('EDITING_SITE') : $this->view->_('CREATING_SITE');
        }
        $Form = new Form(array(
            'Item' => $Item, 
            'parentUrl' => $this->url . '&id=' . $Parent->id . '#_' . $Type->urn, 
            'caption' => $Item->id ? $Item->name : $this->_('CREATING_MATERIAL'),
            'export' => function($Form) use ($t, $Parent) {
                $Form->exportDefault();
                $Form->Item->editor_id = $t->application->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            }
        ));
        $commonTab = new FormTab(array(
            'name' => 'common', 
            'caption' => $this->view->_('GENERAL'), 
            'children' => array(
                array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'),
                array('type' => 'htmlarea', 'name' => 'description', 'caption' => $this->view->_('DESCRIPTION'))
            )
        ));
        $seoTab = new FormTab(array(
            'name' => 'seo', 
            'caption' => $this->view->_('SEO'), 
            'children' => array(
                array('name' => 'urn', 'caption' => $this->view->_('URN')),
                array('name' => 'meta_title', 'caption' => $this->view->_('META_TITLE')),
                array('name' => 'meta_description', 'caption' => $this->view->_('META_DESCRIPTION')),
                array('name' => 'meta_keywords', 'caption' => $this->view->_('META_KEYWORDS'))
            )
        ));
        $serviceTab = new FormTab(array(
            'name' => 'service', 
            'caption' => $this->view->_('SERVICE'), 
            'children' => array(array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_($Parent->id ? 'VISIBLE' : 'IS_ACTIVE'), 'default' => 1))
        ));
        if ($Item->id) {
            $serviceTab->children[] = array('name' => 'post_date', 'caption' => $this->view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'modify_date', 'caption' => $this->view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
        }
        foreach ($Item->fields as $row) {
            $commonTab->children[] = $row->Field;
        }

        $Form->children = array($commonTab, $seoTab, $serviceTab);
        if (!$Type->global_type){
            $Form->children[] = new FormTab(array(
                'name' => 'pages',
                'caption' => $this->view->_('PAGES'),
                'children' => array(
                    array(
                        'type' => 'checkbox', 
                        'multiple' => true, 
                        'name' => 'cats', 
                        'caption' => $this->view->_('PAGES'),
                        'required' => 'required', 
                        'children' => $CONTENT['cats'], 
                        'default' => array((int)$Parent->id),
                        'import' => function($Field) { return $Field->Form->Item->pages_ids; }
                    )
                )
            ));
        }
        $OUT = $Form->process();
        $OUT['Parent'] = $Parent;
        $OUT['Type'] = $Type;
        $this->view->edit_material($OUT);
    }
    
    
    public function config()
    {
        return array(
            array('type' => 'number', 'name' => 'tnsize', 'caption' => $this->view->_('THUMBNAIL_SIZE')),
            array('type' => 'number', 'name' => 'maxsize', 'caption' => $this->view->_('MAX_IMAGE_SIZE'))
        );
    }
}