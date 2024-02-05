<?php
/**
 * Форма редактирования типа материалов
 */
namespace RAAS\CMS;

use RAAS\Form as RAASForm;

/**
 * Класс формы редактирования типа материалов
 * @property-read ViewSub_Dev $view Представление
 */
class EditMaterialTypeForm extends RAASForm
{
    /**
     * Не создавать сниппеты
     */
    const CREATE_MATERIAL_TYPE_NONE = 0;

    /**
     * Создать сниппет для главной страницы
     */
    const CREATE_MATERIAL_TYPE_SIMPLE = 1;

    /**
     * Создать сниппет для отдельной страницы
     */
    const CREATE_MATERIAL_TYPE_EXTENDED = 2;

    /**
     * Создать сниппеты для главной и для отдельной страниц
     */
    const CREATE_MATERIAL_TYPE_BOTH = 3;

    /**
     * Без шаблона
     */
    const MATERIAL_TYPE_TEMPLATE_NONE = '';

    /**
     * Шаблон "Новости"
     */
    const MATERIAL_TYPE_TEMPLATE_NEWS = 'news';

    /**
     * Шаблон "Баннеры"
     */
    const MATERIAL_TYPE_TEMPLATE_BANNERS = 'banners';

    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Dev::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $Parent = isset($params['Parent']) ? $params['Parent'] : null;

        $defaultParams = [
            'caption' => $Item->id
                      ?  $Item->name
                      : $this->view->_('CREATING_MATERIAL_TYPE'),
            'parentUrl' => Sub_Dev::i()->url . '&action=material_types',
            'export' => function ($form) use ($Parent) {
                $form->exportDefault();
                if (!$form->Item->id) {
                    $form->Item->pid = (int)$Parent->id;
                }
                if ($Parent->id) {
                    $form->Item->global_type = $Parent->global_type;
                }
            },
            'oncommit' => function () use ($view, $Item) {
                if ($_POST['template'] ?? null) {
                    switch ($_POST['template']) {
                        case EditMaterialTypeForm::MATERIAL_TYPE_TEMPLATE_NEWS:
                            $dateField = new Material_Field([
                                'pid' => $Item->id,
                                'name' => $view->_('DATE'),
                                'urn' => 'date',
                                'datatype' => 'date',
                                'show_in_table' => 1,
                            ]);
                            $dateField->commit();

                            $F = new Material_Field([
                                'pid' => $Item->id,
                                'name' => $view->_('IMAGE'),
                                'multiple' => 1,
                                'urn' => 'images',
                                'datatype' => 'image',
                            ]);
                            $F->commit();

                            $F = new Material_Field([
                                'pid' => $Item->id,
                                'name' => $view->_('BRIEF_TEXT'),
                                'multiple' => 0,
                                'urn' => 'brief',
                                'datatype' => 'textarea',
                            ]);
                            $F->commit();
                            break;
                        case EditMaterialTypeForm::MATERIAL_TYPE_TEMPLATE_BANNERS:
                            $F = new Material_Field([
                                'pid' => $Item->id,
                                'name' => $view->_('URL'),
                                'urn' => 'url',
                                'datatype' => 'text',
                                'show_in_table' => 1,
                            ]);
                            $F->commit();

                            $F = new Material_Field([
                                'pid' => $Item->id,
                                'name' => $view->_('IMAGE'),
                                'urn' => 'image',
                                'datatype' => 'image',
                            ]);
                            $F->commit();
                            break;
                    }
                }
                if ($_POST['add_snippet'] ?? null) {
                    $add = (int)$_POST['add_snippet'];
                    $urn = $Item->urn;
                    $pid = Snippet_Folder::importByURN('__raas_views')->id;
                    $name = $Item->name;
                    if ($add &
                        EditMaterialTypeForm::CREATE_MATERIAL_TYPE_SIMPLE
                    ) {
                        $f = Package::i()->resourcesDir
                           . '/material_main.tmp.php';
                        $text = file_get_contents($f);
                        $text = str_ireplace(
                            '{BLOCK_NAME}',
                            $urn . '_main',
                            $text
                        );
                        $text = str_ireplace('{MATERIAL_NAME}', $name, $text);

                        $s = new Snippet();
                        $s->pid = $pid;
                        $s->urn = $urn . '_main';
                        $s->name = $name . ' ' . $view->_('FOR_MAIN');
                        $s->description = $text;
                        $s->commit();
                    }
                    if ($add &
                        EditMaterialTypeForm::CREATE_MATERIAL_TYPE_EXTENDED
                    ) {
                        $f = Package::i()->resourcesDir . '/material.tmp.php';
                        $text = file_get_contents($f);
                        $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
                        $text = str_ireplace('{MATERIAL_NAME}', $name, $text);

                        $s = new Snippet();
                        $s->pid = $pid;
                        $s->urn = $urn;
                        $s->name = $name;
                        $s->description = $text;
                        $s->commit();
                    }
                }
            },
            'children' => [
                [
                    'name' => 'name',
                    'caption' => $this->view->_('NAME'),
                    'required' => 'required'
                ],
                [
                    'name' => 'urn',
                    'caption' => $this->view->_('URN')
                ],
                [
                    'type' => 'checkbox',
                    'name' => 'global_type',
                    'caption' => $this->view->_('GLOBAL_MATERIALS'),
                    'default' => $Parent->id ? $Parent->global_type : 1,
                    'disabled' => (bool)$Parent->id
                ]
            ]
        ];
        if (!$Item->id && !$Parent->id) {
            $defaultParams['children']['template'] = [
                'type' => 'select',
                'name' => 'template',
                'caption' => $this->view->_('MATERIAL_TYPE_TEMPLATE'),
                'children' => [
                    [
                        'value' => EditMaterialTypeForm::MATERIAL_TYPE_TEMPLATE_NONE,
                        'caption' => $this->view->_('_NONE'),
                    ],
                    [
                        'value' => EditMaterialTypeForm::MATERIAL_TYPE_TEMPLATE_NEWS,
                        'caption' => $this->view->_('NEWS'),
                    ],
                    [
                        'value' => EditMaterialTypeForm::MATERIAL_TYPE_TEMPLATE_BANNERS,
                        'caption' => $this->view->_('BANNERS'),
                    ],
                ]
            ];
            $defaultParams['children']['add_snippet'] = [
                'type' => 'select',
                'name' => 'add_snippet',
                'caption' => $this->view->_('ADD_SNIPPET_FOR_THIS_TYPE'),
                'children' => [
                    [
                        'value' => EditMaterialTypeForm::CREATE_MATERIAL_TYPE_NONE,
                        'caption' => $this->view->_('_NONE'),
                    ],
                    [
                        'value' => EditMaterialTypeForm::CREATE_MATERIAL_TYPE_SIMPLE,
                        'caption' => $this->view->_(
                            'ADD_SNIPPET_FOR_THIS_TYPE_SIMPLE_FOR_MAIN'
                        ),
                    ],
                    [
                        'value' => EditMaterialTypeForm::CREATE_MATERIAL_TYPE_EXTENDED,
                        'caption' => $this->view->_(
                            'ADD_SNIPPET_FOR_THIS_TYPE_EXTENDED'
                        ),
                    ],
                    [
                        'value' => EditMaterialTypeForm::CREATE_MATERIAL_TYPE_BOTH,
                        'caption' => $this->view->_(
                            'ADD_SNIPPET_FOR_THIS_TYPE_BOTH'
                        ),
                    ],
                ]
            ];
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
