<?php
/**
 * Шаблон типа материалов "Баннеры"
 */
namespace RAAS\CMS;

use Mustache_Engine;
use RAAS\Application;
use RAAS\Attachment;

/**
 * Класс шаблона типа материалов "Баннеры"
 */
class BannersTemplate extends MaterialTypeTemplate
{
    public function createFields()
    {
        $urlField = new Material_Field([
            'pid' => $this->materialType->id,
            'name' => View_Web::i()->_('URL'),
            'urn' => 'url',
            'datatype' => 'text',
            'show_in_table' => 1,
        ]);
        $urlField->commit();

        $imageField = new Material_Field([
            'pid' => $this->materialType->id,
            'name' => View_Web::i()->_('IMAGE'),
            'urn' => 'image',
            'datatype' => 'image',
            'show_in_table' => 1,
        ]);
        $imageField->commit();

        return [
            $urlField->urn => $urlField,
            $imageField->urn => $imageField,
        ];
    }


    public function createBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/banners/banners.tmp.php';
        $snippet = $this->createSnippetByFile($filename);
        return $snippet;
    }


    public function createListSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/banners/banners_list.tmp.php';
        $snippet = $this->createSnippetByFile(
            $filename,
            'list',
            View_Web::i()->_('MATERIAL_TEMPLATE_LIST_SUFFIX')
        );
        return $snippet;
    }


    public function createItemSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/banners/banners_item.tmp.php';
        $snippet = $this->createSnippetByFile(
            $filename,
            'item',
            View_Web::i()->_('MATERIAL_TEMPLATE_LIST_ITEM_SUFFIX')
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
                'pages_var_name' => '',
                'rows_per_page' => 0,
                'location' => 'banners',
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
        $imagesUrls = $photosRetriever->retrieve(3);
        for ($i = 0; $i < 3; $i++) {
            $text = $textRetriever->retrieve();
            $item = new Material([
                'pid' => (int)$this->materialType->id,
                'vis' => 1,
                'name' => $text['name'],
                'description' => $text['brief'],
                'priority' => ($i + 1) * 10,
                'sitemaps_priority' => 0.5
            ]);
            $item->commit();
            $att = Attachment::createFromFile(
                $imagesUrls[$i],
                $this->materialType->fields['images']
            );
            $item->fields['image']->addValue(json_encode([
                'vis' => 1,
                'name' => '',
                'description' => '',
                'attachment' => (int)$att->id
            ]));
            $item->fields['url']->addValue('#');
            $result[] = $item;
        }
        return $result;
    }
}
