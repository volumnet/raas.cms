<?php
/**
 * Форма редактирования поля
 */
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Option;
use RAAS\FieldSet;
use RAAS\Form as RAASForm;
use RAAS\FormTab;

/**
 * Класс формы редактирования поля
 * @property-read ViewSub_Dev $view Представление
 */
class EditFieldForm extends RAASForm
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
        $parent = isset($params['meta']['Parent']) ? $params['meta']['Parent'] : null;
        $parentUrl = $params['meta']['parentUrl'] ?? '';

        $defaultParams = [
            'caption' => ($item && $item->id) ? $item->name : $this->view->_('CREATING_FIELD'),
            'parentUrl' => $parentUrl,
            'export' => function ($form) use ($item, $parent) {
                $form->exportDefault();
                if (!$form->Item->id && isset($parent) && $parent && $parent->id) {
                    $form->Item->pid = (int)$parent->id;
                }
            },
            'children' => [
                'common' => $this->getCommonTab($parent),
            ]
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Возвращает основную вкладку редактирования поля
     * @param ?SOME $parent Родительский объект
     * @return FormTab
     */
    public function getCommonTab(?SOME $parent = null)
    {
        $content = [];
        foreach (Field::$fieldTypes as $key) {
            $content['datatypes'][] = [
                'value' => $key,
                'caption' => $this->view->_('DATATYPE_' . str_replace('-', '_', strtoupper($key)))
            ];
        }
        $content['datatypes'][] = [
            'value' => 'material',
            'caption' => $this->view->_('DATATYPE_MATERIAL')
        ];
        foreach (Field::$sourceTypes as $key) {
            $content['sourcetypes'][] = [
                'value' => $key,
                'caption' => $this->view->_('SOURCETYPE_' . strtoupper($key)),
                'data-hint' => $this->view->_('SOURCETYPE_' . strtoupper($key) . '_HINT')
            ];
        }

        $temp = new Dictionary();
        $content['dictionaries'] = [
            'Set' => array_merge(
                [new Dictionary(['id' => 0, 'name' => $this->view->_('SELECT_DICTIONARY')])],
                $temp->children
            ),
            'level' => 0
        ];

        $children = [];
        $children['name'] = [
            'name' => 'name',
            'caption' => $this->view->_('NAME'),
            'class' => 'span5',
            'required' => 'required'
        ];
        $children['urn'] = [
            'name' => 'urn',
            'caption' => $this->view->_('URN'),
            'class' => 'span5',
        ];

        $fieldGroups = [];
        if ($parent) {
            if ($parent->id) {
                $fieldGroups = (array)$parent->fieldGroups;
            } elseif ($parent instanceof Page) {
                $fieldGroups = PageFieldGroup::getSet();
                usort($fieldGroups, fn($a, $b) => ($a->priority - $b->priority));
            } elseif ($parent instanceof User) {
                $fieldGroups = UserFieldGroup::getSet();
                usort($fieldGroups, fn($a, $b) => ($a->priority - $b->priority));
            }
        }
        if (count($fieldGroups) > 1) {
            $children['gid'] = [
                'type' => 'select',
                'name' => 'gid',
                'caption' => $this->view->_('FIELD_GROUP'),
                'children' => array_values(array_map(function ($fieldGroup) {
                    $option = new Option([
                        'value' => (int)$fieldGroup->id,
                        'caption' => $fieldGroup->name ?: $this->view->_('GENERAL'),
                    ]);
                    return $option;
                }, $fieldGroups)),
            ];
        }

        $children['vis'] = [
            'type' => 'checkbox',
            'name' => 'vis',
            'caption' => $this->view->_('VISIBLE'),
            'default' => 1
        ];
        $children['required'] = [
            'type' => 'checkbox',
            'name' => 'required',
            'caption' => $this->view->_('REQUIRED')
        ];
        $children['multiple'] = [
            'type' => 'checkbox',
            'name' => 'multiple',
            'caption' => $this->view->_('MULTIPLE')
        ];
        $children['maxlength'] = [
            'type' => 'number',
            'name' => 'maxlength',
            'caption' => $this->view->_('MAXLENGTH')
        ];
        $children['datatype'] = [
            'type' => 'select',
            'name' => 'datatype',
            'caption' => $this->view->_('DATATYPE'),
            'children' => $content['datatypes'],
            'default' => 'text'
        ];
        $children['source_type'] = [
            'type' => 'select',
            'name' => 'source_type',
            'caption' => $this->view->_('SOURCETYPE'),
            'children' => $content['sourcetypes'],
            'data-hint' => ''
        ];
        $children['source'] = [
            'name' => 'source',
            'caption' => $this->view->_('SOURCE'),
            'template' => 'cms/dev_edit_field.source.inc.php',
            'check' => function ($field) {
                if (in_array($_POST['datatype'], ['select', 'radio']) ||
                    (($_POST['datatype'] == 'checkbox') && ($_POST['multiple'] ?? false))
                ) {
                    if (!trim((string)($_POST['source_type'] ?? '')) || !trim((string)($_POST['source'] ?? ''))) {
                        return [
                            'name' => 'MISSED',
                            'value' => 'source',
                            'description' => 'ERR_NO_DATA_SOURCE'
                        ];
                    }
                }
            },
            'children' => $content['dictionaries']
        ];
        $children['range'] = new FieldSet([
            'template' => 'cms/dev_edit_field.range.inc.php',
            'caption' => $this->view->_('RANGE'),
            'children' => [
                [
                    'type' => 'number',
                    'name' => 'min_val',
                    'class' => 'span1'
                ],
                [
                    'type' => 'number',
                    'name' => 'max_val',
                    'class' => 'span1'
                ],
                [
                    'type' => 'text',
                    'name' => 'step',
                    'class' => 'span1',
                    'default' => 1
                ],
            ]
        ]);
        $children['defval'] = [
            'name' => 'defval',
            'caption' => $this->view->_('DEFAULT_VALUE'),
        ];
        $children['placeholder'] = [
            'name' => 'placeholder',
            'caption' => $this->view->_('PLACEHOLDER'),
        ];
        $children['pattern'] = [
            'name' => 'pattern',
            'caption' => $this->view->_('PATTERN'),
        ];
        $children['preprocessor_id'] = new InterfaceField([
            'name' => 'preprocessor_id',
            'caption' => $this->view->_('PREPROCESSOR'),
            'meta' => [
                'rootInterfaceClass' => FilesProcessorInterface::class,
                'interfaceClassnameFieldName' => 'preprocessor_classname',
            ],
        ]);
        $children['postprocessor_id'] = new InterfaceField([
            'name' => 'postprocessor_id',
            'caption' => $this->view->_('POSTPROCESSOR'),
            'meta' => [
                'rootInterfaceClass' => FilesProcessorInterface::class,
                'interfaceClassnameFieldName' => 'postprocessor_classname',
            ],
        ]);
        $children['show_in_table'] = [
            'type' => 'checkbox',
            'name' => 'show_in_table',
            'caption' => $this->view->_('SHOW_IN_TABLE')
        ];

        return new FormTab([
            'name' => 'common',
            'caption' => $this->view->_('GENERAL'),
            'children' => $children
        ]);
    }
}
