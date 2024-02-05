<?php
/**
 * Вкладка доступа для пользователей
 * (для редактирования блока, материала, страницы)
 */
namespace RAAS\CMS;

use RAAS\FormTab;

/**
 * Класс вкладки доступа для пользователей
 * (для редактирования блока, материала, страницы)
 * @property-read ViewSub_Main $view Представление
 */
class CMSAccessFormTab extends FormTab
{
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
        $CONTENT = [];
        $CONTENT['access_allow'] = [
            [
                'value' => 0,
                'caption' => $this->view->_('DENY')
            ],
            [
                'value' => 1,
                'caption' => $this->view->_('ALLOW')
            ],
        ];
        $CONTENT['access_to_type'] = [
            [
                'value' => CMSAccess::TO_ALL,
                'caption' => $this->view->_('ACCESS_TO_ALL')
            ],
            [
                'value' => CMSAccess::TO_UNREGISTERED,
                'caption' => $this->view->_('ACCESS_TO_UNREGISTERED')
            ],
            [
                'value' => CMSAccess::TO_REGISTERED,
                'caption' => $this->view->_('ACCESS_TO_REGISTERED')
            ],
            [
                'value' => CMSAccess::TO_USER,
                'caption' => $this->view->_('ACCESS_TO_USER'),
                'data-show' => 'uid'
            ],
            [
                'value' => CMSAccess::TO_GROUP,
                'caption' => $this->view->_('ACCESS_TO_GROUP'),
                'data-show' => 'gid'
            ],
        ];
        $CONTENT['access_gid'] = ['Set' => Group::getSet()];


        $defaultParams = [
            'caption' => $this->view->_('ACCESS_RIGHTS'),
            'name' => 'access',
            'template' => 'cmsaccess.inc.php',
            'children' => [
                'access_id' => [
                    'type' => 'hidden',
                    'name' => 'access_id',
                    'multiple' => true
                ],
                'access_allow' => [
                    'type' => 'select',
                    'name' => 'access_allow',
                    'multiple' => true,
                    'children' => $CONTENT['access_allow']
                ],
                'access_to_type' => [
                    'type' => 'select',
                    'name' => 'access_to_type',
                    'multiple' => true,
                    'children' => $CONTENT['access_to_type']
                ],
                'access_uid' => [
                    'name' => 'access_uid',
                    'multiple' => true
                ],
                'access_gid' => [
                    'type' => 'select',
                    'name' => 'access_gid',
                    'multiple' => true,
                    'children' => $CONTENT['access_gid']
                ],
            ],
            'import' => function ($FormTab) {
                $DATA = [];
                if ($FormTab->Form->Item->access) {
                    foreach ((array)$FormTab->Form->Item->access as $row) {
                        $DATA['access_id'][] = (int)$row->id;
                        $DATA['access_allow'][] = (int)$row->allow;
                        $DATA['access_to_type'][] = (int)$row->to_type;
                        $DATA['access_uid'][] = (int)$row->uid;
                        $DATA['access_gid'][] = (int)$row->gid;
                    }
                }
                return $DATA;
            },
            'oncommit' => function ($FormTab) {
                $Item = $FormTab->Form->Item;
                if ($Item->id) {
                    $FK = $Item->_children();
                    $FK = $FK['access']['FK'];
                    $presentIds = array_map('intval', (array)($_POST['access_id'] ?? []));
                    $presentIds = array_filter($presentIds);
                    $presentIds[] = 0;
                    $presentIds = array_unique($presentIds);
                    $sqlQuery = "DELETE FROM " . CMSAccess::_tablename()
                              . " WHERE " . $FK . " = ?
                                    AND id NOT IN (" . implode(", ", $presentIds) . ")";
                    $Item->_SQL()->query([$sqlQuery, (int)$Item->id]);
                    foreach ((array)($_POST['access_id'] ?? []) as $key => $val) {
                        $access = new CMSAccess($val);
                        $access->page_id = $access->material_id = $access->block_id = 0;
                        $access->uid = $access->gid = 0;
                        $access->$FK = $Item->id;
                        $access->allow = (bool)(int)$_POST['access_allow'][$key];
                        $access->to_type = (int)$_POST['access_to_type'][$key];
                        $access->priority = (int)$key;
                        if ((int)$_POST['access_to_type'][$key] == CMSAccess::TO_USER) {
                            $access->uid = (int)$_POST['access_uid'][$key];
                        } elseif ((int)$_POST['access_to_type'][$key] == CMSAccess::TO_GROUP) {
                            $access->gid = (int)$_POST['access_gid'][$key];
                        }
                        $access->commit();
                    }
                    if ($Item instanceof Page) {
                        CMSAccess::refreshPagesAccessCache(null, $Item);
                    } elseif ($Item instanceof Material) {
                        CMSAccess::refreshMaterialsAccessCache(null, $Item);
                    } elseif ($Item instanceof Block) {
                        CMSAccess::refreshBlocksAccessCache(null, $Item);
                    }
                }
            }
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
