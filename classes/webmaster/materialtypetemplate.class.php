<?php
/**
 * Абстрактный шаблон типа материалов
 */
namespace RAAS\CMS;

use Mustache_Engine;
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
     * Папка виджетов типа
     * @var Snippet_Folder
     */
    protected $_widgetsFolder;

    /**
     * Шаблонизатор Mustache
     * @var Mustache_Engine
     */
    protected $mustache;

    public function __get($var)
    {
        switch ($var) {
            case 'widgetsFolder':
                if (!$this->_widgetsFolder) {
                    $this->_widgetsFolder = Snippet_Folder::importByURN('__raas_views');
                }
                return $this->_widgetsFolder;
                break;
        }
    }


    /**
     * Конструктор класса
     * @param Material_Type $materialType Тип материалов
     */
    public function __construct(Material_Type $materialType)
    {
        $this->mustache = new Mustache_Engine();
        $this->materialType = $materialType;
    }


    /**
     * Производит рендеринг шаблона
     * @param string $templateText Текст шаблона
     * @return string
     */
    protected function render($templateText, $widgetName = '', $widgetURN = '')
    {
        if (!$widgetName) {
            $widgetName = $this->materialType->name;
        }
        if (!$widgetURN) {
            $widgetURN = $this->materialType->urn;
        }
        $text = $this->mustache->render($templateText, [
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
        ]);
        return $text;
    }


    /**
     * Производит рендеринг шаблона из файла
     * @param string $templateFile Файл шаблона
     * @return string
     */
    protected function renderFile($templateFile, $widgetName = '', $widgetCSSClassname = '')
    {
        $templateText = file_get_contents($templateFile);
        $text = $this->render($templateText, $widgetName, $widgetCSSClassname);
        return $text;
    }


    /**
     * Создает сниппет по файлу шаблона
     * @param string $templateFile Файл шаблона
     * @param string $urnSuffix Суффикс URN сниппета
     * @param string $nameSuffix Суффикс имени сниппета
     * @return Snippet
     */
    protected function createSnippetByFile(
        $templateFile,
        $urnSuffix = '',
        $nameSuffix = ''
    ) {
        $urn = $this->materialType->urn . ($urnSuffix ? '_' . $urnSuffix : '');
        $name = $this->materialType->name
              . ($nameSuffix ? ' — ' . $nameSuffix : '');
        $text = $this->renderFile($templateFile);
        $snippet = new Snippet([
            'pid' => (int)$this->widgetsFolder->id,
            'urn' => $urn,
            'name' => $name,
            'description' => $text,
        ]);
        $snippet->commit();
        return $snippet;
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
        $snippet = $this->createSnippetByFile($filename);
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
        $snippet = $this->createSnippetByFile(
            $filename,
            'main',
            View_Web::i()->_('MATERIAL_TEMPLATE_MAIN_SUFFIX')
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
