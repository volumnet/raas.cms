<?php
/**
 * Шаблон типа материалов "Новости"
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Attachment;

/**
 * Класс шаблона типа материалов "Новости"
 */
class NewsTemplate extends MaterialTypeTemplate
{
    public function createFields()
    {
        $dateField = new Material_Field([
            'pid' => $this->materialType->id,
            'name' => View_Web::i()->_('DATE'),
            'urn' => 'date',
            'datatype' => 'date',
            'show_in_table' => 1,
        ]);
        $dateField->commit();

        $imagesField = new Material_Field([
            'pid' => $this->materialType->id,
            'name' => View_Web::i()->_('IMAGE'),
            'multiple' => 1,
            'urn' => 'images',
            'datatype' => 'image',
            'show_in_table' => 1,
        ]);
        $imagesField->commit();

        $briefField = new Material_Field([
            'pid' => $this->materialType->id,
            'name' => View_Web::i()->_('BRIEF_TEXT'),
            'multiple' => 0,
            'urn' => 'brief',
            'datatype' => 'textarea',
        ]);
        $briefField->commit();

        $noindexField = new Material_Field([
            'pid' => $this->materialType->id,
            'name' => View_Web::i()->_('NO_INDEX'),
            'urn' => 'noindex',
            'datatype' => 'checkbox'
        ]);
        $noindexField->commit();

        return [
            $dateField->urn => $dateField,
            $imagesField->urn => $imagesField,
            $briefField->urn => $briefField,
            $noindexField->urn => $noindexField,
        ];
    }


    public function createBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'sort_field_default' => $this->materialType->fields['date']->id,
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
                'sitemaps_priority' => 0.5,
            ]);
            $item->commit();
            $item->fields['date']->addValue(
                date('Y-m-d H:i', time() - rand(0, 86400 * 7))
            );
            $item->fields['brief']->addValue($text['brief']);
            for ($j = 0; $j < 5; $j++) {
                $att = Attachment::createFromFile(
                    $imagesUrls[($i * 5) + $j],
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
