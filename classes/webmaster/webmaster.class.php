<?php
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Application;
use RAAS\Attachment;

/**
 * Класс вебмастера
 * @property-read Page $Site Первая корневая страница
 * @property-read string $nextImage URL следующей картинки
 * @property-read [
 *                    'name' => string Заголовок статьи,
 *                    'text' => string Текст статьи в формате HTML,
 *                    'brief' => string Краткий текст статьи в формате
 *                                      plain text,
 *                ] $nextText Следующий текст
 * @property-read [
 *                    'name' => [
 *                        'first' => string Имя,
 *                        'last' => string Фамилия,
 *                        'middle' => string Отчество
 *                    ],
 *                    'location' => [
 *                        'building' => int Дом,
 *                        'street' => string Улица,
 *                        'city' => string Город,
 *                        'state' => string Область,
 *                        'zip' => int Индекс,
 *                    ],
 *                    'username' => string Логин,
 *                    'email' => string E-mail,
 *                    'password' => string Пароль,
 *                    'salt' => string Соль для пароля,
 *                    'md5' => string MD5 пароля,
 *                    'sha1' => string SHA1 пароля,
 *                    'sha256' => string SHA256 пароля,
 *                    'phone' => string Телефон,
 *                    'cell' => string Мобильный телефон,
 *                    'picture' => [
 *                        'large' => string URL большой картинки,
 *                        'medium' => string URL средней картинки,
 *                        'thumbnail' => string URL эскиза
 *                    ],
 *                    'pic' => [
 *                        'name' => string Имя оригинального файла,
 *                        'filepath' => string Местоположение файла во временной
 *                                             папке
 *                    ],
 *                ] $nextUser Следующий пользователь
 */
class Webmaster
{
    /**
     * Количество получаемых изображений
     */
    const IMAGES_TO_RETRIEVE = 10;

    /**
     * Количество получаемых текстов
     */
    const TEXTS_TO_RETRIEVE = 10;

    /**
     * Количество получаемых людей
     */
    const USERS_TO_RETRIEVE = 10;

    protected static $instance;

    /**
     * Полученные изображения
     * @var array<
     *          [
     *              'url' : string =>  URL изображения,
     *              'filename' : string => путь к файлу локальной сохраненной
     *                                     копии
     *          ]
     *      >
     */
    protected static $imagesRetrieved = [];

    /**
     * Полученные тексты
     * @var array<
     *          [
     *              'name' : string => заголовок текста,
     *              'text' : string => HTML-текст,
     *              'brief' : string => краткое описание
     *          ]
     *      >
     */
    protected static $textsRetrieved = [];

    /**
     * Полученные люди
     * @var array<
     *          [
     *              'name' : string => имя,
     *              'phone' : string => телефон,
     *              'email' : string => e-mail,
     *              'pic' => [
     *                  'filepath': string => путь к файлу локальной сохраненной
     *                                        копии,
     *                  'name': string => имя файла
     *              ]
     *          ]
     *      >
     */
    protected static $usersRetrieved = [];

    /**
     * Корневая страница
     * @var Page
     */
    protected $site;

    /**
     * Папка интерфейсов
     * @var Snippet_Folder
     */
    protected $_interfacesFolder;

    /**
     * Папка представлений
     * @var Snippet_Folder
     */
    protected $_widgetsFolder;

    /**
     * Карта сайта
     * @var Page
     */
    protected static $map;

    public function __get($var)
    {
        switch ($var) {
            case 'nextImage':
                if (!self::$imagesRetrieved) {
                    $fpr = new FishPhotosRetriever();
                    self::$imagesRetrieved = $fpr->retrieve(
                        self::IMAGES_TO_RETRIEVE
                    );
                }
                $images = self::$imagesRetrieved;
                shuffle($images);
                $image = array_shift($images);
                return $image;
                break;
            case 'nextText':
                if (!self::$textsRetrieved) {
                    $fpr = new FishYandexReferatsRetriever();
                    for ($i = 0; $i < self::TEXTS_TO_RETRIEVE; $i++) {
                        self::$textsRetrieved[] = $fpr->retrieve();
                    }
                }
                $texts = self::$textsRetrieved;
                shuffle($texts);
                $temp = array_shift($texts);
                return $temp;
                break;
            case 'nextUser':
                if (!self::$usersRetrieved) {
                    $fpr = new FishRandomUserRetriever();
                    for ($i = 0; $i < self::USERS_TO_RETRIEVE; $i++) {
                        self::$usersRetrieved[] = $fpr->retrieve();
                    }
                }
                $users = self::$usersRetrieved;
                shuffle($users);
                $temp = array_shift($users);
                return $temp;
                break;
            case 'Site':
                if (!$this->site) {
                    $sites = Page::getSet([
                        'where' => "NOT pid",
                        'orderBy' => "priority",
                        'limit' => 1,
                    ]);
                    $this->site = array_shift($sites);
                    $this->site->trust();
                }
                return $this->site;
                break;
            case 'interfacesFolder':
                if (!$this->_interfacesFolder) {
                    $interfacesFolder = Snippet_Folder::importByURN('__raas_interfaces');
                    if ($interfacesFolder->id) {
                        $this->_interfacesFolder = $interfacesFolder;
                        $this->_interfacesFolder->trust();
                    }
                }
                return $this->_interfacesFolder;
                break;
            case 'widgetsFolder':
                if (!$this->_widgetsFolder) {
                    $widgetsFolder = Snippet_Folder::importByURN('__raas_views');
                    if ($widgetsFolder->id) {
                        $this->_widgetsFolder = $widgetsFolder;
                        $this->_widgetsFolder->trust();
                    }
                }
                return $this->_widgetsFolder;
                break;
            default:
                return Package::i()->__get($var);
                break;
        }
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
    public function checkSnippet(
        Snippet_Folder $parent,
        $urn,
        $name,
        $description,
        $locked = true
    ) {
        $snippet = Snippet::importByURN($urn);
        if (!$snippet->id) {
            $snippet = new Snippet([
                'pid' => (int)$parent->id,
                'urn' => $urn,
                'locked' => (int)$locked
            ]);
        }
        if ($locked || !$snippet->id) {
            $snippet->name = $this->view->_($name);
            $snippet->description = $description;
        }
        $snippet->commit();
        return $snippet;
    }


    /**
     * Создает сниппет по шаблону
     * @param string $urn URN сниппета
     * @param string $name Наименование
     * @param int $folderId ID# папки, куда размещаем
     * @param string $templateSnippetFilename Имя файла, откуда берем код
     * @param array<string[] => mixed> $replaceData Данные для подстановки в код
     */
    public function createSnippet(
        $urn,
        $name,
        $folderId,
        $templateSnippetFilename,
        $replaceData = []
    ) {
        $snippetText = file_get_contents($templateSnippetFilename);
        if ($replaceData) {
            $newReplaceData = [];
            foreach ($replaceData as $key => $val) {
                $newReplaceData['{{' . $key . '}}'] = $val;
            }
            $snippetText = strtr($snippetText, $newReplaceData);
        }
        $snippet = new Snippet([
            'name' => $name,
            'urn' => $urn,
            'pid' => $folderId,
            'description' => $snippetText,
        ]);
        $snippet->commit();
        return $snippet;
    }


    /**
     * Создаем стандартные интерфейсы
     * @return array[Snippet] массив созданных или существующих интерфейсов
     */
    public function checkStdInterfaces()
    {
        if (!$this->interfacesFolder) {
            $this->SQL->add(
                SOME::_dbprefix() . "cms_snippet_folders",
                [
                    'urn' => '__raas_interfaces',
                    'name' => View_Web::i()->_('INTERFACES'),
                    'pid' => 0,
                    'locked' => 1,
                ]
            );
        }
        if (!$this->widgetsFolder) {
            $this->SQL->add(
                SOME::_dbprefix() . "cms_snippet_folders",
                [
                    'urn' => '__raas_views',
                    'name' => View_Web::i()->_('VIEWS'),
                    'pid' => 0,
                    'locked' => 1,
                ]
            );
        }

        $interfaces = [];
        $interfacesData = [
            '__raas_material_interface' => [
                'name' => 'MATERIAL_STANDARD_INTERFACE',
                'filename' => 'material_interface',
            ],
            '__raas_form_interface' => [
                'name' => 'FORM_STANDARD_INTERFACE',
                'filename' => 'form_interface',
            ],
            '__raas_menu_interface' => [
                'name' => 'MENU_STANDARD_INTERFACE',
                'filename' => 'menu_interface',
            ],
            '__raas_search_interface' => [
                'name' => 'SEARCH_STANDARD_INTERFACE',
                'filename' => 'search_interface',
            ],
            '__raas_form_notify' => [
                'name' => 'FORM_STANDARD_NOTIFICATION',
                'filename' => 'form_notification',
            ],
            '__raas_cache_interface' => [
                'name' => 'CACHE_STANDARD_INTERFACE',
                'filename' => 'cache_interface',
            ],
            '__raas_watermark_interface' => [
                'name' => 'WATERMARK_STANDARD_INTERFACE',
                'filename' => 'watermark_interface',
            ],
        ];
        foreach ($interfacesData as $interfaceURN => $interfaceData) {
            $interfaces[$interfaceURN] = $this->checkSnippet(
                $this->interfacesFolder,
                $interfaceURN,
                $interfaceData['name'],
                file_get_contents(
                    Package::i()->resourcesDir .
                    '/interfaces/' . $interfaceData['filename'] . '.php'
                )
            );
        }
        return $interfaces;
    }


    /**
     * Добавим стандартный шаблон
     * @return Template шаблон, созданный или первый найденный
     */
    public function createTemplate()
    {
        $temp = Template::getSet();
        if ($temp) {
            return $temp[0];
        }

        $locations = [
            [1, ['menu_top', 9], ['socials_top', 3]],
            [2, ['logo', 4], ['contacts_top', 4], ['menu_user', 4]],
            [1, ['menu_main', 9], ['search_form', 3]],
            [1, ['banners', 12]],
            [4, ['left', 3], ['content', 6], ['right', 3]],
            [1, ['', 3], ['share', 6]],
        ];
        for ($i = 2; $i <= 5; $i++) {
            $locations[] = [
                2,
                ['left' . $i, 3],
                ['content' . $i, 6],
                ['right' . $i, 3]
            ];
        }
        $locations[] = [
            2,
            ['copyrights', 3],
            ['contacts_bottom', 3],
            ['menu_bottom', 3],
            ['socials_bottom', 3]
        ];
        $locations[] = [2, ['head_counters', 6], ['footer_counters', 6]];
        $locations[] = [2, ['top_body_counters', 6]];

        $locationsInfo = [];
        $gap = 10;
        $rowHeight = 60;
        $colWidth = 50;
        $y = $gap;
        foreach ($locations as $row) {
            $locationHeight = $row[0] * $rowHeight;
            $x = $gap;
            for ($i = 1; $i < count($row); $i++) {
                $locationURI = $row[$i][0];
                $locationWidth = $row[$i][1] * $colWidth - $gap;
                if ($locationURI) {
                    $locationsInfo[$locationURI] = [
                        'urn' => $locationURI,
                        'x' => $x,
                        'y' => $y,
                        'width' => $locationWidth,
                        'height' => $locationHeight
                    ];
                }
                $x += $locationWidth + $gap;
            }
            $y += $locationHeight + $gap;
        }
        $locationsInfo['footer_counters']['height'] = ($locationsInfo['footer_counters']['height'] * 2) + $gap;

        $template = new Template([
            'name' => View_Web::i()->_('MAIN_TEMPLATE'),
            'urn' => 'main',
            'description' => file_get_contents(
                Package::i()->resourcesDir . '/template.tmp.php'
            ),
            'locations_info' => json_encode($locationsInfo),
            'width' => ($colWidth * 12) + $gap,
            'height' => $y,
        ]);
        $template->commit();
        return $template;
    }


    /**
     * Добавим поля страниц
     * @return array[Page_Field] массив созданных или существующих полей
     */
    public function createPageFields()
    {
        $fields = [];
        foreach ([
            [
                'name' => View_Web::i()->_('DESCRIPTION'),
                'urn' => '_description_',
                'datatype' => 'htmlarea'
            ],
            [
                'name' => View_Web::i()->_('IMAGE'),
                'urn' => 'image',
                'datatype' => 'image',
                'show_in_table' => 1,
            ],
            [
                'name' => View_Web::i()->_('NO_INDEX'),
                'urn' => 'noindex',
                'datatype' => 'checkbox'
            ],
            [
                'name' => View_Web::i()->_('BACKGROUND'),
                'urn' => 'background',
                'datatype' => 'image'
            ],
        ] as $row) {
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
     * @return array<Snippet> Массив созданных или существующих виджетов
     */
    public function createWidgets()
    {
        $widgets = [];
        $widgetsData = [
            'share/share' => View_Web::i()->_('SHARE'),
            'triggers/triggers' => View_Web::i()->_('TRIGGERS'),
            'pagination/pagination' => View_Web::i()->_('PAGINATION'),
            'breadcrumbs/breadcrumbs' => View_Web::i()->_('BREADCRUMBS'),
            'sitemap/sitemap_xml' => View_Web::i()->_('SITEMAP_XML'),
            'cookies_notification/cookies_notification' => View_Web::i()->_('COOKIES_NOTIFICATION'),
            'feedback/feedback' => View_Web::i()->_('FEEDBACK'),
            'feedback/feedback_modal' => View_Web::i()->_('FEEDBACK_MODAL'),
            'feedback/order_call_modal' => View_Web::i()->_('ORDER_CALL_MODAL'),
        ];
        foreach ($widgetsData as $url => $name) {
            $urn = explode('/', $url);
            $urn = $urn[count($urn) - 1];
            $widget = Snippet::importByURN($urn);
            if (!$widget->id) {
                $widget = $this->createSnippet(
                    $urn,
                    $name,
                    (int)$this->widgetsFolder->id,
                    Package::i()->resourcesDir . '/widgets/' . $url . '.tmp.php',
                    [
                        'WIDGET_NAME' => $name,
                        'WIDGET_URN' => $urn,
                        'WIDGET_CSS_CLASSNAME' => str_replace('_', '-', $urn)
                    ]
                );
            }
            $widgets[$urn] = $widget;
        }

        return $widgets;
    }


    /**
     * Создадим формы
     * @param array<[
     *          'name' => string Наименование,
     *          'urn' => string URN,
     *          'fields' => array<[
     *              'name' => string Наименование,
     *              'urn' => string URN,
     *              'required' => bool Поле обязательно для заполнения,
     *              'datatype' => string Тип данных,
     *              'show_in_table' => bool Отображать в таблице,
     *          ]> Поля формы
     *      ]> $formsData Данные по формам
     * @return array<
     *             string[] URN формы => Form
     *         > Созданные или существующие формы
     */
    public function createForms(array $formsData)
    {
        $forms = [];
        foreach ($formsData as $formData) {
            $form = Form::importByURN($formData['urn']);
            if (!$form->id) {
                $form = $this->createForm($formData);
            }
            $forms[$formData['urn']] = $form;
        }
        return $forms;
    }


    /**
     * Создает одну форму
     * @param [
     *          'name' => string Наименование,
     *          'urn' => string URN,
     *          'fields' => array<[
     *              'name' => string Наименование,
     *              'urn' => string URN,
     *              'required' => bool Поле обязательно для заполнения,
     *              'datatype' => string Тип данных,
     *              'show_in_table' => bool Отображать в таблице,
     *          ]> Поля формы
     *      ] $formData Данные по форме
     * @return Form Созданная форма
     */
    public function createForm(array $formData)
    {
        $form = new Form([
            'name' => trim($formData['name']),
            'urn' => trim($formData['urn']),
            'material_type' => (int)$formData['material_type'],
            'create_feedback' => (int)!$formData['material_type'],
            'signature' => true,
            'antispam' => 'hidden',
            'antispam_field_name' => '_name',
            'interface_id' => (int)$formData['interface_id'],
        ]);
        $form->commit();
        foreach ((array)$formData['fields'] as $fieldData) {
            $fieldData['pid'] = (int)$form->id;
            $fieldData['required'] = (int)(bool)$fieldData['required'];
            $fieldData['show_in_table'] = (int)(bool)$fieldData['show_in_table'];
            $field = new Form_Field($fieldData);
            $field->commit();
        }
        return $form;
    }


    /**
     * Создадим меню
     * @param array<[
     *          'pageId' => int ID# страницы, от которой создается меню,
     *          'urn' => string URN,
     *          'inherit' => int Уровень наследования,
     *          'name' => string Наименование,
     *          'realize' => bool Распаковать меню,
     *          'addMainPageLink' => bool Добавить первой ссылку на главную,
     *          'blockLocation' => string Размещение для блока,
     *          'fullMenu' => bool Полное меню,
     *          'blockPage' => Page Страница, где размещаем меню,
     *          'inheritBlock' => bool Наследовать блок,
     *      ]> $menusData Данные по создаваемым меню
     * @return array<string[] URN меню => Menu> Созданные или существующие меню
     */
    public function createMenus(array $menusData = [])
    {
        $menuWidgetFilename = Package::i()->resourcesDir
                            . '/widgets/menu/menu.tmp.php';
        $cacheInterfaceId = Snippet::importByURN('__raas_cache_interface')->id;
        $menuInterface =  Snippet::importByURN('__raas_menu_interface');
        $menus = [];
        foreach ($menusData as $menuData) {
            // Создадим собственно меню
            $menu = Menu::importByURN($menuData['urn']);
            if (!$menu->id) {
                $menu = $this->createMenu(
                    trim($menuData['urn']),
                    trim($menuData['name']),
                    (int)$menuData['pageId'],
                    (int)$menuData['inherit'],
                    (bool)$menuData['realize'],
                    (bool)$menuData['addMainPageLink']
                );
            }
            $menus[$menuData['urn']] = $menu;

            // Создадим виджет под меню
            $menuWidget = Snippet::importByURN('menu_' . $menuData['urn']);
            if (!$menuWidget->id) {
                $menuWidget = $this->createSnippet(
                    trim('menu_' . $menuData['urn']),
                    trim($menuData['name']),
                    (int)$this->widgetsFolder->id,
                    trim($menuWidgetFilename),
                    [
                        'MENU_NAME' => $menuData['name'],
                        'MENU_CSS_CLASSNAME' => 'menu-' . $menuData['urn'],
                    ]
                );
            }

            // Создадим блок
            if ($menuData['blockLocation']) {
                $blockData = [
                    'menu' => (int)$menu->id,
                    'full_menu' => (int)$menuData['fullMenu']
                ];
                if ($menuData['fullMenu']) {
                    $blockData['cache_type'] = Block::CACHE_DATA;
                    $blockData['cache_interface_id'] = (int)$cacheInterfaceId;
                }
                $this->createBlock(
                    new Block_Menu($blockData),
                    $menuData['blockLocation'],
                    $menuInterface,
                    $menuWidget,
                    $menuData['blockPage'] ?: $this->Site,
                    (bool)$menuData['inheritBlock']
                );
            }
        }

        return $menus;
    }


    /**
     * Создает одно меню
     * @param string $urn URN меню
     * @param string $name Наименование меню
     * @param int $pageId ID# страницы, из которой создаем меню
     * @param int $inherit Уровень наследования,
     * @param bool $realize Распаковать меню
     * @param bool $addMainPageLink Создать первой ссылку на главную страницу
     * @return Menu
     */
    public function createMenu(
        $urn,
        $name,
        $pageId = 0,
        $inherit = 0,
        $realize = false,
        $addMainPageLink = false
    ) {
        $menuData = [
            'urn' => $urn,
            'name' => $name,
            'page_id' => $pageId,
            'inherit' => $inherit,
        ];
        $menu = new Menu($menuData);
        $menu->commit();
        $menu->rollback();
        if ($realize) {
            $menu->realize();
            $sqlQuery = "SET @priority := ?";
            $sqlBind = [$addMainPageLink ? 10 : 0];
            $this->SQL->query([$sqlQuery, $sqlBind]);
            $sqlQuery = "UPDATE " . Menu::_tablename()
                      . " SET priority = (@priority := @priority + ?)
                        WHERE pid = ?
                     ORDER BY priority";
            $sqlBind = [10, (int)$menu->id];
            $this->SQL->query([$sqlQuery, $sqlBind]);
            if ($addMainPageLink) {
                $mainLink = new Menu([
                    'pid' => $menu->id,
                    'page_id' => $this->Site->id,
                    'name' => $this->Site->name,
                    'priority' => 10,
                ]);
                $mainLink->commit();
            }
        }
        return $menu;
    }


    /**
     * Создает компанию и связанные блоки
     */
    public function createCompany()
    {
        $materialType = Material_Type::importByURN('company');
        if (!$materialType->id) {
            $materialType = new Material_Type([
                'name' => View_Web::i()->_('COMPANY'),
                'urn' => 'company',
                'global_type' => 1
            ]);
            $materialType->commit();
            $newMaterialType = true;
        }
        $materialTemplate = new CompanyTemplate($materialType, $this);
        if ($newMaterialType) {
            $fields = $materialTemplate->createFields();
            $materialTemplate->createMaterials();
        }
        $logoWidget = Snippet::importByURN('logo');
        if (!$logoWidget->id) {
            $logoWidget = $materialTemplate->createLogoBlockSnippet();
            $logoBlock = $materialTemplate->createLogoBlock(
                $this->Site,
                $logoWidget,
                ['nat' => 0]
            );
        }

        $contactsTopWidget = Snippet::importByURN('contacts_top');
        if (!$widget->id) {
            $contactsTopWidget = $materialTemplate->createContactsTopBlockSnippet();
            $contactsTopBlock = $materialTemplate->createContactsTopBlock(
                $this->Site,
                $contactsTopWidget,
                ['nat' => 0]
            );
        }

        $contactsBottomWidget = Snippet::importByURN('contacts_bottom');
        if (!$contactsBottomWidget->id) {
            $contactsBottomWidget = $materialTemplate->createContactsBottomBlockSnippet();
            $contactsBottomBlock = $materialTemplate->createContactsBottomBlock(
                $this->Site,
                $contactsBottomWidget,
                ['nat' => 0]
            );
        }

        $socialsTopWidget = Snippet::importByURN('socials_top');
        if (!$socialsTopWidget->id) {
            $socialsTopWidget = $materialTemplate->createSocialsTopBlockSnippet();
            $socialsTopBlock = $materialTemplate->createSocialsTopBlock(
                $this->Site,
                $socialsTopWidget,
                ['nat' => 0]
            );
        }

        $socialsBottomWidget = Snippet::importByURN('socials_bottom');
        if (!$socialsBottomWidget->id) {
            $socialsBottomWidget = $materialTemplate->createSocialsBottomBlockSnippet();
            $socialsBottomBlock = $materialTemplate->createSocialsBottomBlock(
                $this->Site,
                $socialsBottomWidget,
                ['nat' => 0]
            );
        }

        $copyrightsWidget = Snippet::importByURN('copyrights');
        if (!$copyrightsWidget->id) {
            $copyrightsWidget = $materialTemplate->createCopyrightsBlockSnippet();
            $copyrightsBlock = $materialTemplate->createCopyrightsBlock(
                $this->Site,
                $copyrightsWidget,
                ['nat' => 0]
            );
        }
    }


    /**
     * Создает баннеры
     */
    public function createBanners()
    {
        $materialType = Material_Type::importByURN('banners');
        if (!$materialType->id) {
            $materialType = new Material_Type([
                'name' => View_Web::i()->_('BANNERS'),
                'urn' => 'banners',
                'global_type' => 1
            ]);
            $materialType->commit();
            $newMaterialType = true;
        }
        $materialTemplate = new BannersTemplate($materialType, $this);
        if ($newMaterialType) {
            $fields = $materialTemplate->createFields();
        }
        $widget = Snippet::importByURN('banners');
        if (!$widget->id) {
            $widget = $materialTemplate->createBlockSnippet();
            $block = $materialTemplate->createBlock(
                $this->Site,
                $widget,
                ['nat' => 0]
            );
            $materialTemplate->createMaterials();
        }
    }


    /**
     * Создает особенности
     */
    public function createFeatures()
    {
        $materialType = Material_Type::importByURN('features');
        if (!$materialType->id) {
            $materialType = new Material_Type([
                'name' => View_Web::i()->_('FEATURES'),
                'urn' => 'features',
                'global_type' => 1
            ]);
            $materialType->commit();
            $newMaterialType = true;
        }
        $materialTemplate = new FeaturesTemplate($materialType, $this);
        if ($newMaterialType) {
            $fields = $materialTemplate->createFields();
        }
        $widget = Snippet::importByURN('features_main');
        if (!$widget->id) {
            $widget = $materialTemplate->createMainPageSnippet();
            $block = $materialTemplate->createBlock(
                $this->Site,
                $widget,
                ['nat' => 0]
            );
            $materialTemplate->createMaterials();
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
        if (!$this->site->id) {
            $host = $_SERVER['HTTP_HOST'];
            if (stristr($host, '.volumnet.ru')) {
                $urn = str_replace('.volumnet.ru', '', $host) . ' ' . $host;
            } else {
                $urn = $host . ' ' . $host . '.volumnet.ru';
            }
            $this->site = $this->createPage([
                'name' => View_Web::i()->_('MAIN_PAGE'),
                'urn' => $urn,
                'template' => (int)$template->id,
                'inherit_cache' => 0
            ]);

            $this->createBlock(
                new Block_Form([
                    'form' => $forms['feedback']->id ?: 0
                ]),
                'footer_counters',
                '__raas_form_interface',
                'feedback_modal',
                $this->site,
                true
            );

            $this->createBlock(
                new Block_Form([
                    'form' => $forms['order_call']->id ?: 0
                ]),
                'footer_counters',
                '__raas_form_interface',
                'order_call_modal',
                $this->site,
                true
            );

            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('YANDEX_METRIKA'),
                    'description' => '',
                    'wysiwyg' => 0,
                ]),
                'top_body_counters',
                null,
                null,
                $this->site,
                true
            );

            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('GOOGLE_ANALYTICS'),
                    'description' => '',
                    'wysiwyg' => 0
                ]),
                'head_counters',
                null,
                null,
                $this->site,
                true
            );

            $this->createBlock(
                new Block_PHP(),
                'footer_counters',
                null,
                'triggers',
                $this->site,
                true
            );

            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('COOKIES_NOTIFICATION'),
                    'description' => file_get_contents(
                        Package::i()->resourcesDir .
                        '/html/privacy/cookies_notification.html'
                    ),
                    'wysiwyg' => 0
                ]),
                'footer_counters',
                null,
                'cookies_notification',
                $this->site,
                true
            );

            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('WELCOME'),
                    'description' => file_get_contents(
                        Package::i()->resourcesDir . '/html/main/main.html'
                    ),
                    'wysiwyg' => 1,
                ]),
                'content',
                null,
                null,
                $this->site
            );

            $this->createFeatures();
        }
        return $this->site;
    }


    /**
     * Создаем разделы "Наши услуги"
     * @return Page Страница "Наши услуги"
     */
    public function createServices()
    {
        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'services'"]
        ]);
        if ($temp) {
            $services = $temp[0];
        } else {
            $services = $this->createPage(
                [
                    'name' => View_Web::i()->_('OUR_SERVICES'),
                    'urn' => 'services'
                ],
                $this->Site,
                true
            );
            for ($i = 1; $i <= 3; $i++) {
                $service = $this->createPage(
                    [
                        'name' => View_Web::i()->_('OUR_SERVICE') . ' ' . $i,
                        'urn' => 'service' . $i
                    ],
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
        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'contacts'"]
        ]);
        if ($temp) {
            $contacts = $temp[0];
        } else {
            $contacts = $this->createPage(
                ['name' => View_Web::i()->_('CONTACTS'), 'urn' => 'contacts'],
                $this->Site
            );
            $contactsWidget = Snippet::importByURN('contacts');
            if (!$contactsWidget->id) {
                $materialType = Material_Type::importByURN('company');
                $materialTemplate = new CompanyTemplate($materialType, $this);
                $contactsWidget = $materialTemplate->createContactsBlockSnippet();
                $contactsBlock = $materialTemplate->createContactsBlock(
                    $contacts,
                    $contactsWidget,
                    ['nat' => 0]
                );
            }

            $this->createBlock(
                new Block_Form([
                    'form' => (int)$feedbackForm->id
                ]),
                'content',
                '__raas_form_interface',
                'feedback',
                $contacts
            );
        }
        return $contacts;
    }


    /**
     * Создаем страницу "Обработка персональных данных"
     * @return Page Созданная или существующая страница
     */
    public function createPrivacy()
    {
        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'privacy'"]
        ]);
        if ($temp) {
            $privacy = $temp[0];
        } else {
            $privacy = $this->createPage(
                [
                    'name' => View_Web::i()->_('PRIVACY_PAGE_NAME'),
                    'urn' => 'privacy',
                    'response_code' => 200,
                ],
                $this->Site
            );
            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('PRIVACY_PAGE_NAME'),
                    'description' => file_get_contents(
                        Package::i()->resourcesDir .
                        '/html/privacy/privacy_page.html'
                    ),
                    'wysiwyg' => 1,
                ]),
                'content',
                null,
                null,
                $privacy
            );
        }

        $this->createBlock(
            new Block_HTML([
                'name' => View_Web::i()->_('PRIVACY_BLOCK_NAME'),
                'description' => file_get_contents(
                    Package::i()->resourcesDir . '/html/privacy/privacy_block.html'
                ),
                'wysiwyg' => 1,
            ]),
            'copyrights',
            null,
            null,
            $this->site,
            true
        );

        return $privacy;
    }


    /**
     * Создаем страницу 404
     * @return Page Созданная или существующая страница
     */
    public function create404()
    {
        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = '404'"]
        ]);
        if ($temp) {
            $p404 = $temp[0];
        } else {
            $p404 = $this->createPage(
                [
                    'name' => View_Web::i()->_('PAGE_404'),
                    'urn' => '404',
                    'response_code' => 404
                ],
                $this->Site
            );
            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('PAGE_404'),
                    'description' => View_Web::i()->_('PAGE_404_TEXT'),
                    'wysiwyg' => 1,
                ]),
                'content',
                null,
                null,
                $p404
            );
        }
        return $p404;
    }


    /**
     * Создание карты сайта
     * @return Page Созданная или существующая страница
     */
    public function createMap()
    {
        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'map'"]
        ]);
        if ($temp) {
            $map = $temp[0];
        } else {
            $map = $this->createPage(
                [
                    'name' => View_Web::i()->_('SITEMAP'),
                    'urn' => 'map',
                    'response_code' => 200
                ],
                $this->Site
            );
        }
        return $map;
    }


    /**
     * Создание sitemap.xml
     * @return Page Созданная или существующая страница
     */
    public function createSitemapsXml()
    {
        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'sitemaps'"]
        ]);
        if ($temp) {
            $sitemaps = $temp[0];
        } else {
            $sitemaps = $this->createPage(
                [
                    'name' => $this->view->_('SITEMAP_XML'),
                    'urn' => 'sitemaps',
                    'template' => 0,
                    'mime' => 'application/xml',
                    'cache' => 1,
                    'response_code' => 200
                ],
                $this->Site
            );
            $B = new Block_PHP(['name' => $this->view->_('SITEMAP_XML')]);
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
        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'robots'"]
        ]);
        if ($temp) {
            $robots = $temp[0];
        } else {
            $robots = $this->createPage(
                [
                    'name' => View_Web::i()->_('ROBOTS_TXT'),
                    'urn' => 'robots',
                    'template' => 0,
                    'cache' => 1,
                    'response_code' => 200,
                    'mime' => 'text/plain',
                ],
                $this->Site
            );
            $robotsTXT = file_get_contents(
                Package::i()->resourcesDir . '/html/robots/robots.txt'
            );
            $robotsTXT = strtr(
                $robotsTXT,
                ['{{HOST}}' => $_SERVER['HTTP_HOST']]
            );
            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('ROBOTS_TXT'),
                    'description' => $robotsTXT,
                    'wysiwyg' => 0
                ]),
                '',
                null,
                null,
                $robots
            );
        }
        return $robots;
    }


    /**
     * Создание custom.css
     * @return Page Созданная или существующая страница
     */
    public function createCustomCss()
    {
        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'custom_css'"]
        ]);
        if ($temp) {
            $customCss = $temp[0];
        } else {
            $customCss = $this->createPage(
                [
                    'name' => View_Web::i()->_('CUSTOM_CSS'),
                    'urn' => 'custom_css',
                    'template' => 0,
                    'cache' => 1,
                    'response_code' => 200,
                    'mime' => 'text/css',
                ],
                $this->Site
            );
            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('CUSTOM_CSS'),
                    'description' => '',
                    'wysiwyg' => 0
                ]),
                '',
                null,
                null,
                $customCss
            );
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
        $forms = $this->createForms([
            [
                'name' => View_Web::i()->_('FEEDBACK'),
                'urn' => 'feedback',
                'interface_id' => (int)$interfaces['__raas_form_notify']->id,
                'fields' => [
                    [
                        'name' => View_Web::i()->_('YOUR_NAME'),
                        'urn' => 'full_name',
                        'required' => 1,
                        'datatype' => 'text',
                        'show_in_table' => 1,
                    ],
                    [
                        'name' => View_Web::i()->_('PHONE'),
                        'urn' => 'phone',
                        'datatype' => 'text',
                        'show_in_table' => 1,
                    ],
                    [
                        'name' => View_Web::i()->_('EMAIL'),
                        'urn' => 'email',
                        'datatype' => 'text',
                        'show_in_table' => 1,
                    ],
                    [
                        'name' => View_Web::i()->_('QUESTION_TEXT'),
                        'urn' => '_description_',
                        'required' => 1,
                        'datatype' => 'textarea',
                    ],
                    [
                        'name' => View_Web::i()->_('AGREE_PRIVACY_POLICY'),
                        'urn' => 'agree',
                        'required' => 1,
                        'datatype' => 'checkbox',
                    ],
                ],
            ],
            [
                'name' => View_Web::i()->_('ORDER_CALL'),
                'urn' => 'order_call',
                'interface_id' => (int)$interfaces['__raas_form_notify']->id,
                'fields' => [
                    [
                        'name' => View_Web::i()->_('PHONE'),
                        'urn' => 'phone_call',
                        'datatype' => 'text',
                        'show_in_table' => 1,
                    ],
                    [
                        'name' => View_Web::i()->_('AGREE_PRIVACY_POLICY'),
                        'urn' => 'agree',
                        'required' => 1,
                        'datatype' => 'checkbox',
                    ],
                ],
            ]
        ]);
        $this->site = $this->createMainPage($template, $forms);
        $this->createBanners();
        $this->createCompany();
        $this->createPrivacy();

        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'about'"]
        ]);
        if ($temp) {
            $about = $temp[0];
        } else {
            $about = $this->createPage(
                ['name' => View_Web::i()->_('ABOUT_US'), 'urn' => 'about'],
                $this->Site,
                true
            );
        }

        $this->createServices();

        $news = $this->createNews(
            View_Web::i()->_('NEWS'),
            'news',
            View_Web::i()->_('NEWS_MAIN')
        );
        $contacts = $this->createContacts($forms['feedback']);
        $p404 = $this->create404();
        $this->map = $this->createMap();
        $sitemaps = $this->createSitemapsXml();
        $robots = $this->createRobotsTxt();
        $customCss = $this->createCustomCss();
        $menus = $this->createMenus([
            [
                'pageId' => (int)$this->Site->id,
                'urn' => 'top',
                'inherit' => 10,
                'name' => View_Web::i()->_('TOP_MENU'),
                'blockLocation' => 'menu_top',
                'fullMenu' => true,
                'inheritBlock' => true,
            ],
            [
                'pageId' => (int)$this->Site->id,
                'urn' => 'main',
                'inherit' => 10,
                'name' => View_Web::i()->_('MAIN_MENU'),
                'blockLocation' => 'menu_main',
                'fullMenu' => true,
                'inheritBlock' => true,
            ],
            [
                'pageId' => (int)$this->Site->id,
                'urn' => 'bottom',
                'inherit' => 1,
                'name' => View_Web::i()->_('BOTTOM_MENU'),
                'blockLocation' => 'menu_bottom',
                'fullMenu' => true,
                'inheritBlock' => true,
            ],
            [
                'pageId' => (int)$this->Site->id,
                'urn' => 'sitemap',
                'inherit' => 10,
                'name' => View_Web::i()->_('SITEMAP'),
                'blockLocation' => 'content',
                'fullMenu' => false,
                'blockPage' => $this->map,
                'inheritBlock' => false,
            ],
            [
                'pageId' => (int)$this->Site->id,
                'urn' => 'mobile',
                'inherit' => 10,
                'name' => View_Web::i()->_('MOBILE_MENU'),
                'blockLocation' => 'footer_counters',
                'fullMenu' => true,
                'inheritBlock' => true,
            ],
        ]);

        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'ajax'"]
        ]);
        if ($temp) {
            $ajax = $temp[0];
        } else {
            $ajax = $this->createPage(
                [
                    'name' => View_Web::i()->_('AJAX'),
                    'urn' => 'ajax',
                    'template' => 0,
                    'cache' => 0,
                    'response_code' => 200
                ],
                $this->Site
            );
        }

        $this->createBlock(
            new Block_PHP(),
            'share',
            null,
            'share',
            $this->Site,
            true
        );
    }


    /**
     * Создает страницу
     * @param array<string[] => mixed> $params Дополнительные параметры для
     *                                         создания страницы
     * @param Page $parent Родительская страница
     * @param bool $addUnderConstruction Добавить текстовый блок
     *                                   "страница в стадии наполнения"
     * @return Page
     */
    protected function createPage(
        array $params,
        Page $parent = null,
        $addUnderConstruction = false
    ) {
        $uid = Application::i()->user->id;
        $P = new Page([
            'vis' => 1,
            'author_id' => $uid,
            'editor_id' => $uid,
            'cache' => 1,
            'inherit_cache' => 1,
            'inherit_template' => 0,
            'lang' => 'ru',
            'inherit_lang' => 1,
        ]);
        if ($parent) {
            $P->pid = $parent->id;
            foreach ($parent->getArrayCopy() as $key => $val) {
                if (!in_array($key, ['id', 'pid'])) {
                    $P->$key = $val;
                }
            }
        }
        foreach ($params as $key => $val) {
            $P->$key = $val;
        }
        $P->commit();
        if ($addUnderConstruction) {
            $this->createBlock(
                new Block_HTML([
                    'name' => View_Web::i()->_('TEXT_BLOCK'),
                    'description' => '<p>'
                                  .     View_Web::i()->_('PAGE_UNDER_CONSTRUCTION')
                                  .  '</p>',
                    'wysiwyg' => 1,
                ]),
                'content',
                null,
                null,
                $P
            );
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
        $newMaterialType = false;
        $materialType = Material_Type::importByURN($urn);
        if (!$materialType->id) {
            $materialType = new Material_Type([
                'name' => $name,
                'urn' => $urn,
                'global_type' => 1
            ]);
            $materialType->commit();
            $newMaterialType = true;
        }
        $materialTemplate = new NewsTemplate($materialType, $this);
        if ($newMaterialType) {
            $fields = $materialTemplate->createFields();
        }

        $widget = Snippet::importByURN($urn);
        if (!$widget->id) {
            $widget = $materialTemplate->createBlockSnippet();
        }

        if ($nameMain) {
            $mainWidget = Snippet::importByURN($urn . '_main');
            if (!$mainWidget->id) {
                $mainWidget = $materialTemplate->createMainPageSnippet();
            }
        }

        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = '" . $urn . "'"]
        ]);
        if ($temp) {
            $page = $temp[0];
        } else {
            $page = $materialTemplate->createPage($this->Site);
            $block = $materialTemplate->createBlock($page, $widget);
            if ($nameMain) {
                $blockMain = $materialTemplate->createBlock(
                    $this->Site,
                    $mainWidget,
                    [
                        'nat' => 0,
                        'pages_var_name' => '',
                        'rows_per_page' => 0,
                    ]
                );
            }
            // Создадим материалы
            $materialTemplate->createMaterials();
        }
        return $page;
    }


    /**
     * Создает раздел "Фотогалерея"
     * @param string $name Наименование модуля
     * @param string $urn URN модуля
     */
    public function createPhotos($name, $urn)
    {
        $temp = Material_Type::importByURN($urn);
        if (!$temp->id) {
            $MT = new Material_Type([
                'name' => $name,
                'urn' => $urn,
                'global_type' => 1,
            ]);
            $MT->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('IMAGE'),
                'multiple' => 1,
                'urn' => 'images',
                'datatype' => 'image',
                'show_in_table' => 1,
            ]);
            $F->commit();
        }

        $temp = Snippet::importByURN($urn);
        if (!$temp->id) {
            $f = Package::i()->resourcesDir . '/photos.tmp.php';
            $text = file_get_contents($f);
            $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
            $text = str_ireplace('{MATERIAL_NAME}', $name, $text);
            $S = new Snippet([
                'name' => $name,
                'urn' => $urn,
                'pid' => $this->widgetsFolder->id,
                'description' => $text
            ]);
            $S->commit();
        }

        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'photos'"]
        ]);
        if ($temp) {
            $page = $temp[0];
        } else {
            $page = $this->createPage(
                ['name' => $name, 'urn' => $urn],
                $this->Site
            );
            $blockMaterial = new Block_Material([
                'material_type' => (int)$MT->id,
                'nat' => 1,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'asc!',
            ]);
            $this->createBlock(
                $blockMaterial,
                'content',
                '__raas_material_interface',
                $urn,
                $page
            );

            // Создадим материалы
            for ($i = 0; $i < 3; $i++) {
                $temp = $this->nextText;
                $item = new Material([
                    'pid' => (int)$MT->id,
                    'vis' => 1,
                    'name' => $temp['name'],
                    'description' => $temp['text'],
                    'priority' => ($i + 1) * 10,
                    'sitemaps_priority' => 0.5
                ]);
                $item->commit();
                for ($j = 0; $j < 10; $j++) {
                    $att = Attachment::createFromFile(
                        $this->nextImage,
                        $MT->fields['images']
                    );
                    $item->fields['images']->addValue(json_encode([
                        'vis' => 1,
                        'name' => '',
                        'description' => '',
                        'attachment' => (int)$att->id
                    ]));
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
        $name = View_Web::i()->_('SITE_SEARCH');
        $urn = 'search';

        $temp = Snippet::importByURN('search');
        if (!$temp->id) {
            $f = Package::i()->resourcesDir . '/search.tmp.php';
            $S = new Snippet([
                'name' => $name,
                'urn' => 'search',
                'pid' => $this->widgetsFolder->id,
                'description' => file_get_contents($f),
            ]);
            $S->commit();
        }

        $temp = Snippet::importByURN('search_form');
        if (!$temp->id) {
            $f = Package::i()->resourcesDir . '/search_form.tmp.php';
            $S = new Snippet([
                'name' => View_Web::i()->_('SEARCH_FORM'),
                'urn' => 'search_form',
                'pid' => $this->widgetsFolder->id,
                'description' => file_get_contents($f),
            ]);
            $S->commit();
            $B = new Block_PHP();
            $this->createBlock(
                $B,
                'search_form',
                '',
                'search_form',
                $this->Site,
                true
            );
        }

        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = 'search'"]
        ]);
        if ($temp) {
            $page = $temp[0];
        } else {
            $page = $this->createPage(
                ['name' => $name, 'urn' => 'search', 'response_code' => 200],
                $this->Site
            );
            $B = new Block_Search([
                'search_var_name' => 'search_string',
                'min_length' => 3,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
            ]);
            $this->createBlock(
                $B,
                'content',
                '__raas_search_interface',
                'search',
                $page
            );
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
            $MT = new Material_Type([
                'name' => $name,
                'urn' => $urn,
                'global_type' => 1,
            ]);
            $MT->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('DATE'),
                'urn' => 'date',
                'datatype' => 'date',
            ]);
            $F->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('PHONE'),
                'urn' => 'phone',
                'datatype' => 'text',
            ]);
            $F->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('EMAIL'),
                'urn' => 'email',
                'datatype' => 'email',
            ]);
            $F->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('IMAGE'),
                'urn' => 'image',
                'datatype' => 'image', 'show_in_table' => 0,
            ]);
            $F->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('ANSWER_DATE'),
                'urn' => 'answer_date',
                'datatype' => 'date',
            ]);
            $F->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('ANSWER_NAME'),
                'urn' => 'answer_name',
                'datatype' => 'text',
            ]);
            $F->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('ANSWER_GENDER'),
                'urn' => 'answer_gender',
                'datatype' => 'select',
                'source_type' => 'ini',
                'source' => '0 = "' . View_Web::i()->_('FEMALE') . '"' . "\n"
                         .  '1 = "' . View_Web::i()->_('MALE') . '"'
            ]);
            $F->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('ANSWER_IMAGE'),
                'urn' => 'answer_image',
                'datatype' => 'image', 'show_in_table' => 0,
            ]);
            $F->commit();

            $F = new Material_Field([
                'pid' => $MT->id,
                'name' => View_Web::i()->_('ANSWER'),
                'urn' => 'answer',
                'datatype' => 'htmlarea',
            ]);
            $F->commit();
        }

        $S = Snippet::importByURN('__raas_form_notify');
        $FRM = Form::importByURN($urn);
        if (!$FRM->id) {
            $FRM = $this->createForm([
                'name' => $name,
                'urn' => $urn,
                'material_type' => (int)$MT->id,
                'interface_id' => (int)$S->id,
                'fields' => [
                    [
                        'name' => View_Web::i()->_('YOUR_NAME'),
                        'urn' => 'name',
                        'required' => 1,
                        'datatype' => 'text',
                        'show_in_table' => 1,
                    ],
                    [
                        'name' => View_Web::i()->_('PHONE'),
                        'urn' => 'phone',
                        'datatype' => 'text',
                        'show_in_table' => 1,
                    ],
                    [
                        'name' => View_Web::i()->_('EMAIL'),
                        'urn' => 'email',
                        'datatype' => 'email',
                        'show_in_table' => 0,
                    ],
                    [
                        'name' => View_Web::i()->_('YOUR_PHOTO'),
                        'urn' => 'image',
                        'datatype' => 'image',
                        'show_in_table' => 0,
                    ],
                    [
                        'name' => View_Web::i()->_('QUESTION_TEXT'),
                        'urn' => 'description',
                        'required' => 1,
                        'datatype' => 'textarea',
                        'show_in_table' => 0,
                    ]
                ]
            ]);
        }

        $temp = Snippet::importByURN($urn);
        if (!$temp->id) {
            $f = Package::i()->resourcesDir . '/faq.tmp.php';
            $text = file_get_contents($f);
            $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
            $text = str_ireplace('{MATERIAL_NAME}', $name, $text);
            $S = new Snippet([
                'name' => $name,
                'urn' => $urn,
                'pid' => $this->widgetsFolder->id,
                'description' => $text
            ]);
            $S->commit();
        }

        $temp = Snippet::importByURN($urn . '_main');
        if (!$temp->id) {
            $f = Package::i()->resourcesDir . '/faq_main.tmp.php';
            $text = file_get_contents($f);
            $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
            $text = str_ireplace('{FAQ_NAME}', $name, $text);
            $S = new Snippet([
                'name' => $mainName,
                'urn' => $urn . '_main',
                'pid' => $this->widgetsFolder->id,
                'description' => $text
            ]);
            $S->commit();
        }

        $temp = Page::getSet([
            'where' => ["pid = " . (int)$this->Site->id, "urn = '" . $urn . "'"]
        ]);
        if ($temp) {
            $faqPage = $temp[0];
        } else {
            $faqPage = $this->createPage(
                ['name' => $name, 'urn' => $urn],
                $this->Site
            );
            $B = new Block_Material([
                'material_type' => (int)$MT->id,
                'nat' => 1,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'desc!',
            ]);
            $this->createBlock(
                $B,
                'content',
                '__raas_material_interface',
                $urn,
                $faqPage
            );

            $B = new Block_HTML(['description' => '<p>' .  View_Web::i()->_(
                $urn == 'reviews' ?
                'YOU_CAN_LEAVE_YOUR_RESPONSE' :
                'YOU_CAN_ASK_YOUR_QUESTION'
            ) .  '</p>']);
            $this->createBlock($B, 'content', null, null, $faqPage);

            $B = new Block_Form(['form' => $FRM->id,]);
            $this->createBlock(
                $B,
                'content',
                '__raas_form_interface',
                'feedback',
                $faqPage
            );

            $B = new Block_Material([
                'material_type' => (int)$MT->id,
                'nat' => 0,
                'pages_var_name' => '',
                'rows_per_page' => 3,
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'desc!',
            ]);
            $this->createBlock(
                $B,
                'left',
                '__raas_material_interface',
                $urn . '_main',
                $this->Site,
                true,
                [$faqPage->id]
            );

            // Создадим материалы
            for ($i = 0; $i < 3; $i++) {
                $user = $this->nextUser;
                $answer = $this->nextUser;
                $temp = $this->nextText;
                $item = new Material([
                    'pid' => (int)$MT->id,
                    'vis' => 1,
                    'name' => $user['name']['first'] . ' '
                           .  $user['name']['last'],
                    'description' => $temp['name'],
                    'priority' => ($i + 1) * 10,
                    'sitemaps_priority' => 0.5
                ]);
                $item->commit();
                $t = time() - 86400 * rand(1, 7);
                $t1 = $t + rand(0, 86400);
                $item->fields['date']->addValue(date('Y-m-d', $t));
                $item->fields['phone']->addValue($user['phone']);
                $item->fields['email']->addValue($user['email']);
                $item->fields['answer_date']->addValue(date('Y-m-d', $t1));
                $item->fields['answer_name']->addValue(
                    $answer['name']['first'] . ' ' . $answer['name']['last']
                );
                $item->fields['answer_gender']->addValue(
                    (int)($answer['gender'] == 'male')
                );
                $item->fields['answer']->addValue($temp['text']);
                $att = Attachment::createFromFile(
                    $user['pic']['filepath'],
                    $MT->fields['image']
                );
                $item->fields['image']->addValue(json_encode([
                    'vis' => 1,
                    'name' => '',
                    'description' => '',
                    'attachment' => (int)$att->id
                ]));
                $att = Attachment::createFromFile(
                    $answer['pic']['filepath'],
                    $MT->fields['answer_image']
                );
                $item->fields['answer_image']->addValue(json_encode([
                    'vis' => 1,
                    'name' => '',
                    'description' => '',
                    'attachment' => (int)$att->id
                ]));
            }
        }
    }


    /**
     * Создать блок
     * @param Block $block Подготовленный для сохранения блок
     * @param string $location Размещение блока
     * @param Snippet|int|string $interface Интерфейс, ID# или URN интерфейса
     *                                      блока
     * @param Snippet|int|string $widget Виджет, ID# или URN виджета блока
     * @param Page $startPage Исходная страница блока
     * @param boolean $inherit Наследовать ли блок
     * @param array<int> $excludeFromInheritanceIds ID# страниц, которые нужно
     *                                              исключить из наследования
     */
    public function createBlock(
        Block $block,
        $location,
        $interface,
        $widget,
        Page $startPage,
        $inherit = false,
        array $excludeFromInheritance = []
    ) {
        $block->location = $location;
        $block->vis = 1;
        $block->author_id = $block->editor_id = Application::i()->user->id;
        $startPage->rollback();
        if ($inherit) {
            $cats = array_values(array_filter(
                $startPage->selfAndChildren,
                function ($x) use ($startPage) {
                    return $x->template == $startPage->template;
                }
            ));
            $block->inherit = 1;
        } else {
            $cats = [$startPage];
        }
        $catsIds = array_map(
            function ($x) {
                return (int)$x->id;
            },
            $cats
        );
        $catsIds = array_diff($catsIds, $excludeFromInheritance);
        $block->cats = $catsIds;
        $block->interface_id = 0;
        $block->widget_id = 0;
        if ($interface) {
            if ($interface instanceof Snippet) {
                $snippetInterface = $interface;
            } elseif (is_numeric($interface)) {
                $snippetInterface = new Snippet($interface);
            } else {
                $snippetInterface = Snippet::importByURN($interface);
            }
            if ($snippetInterface->id) {
                $block->interface_id = (int)$snippetInterface->id;
            }
        }
        if ($widget) {
            if ($widget instanceof Snippet) {
                $snippetWidget = $widget;
            } elseif (is_numeric($widget)) {
                $snippetWidget = new Snippet($widget);
            } else {
                $snippetWidget = Snippet::importByURN($widget);
            }
            if ($snippetWidget->id) {
                $block->widget_id = (int)$snippetWidget->id;
            }
        }
        $block->commit();
        return $block;
    }
}
