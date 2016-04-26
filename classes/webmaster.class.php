<?php
namespace RAAS\CMS;

use \RAAS\Application;
use \RAAS\Attachment;
use \SOME\SOME;
use Mustache_Engine;

class Webmaster 
{
    protected static $instance;

    public function __get($var)
    {
        switch ($var) {
            default:
                return Package::i()->__get($var);
                break;
        }
    }


    /**
     * Создаем стандартные сниппеты
     */
    public function checkStdSnippets()
    {
        $Item = Snippet_Folder::importByURN('__raas_interfaces');
        if (!$Item->id) {
            $this->SQL->add(
                SOME::_dbprefix() . "cms_snippet_folders", 
                array('urn' => '__raas_interfaces', 'name' => $this->view->_('INTERFACES'), 'pid' => 0, 'locked' => 1)
            );
        }
        $Item = Snippet_Folder::importByURN('__raas_views');
        if (!$Item->id) {
            $this->SQL->add(
                SOME::_dbprefix() . "cms_snippet_folders", 
                array('urn' => '__raas_views', 'name' => $this->view->_('VIEWS'), 'pid' => 0, 'locked' => 1)
            );
        }

        $Item = Snippet::importByURN('__raas_material_interface');
        if (!$Item->id) {
            $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => '__raas_material_interface', 'locked' => 1));
        }
        $Item->name = $this->view->_('MATERIAL_STANDARD_INTERFACE');
        $Item->description = $this->stdMaterialInterface;
        $Item->commit();

        $Item = Snippet::importByURN('__raas_form_interface');
        if (!$Item->id) {
            $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => '__raas_form_interface', 'locked' => 1));
        }
        $Item->name = $this->view->_('FORM_STANDARD_INTERFACE');
        $Item->description = $this->stdFormInterface;
        $Item->commit();

        $Item = Snippet::importByURN('__raas_menu_interface');
        if (!$Item->id) {
            $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => '__raas_menu_interface', 'locked' => 1));
        }
        $Item->name = $this->view->_('MENU_STANDARD_INTERFACE');
        $Item->description = $this->stdMenuInterface;
        $Item->commit();

        $Item = Snippet::importByURN('__raas_search_interface');
        if (!$Item->id) {
            $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => '__raas_search_interface', 'locked' => 1));
        }
        $Item->name = $this->view->_('SEARCH_STANDARD_INTERFACE');
        $Item->description = $this->stdSearchInterface;
        $Item->commit();

        $Item = Snippet::importByURN('__raas_form_notify');
        if (!$Item->id) {
            $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => '__raas_form_notify', 'locked' => 1));
        }
        $Item->name = $this->view->_('FORM_STANDARD_NOTIFICATION');
        $Item->description = $this->stdFormTemplate;
        $Item->commit();

        $Item = Snippet::importByURN('__raas_cache_interface');
        if (!$Item->id) {
            $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => '__raas_cache_interface', 'locked' => 1));
        }
        $Item->name = $this->view->_('CACHE_STANDARD_INTERFACE');
        $Item->description = $this->stdCacheInterface;
        $Item->commit();

        $Item = Snippet::importByURN('__raas_watermark_interface');
        if (!$Item->id) {
            $Item = new Snippet(array('pid' => Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => '__raas_watermark_interface', 'locked' => 1));
        }
        $Item->name = $this->view->_('WATERMARK_STANDARD_INTERFACE');
        $Item->description = $this->stdWatermarkInterface;
        $Item->commit();
    }


    public function createSite() 
    {
        // Добавим стандартный шаблон
        $temp = Template::getSet();
        if (!$temp) {
            $T = new Template();
            $T->name = $this->view->_('MAIN_PAGE');
            $T->urn = 'main';
            $f = $this->resourcesDir . '/template.tmp.php';
            $T->description = file_get_contents($f);
            $T->locations_info = '['
                               .    '{"urn":"logo","x":"10","y":"0","width":"150","height":"120"},'
                               .    '{"urn":"contacts_top","x":"500","y":"0","width":"150","height":"120"},'
                               .    '{"urn":"menu_top","x":"10","y":"130","width":"640","height":"60"},'
                               .    '{"urn":"banners","x":"10","y":"200","width":"640","height":"60"},'
                               .    '{"urn":"left","x":"10","y":"270","width":"150","height":"220"},'
                               .    '{"urn":"content","x":"170","y":"270","width":"320","height":"220"},'
                               .    '{"urn":"right","x":"500","y":"270","width":"150","height":"220"},'
                               .    '{"urn":"content2","x":"10","y":"500","width":"640","height":"90"},'
                               .    '{"urn":"content3","x":"170","y":"600","width":"320","height":"90"},'
                               .    '{"urn":"content4","x":"10","y":"700","width":"640","height":"90"},'
                               .    '{"urn":"content5","x":"170","y":"800","width":"320","height":"90"},'
                               .    '{"urn":"share","x":"170","y":"900","width":"320","height":"60"},'
                               .    '{"urn":"copyrights","x":"10","y":"960","width":"150","height":"120"},'
                               .    '{"urn":"menu_bottom","x":"500","y":"960","width":"150","height":"120"},'
                               .    '{"urn":"head_counters","x":"10","y":"1090","width":"315","height":"220"},'
                               .    '{"urn":"footer_counters","x":"335","y":"1090","width":"315","height":"220"}'
                               . ']';
            $T->width = 660;
            $T->height = 1320;
            $T->commit();
        }


        // Добавим поля страниц
        $pf = new Page_Field(array('name' => $this->view->_('DESCRIPTION'), 'urn' => '_description_', 'datatype' => 'htmlarea'));
        $pf->commit();
        $pf = new Page_Field(array('name' => $this->view->_('IMAGE'), 'urn' => 'image', 'datatype' => 'image'));
        $pf->commit();
        $pf = new Page_Field(array('name' => $this->view->_('NO_INDEX'), 'urn' => 'noindex', 'datatype' => 'checkbox'));
        $pf->commit();
        $pf = new Page_Field(array('name' => $this->view->_('BACKGROUND'), 'urn' => 'background', 'datatype' => 'image'));
        $pf->commit();
        

        // Добавим виджеты
        $snippets = array(
            'banners' => $this->view->_('BANNERS'), 
            'feedback' => $this->view->_('FEEDBACK'), 
            'feedback_modal' => $this->view->_('FEEDBACK_MODAL'), 
            'head' => $this->view->_('HEAD_TAG'),
            'order_call_modal' => $this->view->_('ORDER_CALL_MODAL'), 
            // 'search' => $this->view->_('SITE_SEARCH'),
            'sitemap_xml' => $this->view->_('SITEMAP_XML'),
            'logo' => $this->view->_('LOGO'),
            'robots' => $this->view->_('ROBOTS_TXT'),
            'menu_content' => $this->view->_('SITEMAP'),
            'menu_top' => $this->view->_('TOP_MENU'),
            'menu_left' => $this->view->_('LEFT_MENU'),
            'menu_bottom' => $this->view->_('BOTTOM_MENU'),
        );
        $VF = Snippet_Folder::importByURN('__raas_views');
        foreach ($snippets as $urn => $name) {
            $temp = Snippet::importByURN($urn);
            if (!$temp->id) {
                $S = new Snippet();
                $S->name = $name;
                $S->urn = $urn;
                $S->pid = $VF->id;
                if (stristr($urn, 'menu_')) {
                    $f = $this->resourcesDir . '/menu.tmp.php';
                    $S->description = str_ireplace('{MENU_NAME}', $urn, file_get_contents($f));
                } else {
                    $f = $this->resourcesDir . '/' . $urn . '.tmp.php';
                    $S->description = file_get_contents($f);
                }
                $S->commit();
            }
        }

        // Добавим типы материалов
        $MT = Material_Type::importByURN('banners');
        if (!$MT->id) {
            $MT = new Material_Type(array('name' => $this->view->_('BANNERS'), 'urn' => 'banners', 'global_type' => 1,));
            $MT->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('URL'), 'urn' => 'url', 'datatype' => 'text', 'show_in_table' => 1,));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('IMAGE'), 'urn' => 'image', 'datatype' => 'image', 'show_in_table' => 1,));
            $F->commit();
        }

        // Добавим формы
        $temp = Form::getSet();
        if (!$temp) {
            $snippetFormNotify = Snippet::importByURN('__raas_form_notify');
            $FRM = new Form(array(
                'name' => $this->view->_('FEEDBACK'), 
                'urn' => 'feedback',
                'create_feedback' => 1,
                'signature' => 1,
                'antispam' => 'hidden',
                'antispam_field_name' => '_name',
                'interface_id' => (int)$snippetFormNotify->id,
            ));
            $FRM->commit();

            $F = new Form_Field(array(
                'pid' => $FRM->id, 'name' => $this->view->_('YOUR_NAME'), 'urn' => 'full_name', 'required' => 1, 'datatype' => 'text', 'show_in_table' => 1,
            ));
            $F->commit();

            $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('PHONE'), 'urn' => 'phone', 'datatype' => 'text', 'show_in_table' => 1,));
            $F->commit();

            $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('EMAIL'), 'urn' => 'email', 'datatype' => 'text', 'show_in_table' => 1,));
            $F->commit();

            $F = new Form_Field(array(
                'pid' => $FRM->id, 'name' => $this->view->_('QUESTION_TEXT'), 'urn' => '_description_', 'required' => 1, 'datatype' => 'textarea',
            ));
            $F->commit();


            $FRM = new Form(array(
                'name' => $this->view->_('ORDER_CALL'), 
                'urn' => 'order_call',
                'create_feedback' => 1,
                'signature' => 1,
                'antispam' => 'hidden',
                'antispam_field_name' => '_name',
                'interface_id' => (int)$snippetFormNotify->id,
            ));
            $FRM->commit();

            $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('PHONE'), 'urn' => 'phone_call', 'datatype' => 'text', 'show_in_table' => 1,));
            $F->commit();
        }

        if (!Page::getSet()) {
            $temp = Template::getSet();
            $host = $_SERVER['HTTP_HOST'];
            $Site = $this->createPage(array(
                'name' => $this->view->_('MAIN_PAGE'), 'urn' => $host . ' ' . $host . '.volumnet.ru', 'template' => ($temp ? $temp[0]->id : 0)
            ));
            $about = $this->createPage(array('name' => $this->view->_('ABOUT_US'), 'urn' => 'about'), $Site, true);
            $services = $this->createPage(array('name' => $this->view->_('OUR_SERVICES'), 'urn' => 'services'), $Site, true);
            for ($i = 1; $i <= 3; $i++) {
                $service = $this->createPage(array('name' => $this->view->_('OUR_SERVICE') . ' ' . $i, 'urn' => 'service' . $i), $services, true);
            }

            $this->createNews($this->view->_('NEWS'), 'news', $this->view->_('NEWS_MAIN'));
            $contacts = $this->createPage(array('name' => $this->view->_('CONTACTS'), 'urn' => 'contacts'), $Site);
            $map = $this->createPage(array('name' => $this->view->_('SITEMAP'), 'urn' => 'map', 'response_code' => 200), $Site);
            $p404 = $this->createPage(array('name' => $this->view->_('PAGE_404'), 'urn' => '404', 'response_code' => 404), $Site);
            $sitemaps = $this->createPage(array('name' => $this->view->_('SITEMAP_XML'), 'urn' => 'sitemaps', 'template' => 0, 'cache' => 0, 'response_code' => 200), $Site);
            $robots = $this->createPage(array('name' => $this->view->_('ROBOTS_TXT'), 'urn' => 'robots', 'template' => 0, 'cache' => 0, 'response_code' => 200), $Site);

            if (!Menu::getSet()) {
                $MNU = new Menu(array('page_id' => $Site->id, 'urn' => 'top', 'inherit' => 10, 'name' => $this->view->_('TOP_MENU'),));
                $MNU->commit();

                $MNU = new Menu(array('page_id' => $Site->id, 'urn' => 'bottom', 'inherit' => 1, 'name' => $this->view->_('BOTTOM_MENU'),));
                $MNU->commit();

                // $MNU = new Menu(array('page_id' => $Site->id, 'urn' => 'left', 'inherit' => 10, 'name' => $this->view->_('LEFT_MENU'),));
                // $MNU->commit();

                $MNU = new Menu(array('page_id' => $Site->id, 'urn' => 'sitemap', 'inherit' => 10, 'name' => $this->view->_('SITEMAP'),));
                $MNU->commit();
            }

            $B = new Block_HTML(array('name' => $this->view->_('LOGO'), 'description' => file_get_contents($this->resourcesDir . '/logo_block.tmp.php'), 'wysiwyg' => 1,));
            $this->createBlock($B, 'logo', null, 'logo', $Site, true);

            $B = new Block_HTML(array('name' => $this->view->_('CONTACTS'), 'description' => file_get_contents($this->resourcesDir . '/contacts_top.tmp.php'), 'wysiwyg' => 0,));
            $this->createBlock($B, 'contacts_top', null, null, $Site, true);

            $MNU = Menu::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('TOP_MENU')) . "'"));
            $MNU = $MNU ? $MNU[0] : null;
            $B = new Block_Menu(array('menu' => $MNU->id ?: 0, 'full_menu' => 1,));
            $this->createBlock($B, 'menu_top', '__raas_menu_interface', 'menu_top', $Site, true);

            // $MNU = Menu::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('LEFT_MENU')) . "'"));
            // $MNU = $MNU ? $MNU[0] : null;
            // $B = new Block_Menu(array('menu' => $MNU->id ?: 0, 'full_menu' => 1,));
            // $this->createBlock($B, 'left', '__raas_menu_interface', 'menu_left', $Site, true);

            $B = new Block_HTML(array('name' => $this->view->_('COPYRIGHTS'), 'description' => file_get_contents($this->resourcesDir . '/copyrights.tmp.php'), 'wysiwyg' => 1,));
            $this->createBlock($B, 'copyrights', null, null, $Site, true);

            $B = new Block_HTML(array('name' => $this->view->_('SHARE'), 'description' => file_get_contents($this->resourcesDir . '/share.tmp.php'), 'wysiwyg' => 0,));
            $this->createBlock($B, 'share', null, null, $Site, true);

            $MNU = Menu::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('BOTTOM_MENU')) . "'"));
            $MNU = $MNU ? $MNU[0] : null;
            $B = new Block_Menu(array('menu' => $MNU->id ?: 0, 'full_menu' => 1,));
            $this->createBlock($B, 'menu_bottom', '__raas_menu_interface', 'menu_bottom', $Site, true);

            $FRM = Form::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('FEEDBACK')) . "'"));
            $FRM = $FRM ? $FRM[0] : null;
            $B = new Block_Form(array('form' => $FRM->id ?: 0,));
            $this->createBlock($B, 'footer_counters', '__raas_form_interface', 'feedback_modal', $Site, true);

            $FRM = Form::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('ORDER_CALL')) . "'"));
            $FRM = $FRM ? $FRM[0] : null;
            $B = new Block_Form(array('form' => $FRM->id ?: 0,));
            $this->createBlock($B, 'footer_counters', '__raas_form_interface', 'order_call_modal', $Site, true);

            $B = new Block_HTML(array('name' => $this->view->_('YANDEX_METRIKA'), 'description' => '', 'wysiwyg' => 0,));
            $this->createBlock($B, 'footer_counters', null, null, $Site, true);

            $B = new Block_HTML(array('name' => $this->view->_('GOOGLE_ANALYTICS'), 'description' => '', 'wysiwyg' => 0,));
            $this->createBlock($B, 'head_counters', null, null, $Site, true);

            $B = new Block_HTML(array('name' => $this->view->_('TRIGGERS'), 'description' => file_get_contents($this->resourcesDir . '/triggers.tmp.php'), 'wysiwyg' => 0,));
            $this->createBlock($B, 'footer_counters', null, null, $Site, true);

            $MT = Material_Type::importByURN('banners');
            $B = new Block_Material(array(
                'material_type' => (int)$MT->id, 
                'nat' => 0,
                'pages_var_name' => 'page',
                'rows_per_page' => 0,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'asc',
            ));
            $this->createBlock($B, 'banners', '__raas_material_interface', 'banners', $Site);
            // Создадим материалы
            $fpr = new FishPhotosRetreiver();
            $fyrr = new FishYandexReferatsRetreiver();
            $images = $fpr->retreive(3);
            $i = 0;
            foreach ($images as $filename => $origFilename) {
                $temp = $fyrr;
                $Item = new Material(array(
                    'pid' => (int)$MT->id, 
                    'vis' => 1, 
                    'name' => $temp['name'], 
                    'description' => $temp['brief'], 
                    'priority' => (++$i) * 10, 
                    'sitemaps_priority' => 0.5
                ));
                $Item->commit();
                $att = new Attachment();
                $att->upload = $filename;
                $att->filename = $origFilename;
                $type = getimagesize($filename);
                $att->mime = image_type_to_mime_type($type[2]);
                $att->parent = $Item->fields['image'];
                $att->image = 1;
                $att->maxWidth = $att->maxHeight = 1920;
                $att->tnsize = 300;
                $att->commit();
                $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
                $Item->fields['image']->addValue(json_encode($row));
                $Item->fields['url']->addValue('#');
            }

            $B = new Block_HTML(array(
                'name' => $this->view->_('WELCOME'), 'description' => file_get_contents($this->resourcesDir . '/main.tmp.php'), 'wysiwyg' => 1,
            ));
            $this->createBlock($B, 'content', null, null, $Site);

            $B = new Block_HTML(array('name' => $this->view->_('MAP'), 'description' => file_get_contents($this->resourcesDir . '/map.tmp.php'), 'wysiwyg' => 0,));
            $this->createBlock($B, 'content', null, null, $contacts);

            $B = new Block_HTML(array(
                'name' => $this->view->_('CONTACTS'), 'description' => file_get_contents($this->resourcesDir . '/contacts.tmp.php'), 'wysiwyg' => 0,
            ));
            $this->createBlock($B, 'content', null, null, $contacts);

            $B = new Block_HTML(array('name' => $this->view->_('FEEDBACK'), 'description' => '<h3>' . $this->view->_('FEEDBACK') . '</h3>', 'wysiwyg' => 1,));
            $this->createBlock($B, 'content', null, null, $contacts);

            $FRM = Form::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('FEEDBACK')) . "'"));
            $FRM = $FRM ? $FRM[0] : null;
            $B = new Block_Form(array('form' => $FRM->id ?: 0,));
            $this->createBlock($B, 'content', '__raas_form_interface', 'feedback', $contacts);

            $B = new Block_HTML(array('name' => $this->view->_('PAGE_404'), 'description' => $this->view->_('PAGE_404_TEXT'), 'wysiwyg' => 1,));
            $this->createBlock($B, 'content', null, null, $p404);

            $MNU = Menu::getSet(array('where' => "name = '" . $this->SQL->real_escape_string($this->view->_('SITEMAP')) . "'"));
            $MNU = $MNU ? $MNU[0] : null;
            $B = new Block_Menu(array('menu' => $MNU->id ?: 0, 'full_menu' => 1, ));
            $this->createBlock($B, 'content', '__raas_menu_interface', 'menu_content', $map);

            $B = new Block_PHP(array('name' => $this->view->_('SITEMAP_XML'),));
            $this->createBlock($B, '', null, 'sitemap_xml', $sitemaps);

            $robotsTXT =file_get_contents($this->resourcesDir . '/robots.txt');
            $m = new Mustache_Engine();
            $robotsTXT = $m->render($robotsTXT, array('host' => $_SERVER['HTTP_HOST']));
            $B = new Block_HTML(array('name' => $this->view->_('ROBOTS_TXT'), 'description' => $robotsTXT, 'wysiwyg' => 0,));
            $this->createBlock($B, '', null, 'robots', $robots);

        }
    }


    protected function createPage(array $params, Page $Parent = null, $addUnderConstruction = false)
    {
        $P = new Page(array(
            'vis' => 1,
            'author_id' => Application::i()->user->id,
            'editor_id' => Application::i()->user->id,
            'cache' => 1,
            'inherit_cache' => 1,
            'inherit_template' => 0,
            'lang' => 'ru',
            'inherit_lang' => 1,
        ));
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
        if ($addUnderConstruction) {
            $B = new Block_HTML(array(
                'name' => $this->view->_('TEXT_BLOCK'), 
                'description' => '<p>' . $this->view->_('PAGE_UNDER_CONSTRUCTION') . '</p>',
                'wysiwyg' => 1,
            ));
            $this->createBlock($B, 'content', null, null, $P);
        }
        return $P;
    }


    public function createNews($name, $urn, $nameMain)
    {
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
        }
        $temp = Material_Type::importByURN($urn);
        if (!$temp->id) {
            $MT = new Material_Type(array('name' => $name, 'urn' => $urn, 'global_type' => 1,));
            $MT->commit();

            $dateField = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('DATE'), 'urn' => 'date', 'datatype' => 'date', 'show_in_table' => 1,));
            $dateField->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('IMAGE'), 'multiple' => 1, 'urn' => 'images', 'datatype' => 'image', 'show_in_table' => 1,));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('BRIEF_TEXT'), 'multiple' => 0, 'urn' => 'brief', 'datatype' => 'textarea',));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('NO_INDEX'), 'urn' => 'noindex', 'datatype' => 'checkbox'));
            $F->commit();

            $VF = Snippet_Folder::importByURN('__raas_views');
            $temp = Snippet::importByURN($urn);
            if (!$temp->id) {
                $f = $this->resourcesDir . '/material.tmp.php';
                $S = new Snippet(array('name' => $name, 'urn' => $urn, 'pid' => $VF->id, 'description' => file_get_contents($f),));
                $S->commit();
            }
            if ($nameMain) {
                $temp = Snippet::importByURN($urn . '_main');
                if (!$temp->id) {
                    $f = $this->resourcesDir . '/material_main.tmp.php';
                    $text = file_get_contents($f);
                    $text = str_ireplace('{BLOCK_NAME}', $urn . '_main', $text);
                    $text = str_ireplace('{MATERIAL_NAME}', $name, $text);
                    $S = new Snippet(array('name' => $nameMain, 'urn' => $urn . '_main', 'pid' => $VF->id, 'description' => $text));
                    $S->commit();
                }
            }
            
            $page = $this->createPage(array('name' => $name, 'urn' => $urn), $Site);
            $blockMaterial = new Block_Material(array(
                'material_type' => (int)$MT->id, 
                'nat' => 1,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
                'sort_field_default' => $dateField->id,
                'sort_order_default' => 'desc!',
            ));
            $this->createBlock($blockMaterial, 'content', '__raas_material_interface', $urn, $page);
            if ($nameMain) {
                $blockMaterial = new Block_Material(array(
                    'material_type' => (int)$MT->id, 
                    'nat' => 1,
                    'pages_var_name' => '',
                    'rows_per_page' => 3,
                    'sort_field_default' => $dateField->id,
                    'sort_order_default' => 'desc!',
                ));
                $this->createBlock($blockMaterial, 'left', '__raas_material_interface', $urn . '_main', $Site);
            }

            // Создадим материалы
            $fpr = new FishPhotosRetreiver();
            $fyrr = new FishYandexReferatsRetreiver();
            $images = $fpr->retreive(3);
            $i = 0;
            foreach ($images as $filename => $origFilename) {
                $temp = $fyrr->retreive();
                $Item = new Material(array(
                    'pid' => (int)$MT->id, 
                    'vis' => 1, 
                    'name' => $temp['name'], 
                    'description' => $temp['text'], 
                    'priority' => (++$i) * 10, 
                    'sitemaps_priority' => 0.5
                ));
                $Item->commit();
                $Item->fields['date']->addValue(date('Y-m-d H:i', time() - rand(0, 86400 * 7)));
                $att = new Attachment();
                $att->upload = $filename;
                $att->filename = $origFilename;
                $type = getimagesize($filename);
                $att->mime = image_type_to_mime_type($type[2]);
                $att->parent = $Item->fields['images'];
                $att->image = 1;
                $att->maxWidth = $att->maxHeight = 1920;
                $att->tnsize = 300;
                $att->commit();
                $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
                $Item->fields['images']->addValue(json_encode($row));
                $Item->fields['brief']->addValue($temp['brief']);
                if (!$i) {
                    $additionalImages = $fpr->retreive(4);
                    foreach ($additionalImages as $key => $val) {
                        $att = new Attachment();
                        $att->upload = $key;
                        $att->filename = $val;
                        $type = getimagesize($key);
                        $att->mime = image_type_to_mime_type($type[2]);
                        $att->parent = $Item->fields['images'];
                        $att->image = 1;
                        $att->maxWidth = $att->maxHeight = 1920;
                        $att->tnsize = 300;
                        $att->commit();
                        $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
                        $Item->fields['image']->addValue(json_encode($row));
                    }
                }
            }
        }
    }


    public function createPhotos($name, $urn)
    {
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
        }
        $temp = Material_Type::importByURN($urn);
        if (!$temp->id) {
            $MT = new Material_Type(array('name' => $name, 'urn' => $urn, 'global_type' => 1,));
            $MT->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('IMAGE'), 'multiple' => 1, 'urn' => 'images', 'datatype' => 'image', 'show_in_table' => 1,));
            $F->commit();

            $VF = Snippet_Folder::importByURN('__raas_views');
            $temp = Snippet::importByURN($urn);
            if (!$temp->id) {
                $f = $this->resourcesDir . '/photos.tmp.php';
                $S = new Snippet(array('name' => $name, 'urn' => $urn, 'pid' => $VF->id, 'description' => file_get_contents($f),));
                $S->commit();
            }
            
            $page = $this->createPage(array('name' => $name, 'urn' => $urn), $Site);
            $blockMaterial = new Block_Material(array(
                'material_type' => (int)$MT->id, 
                'nat' => 0,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'asc!',
            ));
            $this->createBlock($blockMaterial, 'content', '__raas_material_interface', $urn, $page);
        }
    }


    public function createSearch()
    {
        $name = $this->view->_('SITE_SEARCH');
        $urn = 'search';
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
        }
        $temp = Snippet::importByURN($urn);
        if (!$temp->id) {
            $VF = Snippet_Folder::importByURN('__raas_views');
            $temp = Snippet::importByURN($urn);
            if (!$temp->id) {
                $f = $this->resourcesDir . '/' . $urn . '.tmp.php';
                $S = new Snippet(array('name' => $name, 'urn' => $urn, 'pid' => $VF->id, 'description' => file_get_contents($f),));
                $S->commit();
            }
            if (!$temp->id) {
                $f = $this->resourcesDir . '/search_form.tmp.php';
                $S = new Snippet(array('name' => $this->view->_('SEARCH_FORM'), 'urn' => 'search_form', 'pid' => $VF->id, 'description' => file_get_contents($f),));
                $S->commit();
            }
            $page = $this->createPage(array('name' => $name, 'urn' => $urn, 'response_code' => 200), $Site);
            $B = new Block_Search(array('search_var_name' => 'search_string', 'min_length' => 3, 'pages_var_name' => 'page', 'rows_per_page' => 20,));
            $this->createBlock($B, 'content', '__raas_search_interface', $urn, $page);
            $B = new Block_PHP();
            $this->createBlock($B, 'contacts_top', '', 'search_form', $Site, true);
        }
    }


    public function createFAQ($name, $urn, $mainName = null)
    {
        if (!$mainName) {
            $mainName = $name;
        }
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
        }
        $MT = Material_Type::importByURN($urn);
        if (!$MT->id) {
            $MT = new Material_Type(array('name' => $name, 'urn' => $urn, 'global_type' => 1,));
            $MT->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('DATE'), 'urn' => 'date', 'datatype' => 'date',));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('PHONE'), 'urn' => 'phone', 'datatype' => 'text',));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('EMAIL'), 'urn' => 'email', 'datatype' => 'email',));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('IMAGE'), 'urn' => 'image', 'datatype' => 'image', 'show_in_table' => 0,));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('ANSWER_DATE'), 'urn' => 'answer_date', 'datatype' => 'date',));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('ANSWER_NAME'), 'urn' => 'answer_name', 'datatype' => 'text',));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('ANSWER_GENDER'), 'urn' => 'answer_gender', 'datatype' => 'select', 'source_type' => 'ini', 'source' => '0 = "' . $this->view->_('FEMALE') . '"' . "\n" . '1 = "' . $this->view->_('MALE') . '"'));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('ANSWER_IMAGE'), 'urn' => 'answer_image', 'datatype' => 'image', 'show_in_table' => 0,));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('ANSWER'), 'urn' => 'answer', 'datatype' => 'htmlarea',));
            $F->commit();
        }

        $S = Snippet::importByURN('__raas_form_notify');
        $FRM = Form::importByURN($urn);
        if (!$FRM->id) {
            $FRM = new Form(array(
                'name' => $name,
                'urn' => $urn,
                'material_type' => (int)$MT->id,
                'create_feedback' => 0,
                'signature' => 1,
                'antispam' => 'hidden',
                'antispam_field_name' => '_name',
                'interface_id' => (int)$S->id,
            ));
            $FRM->commit();

            $F = new Form_Field(array(
                'pid' => $FRM->id, 'name' => $this->view->_('YOUR_NAME'), 'urn' => 'name', 'required' => 1, 'datatype' => 'text', 'show_in_table' => 1,
            ));
            $F->commit();

            $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('PHONE'), 'urn' => 'phone', 'datatype' => 'text', 'show_in_table' => 1,));
            $F->commit();

            $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('EMAIL'), 'urn' => 'email', 'datatype' => 'email', 'show_in_table' => 0,));
            $F->commit();

            $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('YOUR_PHOTO'), 'urn' => 'image', 'datatype' => 'image', 'show_in_table' => 0,));
            $F->commit();

            $F = new Form_Field(array(
                'pid' => $FRM->id, 'name' => $this->view->_('QUESTION_TEXT'), 'urn' => 'description', 'required' => 1, 'datatype' => 'textarea', 'show_in_table' => 0,
            ));
            $F->commit();
        }

        $VF = Snippet_Folder::importByURN('__raas_views');
        $temp = Snippet::importByURN($urn);
        if (!$temp->id) {
            $f = $this->resourcesDir . '/faq.tmp.php';
            $S = new Snippet(array('name' => $name, 'urn' => $urn, 'pid' => $VF->id, 'description' => file_get_contents($f),));
            $S->commit();
        }
        $temp = Snippet::importByURN($urn . '_main');
        if (!$temp->id) {
            $f = $this->resourcesDir . '/faq_main.tmp.php';
            $text = file_get_contents($f);
            $text = str_ireplace('{FAQ_NAME}', $name, $text);
            $S = new Snippet(array('name' => $mainName, 'urn' => $urn . '_main', 'pid' => $VF->id, 'description' => $text));
            $S->commit();
        }

        $faqPage = $this->createPage(array('name' => $name, 'urn' => $urn), $Site);
        
        $B = new Block_Material(array(
            'material_type' => (int)$MT->id, 
            'nat' => 1,
            'pages_var_name' => 'page',
            'rows_per_page' => 20,
            'sort_field_default' => 'post_date',
            'sort_order_default' => 'desc!',
        ));
        $this->createBlock($B, 'left', '__raas_material_interface', $urn, $faqPage);

        $B = new Block_HTML(array('description' => '<p>' . $this->view->_('YOU_CAN_ASK_YOUR_QUESTION') . '</p>',));
        $this->createBlock($B, 'content', null, null, $faqPage);

        $B = new Block_Form(array('form' => $FRM->id,));
        $this->createBlock($B, 'content', '__raas_form_interface', 'feedback', $faqPage);


        $B = new Block_Material(array(
            'material_type' => (int)$MT->id, 
            'nat' => 0,
            'pages_var_name' => '',
            'rows_per_page' => 3,
            'sort_field_default' => 'post_date',
            'sort_order_default' => 'desc!',
        ));
        $this->createBlock($B, 'content', '__raas_material_interface', $urn . '_main', $Site);

        // Создадим материалы
        $frur = new FishRandomUserRetreiver();
        $fyrr = new FishYandexReferatsRetreiver();
        for ($i = 0; $i < 3; $i++) {
            $user = $frur->retreive();
            $answer = $frur->retreive();
            $temp = $fyrr->retreive();
            $Item = new Material(array(
                'pid' => (int)$MT->id, 
                'vis' => 1, 
                'name' => $user['name']['first'] . ' ' . $user['name']['last'], 
                'description' => $temp['name'], 
                'priority' => ($i + 1) * 10, 
                'sitemaps_priority' => 0.5
            ));
            $Item->commit();
            $t = time() - 86400 * rand(1, 7);
            $t1 = $t + rand(0, 86400);
            $Item->fields['date']->addValue(date('Y-m-d', $t));
            $Item->fields['phone']->addValue($user['phone']);
            $Item->fields['email']->addValue($user['email']);
            $Item->fields['answer_date']->addValue(date('Y-m-d', $t1));
            $Item->fields['answer_name']->addValue($answer['name']['first'] . ' ' . $answer['name']['last']);
            $Item->fields['answer_gender']->addValue((int)($answer['gender'] == 'male'));
            $Item->fields['answer']->addValue($temp['text']);
            $att = new Attachment();
            $att->upload = $user['pic']['filepath'];
            $att->filename = $user['pic']['name'];
            $type = getimagesize($user['pic']['filepath']);
            $att->mime = image_type_to_mime_type($type[2]);
            $att->parent = $Item->fields['image'];
            $att->image = 1;
            $att->maxWidth = $att->maxHeight = 1920;
            $att->tnsize = 300;
            $att->commit();
            $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
            $Item->fields['image']->addValue(json_encode($row));
            $att = new Attachment();
            $att->upload = $answer['pic']['filepath'];
            $att->filename = $answer['pic']['name'];
            $type = getimagesize($answer['pic']['filepath']);
            $att->mime = image_type_to_mime_type($type[2]);
            $att->parent = $Item->fields['image'];
            $att->image = 1;
            $att->maxWidth = $att->maxHeight = 1920;
            $att->tnsize = 300;
            $att->commit();
            $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
            $Item->fields['answer_image']->addValue(json_encode($row));
        }
    }


    /**
     * @todo Проверить работу, возможно работает некорректно
     */
    public function createFeedback($name, $urn)
    {
        $Site = new Page();
        if ($Site->children) {
            $Site = $Site->children[0];
        }
        $S = Snippet::importByURN('__raas_form_notify');
        $FRM = new Form(array(
            'name' => $name, 'create_feedback' => 1, 'signature' => 1, 'antispam' => 'hidden', 'antispam_field_name' => '_name', 'interface_id' => (int)$S->id,
        ));
        $FRM->commit();

        $F = new Form_Field(array(
            'pid' => $FRM->id, 'name' => $this->view->_('YOUR_NAME'), 'urn' => 'full_name', 'required' => 1, 'datatype' => 'text', 'show_in_table' => 1,
        ));
        $F->commit();

        $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('PHONE'), 'urn' => 'phone', 'datatype' => 'text', 'show_in_table' => 1,));
        $F->commit();

        $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('EMAIL'), 'urn' => 'email', 'datatype' => 'text', 'show_in_table' => 1,));
        $F->commit();

        $F = new Form_Field(array('pid' => $FRM->id, 'name' => $this->view->_('QUESTION_TEXT'), 'urn' => '_description_', 'required' => 1, 'datatype' => 'textarea',));
        $F->commit();

        $contacts = $this->createPage(array('name' => $name, 'urn' => $urn), $Site);
        
        $B = new Block_HTML(array('description' => '<h3>' . $this->view->_('FEEDBACK') . '</h3>',));
        $this->createBlock($B, 'content', null, null, $contacts);

        $B = new Block_Form(array('form' => $FRM->id,));
        $this->createBlock($B, 'content', '__raas_form_interface', 'feedback', $contacts);
    }


    public function createBlock(Block $B, $location, $interface = null, $widget = null, $startPage, $inherit = false)
    {
        if (strtolower($type) == 'html') {
            $classname = 'RAAS\\CMS\\Block_HTML';
        } else {
            $classname = 'RAAS\\CMS\\Block_' . ucfirst($type);
        }

        $B->location = $location;
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        if ($inherit) {
            $cats = array_merge(array($startPage), (array)$startPage->all_children);
            $cats = array_filter($cats, function($x) use ($startPage) { return $x->template == $startPage->template; });
            $cats = array_values($cats);
            $B->inherit = 1;
        } else {
            $cats = array($startPage);
        }
        $catsIds = array_map(function($x) { return (int)$x->id; }, $cats);
        $B->cats = $catsIds;
        $B->interface_id = 0;
        $B->widget_id = 0;
        if ($interface) {
            $snippetInterface = Snippet::importByURN($interface);
            if ($snippetInterface) {
                $B->interface_id = (int)$snippetInterface->id;
            }
        }
        if ($widget) {
            $snippetWidget = Snippet::importByURN($widget);
            if ($snippetWidget) {
                $B->widget_id = (int)$snippetWidget->id;
            }
        }
        $B->commit();
    }
}