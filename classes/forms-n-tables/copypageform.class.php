<?php
namespace RAAS\CMS;

use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\FieldSet;
use RAAS\HTMLElement;
use RAAS\Field as RAASField;

/**
 * @todo сделать сохранение параметров
 */
class CopyPageForm extends EditPageForm
{
    protected static $copyOptionsMapping = array(
        '' => array('value' => '', 'caption' => 'DO_NOTHING'),
        'spread' => array('value' => 'spread', 'caption' => 'SPREAD'),
        'unglob' => array('value' => 'unglob', 'caption' => 'UNGLOBALIZE_AND_DO_NOTHING'),
        'unglob_spread' => array('value' => 'spread', 'caption' => 'UNGLOBALIZE_AND_SPREAD'),
        'copy' => array('value' => 'copy', 'caption' => 'COPY'),
        'unglob_copy' => array('value' => 'copy', 'caption' => 'UNGLOBALIZE_AND_COPY'),
    );

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


    public function __construct(array $params = array())
    {
        $params['selfUrl'] = Sub_Main::i()->url . '&action=edit&id=%d';
        $params['newUrl'] = Sub_Main::i()->url . '&action=edit&pid=' . (int)$params['Parent']->id;
        parent::__construct($params);
        $this->caption = $this->view->_('COPY_PAGE');
        $this->meta['Original'] = $Original = $params['Original'];
        $this->children['copy'] = $this->getCopyTab($Original);
        $Item = isset($params['Item']) ? $params['Item'] : null;
        // 2017-08-24, AVS: поменял $Original на $Item с целью смены названия и URN с суффиксом 2
        $this->defaultize($this, $Item);
    }


    protected function defaultize(HTMLElement $el, Page $Item)
    {
        if (!($el instanceof RAASField) && $el->children) {
            foreach ($el->children as $row) {
                $this->defaultize($row, $Item);
            }
        } else {
            $val = $Item->{$el->name};
            if ($el->name == 'access_id') {
                if (count($Item->access)) {
                    $el->default = array_fill(0, count($Item->access), '');
                }
            } elseif ($el->name == 'access_allow') {
                $el->default = array_map(function ($x) {
                    return (int)$x->allow;
                }, (array)$Item->access);
            } elseif ($el->name == 'access_to_type') {
                $el->default = array_map(function ($x) {
                    return (int)$x->to_type;
                }, (array)$Item->access);
            } elseif ($el->name == 'access_uid') {
                $el->default = array_map(function ($x) {
                    return (int)$x->uid;
                }, (array)$Item->access);
            } elseif ($el->name == 'access_gid') {
                $el->default = array_map(function ($x) {
                    return (int)$x->gid;
                }, (array)$Item->access);
            } elseif (!$Item->pid && ($el->name == 'urn')) {
                $el->default = '';
            } elseif (in_array($el->type, array('datetime', 'date', 'time')) && (strtotime($val) <= 0)) {
            } elseif ($val) {
                $el->default = $Item->{$el->name};
            }
        }
    }


    protected function getCopyOptions()
    {
        $options = func_get_args();
        $result = array();
        foreach (static::$copyOptionsMapping as $key => $val) {
            if (in_array($key, $options)) {
                $val['caption'] = $this->view->_($val['caption']);
                $result[] = $val;
            }
        }
        return $result;
    }


    protected function getCopyTab(Page $Original)
    {
        if ($Original->pid) {
            $defBlockActions = $this->getCopyOptions('', 'spread');
            $defBlockAction = 'spread';
        } else {
            $defBlockActions = $this->getCopyOptions('', 'copy', 'spread');
            $defBlockAction = 'copy';
        }

        $copyTab = new FormTab(array(
            'name' => 'copy',
            'caption' => $this->view->_('COPY_PARAMS'),
            'children' => array(
                'blocks' => new FieldSet(array(
                    'name' => 'blocks',
                    'caption' => $this->view->_('BLOCKS'),
                    'children' => array(
                        'text_blocks_inherited' => array(
                            'type' => 'select',
                            'name' => 'text_blocks_inherited',
                            'caption' => $this->view->_('INHERITED_AND_MULTIPAGE_TEXT_BLOCKS'),
                            'children' => $defBlockActions,
                            'default' => $defBlockAction,
                            'class' => 'span4'
                        ),
                        'text_blocks_single' => array(
                            'type' => 'select',
                            'name' => 'text_blocks_single',
                            'caption' => $this->view->_('SINGLE_PAGE_TEXT_BLOCKS'),
                            'children' => array(
                                array('value' => '', 'caption' => $this->view->_('DO_NOTHING')),
                                array('value' => 'copy', 'caption' => $this->view->_('COPY')),
                            ),
                            'default' => 'copy',
                            'class' => 'span4'
                        ),
                        'other_blocks' => array(
                            'type' => 'select',
                            'name' => 'other_blocks',
                            'caption' => $this->view->_('OTHER_BLOCKS'),
                            'children' => $defBlockActions,
                            'default' => $defBlockAction,
                            'class' => 'span4'
                        ),
                    )
                ))
            ),
            'export' => 'is_null',
            'oncommit' => 'is_null'
        ));
        $materialFieldSet = new FieldSet(array(
            'name' => 'materials',
            'caption' => $this->view->_('MATERIALS'),
            'children' => array()
        ));
        $mtypes = $Original->affectedMaterialTypesWithChildren;
        usort($mtypes, function ($a, $b) {
            return strcasecmp($a->name, $b->name);
        });
        foreach ($mtypes as $mtype) {
            if ($mtype->global_type) {
                if ($mtype->nat) {
                    if ($Original->pid) {
                        $options = $this->getCopyOptions('', 'unglob');
                        $default = 'unglob';
                    } else {
                        $options = $this->getCopyOptions('', 'unglob_copy', 'unglob_spread', 'unglob');
                        $default = 'copy';
                    }
                } else {
                    if ($Original->pid) {
                        $options = $this->getCopyOptions('');
                        $default = '';
                    } else {
                        $options = $this->getCopyOptions('', 'unglob', 'unglob_copy', 'unglob_spread');
                        $default = 'spread';
                    }
                }
            } else {
                $options = $defActions;
                if ($mtype->nat) {
                    if ($Original->pid) {
                        $options = $this->getCopyOptions('', 'spread');
                        $default = 'spread';
                    } else {
                        $options = $this->getCopyOptions('', 'spread', 'copy');
                        $default = 'copy';
                    }
                } else {
                    if ($Original->pid) {
                        $options = $this->getCopyOptions('', 'spread');
                        $default = 'spread';
                    } else {
                        $options = $this->getCopyOptions('', 'copy', 'spread');
                        $default = 'spread';
                    }
                }
            }
            $materialFieldSet->children['materials_' . $mtype->urn] = new RAASField(array(
                'type' => 'select',
                'name' => 'materials_' . $mtype->urn,
                'caption' => $mtype->name,
                'children' => $options,
                'default' => $default,
                'class' => 'span4',
            ));
        }
        $copyTab->children['materials'] = $materialFieldSet;
        $copyTab->oncommit = function ($formTab) use ($Original) {
            $Item = $formTab->Form->Item;
            $params = array();
            $params['text_blocks_inherited'] = $_POST['text_blocks_inherited'];
            $params['text_blocks_single'] = $_POST['text_blocks_single'];
            $params['other_blocks'] = $_POST['other_blocks'];
            foreach ($_POST as $key => $val) {
                if (preg_match('/^materials_(.*?)$/umi', $key, $regs)) {
                    $params['materials'][$regs[1]] = $val;
                }
            }
            $pch = new PageCopyHelper($params, $Original, $Item);
            $pch->oncopy();
        };
        return $copyTab;
    }
}
