<?php
/**
 * Форма редактирования формы
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\Form as RAASForm;
use RAAS\Option;

/**
 * Класс формы редактирования формы
 * @property-read ViewSub_Dev $view Представление
 */
class EditFormForm extends RAASForm
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
        $CONTENT = [];
        $CONTENT['material_types'] = (array)Material_Type::getSet();
        foreach ([
            '' => '_OFF',
            // 'captcha' => 'CAPTCHA',
            // 2020-11-04, AVS: убрал, т.к. ни разу не воспользовались,
            // и вообще не ясно, как с этим работать
            'hidden' => 'HIDDEN_FIELD',
            'smart' => 'SMART_ANTISPAM',
        ] as $key => $val) {
            $CONTENT['antispam'][] = [
                'value' => $key,
                'caption' => $view->_($val)
            ];
        }
        $wf = function (Snippet_Folder $x) use (&$wf) {
            $temp = [];
            foreach ($x->children as $row) {
                if (strtolower($row->urn) != '__raas_views') {
                    $o = new Option([
                        'value' => '',
                        'caption' => $row->name,
                        'disabled' => 'disabled'
                    ]);
                    $o->__set('children', $wf($row));
                    $temp[] = $o;
                }
            }
            foreach ($x->snippets as $row) {
                $temp[] = new Option([
                    'value' => $row->id,
                    'caption' => $row->urn . (($row->name && ($row->name != $row->urn)) ? (': ' . $row->name) : ''),
                ]);
            }
            return $temp;
        };
        $field = new RAASField();

        $defaultParams = [
            'caption' => $Item->id ? $Item->name : $view->_('CREATING_FORM'),
            'parentUrl' => Sub_Dev::i()->url . '&action=forms',
            'meta' => [],
            'children' => [
                [
                    'name' => 'name',
                    'caption' => $view->_('NAME'),
                    'required' => 'required'
                ],
                [
                    'name' => 'urn',
                    'caption' => $view->_('URN')
                ],
                [
                    'type' => 'select',
                    'name' => 'material_type',
                    'caption' => $view->_('MATERIAL_TYPE'),
                    'children' => ['Set' => $CONTENT['material_types']],
                    'placeholder' => $view->_('_NONE')
                ],
                [
                    'type' => 'checkbox',
                    'name' => 'create_feedback',
                    'caption' => $view->_('CREATE_FEEDBACK'),
                    'default' => 1,
                ],
                [
                    'type' => 'checkbox',
                    'name' => 'signature',
                    'caption' => $view->_('REQUIRE_UNIQUE'),
                    'default' => 1,
                ],
                [
                    'type' => 'select',
                    'name' => 'antispam',
                    'caption' => $view->_('ANTISPAM_FIELD'),
                    'children' => $CONTENT['antispam'],
                    'default' => 'hidden'
                ],
                [
                    'name' => 'antispam_field_name',
                    'caption' => $view->_('ANTISPAM_VARIABLE'),
                    'default' => '_question'
                ],
                [
                    'name' => 'email',
                    'caption' => $view->_('EMAIL_TO_SEND_NOTIFY'),
                    'data-hint' => $view->_('SPACE_COMMA_SEMICOLON_SEPARATED'),
                    'default' => Application::i()->user->email
                ],
                [
                    'type' => 'select',
                    'class' => 'input-xxlarge',
                    'name' => 'interface_id',
                    'required' => true,
                    'caption' => $view->_('INTERFACE'),
                    'placeholder' => $view->_('_NONE'),
                    'children' => $wf(new Snippet_Folder()),
                    'default' => Snippet::importByURN('__raas_form_notify')->id,
                ],
            ]
        ];
        if ($usingBlocks = $Item->usingBlocks) {
            $defaultParams['meta']['blocksTable'] = new EntityUsersTable([
                'caption' => $this->view->_('BLOCKS'),
                'Set' => $usingBlocks,
            ]);
        }
        if ($usingCartTypes = $Item->usingCartTypes) {
            $shopViewClassname = 'RAAS\CMS\Shop\ViewSub_Dev';
            $defaultParams['meta']['cartTypesTable'] = new EntityUsersTable([
                'caption' => $shopViewClassname::i()->_('CART_TYPES'),
                'Set' => $usingCartTypes,
            ]);
        }
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
