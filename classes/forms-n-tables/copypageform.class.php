<?php
/**
 * Форма копирование страницы
 */
namespace RAAS\CMS;

use \RAAS\FormTab;
use \RAAS\FieldSet;
use RAAS\HTMLElement;
use RAAS\Field as RAASField;

/**
 * Класс формы копирования страницы
 * @todo сделать сохранение параметров
 * @property-read ViewSub_Main $view Представление
 */
class CopyPageForm extends EditPageForm
{
    /**
     * Параметры копирования
     * @var array<string[] Значение параметра => [
     *          'value' => string Значение парамера,
     *          'caption' => ID# перевода названия параметра
     *      ]>
     */
    protected static $copyOptionsMapping = [
        '' => [
            'value' => '',
            'caption' => 'DO_NOTHING'
        ],
        'spread' => [
            'value' => 'spread',
            'caption' => 'SPREAD'
        ],
        'unglob' => [
            'value' => 'unglob',
            'caption' => 'UNGLOBALIZE_AND_DO_NOTHING'
        ],
        'unglob_spread' => [
            'value' => 'spread',
            'caption' => 'UNGLOBALIZE_AND_SPREAD'
        ],
        'copy' => [
            'value' => 'copy',
            'caption' => 'COPY'
        ],
        'unglob_copy' => [
            'value' => 'copy',
            'caption' => 'UNGLOBALIZE_AND_COPY'
        ],
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Main::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $params['selfUrl'] = Sub_Main::i()->url . '&action=edit&id=%d';
        $params['newUrl'] = Sub_Main::i()->url . '&action=edit&pid='
                          . (int)$params['Parent']->id;
        parent::__construct($params);
        $this->caption = $this->view->_('COPY_PAGE');
        $this->meta['Original'] = $original = $params['Original'];
        $this->children['copy'] = $this->getCopyTab($original);
        $item = isset($params['Item']) ? $params['Item'] : null;
        // 2017-08-24, AVS: поменял $original на $item с целью смены названия
        // и URN с суффиксом 2
        $this->defaultize($this, $item);
    }


    /**
     * Устанавливает значение по умолчанию для HTML-элемента
     * (равное копируемой странице)
     * @param HTMLElement $el Элемент, для которого устанавливаем значение
     * @param Page $item Копируемая страница
     */
    protected function defaultize(HTMLElement $el, Page $item)
    {
        if (!($el instanceof RAASField) && $el->children) {
            foreach ($el->children as $row) {
                $this->defaultize($row, $item);
            }
        } else {
            $val = $item->{$el->name};
            if ($el->name == 'access_id') {
                if (count($item->access)) {
                    $el->default = array_fill(0, count($item->access), '');
                }
            } elseif ($el->name == 'access_allow') {
                $el->default = array_map(function ($x) {
                    return (int)$x->allow;
                }, (array)$item->access);
            } elseif ($el->name == 'access_to_type') {
                $el->default = array_map(function ($x) {
                    return (int)$x->to_type;
                }, (array)$item->access);
            } elseif ($el->name == 'access_uid') {
                $el->default = array_map(function ($x) {
                    return (int)$x->uid;
                }, (array)$item->access);
            } elseif ($el->name == 'access_gid') {
                $el->default = array_map(function ($x) {
                    return (int)$x->gid;
                }, (array)$item->access);
            } elseif (!$item->pid && ($el->name == 'urn')) {
                $el->default = '';
            } elseif (in_array($el->type, ['datetime', 'date', 'time']) &&
                (strtotime($val) <= 0)
            ) {
            } elseif ($val) {
                $el->default = $item->{$el->name};
            }
        }
    }


    /**
     * Получает параметры копирования
     * @param array<string> ...$options Какие параметры получить
     * @return array<[
     *             'value' => string Значение парамера,
     *             'caption' => Название параметра
     *         ]>
     */
    protected function getCopyOptions(...$options)
    {
        $result = [];
        foreach (static::$copyOptionsMapping as $key => $val) {
            if (in_array($key, $options)) {
                $val['caption'] = $this->view->_($val['caption']);
                $result[] = $val;
            }
        }
        return $result;
    }


    /**
     * Получает вкладку параметров копирования
     * @param Page $original Копируемая страница
     * @return FormTab
     */
    protected function getCopyTab(Page $original)
    {
        if ($original->pid) {
            $defBlockActions = $this->getCopyOptions('', 'spread');
            $defBlockAction = 'spread';
        } else {
            $defBlockActions = $this->getCopyOptions('', 'copy', 'spread');
            $defBlockAction = 'copy';
        }

        $copyTab = new FormTab([
            'name' => 'copy',
            'caption' => $this->view->_('COPY_PARAMS'),
            'children' => [
                'blocks' => new FieldSet([
                    'name' => 'blocks',
                    'caption' => $this->view->_('BLOCKS'),
                    'children' => [
                        'text_blocks_inherited' => [
                            'type' => 'select',
                            'name' => 'text_blocks_inherited',
                            'caption' => $this->view->_(
                                'INHERITED_AND_MULTIPAGE_TEXT_BLOCKS'
                            ),
                            'children' => $defBlockActions,
                            'default' => $defBlockAction,
                            'class' => 'span4'
                        ],
                        'text_blocks_single' => [
                            'type' => 'select',
                            'name' => 'text_blocks_single',
                            'caption' => $this->view->_('SINGLE_PAGE_TEXT_BLOCKS'),
                            'children' => [
                                [
                                    'value' => '',
                                    'caption' => $this->view->_('DO_NOTHING')
                                ],
                                [
                                    'value' => 'copy',
                                    'caption' => $this->view->_('COPY')
                                ],
                            ],
                            'default' => 'copy',
                            'class' => 'span4'
                        ],
                        'other_blocks' => [
                            'type' => 'select',
                            'name' => 'other_blocks',
                            'caption' => $this->view->_('OTHER_BLOCKS'),
                            'children' => $defBlockActions,
                            'default' => $defBlockAction,
                            'class' => 'span4'
                        ],
                    ]
                ])
            ],
            'export' => 'is_null',
            'oncommit' => 'is_null'
        ]);
        $materialFieldSet = new FieldSet([
            'name' => 'materials',
            'caption' => $this->view->_('MATERIALS'),
            'children' => []
        ]);
        $mtypes = $original->affectedMaterialTypesWithChildren;
        usort($mtypes, function ($a, $b) {
            return strcasecmp($a->name, $b->name);
        });
        foreach ($mtypes as $mtype) {
            if ($mtype->global_type) {
                if ($mtype->nat) {
                    if ($original->pid) {
                        $options = $this->getCopyOptions('', 'unglob');
                        $default = 'unglob';
                    } else {
                        $options = $this->getCopyOptions(
                            '',
                            'unglob_copy',
                            'unglob_spread',
                            'unglob'
                        );
                        $default = 'copy';
                    }
                } else {
                    if ($original->pid) {
                        $options = $this->getCopyOptions('');
                        $default = '';
                    } else {
                        $options = $this->getCopyOptions(
                            '',
                            'unglob',
                            'unglob_copy',
                            'unglob_spread'
                        );
                        $default = 'spread';
                    }
                }
            } else {
                $options = $defActions;
                if ($mtype->nat) {
                    if ($original->pid) {
                        $options = $this->getCopyOptions('', 'spread');
                        $default = 'spread';
                    } else {
                        $options = $this->getCopyOptions('', 'spread', 'copy');
                        $default = 'copy';
                    }
                } else {
                    if ($original->pid) {
                        $options = $this->getCopyOptions('', 'spread');
                        $default = 'spread';
                    } else {
                        $options = $this->getCopyOptions('', 'copy', 'spread');
                        $default = 'spread';
                    }
                }
            }
            $materialFieldSet->children['materials_' . $mtype->urn] = new RAASField([
                'type' => 'select',
                'name' => 'materials_' . $mtype->urn,
                'caption' => $mtype->name,
                'children' => $options,
                'default' => $default,
                'class' => 'span4',
            ]);
        }
        $copyTab->children['materials'] = $materialFieldSet;
        $copyTab->oncommit = function ($formTab) use ($original) {
            $item = $formTab->Form->Item;
            $params = [];
            $params['text_blocks_inherited'] = $_POST['text_blocks_inherited'];
            $params['text_blocks_single'] = $_POST['text_blocks_single'];
            $params['other_blocks'] = $_POST['other_blocks'];
            foreach ($_POST as $key => $val) {
                if (preg_match('/^materials_(.*?)$/umi', $key, $regs)) {
                    $params['materials'][$regs[1]] = $val;
                }
            }
            $pch = new PageCopyHelper($params, $original, $item);
            $pch->oncopy();
        };
        return $copyTab;
    }
}
