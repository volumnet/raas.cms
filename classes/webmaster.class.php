<?php
namespace RAAS\CMS;
use \RAAS\Application;

class Webmaster 
{
    protected static $instance;

    public function __get($var)
    {
        switch ($var) {
            default:
                return \RAAS\CMS\Package::i()->__get($var);
                break;
        }
    }


    /**
     * Создаем стандартные сниппеты
     */
    public function checkStdSnippets()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_forms", $this->tables) && in_array(\SOME\SOME::_dbprefix() . "cms_forms", $this->tables)) {
            $Item = Snippet_Folder::importByURN('__RAAS_interfaces');
            if (!$Item->id) {
                $this->SQL->add(
                    \SOME\SOME::_dbprefix() . "cms_snippet_folders", 
                    array('urn' => '__RAAS_interfaces', 'name' => $this->view->_('INTERFACES'), 'pid' => 0, 'locked' => 1)
                );
            }
            $Item = Snippet_Folder::importByURN('__RAAS_views');
            if (!$Item->id) {
                $this->SQL->add(
                    \SOME\SOME::_dbprefix() . "cms_snippet_folders", 
                    array('urn' => '__RAAS_views', 'name' => $this->view->_('VIEWS'), 'pid' => 0, 'locked' => 1)
                );
            }

            $Item = Snippet::importByURN('__RAAS_material_interface');
            if (!$Item->id) {
                $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__RAAS_interfaces')->id, 'urn' => '__RAAS_material_interface', 'locked' => 1));
            }
            $Item->name = $this->view->_('MATERIAL_STANDARD_INTERFACE');
            $Item->description = $this->stdMaterialInterface;
            $Item->commit();

            $Item = Snippet::importByURN('__RAAS_form_interface');
            if (!$Item->id) {
                $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__RAAS_interfaces')->id, 'urn' => '__RAAS_form_interface', 'locked' => 1));
            }
            $Item->name = $this->view->_('FORM_STANDARD_INTERFACE');
            $Item->description = $this->stdFormInterface;
            $Item->commit();

            $Item = Snippet::importByURN('__RAAS_menu_interface');
            if (!$Item->id) {
                $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__RAAS_interfaces')->id, 'urn' => '__RAAS_menu_interface', 'locked' => 1));
            }
            $Item->name = $this->view->_('MENU_STANDARD_INTERFACE');
            $Item->description = $this->stdMenuInterface;
            $Item->commit();

            $Item = Snippet::importByURN('__RAAS_search_interface');
            if (!$Item->id) {
                $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__RAAS_interfaces')->id, 'urn' => '__RAAS_search_interface', 'locked' => 1));
            }
            $Item->name = $this->view->_('SEARCH_STANDARD_INTERFACE');
            $Item->description = $this->stdSearchInterface;
            $Item->commit();

            $Item = Snippet::importByURN('__RAAS_form_notify');
            if (!$Item->id) {
                $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__RAAS_interfaces')->id, 'urn' => '__RAAS_form_notify', 'locked' => 1));
            }
            $Item->name = $this->view->_('FORM_STANDARD_NOTIFICATION');
            $Item->description = $this->stdFormTemplate;
            $Item->commit();
        }
    }


    public function createSite() 
    {
        // Добавим стандартный шаблон
        $temp = Template::getSet();
        if (!$temp) {
            $T = new Template();
            $T->name = $this->view->_('MAIN_PAGE');
            $f = $this->resourcesDir . '/template.tmp.php';
            $T->description = file_get_contents($f);
            $T->commit();
        }

        // Добавим виджеты
        $snippets = array(
            'banners' => $this->view->_('BANNERS'), 
            'feedback' => $this->view->_('FEEDBACK'), 
            'feedback_inner' => $this->view->_('FEEDBACK_INNER'), 
            'head' => $this->view->_('HEAD'),
            'map' => $this->view->_('MAP'),
            'menu_content' => $this->view->_('SITEMAP'),
            'menu_top' => $this->view->_('TOP_MENU'),
            'search' => $this->view->_('SITE_SEARCH'),
            'sitemap_xml' => $this->view->_('SITEMAP_XML'),
        );
        $VF = Snippet_Folder::importByURN('__RAAS_views');
        foreach ($snippets as $urn => $name) {
            $temp = Snippet::importByURN($urn);
            if (!$temp->id) {
                $S = new Snippet();
                $S->name = $name;
                $S->urn = $urn;
                $S->pid = $VF->id;
                $f = $this->resourcesDir . '/' . $urn . '.php';
                $S->description = file_get_contents($f);
                $S->commit();
                // copy($f, $this->verstkaDir . '/' . $urn . '.php');
            }
        }

        $S = new Snippet();
        $S->name = $this->view->_('ROBOTS_TXT');
        $S->urn = 'robots';
        $S->pid = 0;
        $S->description = '';
        $S->commit();

        $temp = Material_Type::importByURN('banners');
        if (!$temp->id) {
            $MT = new Material_Type();
            $MT->name = $this->view->_('BANNERS');
            $MT->urn = 'banners';
            $MT->global_type = 1;
            $MT->commit();

            $F = new Material_Field();
            $F->pid = $MT->id;
            $F->name = $this->view->_('URL');
            $F->urn = 'url';
            $F->datatype = 'text';
            $F->show_in_table = 1;
            $F->commit();

            $F = new Material_Field();
            $F->pid = $MT->id;
            $F->name = $this->view->_('IMAGE');
            $F->urn = 'image';
            $F->datatype = 'image';
            $F->commit();
        }

        $temp = \RAAS\CMS\Form::getSet();
        if (!$temp) {
            $S = Snippet::importByURN('__RAAS_form_notify');
            $FRM = new \RAAS\CMS\Form();
            $FRM->name = $this->view->_('FEEDBACK');
            $FRM->create_feedback = 1;
            $FRM->signature = 0;
            $FRM->antispam = 'hidden';
            $FRM->antispam_field_name = 'name';
            $FRM->interface_id = (int)$S->id;
            $FRM->commit();

            $F = new Form_Field();
            $F->pid = $FRM->id;
            $F->name = $this->view->_('YOUR_NAME');
            $F->urn = 'full_name';
            $F->required = 1;
            $F->datatype = 'text';
            $F->show_in_table = 1;
            $F->commit();

            $F = new Form_Field();
            $F->pid = $FRM->id;
            $F->name = $this->view->_('PHONE');
            $F->urn = 'phone';
            $F->datatype = 'text';
            $F->show_in_table = 1;
            $F->commit();

            $F = new Form_Field();
            $F->pid = $FRM->id;
            $F->name = $this->view->_('EMAIL');
            $F->urn = 'email';
            $F->datatype = 'text';
            $F->show_in_table = 1;
            $F->commit();

            $F = new Form_Field();
            $F->pid = $FRM->id;
            $F->name = $this->view->_('QUESTION_TEXT');
            $F->urn = '_description_';
            $F->required = 1;
            $F->datatype = 'textarea';
            $F->commit();
        }

        if (!Page::getSet()) {
            $temp = Template::getSet();
            $Site = $this->createPage(array(
                'name' => $siteName, 
                'meta_title' => $siteName,
                'urn' =>  ($siteDomains ? $siteDomains . ' ' : '') . 'localhost ' . $_SERVER['HTTP_HOST'] . ' ' . $_SERVER['HTTP_HOST'] . '.volumnet.ru',
                'template' => ($temp ? $temp[0]->id : 0)
            ));

            if (!Menu::getSet()) {
                $M = new Menu();
                $M->page_id = $Site->id;
                $M->inherit = 10;
                $M->name = $this->view->_('TOP_MENU');
                $M->commit();

                $M = new Menu();
                $M->page_id = $Site->id;
                $M->inherit = 10;
                $M->name = $this->view->_('SITEMAP');
                $M->commit();
            }

            $contacts = $this->createPage(array('name' => $this->view->_('CONTACTS'), 'urn' => 'contacts'), $Site);
            $map = $this->createPage(array('name' => $this->view->_('SITEMAP'), 'urn' => 'map', 'response_code' => 200), $Site);
            $ajax = $this->createPage(array('name' => $this->view->_('AJAX'), 'urn' => 'ajax', 'template' => 0, 'cache' => 0, 'response_code' => 200), $Site);
            $feedbackAJAX = $this->createPage(array('name' => $this->view->_('FEEDBACK'), 'urn' => 'feedback', 'template' => 0, 'cache' => 0), $ajax);
            $p404 = $this->createPage(array('name' => $this->view->_('PAGE_404'), 'urn' => '404', 'response_code' => 404), $Site);
            $sitemaps = $this->createPage(array('name' => $this->view->_('SITEMAP_XML'), 'urn' => 'sitemaps', 'template' => 0, 'cache' => 0, 'response_code' => 200), $Site);
            $robots = $this->createPage(array('name' => $this->view->_('ROBOTS_TXT'), 'urn' => 'robots', 'template' => 0, 'cache' => 0, 'response_code' => 200), $Site);

            $B = new Block_HTML();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->description = $this->view->_('PAGE_UNDER_CONSTRUCTION');
            $B->cats = array((int)$Site->id);
            $B->commit();

            $S = Snippet::importByURN('map');
            $B = new Block_PHP();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->widget_id = $S->id;
            $B->cats = array($contacts->id);
            $B->commit();

            $B = new Block_HTML();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->name = $this->view->_('CONTACTS');
            $B->cats = array($contacts->id);
            $B->commit();

            $B = new Block_HTML();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->description = '<h3>' . $this->view->_('FEEDBACK') . '</h3>';
            $B->cats = array($contacts->id);
            $B->commit();

            $FRM = Form::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('FEEDBACK')) . "'"));
            $I = Snippet::importByURN('__RAAS_form_interface');
            $S = Snippet::importByURN('feedback');
            $B = new Block_Form();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->cats = array($contacts->id);
            $B->form = $FRM ? $FRM[0]->id : 0;
            $B->widget_id = $S->id;
            $B->interface_id = $I->id;
            $B->commit();

            $FRM = Form::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('FEEDBACK')) . "'"));
            $I = Snippet::importByURN('__RAAS_form_interface');
            $S = Snippet::importByURN('feedback_inner');
            $B = new Block_Form();
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->cats = array($feedbackAJAX->id);
            $B->form = $FRM ? $FRM[0]->id : 0;
            $B->widget_id = $S->id;
            $B->interface_id = $I->id;
            $B->commit();

            $B = new Block_HTML();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->description = $this->view->_('PAGE_404_TEXT');
            $B->cats = array($p404->id);
            $B->commit();

            $MNU = Menu::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('SITEMAP')) . "'"));
            $S = Snippet::importByURN('menu_content');
            $I = Snippet::importByURN('__RAAS_menu_interface');
            $B = new Block_Menu();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->widget_id = $S->id;
            $B->interface_id = $I->id;
            $B->menu = $MNU ? $MNU[0]->id : 0;
            $B->full_menu = 1;
            $B->cats = array($map->id);
            $B->commit();

            $S = Snippet::importByURN('sitemaps');
            $B = new Block_PHP();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->widget_id = $S->id;
            $B->cats = array($sitemaps->id);
            $B->commit();

            $S = Snippet::importByURN('robots');
            $B = new Block_PHP();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->widget_id = $S->id;
            $B->cats = array($robots->id);
            $B->commit();
        }
    }


    protected function createPage(array $params, Page $Parent = null)
    {
        $P = new Page();
        $P->vis = 1;
        $P->author_id = $P->editor_id = Application::i()->user->id;
        $P->inherit_meta_title = true;
        $P->cache = 1;
        $P->inherit_cache = 1;
        $P->inherit_template = 0;
        $P->lang = 'ru';
        $P->inherit_lang = 1;
        if ($Parent) {
            $P->pid = $Parent->id;
            foreach ($Parent->getArrayCopy() as $key => $val) {
                if (!in_array($key, array('id', 'pid'))) {
                    $P->$key = $val;
                }
            }
        }
        foreach ($params as $key => $val) {
            $P->$key = $val;
        }
        $P->commit();
        return $P;
    }


    public function createInfos($infos)
    {
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
        }
        $infos = preg_split('/\\r\\n|\\r|\\n/i', trim($infos));
        $temp = array();
        $backtrace = array();
        for ($i = 0; $i < count($infos); $i++) {
            preg_match('/^(\\-*)(.*?)$/i', $infos[$i], $regs);
            $step = strlen($regs[1]);
            list($name, $urn) = preg_split('/(:|;)/i', $regs[2]);
            $name = trim($name);
            $urn = trim($urn);

            if ($step > 0 && $backtrace && is_array($backtrace)) {
                $backtrace = array_slice((array)$backtrace, 0, $step);
                $context = $backtrace[count((array)$backtrace) - 1];
            } else {
                $backtrace = array();
                $context = 0;
            }
            $Parent = (int)$context ? new Page((int)$context) : $Site;
            $arr = array();
            $arr['name'] = $name;
            if ($urn) {
                $arr['urn'] = $urn;
            }
            $Page = $this->createPage(array('name' => $name, 'urn' => $urn), $Parent);
            $context = (int)$Page->id;
            $backtrace[] = $context;
        }
    }


    public function createNews($name, $urn)
    {
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
        }
        $temp = Material_Type::importByURN($urn);
        if (!$temp->id) {
            $MT = new Material_Type();
            $MT->name = $name;
            $MT->urn = $urn;
            $MT->global_type = 1;
            $MT->commit();

            $dateField = new Material_Field();
            $dateField->pid = $MT->id;
            $dateField->name = $this->view->_('DATE');
            $dateField->urn = 'date';
            $dateField->datatype = 'date';
            $dateField->show_in_table = 1;
            $dateField->commit();

            $F = new Material_Field();
            $F->pid = $MT->id;
            $F->name = $this->view->_('IMAGE');
            $F->multiple = 1;
            $F->urn = 'images';
            $F->datatype = 'image';
            $F->commit();

            $VF = Snippet_Folder::importByURN('__RAAS_views');
            $temp = Snippet::importByURN('news');
            if (!$temp->id) {
                $S = new Snippet();
                $S->name = $name;
                $S->urn = $urn;
                $S->pid = $VF->id;
                $f = $this->resourcesDir . '/news.php';
                $S->description = file_get_contents($f);
                $S->commit();
                copy($f, $this->verstkaDir . '/news.php');
            }
            
            $page = $this->createPage(array('name' => $name, 'urn' => $urn), $Site);
            $I = Snippet::importByURN('__RAAS_material_interface');
            $S = Snippet::importByURN('news');
            $B = new Block_Material();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->interface_id = $I->id;
            $B->widget_id = $S->id;
            $B->cats = array($page->id);
            $B->material_type = (int)$MT->id;
            $B->nat = 1;
            $B->pages_var_name = 'page';
            $B->rows_per_page = 20;
            $B->sort_field_default = $dateField->id;
            $B->sort_order_default = 'desc!';
            $B->commit();
        }
    }


    public function createSearch()
    {
        $name = $this->view->_('SEARCH');
        $urn = 'search';
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
        }
        $temp = Snippet::importByURN($urn);
        if (!$temp->id) {
            $VF = Snippet_Folder::importByURN('__RAAS_views');
            $temp = Snippet::importByURN($urn);
            if (!$temp->id) {
                $S = new Snippet();
                $S->name = $name;
                $S->urn = $urn;
                $S->pid = $VF->id;
                $f = $this->resourcesDir . '/' . $urn . '.php';
                $S->description = file_get_contents($f);
                $S->commit();
                copy($f, $this->verstkaDir . '/' . $urn . '.php');
            }

            $page = $this->createPage(array('name' => $name, 'urn' => $urn, 'response_code' => 200), $Site);
            $I = Snippet::importByURN('__RAAS_search_interface');
            $S = Snippet::importByURN($urn);
            $B = new Block_Search();
            $B->location = 'content';
            $B->vis = 1;
            $B->author_id = $B->editor_id = Application::i()->user->id;
            $B->interface_id = $I->id;
            $B->widget_id = $S->id;
            $B->cats = array($page->id);
            $B->search_var_name = 'search_string';
            $B->min_length = 3;
            $B->pages_var_name = 'page';
            $B->rows_per_page = 20;
            $B->commit();
        }
    }


    public function createFAQ($name, $urn)
    {
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
            $ajax = array_shift(array_filter($Site->children, function($x) { return $x->urn == 'ajax'; }));
        }
        $MT = new Material_Type();
        $MT->name = $name;
        $MT->urn = $urn;
        $MT->global_type = 1;
        $MT->commit();

        $F = new Material_Field();
        $F->pid = $MT->id;
        $F->name = $this->view->_('PHONE');
        $F->urn = 'phone';
        $F->datatype = 'text';
        $F->commit();

        $F = new Material_Field();
        $F->pid = $MT->id;
        $F->name = $this->view->_('ANSWER');
        $F->urn = 'answer';
        $F->datatype = 'textarea';
        $F->commit();

        $S = Snippet::importByURN('__RAAS_form_notify');
        $FRM = new \RAAS\CMS\Form();
        $FRM->name = $name;
        $FRM->material_type = (int)$MT->id;
        $FRM->create_feedback = 0;
        $FRM->signature = 0;
        $FRM->antispam = 'hidden';
        $FRM->antispam_field_name = 'name';
        $FRM->interface_id = (int)$S->id;
        $FRM->commit();

        $F = new Form_Field();
        $F->pid = $FRM->id;
        $F->name = $this->view->_('YOUR_NAME');
        $F->urn = 'name';
        $F->required = 1;
        $F->datatype = 'text';
        $F->show_in_table = 1;
        $F->commit();

        $F = new Form_Field();
        $F->pid = $FRM->id;
        $F->name = $this->view->_('PHONE');
        $F->urn = 'phone';
        $F->datatype = 'text';
        $F->show_in_table = 1;
        $F->commit();

        $F = new Form_Field();
        $F->pid = $FRM->id;
        $F->name = $this->view->_('QUESTION_TEXT');
        $F->urn = 'description';
        $F->required = 1;
        $F->datatype = 'textarea';
        $F->show_in_table = 0;
        $F->commit();

        $VF = Snippet_Folder::importByURN('__RAAS_views');
        $temp = Snippet::importByURN('faq');
        if (!$temp->id) {
            $S = new Snippet();
            $S->name = $this->view->_('FAQ');
            $S->urn = 'faq';
            $S->pid = $VF->id;
            $f = $this->resourcesDir . '/faq.php';
            $S->description = file_get_contents($f);
            $S->commit();
            copy($f, $this->verstkaDir . '/faq.php');
        }

        $faqPage = $this->createPage(array('name' => $name, 'urn' => $urn), $Site);
        $faqAJAX = $this->createPage(array('name' => $name, 'urn' => $urn, 'template' => 0, 'cache' => 0), $ajax);

        $B = new Block_HTML();
        $B->location = 'content';
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        $B->description = '<p>' . $this->view->_('YOU_CAN_ASK_YOUR_QUESTION') . '</p>';
        $B->cats = array($faqPage->id);
        $B->commit();

        $I = Snippet::importByURN('__RAAS_form_interface');
        $S = Snippet::importByURN('feedback');
        $B = new Block_Form();
        $B->location = 'content';
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        $B->cats = array($faqPage->id);
        $B->form = $FRM ? $FRM[0]->id : 0;
        $B->widget_id = $S->id;
        $B->interface_id = $I->id;
        $B->commit();

        $I = Snippet::importByURN('__RAAS_form_interface');
        $S = Snippet::importByURN('feedback_inner');
        $B = new Block_Form();
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        $B->cats = array($faqAJAX->id);
        $B->form = $FRM ? $FRM[0]->id : 0;
        $B->widget_id = $S->id;
        $B->interface_id = $I->id;
        $B->commit();
    
        $I = Snippet::importByURN('__RAAS_material_interface');
        $S = Snippet::importByURN('faq');
        $B = new Block_Material();
        $B->location = 'content';
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        $B->interface_id = $I->id;
        $B->widget_id = $S->id;
        $B->cats = array($faqPage->id);
        $B->material_type = (int)$MT->id;
        $B->nat = 1;
        $B->pages_var_name = 'page';
        $B->rows_per_page = 20;
        $B->sort_field_default = 'post_date';
        $B->sort_order_default = 'desc!';
        $B->commit();
    }


    /**
     * @todo Проверить работу, возможно работает некорректно
     */
    public function createFeedback($name, $urn)
    {
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
            $ajax = array_shift(array_filter($Site->children, function($x) { return $x->urn == 'ajax'; }));
        }
        $S = Snippet::importByURN('__RAAS_form_notify');
        $FRM = new \RAAS\CMS\Form();
        $FRM->name = $name;
        $FRM->create_feedback = 1;
        $FRM->signature = 0;
        $FRM->antispam = 'hidden';
        $FRM->antispam_field_name = 'name';
        $FRM->interface_id = (int)$S->id;
        $FRM->commit();

        $F = new Form_Field();
        $F->pid = $FRM->id;
        $F->name = $this->view->_('YOUR_NAME');
        $F->urn = 'full_name';
        $F->required = 1;
        $F->datatype = 'text';
        $F->show_in_table = 1;
        $F->commit();

        $F = new Form_Field();
        $F->pid = $FRM->id;
        $F->name = $this->view->_('PHONE');
        $F->urn = 'phone';
        $F->datatype = 'text';
        $F->show_in_table = 1;
        $F->commit();

        $F = new Form_Field();
        $F->pid = $FRM->id;
        $F->name = $this->view->_('EMAIL');
        $F->urn = 'email';
        $F->datatype = 'text';
        $F->show_in_table = 1;
        $F->commit();

        $F = new Form_Field();
        $F->pid = $FRM->id;
        $F->name = $this->view->_('QUESTION_TEXT');
        $F->urn = '_description_';
        $F->required = 1;
        $F->datatype = 'textarea';
        $F->commit();

        $contacts = $this->createPage(array('name' => $name, 'urn' => $urn), $Site);
        $feedbackAJAX = $this->createPage(array('name' => $name, 'urn' => $urn, 'template' => 0, 'cache' => 0), $ajax);
        
        $B = new Block_HTML();
        $B->location = 'content';
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        $B->description = '<h3>' . $this->view->_('FEEDBACK') . '</h3>';
        $B->cats = array($contacts->id);
        $B->commit();

        $I = Snippet::importByURN('__RAAS_form_interface');
        $S = Snippet::importByURN('feedback');
        $B = new Block_Form();
        $B->location = 'content';
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        $B->cats = array($contacts->id);
        $B->form = $FRM ? $FRM[0]->id : 0;
        $B->widget_id = $S->id;
        $B->interface_id = $I->id;
        $B->commit();

        $I = Snippet::importByURN('__RAAS_form_interface');
        $S = Snippet::importByURN('feedback_inner');
        $B = new Block_Form();
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        $B->cats = array($feedbackAJAX->id);
        $B->form = $FRM ? $FRM[0]->id : 0;
        $B->widget_id = $S->id;
        $B->interface_id = $I->id;
        $B->commit();
    }
}