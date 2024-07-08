<?php
/**
 * Форма редактирования типа материалов
 */
declare(strict_types=1);

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
        $item = isset($params['Item']) ? $params['Item'] : null;
        $parent = isset($params['Parent']) ? $params['Parent'] : null;

        $defaultParams = [
            'caption' => $item->id
                      ?  $item->name
                      : $this->view->_('CREATING_MATERIAL_TYPE'),
            'parentUrl' => Sub_Dev::i()->url . '&action=material_types',
            'export' => function ($form) use ($parent) {
                $form->exportDefault();
                if (!$form->Item->id) {
                    $form->Item->pid = (int)$parent->id;
                }
                if ($parent->id) {
                    $form->Item->global_type = $parent->global_type;
                }
            },
            'oncommit' => function () use ($view, $item) {
                if ($_POST['add_snippet'] ?? null) {
                    $add = (int)$_POST['add_snippet'];
                    $urn = $item->urn;
                    $pid = Snippet_Folder::importByURN('__raas_views')->id;
                    // $name = $item->name;
                    if ($add & EditMaterialTypeForm::CREATE_MATERIAL_TYPE_SIMPLE) {
                        // $f = Package::i()->resourcesDir . '/widgets/material_main.tmp.php';
                        // $text = file_get_contents($f);

                        $s = new Snippet();
                        $s->pid = $pid;
                        if ($add & EditMaterialTypeForm::CREATE_MATERIAL_TYPE_EXTENDED) {
                            $s->urn = $urn . '_main';
                            // $s->name = $name . ' ' . $view->_('FOR_MAIN');
                            // $text = str_ireplace('{BLOCK_NAME}', $urn . '-main', $text);
                        } else {
                            $s->urn = $urn;
                            // $s->name = $name;
                        }
                        // $text = str_ireplace('{BLOCK_NAME}', str_replace('_', '-', $s->urn), $text);
                        // $text = str_ireplace('{MATERIAL_NAME}', $s->name, $text);
                        // $s->description = $text;
                        $s->commit();
                    }
                    if ($add & EditMaterialTypeForm::CREATE_MATERIAL_TYPE_EXTENDED) {
                        // $f = Package::i()->resourcesDir . '/widgets/material.tmp.php';
                        // $text = file_get_contents($f);
                        // $text = str_ireplace('{BLOCK_NAME}', $urn, $text);
                        // $text = str_ireplace('{MATERIAL_NAME}', $name, $text);

                        $s = new Snippet();
                        $s->pid = $pid;
                        $s->urn = $urn;
                        // $s->name = $name;
                        // $s->description = $text;
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
                    'default' => ($parent && $parent->id) ? $parent->global_type : 1,
                    'disabled' => (bool)($parent ? $parent->id : false)
                ]
            ]
        ];
        if (!($item && $item->id) && !($parent && $parent->id)) {
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
