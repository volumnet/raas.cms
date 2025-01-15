<?php
/**
 * Шаблон типа материалов "Наши преимущества"
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Attachment;

/**
 * Класс шаблона типа материалов "Наши преимущества"
 */
class FeaturesTemplate extends MaterialTypeTemplate
{
    public $createMainSnippet = true;

    public $createMainBlock = true;

    public $createPage = false;

    public static $global = true;

    public function createFields()
    {
        $imageField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('IMAGE'),
            'urn' => 'image',
            'datatype' => 'image',
            'show_in_table' => 1,
        ]);
        $imageField->commit();

        $iconField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('ICON'),
            'urn' => 'icon',
            'datatype' => 'text',
            'show_in_table' => 1,
        ]);
        $iconField->commit();

        return [
            $imageField->urn => $imageField,
            $iconField->urn => $iconField,
        ];
    }


    public function createMainPageSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/features/features_main.tmp.php';
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


    public function createBlock(
        Page $page,
        ?Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'pages_var_name' => '',
                'rows_per_page' => 0,
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    public function createMaterials(array $pagesIds = [])
    {
        $result = [];
        $icons = ['smile-o', 'thumbs-o-up', 'rub'];
        for ($i = 0; $i < 3; $i++) {
            $item = new Material([
                'pid' => (int)$this->materialType->id,
                'vis' => 1,
                'name' => View_Web::i()->_('FEATURE_' . ($i + 1)),
                'description' => View_Web::i()->_(
                    'FEATURE_' . ($i + 1) . '_TEXT'
                ),
                'priority' => ($i + 1) * 10,
                'sitemaps_priority' => 0.5
            ]);
            $item->commit();
            $item->fields['icon']->addValue($icons[$i]);
            $result[] = $item;
        }
        return $result;
    }


    public function create()
    {
        $mainWidget = Snippet::importByURN($this->materialType->urn . '_main');
        if (!($mainWidget && $mainWidget->id)) {
            $mainWidget = $this->createMainPageSnippet();
        }
        if ($this->createMainBlock) {
            $blockMain = $this->createBlock(
                $this->webmaster->Site,
                $mainWidget,
                [
                    'nat' => 0,
                    'pages_var_name' => '',
                    'rows_per_page' => 0,
                ]
            );
        }

        $this->createMaterials();
    }
}
