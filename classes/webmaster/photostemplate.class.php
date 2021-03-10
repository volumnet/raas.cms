<?php
/**
 * Шаблон типа материалов "Фотогалерея"
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Attachment;

/**
 * Класс шаблона типа материалов "Фотогалерея"
 */
class PhotosTemplate extends MaterialTypeTemplate
{
    public $createMainSnippet = true;

    public $createMainBlock = false;

    public $createPage = true;

    public static $global = true;

    public function createFields()
    {
        $imagesField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('IMAGE'),
            'multiple' => 1,
            'urn' => 'images',
            'datatype' => 'image',
            'show_in_table' => 1,
        ]);
        $imagesField->commit();

        return [
            $imagesField->urn => $imagesField,
        ];
    }


    public function createBlockSnippet($nat = false)
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/photos/photos.tmp.php';
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


    public function createMainPageSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/photos/photos_main.tmp.php';
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
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'sort_field_default' => 'post_date',
                'sort_order_default' => 'desc!',
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    public function createMaterials(array $pagesIds = [])
    {
        $result = [];
        $textRetriever = new FishYandexReferatsRetriever();
        $photosRetriever = new FishPhotosRetriever();
        $imagesUrls = $photosRetriever->retrieve(15);
        for ($i = 0; $i < 3; $i++) {
            $text = $textRetriever->retrieve();
            $item = new Material([
                'pid' => (int)$this->materialType->id,
                'vis' => 1,
                'name' => $text['name'],
                'description' => $text['text'],
                'priority' => ($i + 1) * 10,
                'sitemaps_priority' => 0.5
            ]);
            $item->commit();
            for ($j = 0; $j < 10; $j++) {
                $att = Attachment::createFromFile(
                    $imagesUrls[($i * 10) + $j],
                    $this->materialType->fields['images']
                );
                $item->fields['images']->addValue(json_encode([
                    'vis' => 1,
                    'name' => '',
                    'description' => '',
                    'attachment' => (int)$att->id
                ]));
            }
            $result[] = $item;
        }
        return $result;
    }
}
