<?php
/**
 * Форма редактирования шаблона
 */
namespace RAAS\CMS;

use ArrayObject;
use RAAS\Field as RAASField;
use RAAS\FieldSet;
use RAAS\Form as RAASForm;
use RAAS\FormTab;

/**
 * Класс формы редактирования шаблона
 * @property-read ViewSub_Dev $view Представление
 */
class EditTemplateForm extends RAASForm
{
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
        $NameField = new RAASField([
            'name' => 'name',
            'caption' => $this->view->_('NAME'),
            'required' => 'required'
        ]);
        $UrnField = new RAASField([
            'name' => 'urn',
            'caption' => $view->_('URN')
        ]);
        $DescriptionField = new RAASField([
            'type' => 'codearea',
            'name' => 'description',
            'caption' => $this->view->_('TEMPLATE_CODE'),
            'required' => 'required'
        ]);
        $BackgroundField = new RAASField([
            'type' => 'image',
            'name' => 'background',
            'caption' => $this->view->_('BACKGROUND'),
            'meta' => [
                'attachmentVar' => 'Background',
                'deleteAttachmentPath' => $this->view->url
                                       .  '&action=delete_template_image&id='
                                       . (int)$Item->id
            ]
        ]);
        $defaultParams = [
            'Item' => $Item,
            'caption' => $this->view->_('EDIT_TEMPLATE'),
            'parentUrl' => $this->view->url . '&action=templates'
        ];
        if ($Item->id) {
            $defaultParams['children'] = [
                new FormTab([
                    'name' => 'edit',
                    'caption' => $this->view->_('EDITING'),
                    'children' => [
                        $NameField,
                        $UrnField,
                        $DescriptionField
                    ]
                ]),
                new FormTab([
                    'name' => 'layout',
                    'caption' => $this->view->_('LAYOUT'),
                    'children' => [
                        new FieldSet([
                            'template' => 'dev_edit_template',
                            'export' => function ($formTab) {
                                $Item = $formTab->Form->Item;
                                foreach (['width', 'height'] as $key) {
                                    if (isset($_POST[$key]) &&
                                        (int)$_POST[$key]
                                    ) {
                                        $Item->$key = (int)$_POST[$key];
                                    }
                                }
                                if (isset($_POST['location'])) {
                                    $Item->locs = new ArrayObject();
                                    foreach ($_POST['location'] as $key => $val) {
                                        $Item->locs[] = [
                                            'urn' => isset($_POST['location'][$key])
                                                  ?  (string)$_POST['location'][$key]
                                                  : 'Location',
                                            'x' => isset($_POST['location-left'][$key])
                                                ?  (string)$_POST['location-left'][$key]
                                                :  0,
                                            'y' => isset($_POST['location-top'][$key])
                                                ?  (string)$_POST['location-top'][$key]
                                                :  0,
                                            'width' => isset($_POST['location-width'][$key])
                                                    ?  (string)$_POST['location-width'][$key]
                                                    :  $Item->width,
                                            'height' => isset($_POST['location-height'][$key])
                                                     ?  (string)$_POST['location-height'][$key]
                                                     :  Location::min_height,
                                        ];
                                    }
                                }
                            }
                        ]),
                        $BackgroundField
                    ]
                ])
            ];
        } else {
            $defaultParams['children'] = [
                $NameField,
                $DescriptionField,
                $BackgroundField
            ];
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
