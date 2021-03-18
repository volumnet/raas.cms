<?php
/**
 * Шаблон типа материалов "Вопрос-ответ"
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Attachment;

/**
 * Класс шаблона типа материалов "Вопрос-ответ"
 */
class FAQTemplate extends MaterialTypeTemplate
{
    public $createMainSnippet = true;

    public $createMainBlock = false;

    public $createPage = true;

    public static $global = true;

    public function createFields()
    {
        $dateField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('DATE'),
            'urn' => 'date',
            'datatype' => 'date',
        ]);
        $dateField->commit();

        $nameField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('FULL_NAME'),
            'urn' => 'full_name',
            'datatype' => 'text',
        ]);
        $nameField->commit();

        $phoneField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 0,
            'name' => View_Web::i()->_('PHONE'),
            'urn' => 'phone',
            'datatype' => 'text',
        ]);
        $phoneField->commit();

        $emailField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 0,
            'name' => View_Web::i()->_('EMAIL'),
            'urn' => 'email',
            'datatype' => 'email',
        ]);
        $emailField->commit();

        $imageField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('IMAGE'),
            'urn' => 'image',
            'datatype' => 'image', 'show_in_table' => 0,
        ]);
        $imageField->commit();

        $answerDateField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('ANSWER_DATE'),
            'urn' => 'answer_date',
            'datatype' => 'date',
        ]);
        $answerDateField->commit();

        $answerNameField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('ANSWER_NAME'),
            'urn' => 'answer_name',
            'datatype' => 'text',
        ]);
        $answerNameField->commit();

        $answerGenderField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('ANSWER_GENDER'),
            'urn' => 'answer_gender',
            'datatype' => 'select',
            'source_type' => 'ini',
            'source' => '0 = "' . View_Web::i()->_('FEMALE') . '"' . "\n"
                     .  '1 = "' . View_Web::i()->_('MALE') . '"'
        ]);
        $answerGenderField->commit();

        $answerImageField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('ANSWER_IMAGE'),
            'urn' => 'answer_image',
            'datatype' => 'image', 'show_in_table' => 0,
        ]);
        $answerImageField->commit();

        $answerField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('ANSWER'),
            'urn' => 'answer',
            'datatype' => 'htmlarea',
        ]);
        $answerField->commit();

        return [
            $dateField->urn => $dateField,
            $nameField->urn => $nameField,
            $phoneField->urn => $phoneField,
            $emailField->urn => $emailField,
            $imageField->urn => $imageField,
            $answerDateField->urn => $answerDateField,
            $answerNameField->urn => $answerNameField,
            $answerGenderField->urn => $answerGenderField,
            $answerImageField->urn => $answerImageField,
            $answerField->urn => $answerField,
        ];
    }


    /**
     * Создает форму вопросов
     * @return Form
     */
    public function createForm()
    {
        $notificationSnippet = Snippet::importByURN('__raas_form_notify');
        $form = $this->webmaster->createForm([
            'name' => $this->materialType->name,
            'urn' => $this->materialType->urn,
            'material_type' => (int)$this->materialType->id,
            'interface_id' => (int)$notificationSnippet->id,
            'fields' => [
                [
                    'vis' => 1,
                    'name' => View_Web::i()->_('YOUR_NAME'),
                    'urn' => 'full_name',
                    'required' => 1,
                    'datatype' => 'text',
                    'show_in_table' => 1,
                ],
                [
                    'vis' => 1,
                    'name' => View_Web::i()->_('PHONE'),
                    'urn' => 'phone',
                    'datatype' => 'text',
                    'show_in_table' => 1,
                ],
                [
                    'vis' => 1,
                    'name' => View_Web::i()->_('EMAIL'),
                    'urn' => 'email',
                    'datatype' => 'email',
                    'show_in_table' => 0,
                ],
                [
                    'vis' => 0,
                    'name' => View_Web::i()->_('YOUR_PHOTO'),
                    'urn' => 'image',
                    'datatype' => 'image',
                    'show_in_table' => 0,
                ],
                [
                    'vis' => 1,
                    'name' => View_Web::i()->_('QUESTION_TEXT'),
                    'urn' => '_name_',
                    'required' => 1,
                    'datatype' => 'textarea',
                    'show_in_table' => 0,
                ],
                [
                    'vis' => 1,
                    'name' => View_Web::i()->_('AGREE_PRIVACY_POLICY'),
                    'urn' => 'agree',
                    'required' => 1,
                    'datatype' => 'checkbox',
                ],
            ]
        ]);
        return $form;
    }


    public function createBlockSnippet($nat = false)
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/faq/faq.tmp.php';
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
                  . '/widgets/materials/faq/faq_main.tmp.php';
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
     * Создает сниппет формы
     */
    public function createFormSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/faq/faq_form.tmp.php';
        $snippet = $this->webmaster->createSnippet(
            $this->materialType->urn . '_form',
            View_Web::i()->_('FAQ_FORM'),
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
                'sort_field_default' => $this->materialType->fields['date']->id,
                'sort_order_default' => 'desc!',
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    /**
     * Создает блок формы
     * @param Page $page Страница материалов
     * @param Form $form Форма
     * @param Snippet|null $widget Виджет блока
     * @param array $additionalData Дополнительные параметры
     * @return Block_Form|null
     */
    public function createFormBlock(
        Page $page,
        Form $form,
        Snippet $widget = null,
        array $additionalData = []
    )
    {
        if ($widget->id && $page->id) {
            $blockData = array_merge([
                'vis' => 1,
                'form' => (int)$form->id,
                'interface_id' => (int)Snippet::importByURN('__raas_form_interface')->id,
                'widget_id' => (int)$widget->id,
                'location' => 'content',
                'cats' => [(int)$page->id],
            ], $additionalData);
            $block = new Block_Form($blockData);
            $block->commit();
            return $block;
        }
    }


    public function createMaterials(array $pagesIds = [])
    {
        $result = [];
        $textRetriever = new FishYandexReferatsRetriever();
        $usersRetriever = new FishRandomUserRetriever();
        for ($i = 0; $i < 3; $i++) {
            $user = $usersRetriever->retrieve();
            $answer = $usersRetriever->retrieve();
            $text = $textRetriever->retrieve();
            $item = new Material([
                'pid' => (int)$this->materialType->id,
                'vis' => 1,
                'name' => $user['name']['first'] . ' '
                       .  $user['name']['last'],
                'description' => $text['name'],
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
            $item->fields['answer']->addValue($text['text']);
            $att = Attachment::createFromFile(
                $user['pic']['filepath'],
                $this->materialType->fields['image']
            );
            $item->fields['image']->addValue(json_encode([
                'vis' => 1,
                'name' => '',
                'description' => '',
                'attachment' => (int)$att->id
            ]));
            $att = Attachment::createFromFile(
                $answer['pic']['filepath'],
                $this->materialType->fields['answer_image']
            );
            $item->fields['answer_image']->addValue(json_encode([
                'vis' => 1,
                'name' => '',
                'description' => '',
                'attachment' => (int)$att->id
            ]));
            $result[] = $item;
        }
        return $result;
    }


    public function create()
    {
        $form = Form::importByURN($urn);
        if (!$form->id) {
            $form = $this->createForm();
        }

        $widget = Snippet::importByURN($urn);
        if (!$widget->id) {
            $widget = $this->createBlockSnippet();
        }

        $formWidget = Snippet::importByURN($urn . '_form');
        if (!$formWidget->id) {
            $formWidget = $this->createFormSnippet();
        }

        $mainWidget = Snippet::importByURN($urn . '_main');
        if (!$mainWidget->id) {
            $mainWidget = $this->createMainPageSnippet();
        }

        $temp = Page::getSet([
            'where' => [
                "pid = " . (int)$this->webmaster->Site->id,
                "urn = '" . $urn . "'"
            ]
        ]);
        if ($temp) {
            $page = $temp[0];
        } else {
            $page = $this->createPage($this->webmaster->Site);
            $block = $this->createBlock($page, $widget);
            $formBlock = $this->createFormBlock($page, $form, $formWidget);

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

            // Создадим материалы
            $this->createMaterials();
        }
        return $page;
    }
}
