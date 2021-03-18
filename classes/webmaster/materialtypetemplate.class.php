<?php
/**
 * Абстрактный шаблон типа материалов
 */
namespace RAAS\CMS;

use RAAS\Application;

/**
 * Класс абстрактного шаблона типа материалов
 * @property-read Snippet_Folder $widgetsFolder Папка виджетов типа
 * @property-read Webmaster $webmaster Вебмастер
 */
class MaterialTypeTemplate
{
    /**
     * Создавать сниппет для главной страницы
     */
    public $createMainSnippet = true;

    /**
     * Создавать блок для главной страницы
     * @var bool
     */
    public $createMainBlock = false;

    /**
     * Создавать отдельную страницу
     */
    public $createPage = true;

    /**
     * Глобальный тип материалов
     */
    public static $global = true;

    /**
     * Тип материалов
     * @var Material_Type
     */
    protected $materialType;

    /**
     * Вебмастер
     * @var Webmaster
     */
    protected $webmaster;

    public function __get($var)
    {
        switch ($var) {
            case 'widgetsFolder':
                return $this->webmaster->widgetsFolder;
                break;
            case 'webmaster':
            case 'materialType':
                return $this->$var;
                break;
        }
    }


    /**
     * Конструктор класса
     * @param Material_Type $materialType Тип материалов
     * @param Webmaster $webmaster Вебмастер
     */
    public function __construct(
        Material_Type $materialType,
        Webmaster $webmaster
    ) {
        $this->materialType = $materialType;
        $this->webmaster = $webmaster;
    }


    /**
     * Получает список данных для подстановки
     * @param string $widgetName Наименование виджета
     * @param string $widgetURN URN виджета
     */
    protected function getReplaceData($widgetName, $widgetURN)
    {
        return [
            'MATERIAL_TYPE_NAME' => $this->materialType->name,
            'MATERIAL_TYPE_URN' => $this->materialType->urn,
            'MATERIAL_TYPE_CSS_CLASSNAME' => str_replace(
                '_',
                '-',
                $this->materialType->urn
            ),
            'WIDGET_NAME' => $widgetName,
            'WIDGET_URN' => $widgetURN,
            'WIDGET_CSS_CLASSNAME' => str_replace('_', '-', $widgetURN),
        ];
    }


    /**
     * Создает поля типа
     * @return array <pre><code>array<
     *     string[] URN поля => Material_Field
     * ></code></pre>
     */
    public function createFields()
    {
    }


    /**
     * Создает основной сниппет для типа
     * @return Snippet
     */
    public function createBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/material/material.tmp.php';
        $snippet = $this->webmaster->createSnippet(
            $this->materialType->urn,
            $this->materialType->name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData(
                $this->materialType->name,
                $this->materialType->urn
            )
        );
        return $snippet;
    }


    /**
     * Создает сниппет главной страницы для типа
     * @return Snippet
     */
    public function createMainPageSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/material/material_main.tmp.php';
        $snippet = $this->webmaster->createSnippet(
            $this->materialType->urn . '_main',
            (
                $this->materialType->name . ' — ' .
                View_Web::i()->_('MATERIAL_TEMPLATE_MAIN_SUFFIX')
            ),
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData(
                $this->materialType->name,
                $this->materialType->urn
            )
        );
        return $snippet;
    }


    /**
     * Создает список материалов
     * @param array<int> $pagesIds ID# страниц, на которых размещаем материалы
     * @return array<Material>
     */
    public function createMaterials(array $pagesIds = [])
    {
    }


    /**
     * Создает страницу материала
     * @param Page $rootPage Корневая страница
     * @return Page
     */
    public function createPage(Page $rootPage)
    {
        $pageData = [
            'name' => $this->materialType->name,
            'urn' => $this->materialType->urn,
        ];
        $page = $this->webmaster->createPage($pageData, $rootPage);
        return $page;
    }


    /**
     * Создает блок материалов на странице
     * @param Page $page Страница материалов
     * @param Snippet|null $widget Виджет блока
     * @param array $additionalData Дополнительные параметры
     * @return Block_Material
     */
    public function createBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        if ($widget->id && $page->id) {
            $blockData = array_merge([
                'vis' => 1,
                'material_type' => (int)$this->materialType->id,
                'nat' => 1,
                'pages_var_name' => 'page',
                'rows_per_page' => 20,
                'sort_field_default' => 'name',
                'sort_order_default' => 'asc',
                'interface_id' => (int)Snippet::importByURN('__raas_material_interface')->id,
                'widget_id' => (int)$widget->id,
                'location' => 'content',
                'inherit' => (int)$inherit,
                'cats' => $additionalData['inherit'] ? $page->selfAndChildrenIds : [(int)$page->id]
            ], $additionalData);
            $block = new Block_Material($blockData);
            $block->commit();
            return $block;
        }
    }


    /**
     * Создает или находит тип материалов, соответствующий URN, и создает к нему
     * шаблон
     * @param Webmaster $webmaster Объект вебмастера
     * @param string $name Наименование
     * @param string $urn URN
     * @return self
     */
    public static function spawn($name, $urn, Webmaster $webmaster)
    {
        $newMaterialType = false;
        $materialType = Material_Type::importByURN($urn);
        if (!$materialType->id) {
            $materialType = new Material_Type([
                'name' => $name,
                'urn' => $urn,
                'global_type' => (int)static::$global,
            ]);
            $materialType->commit();
            $newMaterialType = true;
        }
        $materialTemplate = new static($materialType, $webmaster);
        if ($newMaterialType) {
            $fields = $materialTemplate->createFields();
        }
        return $materialTemplate;
    }


    /**
     * Создает инфраструктуру для типа материалов
     * @return Page|null Созданная или существующая страница, либо null,
     *     если страница не создавалась
     */
    public function create()
    {
        $widget = Snippet::importByURN($this->materialType->urn);
        if (!$widget->id) {
            $widget = $this->createBlockSnippet();
        }

        if ($this->createMainSnippet) {
            $mainWidget = Snippet::importByURN($this->materialType->urn . '_main');
            if (!$mainWidget->id) {
                $mainWidget = $this->createMainPageSnippet();
            }
            if ($this->createMainBlock) {
                $blockMain = $this->createBlock(
                    $this->webmaster->Site,
                    $mainWidget,
                    [
                        'nat' => 0,
                        'pages_var_name' => '',
                        'rows_per_page' => 3,
                    ]
                );
            }
        }

        if ($this->createPage) {
            $temp = Page::getSet(['where' => [
                "pid = " . (int)$this->webmaster->Site->id,
                "urn = '" . $this->materialType->urn . "'"
            ]]);
            if ($temp) {
                $page = $temp[0];
            } else {
                $page = $this->createPage($this->webmaster->Site);
                $block = $this->createBlock($page, $widget);
            }
        } else {
            $block = $this->createBlock(
                $this->webmaster->Site,
                $widget,
                ['nat' => 0]
            );
        }
        $this->createMaterials();

        if ($this->createPage) {
            return $page;
        }
    }
}
