<?php
namespace RAAS\CMS;

use \RAAS\Application;
use \RAAS\Attachment;
use \SOME\SOME;
use Mustache_Engine;

class Webmaster
{
    const IMAGES_TO_RETREIVE = 10;
    const TEXTS_TO_RETREIVE = 10;
    const USERS_TO_RETREIVE = 10;

    protected static $instance;
    protected static $imagesRetreived = array();
    protected static $textsRetreived = array();
    protected static $usersRetreived = array();

    public function __get($var)
    {
        switch ($var) {
            case 'nextImage':
                if (!self::$imagesRetreived) {
                    $fpr = new FishPhotosRetreiver();
                    self::$imagesRetreived = array();
                    $temp = $fpr->retreive(self::IMAGES_TO_RETREIVE);
                    foreach ($temp as $url => $filename) {
                        self::$imagesRetreived[] = array('url' => $url, 'filename' => $filename);
                    }
                }
                $images = self::$imagesRetreived;
                shuffle($images);
                $image = array_shift($images);
                $tempname = tempnam(sys_get_temp_dir(), 'RAAS');
                copy($image['url'], $tempname);
                return array('url' => $tempname, 'filename' => $image['filename']);
                break;
            case 'nextText':
                if (!self::$textsRetreived) {
                    $fpr = new FishYandexReferatsRetreiver();
                    for ($i = 0; $i < self::TEXTS_TO_RETREIVE; $i++) {
                        self::$textsRetreived[] = $fpr->retreive();
                    }
                }
                $texts = self::$textsRetreived;
                shuffle($texts);
                $temp = array_shift($texts);
                return $temp;
                break;
            case 'nextUser':
                if (!self::$usersRetreived) {
                    $fpr = new FishRandomUserRetreiver();
                    for ($i = 0; $i < self::USERS_TO_RETREIVE; $i++) {
                        self::$usersRetreived[] = $fpr->retreive();
                    }
                }
                $users = self::$usersRetreived;
                shuffle($users);
                $temp = array_shift($users);
                return $temp;
                break;
            case 'Site':
                $temp = new Page();
                $temp = $temp->visChildren ? $temp->visChildren[0] : null;
                return $temp;
                break;
            default:
                return Package::i()->__get($var);
                break;
        }
    }


    public function getAttachmentFromFilename($filename, $filepath, $parentField)
    {
        $att = new Attachment();
        $att->copy = true;
        $att->upload = $filepath;
        $att->filename = $filename;
        $type = getimagesize($filepath);
        $att->mime = image_type_to_mime_type($type[2]);
        $att->parent = $parentField;
        $att->image = 1;
        $att->maxWidth = $att->maxHeight = 1920;
        $att->tnsize = 300;
        $att->commit();
        return $att;
    }


    /**
     * Создаем стандартный сниппет
     * @param Snippet_Folder $parent папка, в которой нужно разместить сниппет
     * @param string $urn URN сниппета
     * @param string $name Название сниппета
     * @param string $description Код сниппета
     * @param boolean $locked Заблокирован ли сниппет от редактирования
     * @return Snippet существующий или вновь созданный сниппет
     */
    public function checkSnippet(Snippet_Folder $parent, $urn, $name, $description, $locked = true)
    {
        $Item = Snippet::importByURN($urn);
        if (!$Item->id) {
            $Item = new Snippet(array('pid' => $parent->id, 'urn' => $urn, 'locked' => (int)$locked));
        }
        if ($locked || !$Item->id) {
            $Item->name = $this->view->_($name);
            $Item->description = $description;
        }
        $Item->commit();
        return $Item;
    }


    /**
     * Создаем стандартные интерфейсы
     * @return array[Snippet] массив созданных или существующих интерфейсов
     */
    public function checkStdInterfaces()
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

        $ifo = Snippet_Folder::importByURN('__raas_interfaces');
        $interfaces = array();
        $interfaces['__raas_material_interface'] = $this->checkSnippet($ifo, '__raas_material_interface', 'MATERIAL_STANDARD_INTERFACE', $this->stdMaterialInterface);
        $interfaces['__raas_form_interface'] = $this->checkSnippet($ifo, '__raas_form_interface', 'FORM_STANDARD_INTERFACE', $this->stdFormInterface);
        $interfaces['__raas_menu_interface'] = $this->checkSnippet($ifo, '__raas_menu_interface', 'MENU_STANDARD_INTERFACE', $this->stdMenuInterface);
        $interfaces['__raas_search_interface'] = $this->checkSnippet($ifo, '__raas_search_interface', 'SEARCH_STANDARD_INTERFACE', $this->stdSearchInterface);
        $interfaces['__raas_form_notify'] = $this->checkSnippet($ifo, '__raas_form_notify', 'FORM_STANDARD_NOTIFICATION', $this->stdFormTemplate);
        $interfaces['__raas_cache_interface'] = $this->checkSnippet($ifo, '__raas_cache_interface', 'CACHE_STANDARD_INTERFACE', $this->stdCacheInterface);
        $interfaces['__raas_watermark_interface'] = $this->checkSnippet($ifo, '__raas_watermark_interface', 'WATERMARK_STANDARD_INTERFACE', $this->stdWatermarkInterface);
        return $interfaces;
    }


    /**
     * Добавим стандартный шаблон
     * @return Template шаблон, созданный или первый найденный
     */
    public function createTemplate()
    {
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
                               .    '{"urn":"menu_top","x":"10","y":"130","width":"480","height":"60"},'
                               .    '{"urn":"search_form","x":"500","y":"130","width":"150","height":"60"},'
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
            return $T;
        } else {
            return $temp[0];
        }
    }


    /**
     * Добавим поля страниц
     * @return array[Page_Field] массив созданных или существующих полей
     */
    public function createPageFields()
    {
        $fields = array();
        foreach (array(
            array('name' => $this->view->_('DESCRIPTION'), 'urn' => '_description_', 'datatype' => 'htmlarea'),
            array('name' => $this->view->_('IMAGE'), 'urn' => 'image', 'datatype' => 'image'),
            array('name' => $this->view->_('NO_INDEX'), 'urn' => 'noindex', 'datatype' => 'checkbox'),
            array('name' => $this->view->_('BACKGROUND'), 'urn' => 'background', 'datatype' => 'image')
        ) as $row) {
            $pf = Page_Field::importByURN($row['urn']);
            if (!$pf->id) {
                $pf = new Page_Field($row);
                $pf->commit();
            }
            $fields[$row['urn']] = $pf;
        }
        return $fields;
    }


    /**
     * Добавим виджеты
     * @return array[Snippet] Массив созданных или существующих виджетов
     */
    public function createWidgets()
    {
        $widgets = array();
        $snippets = array(
            'banners' => $this->view->_('BANNERS'),
            'feedback' => $this->view->_('FEEDBACK'),
            'feedback_modal' => $this->view->_('FEEDBACK_MODAL'),
            'head' => $this->view->_('HEAD_TAG'),
            'order_call_modal' => $this->view->_('ORDER_CALL_MODAL'),
            // 'search' => $this->view->_('SITE_SEARCH'),
            'sitemap_xml' => $this->view->_('SITEMAP_XML'),
            'logo' => $this->view->_('LOGO'),
            'features_main' => $this->view->_('FEATURES_MAIN'),
            'robots' => $this->view->_('ROBOTS_TXT'),
            'custom_css' => $this->view->_('CUSTOM_CSS'),
            'menu_content' => $this->view->_('SITEMAP'),
            'menu_top' => $this->view->_('TOP_MENU'),
            // 'menu_left' => $this->view->_('LEFT_MENU'),
            'menu_bottom' => $this->view->_('BOTTOM_MENU'),
        );
        $VF = Snippet_Folder::importByURN('__raas_views');
        foreach ($snippets as $urn => $name) {
            $S = Snippet::importByURN($urn);
            if (!$S->id) {
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
            $widgets[$urn] = $S;
        }
        return $widgets;
    }


    /**
     * Создадим формы
     * @param Snippet $snippetFormNotify сниппет уведомления
     * @return array[Form] созданные или существующие формы
     */
    public function createForms(Snippet $snippetFormNotify)
    {
        $forms = array();
        $FRM = Form::importByURN('feedback');
        if (!$FRM->id) {
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
                'pid' => $FRM->id,
                'name' => $this->view->_('YOUR_NAME'),
                'urn' => 'full_name',
                'required' => 1,
                'datatype' => 'text',
                'show_in_table' => 1,
            ));
            $F->commit();

            $F = new Form_Field(array(
                'pid' => $FRM->id,
                'name' => $this->view->_('PHONE'),
                'urn' => 'phone',
                'datatype' => 'text',
                'show_in_table' => 1,
            ));
            $F->commit();

            $F = new Form_Field(array(
                'pid' => $FRM->id,
                'name' => $this->view->_('EMAIL'),
                'urn' => 'email',
                'datatype' => 'text',
                'show_in_table' => 1,
            ));
            $F->commit();

            $F = new Form_Field(array(
                'pid' => $FRM->id,
                'name' => $this->view->_('QUESTION_TEXT'),
                'urn' => '_description_',
                'required' => 1,
                'datatype' => 'textarea',
            ));
            $F->commit();
        }
        $forms['feedback'] = $FRM;

        $FRM = Form::importByURN('order_call');
        if (!$FRM->id) {
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

            $F = new Form_Field(array(
                'pid' => $FRM->id,
                'name' => $this->view->_('PHONE'),
                'urn' => 'phone_call',
                'datatype' => 'text',
                'show_in_table' => 1,
            ));
            $F->commit();
        }
        $forms['order_call'] = $FRM;

        return $forms;
    }


    /**
     * Создадим меню
     * @return array[Menu] созданные или существующие меню
     */
    public function createMenus()
    {
        $menus = array();
        foreach (array(
            array('page_id' => (int)$this->Site->id, 'urn' => 'top', 'inherit' => 10, 'name' => $this->view->_('TOP_MENU')),
            array('page_id' => (int)$this->Site->id, 'urn' => 'bottom', 'inherit' => 1, 'name' => $this->view->_('BOTTOM_MENU')),
            array('page_id' => (int)$this->Site->id, 'urn' => 'sitemap', 'inherit' => 10, 'name' => $this->view->_('SITEMAP'))
        ) as $row) {
            $MNU = Menu::importByURN($row['urn']);
            if (!$MNU->id) {
                $MNU = new Menu($row);
                $MNU->commit();
            }
            $menus[$row['urn']] = $MNU;
        }

        $stdCacheInterface = Snippet::importByURN('__raas_cache_interface');
        $B = new Block_Menu(array(
            'menu' => (int)$menus['top']->id,
            'full_menu' => 1,
            'cache_type' => Block::CACHE_DATA,
            'cache_interface_id' => (int)$stdCacheInterface->id
        ));
        $this->createBlock($B, 'menu_top', '__raas_menu_interface', 'menu_top', $this->Site, true);
        $B = new Block_Menu(array(
            'menu' => (int)$menus['bottom']->id,
            'full_menu' => 1,
            'cache_type' => Block::CACHE_DATA,
            'cache_interface_id' => (int)$stdCacheInterface->id
        ));
        $this->createBlock($B, 'menu_bottom', '__raas_menu_interface', 'menu_bottom', $this->Site, true);
        return $menus;
    }


    /**
     * Добавим баннеры
     */
    public function createBanners()
    {
        $MT = Material_Type::importByURN('banners');
        if (!$MT->id) {
            $MT = new Material_Type(array('name' => $this->view->_('BANNERS'), 'urn' => 'banners', 'global_type' => 1));
            $MT->commit();

            $F = new Material_Field(array(
                'pid' => $MT->id,
                'name' => $this->view->_('URL'),
                'urn' => 'url',
                'datatype' => 'text',
                'show_in_table' => 1,
            ));
            $F->commit();

            $F = new Material_Field(array(
                'pid' => $MT->id,
                'name' => $this->view->_('IMAGE'),
                'urn' => 'image',
                'datatype' => 'image',
                'show_in_table' => 1,
            ));
            $F->commit();

            $B = new Block_Material(array(
                'material_type' => (int)$MT->id,
                'nat' => 0,
                'pages_var_name' => 'page',
                'rows_per_page' => 0,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'asc',
            ));
            $this->createBlock($B, 'banners', '__raas_material_interface', 'banners', $this->Site);
            // Создадим материалы
            for ($i = 0; $i < 3; $i++) {
                $temp = $this->nextText;
                $Item = new Material(array(
                    'pid' => (int)$MT->id,
                    'vis' => 1,
                    'name' => $temp['name'],
                    'description' => $temp['brief'],
                    'priority' => ($i + 1) * 10,
                    'sitemaps_priority' => 0.5
                ));
                $Item->commit();
                $row = $this->nextImage;
                $att = $this->getAttachmentFromFilename($row['filename'], $row['url'], $MT->fields['image']);
                $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
                $Item->fields['image']->addValue(json_encode($row));
                $Item->fields['url']->addValue('#');
            }
        }
    }


    /**
     * Добавим особенности
     */
    public function createFeatures()
    {
        $MT = Material_Type::importByURN('features');
        if (!$MT->id) {
            $MT = new Material_Type(array('name' => $this->view->_('FEATURES'), 'urn' => 'features', 'global_type' => 1));
            $MT->commit();

            $F = new Material_Field(array(
                'pid' => $MT->id,
                'name' => $this->view->_('IMAGE'),
                'urn' => 'image',
                'datatype' => 'image',
                'show_in_table' => 1,
            ));
            $F->commit();

            $F = new Material_Field(array(
                'pid' => $MT->id,
                'name' => $this->view->_('ICON'),
                'urn' => 'icon',
                'datatype' => 'text',
                'show_in_table' => 1,
            ));
            $F->commit();

            $B = new Block_Material(array(
                'material_type' => (int)$MT->id,
                'nat' => 0,
                'pages_var_name' => 'page',
                'rows_per_page' => 0,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'asc',
            ));
            $this->createBlock($B, 'content', '__raas_material_interface', 'features_main', $this->Site);
            // Создадим материалы
            $icons = array('smile-o', 'thumbs-o-up', 'rub');
            for ($i = 0; $i < 3; $i++) {
                $Item = new Material(array(
                    'pid' => (int)$MT->id,
                    'vis' => 1,
                    'name' => $this->view->_('FEATURE_' . ($i + 1)),
                    'description' => $this->view->_('FEATURE_' . ($i + 1) . '_TEXT'),
                    'priority' => ($i + 1) * 10,
                    'sitemaps_priority' => 0.5
                ));
                $Item->commit();
                $Item->fields['icon']->addValue($icons[$i]);
            }
        }
    }


    /**
     * Создаем главную страницу
     * @param Template $template Шаблон
     * @param array[Form] $forms массив форм
     * @return Page Созданная или существующая главная страница
     */
    public function createMainPage(Template $template, array $forms)
    {
        if (!$this->Site) {
            $host = $_SERVER['HTTP_HOST'];
            if (stristr($host, '.volumnet.ru')) {
                $urn = str_replace('.volumnet.ru', '', $host) . ' ' . $host;
            } else {
                $urn = $host . ' ' . $host . '.volumnet.ru';
            }
            $Site = $this->createPage(array(
                'name' => $this->view->_('MAIN_PAGE'),
                'urn' => $urn,
                'template' => (int)$template->id,
                'inherit_cache' => 0
            ));

            $B = new Block_HTML(array(
                'name' => $this->view->_('LOGO'),
                'description' => file_get_contents($this->resourcesDir . '/logo_block.tmp.php'),
                'wysiwyg' => 1
            ));
            $this->createBlock($B, 'logo', null, 'logo', $this->Site, true);

            $B = new Block_HTML(array(
                'name' => $this->view->_('CONTACTS'),
                'description' => file_get_contents($this->resourcesDir . '/contacts_top.tmp.php'),
                'wysiwyg' => 0
            ));
            $this->createBlock($B, 'contacts_top', null, null, $this->Site, true);

            $B = new Block_HTML(array(
                'name' => $this->view->_('SOCIAL_NETWORKS'),
                'description' => file_get_contents($this->resourcesDir . '/socials_top.tmp.php'),
                'wysiwyg' => 0
            ));
            $this->createBlock($B, 'contacts_top', null, null, $this->Site, true);


            $B = new Block_HTML(array(
                'name' => $this->view->_('COPYRIGHTS'),
                'description' => file_get_contents($this->resourcesDir . '/copyrights.tmp.php'),
                'wysiwyg' => 1,
            ));
            $this->createBlock($B, 'copyrights', null, null, $this->Site, true);

            $B = new Block_Form(array('form' => $forms['feedback']->id ?: 0,));
            $this->createBlock($B, 'footer_counters', '__raas_form_interface', 'feedback_modal', $this->Site, true);

            $B = new Block_Form(array('form' => $forms['order_call']->id ?: 0,));
            $this->createBlock($B, 'footer_counters', '__raas_form_interface', 'order_call_modal', $this->Site, true);

            $B = new Block_HTML(array('name' => $this->view->_('YANDEX_METRIKA'), 'description' => '', 'wysiwyg' => 0));
            $this->createBlock($B, 'footer_counters', null, null, $this->Site, true);

            $B = new Block_HTML(array('name' => $this->view->_('GOOGLE_ANALYTICS'), 'description' => '', 'wysiwyg' => 0));
            $this->createBlock($B, 'head_counters', null, null, $this->Site, true);

            $B = new Block_HTML(array(
                'name' => $this->view->_('TRIGGERS'),
                'description' => file_get_contents($this->resourcesDir . '/triggers.tmp.php'),
                'wysiwyg' => 0,
            ));
            $this->createBlock($B, 'footer_counters', null, null, $this->Site, true);

            $B = new Block_HTML(array(
                'name' => $this->view->_('WELCOME'),
                'description' => file_get_contents($this->resourcesDir . '/main.tmp.php'),
                'wysiwyg' => 1,
            ));
            $this->createBlock($B, 'content', null, null, $this->Site);

            $this->createFeatures();
        }
        return $this->Site;
    }


    /**
     * Создаем разделы "Наши услуги"
     * @return Page Страница "Наши услуги"
     */
    public function createServices()
    {
        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'services'")));
        if ($temp) {
            $services = $temp[0];
        } else {
            $services = $this->createPage(
                array('name' => $this->view->_('OUR_SERVICES'), 'urn' => 'services'),
                $this->Site,
                true
            );
            for ($i = 1; $i <= 3; $i++) {
                $service = $this->createPage(
                    array('name' => $this->view->_('OUR_SERVICE') . ' ' . $i, 'urn' => 'service' . $i),
                    $services,
                    true
                );
            }
        }
        return $services;
    }


    /**
     * Создаем раздел "контакты"
     * @param Form $feedbackForm Форма обратной связи
     * @return Page Страница контактов
     */
    public function createContacts(Form $feedbackForm)
    {
        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'contacts'")));
        if ($temp) {
            $contacts = $temp[0];
        } else {
            $contacts = $this->createPage(array('name' => $this->view->_('CONTACTS'), 'urn' => 'contacts'), $this->Site);
            $B = new Block_HTML(array(
                'name' => $this->view->_('MAP'),
                'description' => file_get_contents($this->resourcesDir . '/map.tmp.php'),
                'wysiwyg' => 0,
            ));
            $this->createBlock($B, 'content', null, null, $contacts);

            $B = new Block_HTML(array(
                'name' => $this->view->_('CONTACTS'),
                'description' => file_get_contents($this->resourcesDir . '/contacts.tmp.php'),
                'wysiwyg' => 0,
            ));
            $this->createBlock($B, 'content', null, null, $contacts);

            $B = new Block_HTML(array(
                'name' => $this->view->_('FEEDBACK'),
                'description' => '<h3>' . $this->view->_('FEEDBACK') . '</h3>',
                'wysiwyg' => 1,
            ));
            $this->createBlock($B, 'content', null, null, $contacts);

            $B = new Block_Form(array('form' => (int)$feedbackForm->id));
            $this->createBlock($B, 'content', '__raas_form_interface', 'feedback', $contacts);
        }
        return $contacts;
    }


    /**
     * Создаем страницу 404
     * @return Page Созданная или существующая страница
     */
    public function create404()
    {
        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = '404'")));
        if ($temp) {
            $p404 = $temp[0];
        } else {
            $p404 = $this->createPage(
                array('name' => $this->view->_('PAGE_404'), 'urn' => '404', 'response_code' => 404),
                $this->Site
            );
            $B = new Block_HTML(array(
                'name' => $this->view->_('PAGE_404'),
                'description' => $this->view->_('PAGE_404_TEXT'),
                'wysiwyg' => 1,
            ));
            $this->createBlock($B, 'content', null, null, $p404);
        }
        return $p404;
    }


    /**
     * Создание карты сайта
     * @param Menu $siteMapMenu Меню карты сайта
     * @return Page Созданная или существующая страница
     */
    public function createMap(Menu $siteMapMenu)
    {
        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'map'")));
        if ($temp) {
            $map = $temp[0];
        } else {
            $map = $this->createPage(
                array('name' => $this->view->_('SITEMAP'), 'urn' => 'map', 'response_code' => 200),
                $this->Site
            );
            $B = new Block_Menu(array('menu' => (int)$siteMapMenu->id, 'full_menu' => 1));
            $this->createBlock($B, 'content', '__raas_menu_interface', 'menu_content', $map);
        }
        return $map;
    }


    /**
     * Создание sitemap.xml
     * @return Page Созданная или существующая страница
     */
    public function createSitemapsXml()
    {
        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'sitemaps'")));
        if ($temp) {
            $sitemaps = $temp[0];
        } else {
            $sitemaps = $this->createPage(
                array(
                    'name' => $this->view->_('SITEMAP_XML'),
                    'urn' => 'sitemaps',
                    'template' => 0,
                    'cache' => 0,
                    'response_code' => 200
                ),
                $this->Site
            );
            $B = new Block_PHP(array('name' => $this->view->_('SITEMAP_XML'),));
            $this->createBlock($B, '', null, 'sitemap_xml', $sitemaps);
        }
        return $sitemaps;
    }


    /**
     * Создание robots.txt
     * @return Page Созданная или существующая страница
     */
    public function createRobotsTxt()
    {
        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'robots'")));
        if ($temp) {
            $robots = $temp[0];
        } else {
            $robots = $this->createPage(array('name' => $this->view->_('ROBOTS_TXT'), 'urn' => 'robots', 'template' => 0, 'cache' => 1, 'response_code' => 200), $this->Site);
            $robotsTXT = file_get_contents($this->resourcesDir . '/robots.txt');
            $m = new Mustache_Engine();
            $robotsTXT = $m->render($robotsTXT, array('HOST' => $_SERVER['HTTP_HOST']));
            $B = new Block_HTML(array('name' => $this->view->_('ROBOTS_TXT'), 'description' => $robotsTXT, 'wysiwyg' => 0,));
            $this->createBlock($B, '', null, 'robots', $robots);
        }
        return $robots;
    }


    /**
     * Создание custom.css
     * @return Page Созданная или существующая страница
     */
    public function createCustomCss()
    {
        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'custom_css'")));
        if ($temp) {
            $customCss = $temp[0];
        } else {
            $customCss = $this->createPage(array('name' => $this->view->_('CUSTOM_CSS'), 'urn' => 'custom_css', 'template' => 0, 'cache' => 1, 'response_code' => 200), $this->Site);
            $m = new Mustache_Engine();
            $B = new Block_HTML(array('name' => $this->view->_('CUSTOM_CSS'), 'description' => '', 'wysiwyg' => 0,));
            $this->createBlock($B, '', null, 'custom_css', $customCss);
        }
        return $customCss;
    }



    /**
     * Создание сайта
     */
    public function createSite()
    {
        $template = $this->createTemplate();
        $this->createPageFields();
        $interfaces = $this->checkStdInterfaces();
        $widgets = $this->createWidgets();
        $forms = $this->createForms($interfaces['__raas_form_notify']);
        $this->createMainPage($template, $forms);
        $menus = $this->createMenus();
        $this->createBanners();

        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'about'")));
        if ($temp) {
            $about = $temp[0];
        } else {
            $about = $this->createPage(array('name' => $this->view->_('ABOUT_US'), 'urn' => 'about'), $this->Site, true);
        }

        $this->createServices();

        $news = $this->createNews($this->view->_('NEWS'), 'news', $this->view->_('NEWS_MAIN'));
        $contacts = $this->createContacts($forms['feedback']);
        $p404 = $this->create404();
        $map = $this->createMap($menus['sitemap']);
        $sitemaps = $this->createSitemapsXml();
        $robots = $this->createRobotsTxt();
        $customCss = $this->createCustomCss();

        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'ajax'")));
        if ($temp) {
            $ajax = $temp[0];
        } else {
            $ajax = $this->createPage(
                array(
                    'name' => $this->view->_('AJAX'),
                    'urn' => 'ajax',
                    'template' => 0,
                    'cache' => 0,
                    'response_code' => 200
                ),
                $this->Site
            );
        }

        if (!is_file(Package::i()->filesDir . '/image/logo.png')) {
            copy(Package::i()->resourcesDir . '/logo.png', Package::i()->filesDir . '/image/logo.png');
            chmod(Package::i()->filesDir . '/image/logo.png', 0777);
        }

        $B = new Block_HTML(array(
            'name' => $this->view->_('SHARE'),
            'description' => file_get_contents($this->resourcesDir . '/share.tmp.php'),
            'wysiwyg' => 0,
        ));
        $this->createBlock($B, 'share', null, null, $this->Site, true);
    }


    protected function createPage(array $params, Page $Parent = null, $addUnderConstruction = false)
    {
        $uid = Application::i()->user->id;
        $P = new Page(array(
            'vis' => 1,
            'author_id' => $uid,
            'editor_id' => $uid,
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


    /**
     * Добавление новостей по URN
     * @param string $name Наименование
     * @param string $urn URN
     * @param string $nameMain Наименование блока на главной
     */
    public function createNews($name, $urn, $nameMain = '')
    {
        $MT = Material_Type::importByURN($urn);
        if ($MT->id) {
            $dateField = $MT->fields['date'];
        } else {
            $MT = new Material_Type(array('name' => $name, 'urn' => $urn, 'global_type' => 1));
            $MT->commit();

            $dateField = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('DATE'), 'urn' => 'date', 'datatype' => 'date', 'show_in_table' => 1,));
            $dateField->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('IMAGE'), 'multiple' => 1, 'urn' => 'images', 'datatype' => 'image', 'show_in_table' => 1,));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('BRIEF_TEXT'), 'multiple' => 0, 'urn' => 'brief', 'datatype' => 'textarea',));
            $F->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('NO_INDEX'), 'urn' => 'noindex', 'datatype' => 'checkbox'));
            $F->commit();
        }

        $VF = Snippet_Folder::importByURN('__raas_views');
        $S = Snippet::importByURN($urn);
        if (!$S->id) {
            $f = $this->resourcesDir . '/material.tmp.php';
            $text = file_get_contents($f);
            $text = str_ireplace('{BLOCK_NAME}', str_replace('_main', '', $urn), $text);
            $text = str_ireplace('{MATERIAL_NAME}', $name, $text);
            $S = new Snippet(array('name' => $name, 'urn' => $urn, 'pid' => $VF->id, 'description' => $text));
            $S->commit();
        }

        if ($nameMain) {
            $SM = Snippet::importByURN($urn . '_main');
            if (!$SM->id) {
                $f = $this->resourcesDir . '/material_main.tmp.php';
                $text = file_get_contents($f);
                $text = str_ireplace('{BLOCK_NAME}', $urn . '_main', $text);
                $text = str_ireplace('{MATERIAL_NAME}', $name, $text);
                $SM = new Snippet(
                    array('name' => $nameMain, 'urn' => $urn . '_main', 'pid' => $VF->id, 'description' => $text)
                );
                $SM->commit();
            }
        }

        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = '" . $urn . "'")));
        if ($temp) {
            $page = $temp[0];
        } else {
            $page = $this->createPage(array('name' => $name, 'urn' => $urn), $this->Site);
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
                    'nat' => 0,
                    'pages_var_name' => '',
                    'rows_per_page' => 3,
                    'sort_field_default' => $dateField->id,
                    'sort_order_default' => 'desc!',
                ));
                $this->createBlock($blockMaterial, 'left', '__raas_material_interface', $urn . '_main', $this->Site, true, array($page->id));
            }

            // Создадим материалы
            for ($i = 0; $i < 3; $i++) {
                $temp = $this->nextText;
                $Item = new Material(array(
                    'pid' => (int)$MT->id,
                    'vis' => 1,
                    'name' => $temp['name'],
                    'description' => $temp['text'],
                    'priority' => ($i + 1) * 10,
                    'sitemaps_priority' => 0.5
                ));
                $Item->commit();
                $Item->fields['date']->addValue(date('Y-m-d H:i', time() - rand(0, 86400 * 7)));
                $Item->fields['brief']->addValue($temp['brief']);
                for ($j = 0; $j < 5; $j++) {
                    $row = $this->nextImage;
                    $att = $this->getAttachmentFromFilename($row['filename'], $row['url'], $MT->fields['images']);
                    $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
                    $Item->fields['images']->addValue(json_encode($row));
                }
            }
        }
        return $page;
    }


    public function createPhotos($name, $urn)
    {
        $temp = Material_Type::importByURN($urn);
        if (!$temp->id) {
            $MT = new Material_Type(array('name' => $name, 'urn' => $urn, 'global_type' => 1,));
            $MT->commit();

            $F = new Material_Field(array('pid' => $MT->id, 'name' => $this->view->_('IMAGE'), 'multiple' => 1, 'urn' => 'images', 'datatype' => 'image', 'show_in_table' => 1,));
            $F->commit();
        }

        $VF = Snippet_Folder::importByURN('__raas_views');
        $temp = Snippet::importByURN($urn);
        if (!$temp->id) {
            $f = $this->resourcesDir . '/photos.tmp.php';
            $text = file_get_contents($f);
            $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
            $text = str_ireplace('{MATERIAL_NAME}', $name, $text);
            $S = new Snippet(array('name' => $name, 'urn' => $urn, 'pid' => $VF->id, 'description' => $text));
            $S->commit();
        }

        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'photos'")));
        if ($temp) {
            $page = $temp[0];
        } else {
            $page = $this->createPage(array('name' => $name, 'urn' => $urn), $this->Site);
            $blockMaterial = new Block_Material(array(
                'material_type' => (int)$MT->id,
                'nat' => 1,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'asc!',
            ));
            $this->createBlock($blockMaterial, 'content', '__raas_material_interface', $urn, $page);

            // Создадим материалы
            for ($i = 0; $i < 3; $i++) {
                $temp = $this->nextText;
                $Item = new Material(array(
                    'pid' => (int)$MT->id,
                    'vis' => 1,
                    'name' => $temp['name'],
                    'description' => $temp['text'],
                    'priority' => ($i + 1) * 10,
                    'sitemaps_priority' => 0.5
                ));
                $Item->commit();
                for ($j = 0; $j < 10; $j++) {
                    $row = $this->nextImage;
                    $att = $this->getAttachmentFromFilename($row['filename'], $row['url'], $MT->fields['images']);
                    $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
                    $Item->fields['images']->addValue(json_encode($row));
                }
            }
        }
    }


    /**
     * Создаем поиск
     * @return Page Созданная или существующая страница поиска
     */
    public function createSearch()
    {
        $name = $this->view->_('SITE_SEARCH');
        $urn = 'search';
        $VF = Snippet_Folder::importByURN('__raas_views');

        $temp = Snippet::importByURN('search');
        if (!$temp->id) {
            $f = $this->resourcesDir . '/search.tmp.php';
            $S = new Snippet(array('name' => $name, 'urn' => 'search', 'pid' => $VF->id, 'description' => file_get_contents($f),));
            $S->commit();
        }

        $temp = Snippet::importByURN('search_form');
        if (!$temp->id) {
            $f = $this->resourcesDir . '/search_form.tmp.php';
            $S = new Snippet(array('name' => $this->view->_('SEARCH_FORM'), 'urn' => 'search_form', 'pid' => $VF->id, 'description' => file_get_contents($f),));
            $S->commit();
            $B = new Block_PHP();
            $this->createBlock($B, 'search_form', '', 'search_form', $this->Site, true);
        }

        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = 'search'")));
        if ($temp) {
            $page = $temp[0];
        } else {
            $page = $this->createPage(array('name' => $name, 'urn' => 'search', 'response_code' => 200), $this->Site);
            $B = new Block_Search(array(
                'search_var_name' => 'search_string',
                'min_length' => 3,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
            ));
            $this->createBlock($B, 'content', '__raas_search_interface', 'search', $page);
        }
        return $page;
    }


    /**
     * Создаем "вопрос-ответ"-образную страницу
     * @param string $name Наименование
     * @param string $urn URN
     * @param string $mainName Название блока на главной
     * @return Page Созданная или существующая страница
     */
    public function createFAQ($name, $urn, $mainName = null)
    {
        if (!$mainName) {
            $mainName = $name;
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
            $text = file_get_contents($f);
            $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
            $text = str_ireplace('{MATERIAL_NAME}', $name, $text);
            $S = new Snippet(array('name' => $name, 'urn' => $urn, 'pid' => $VF->id, 'description' => $text));
            $S->commit();
        }

        $temp = Snippet::importByURN($urn . '_main');
        if (!$temp->id) {
            $f = $this->resourcesDir . '/faq_main.tmp.php';
            $text = file_get_contents($f);
            $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
            $text = str_ireplace('{FAQ_NAME}', $name, $text);
            $S = new Snippet(array('name' => $mainName, 'urn' => $urn . '_main', 'pid' => $VF->id, 'description' => $text));
            $S->commit();
        }

        $temp = Page::getSet(array('where' => array("pid = " . (int)$this->Site->id, "urn = '" . $urn . "'")));
        if ($temp) {
            $faqPage = $temp[0];
        } else {
            $faqPage = $this->createPage(array('name' => $name, 'urn' => $urn), $this->Site);
            $B = new Block_Material(array(
                'material_type' => (int)$MT->id,
                'nat' => 1,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'desc!',
            ));
            $this->createBlock($B, 'content', '__raas_material_interface', $urn, $faqPage);

            $B = new Block_HTML(array('description' => '<p>' . $this->view->_($urn == 'reviews' ? 'YOU_CAN_LEAVE_YOUR_RESPONSE' : 'YOU_CAN_ASK_YOUR_QUESTION') . '</p>',));
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
            $this->createBlock($B, 'left', '__raas_material_interface', $urn . '_main', $this->Site, true, array($faqPage->id));

            // Создадим материалы
            for ($i = 0; $i < 3; $i++) {
                $user = $this->nextUser;
                $answer = $this->nextUser;
                $temp = $this->nextText;
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
                $att = $this->getAttachmentFromFilename($user['pic']['name'], $user['pic']['filepath'], $MT->fields['image']);
                $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
                $Item->fields['image']->addValue(json_encode($row));
                $att = $this->getAttachmentFromFilename($answer['pic']['name'], $answer['pic']['filepath'], $MT->fields['answer_image']);
                $row = array('vis' => 1, 'name' => '', 'description' => '', 'attachment' => (int)$att->id);
                $Item->fields['answer_image']->addValue(json_encode($row));
            }
        }
    }


    public function createBlock(Block $B, $location, $interface, $widget, $startPage, $inherit = false, array $excludeFromInheritance = array())
    {
        $B->location = $location;
        $B->vis = 1;
        $B->author_id = $B->editor_id = Application::i()->user->id;
        if ($inherit) {
            $cats = array_merge(array($startPage), (array)$startPage->all_children);
            $cats = array_filter(
                $cats,
                function ($x) use ($startPage) {
                    return $x->template == $startPage->template;
                }
            );
            $cats = array_values($cats);
            $B->inherit = 1;
        } else {
            $cats = array($startPage);
        }
        $catsIds = array_map(
            function ($x) {
                return (int)$x->id;
            },
            $cats
        );
        $catsIds = array_diff($catsIds, $excludeFromInheritance);
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
        return $B;
    }
}
