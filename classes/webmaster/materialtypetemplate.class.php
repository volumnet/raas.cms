<?php
/**
 * Абстрактный шаблон типа материалов
 */
namespace RAAS\CMS;

use RAAS\Application;

/**
 * Класс абстрактного шаблона типа материалов
 * @property-read Snippet_Folder $widgetsFolder Папка виджетов типа
 */
class MaterialTypeTemplate
{
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
                return $this->$var;
                break;
        }
    }


    /**
     * Конструктор класса
     * @param Material_Type $materialType Тип материалов
     * @param Webmaster $webmaster Вебмастер
     */
    public function __construct(Material_Type $materialType, Webmaster $webmaster)
    {
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
     * @return array<string[] URN поля => Material_Field>
     */
    public function createFields()
    {
    }


    /**
     * Создает основной сниппет для типа
     * @param bool $nat Существуют ли статьи материалов для данного типа
     * @return Snippet
     */
    public function createBlockSnippet($nat = false)
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
        $uid = Application::i()->user->id;
        $inheritPageData = $rootPage->getArrayCopy();
        unset($inheritPageData['id'], $inheritPageData['pid']);
        $pageData = array_merge($inheritPageData, [
            'pid' => (int)$rootPage->id,
            'vis' => 1,
            'author_id' => $uid,
            'editor_id' => $uid,
            'name' => $this->materialType->name,
            'urn' => $this->materialType->urn,
            'cache' => 1,
            'inherit_cache' => 1,
            'inherit_template' => 0,
            'lang' => 'ru',
            'inherit_lang' => 1,
        ]);
        $page = new Page($pageData);
        $page->commit();
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
                'cats' => [(int)$page->id]
            ], $additionalData);
            $block = new Block_Material($blockData);
            $block->commit();
            return $block;
        }
    }
}
