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
        $item = isset($params['Item']) ? $params['Item'] : null;
        $defaultParams = [
            'Item' => $item,
            'caption' => $this->view->_('EDIT_TEMPLATE'),
            'parentUrl' => $this->view->url . '&action=templates',
            'children' => [
                'common' => $this->getCommonTab($item),
            ],
        ];
        if ($item->id) {
            $defaultParams['children']['layout'] = $this->getLayoutTab();
            $defaultParams['children']['service'] = $this->getServiceTab();
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает основную вкладку редактирования сниппета
     * @param Template $template Шаблон для редактирования
     * @return FormTab
     */
    protected function getCommonTab(Template $template)
    {
        $arr = [
            'name' => 'common',
            'caption' => $this->view->_('GENERAL'),
            'children' => [
                'name' => [
                    'name' => 'name',
                    'caption' => $this->view->_('NAME'),
                    'required' => true,
                ],
                'urn' => [
                    'name' => 'urn',
                    'caption' => $this->view->_('URN'),
                ],
                'description' => [
                    'type' => 'codearea',
                    'name' => 'description',
                    'caption' => $this->view->_('TEMPLATE_CODE'),
                    'required' => true,
                ],
            ]
        ];
        if (!$template->id) {
            $arr['children']['background'] = $this->getBackgroundField();
        }
        $tab = new FormTab($arr);
        return $tab;
    }


    /**
     * Получает вкладку макета
     * @return FormTab
     */
    protected function getLayoutTab()
    {
        $arr = [
            'name' => 'layout',
            'caption' => $this->view->_('LAYOUT'),
            'children' => [
                new FieldSet([
                    'template' => 'dev_edit_template',
                    'export' => function (FieldSet $fieldSet) {
                        $item = $fieldSet->Form->Item;
                        foreach (['width', 'height'] as $key) {
                            if (isset($_POST[$key]) && (int)$_POST[$key]) {
                                $item->$key = (int)$_POST[$key];
                            }
                        }
                        if (isset($_POST['location'])) {
                            $item->locs = new ArrayObject();
                            foreach ($_POST['location'] as $key => $val) {
                                $item->locs[] = [
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
                                            :  $item->width,
                                    'height' => isset($_POST['location-height'][$key])
                                             ?  (string)$_POST['location-height'][$key]
                                             :  Location::min_height,
                                ];
                            }
                        }
                    }
                ]),
                'background' => $this->getBackgroundField(),
            ]
        ];
        $tab = new FormTab($arr);
        return $tab;
    }


    /**
     * Получает вкладку "Служебные"
     * @return FormTab
     */
    protected function getServiceTab()
    {
        $arr = [
            'name' => 'service',
            'caption' => $this->view->_('SERVICE'),
            'children' => [
                'post_date' => [
                    'name' => 'post_date',
                    'caption' => $this->view->_('CREATED_BY'),
                    'export' => 'is_null',
                    'import' => 'is_null',
                    'template' => 'stat.inc.php'
                ],
                'modify_date' => [
                    'name' => 'modify_date',
                    'caption' => $this->view->_('EDITED_BY'),
                    'export' => 'is_null',
                    'import' => 'is_null',
                    'template' => 'stat.inc.php'
                ],
            ],
        ];
        $tab = new FormTab($arr);
        return $tab;
    }


    /**
     * Получает поле фонового изображения
     * @return RAASField
     */
    protected function getBackgroundField()
    {
        $field = new RAASField([
            'type' => 'image',
            'name' => 'background',
            'caption' => $this->view->_('BACKGROUND'),
            'meta' => [
                'attachmentVar' => 'Background',
                'deleteAttachmentPath' => $this->view->url
                                       .  '&action=delete_template_image&id='
                                       . (int)$item->id
            ]
        ]);
        return $field;
    }
}
