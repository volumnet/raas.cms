<?php
namespace RAAS\CMS;
use \RAAS\Application;
use \RAAS\FormTab;
use \RAAS\FieldSet;
use \RAAS\Field as RAASField;
use \RAAS\Column;

class EditMaterialForm extends \RAAS\Form
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


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $Type = isset($params['Type']) ? $params['Type'] : null;
        $related = isset($params['related']) ? $params['related'] : array();
        $Parent = isset($params['Parent']) ? $params['Parent'] : null;

        if ($Parent->id) {
            $title = $Item->id ? $this->view->_('EDITING_PAGE') : $this->view->_('CREATING_PAGE');
        } else {
            $title = $Item->id ? $this->view->_('EDITING_SITE') : $this->view->_('CREATING_SITE');
        }
        
        $tabs = array();
        $tabs['common'] = $this->getCommonTab($Item, $Type);
        $tabs['seo'] = $this->getSeoTab();
        if (isset(Application::i()->packages['cms']->modules['users'])) {
            $tabs['access'] = new CMSAccessFormTab($params);
        }
        $tabs['service'] = $this->getServiceTab($Item, $Parent);
        $tabs['pages'] = $this->getPagesTab($Item, $Parent, $Type);
        if ($Item->id) {
            foreach ($Item->relatedMaterialTypes as $mtype) {
                if ($params['MSet'][$mtype->urn]) {
                    $tabs['_' . $mtype->urn] = $this->getMTab($Item, $mtype, $params);
                }
            }
        }

        $defaultParams = array(
            'Item' => $Item, 
            'action' => '#',
            'parentUrl' => $this->view->url . '&id=' . $Parent->id . '#_' . $Type->urn, 
            'caption' => $Item->id ? $Item->name : $this->view->_('CREATING_MATERIAL'),
            'children' => $tabs,
            'export' => function($Form) use ($Parent) {
                $Form->exportDefault();
                $Form->Item->editor_id = Application::i()->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            },
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    private function getCommonTab($Item, $Type)
    {
        $commonTab = new FormTab(array('name' => 'common', 'caption' => $this->view->_('GENERAL')));
        if ($Type->children) {
            $commonTab->children['pid'] = new RAASField(array(
                'type' => 'select', 'name' => 'pid', 'caption' => $this->view->_('MATERIAL_TYPE'), 'children' => array('Set' => array($Type))
            ));
            if ($Item->id) {
                $commonTab->children['pid']->onchange = 'if (confirm(\'' . addslashes($this->view->_('CHANGE_MATERIAL_TYPE_EXISTING_CONFIRM')) . '\')) { this.form.submit(); }';
            } else {
                $commonTab->children['pid']->onchange = 'if (confirm(\'' . addslashes($this->view->_('CHANGE_MATERIAL_TYPE_NEW_CONFIRM')) . '\')) { document.location.href = document.location.href.replace(/mtype=\\d+/, \'mtype=\' + this.value); }';
            }
        }
        $commonTab->children['name'] = new RAASField(array('name' => 'name', 'class' => 'span5', 'caption' => $this->view->_('NAME'), 'required' => 'required'));
        $commonTab->children['description'] = new RAASField(array('type' => 'htmlarea', 'name' => 'description', 'caption' => $this->view->_('DESCRIPTION')));
        foreach ($Item->fields as $row) {
            $commonTab->children[] = $row->Field;
        }
        return $commonTab;
    }


    private function getSeoTab()
    {
        $seoTab = new FormTab(array(
            'name' => 'seo', 
            'caption' => $this->view->_('SEO'), 
            'children' => array(
                array('name' => 'urn', 'class' => 'span5', 'caption' => $this->view->_('URN')),
                array('name' => 'meta_title', 'class' => 'span5', 'caption' => $this->view->_('META_TITLE')),
                array('type' => 'textarea', 'name' => 'meta_description', 'class' => 'span5', 'rows' => 5, 'caption' => $this->view->_('META_DESCRIPTION')),
                array('type' => 'textarea', 'name' => 'meta_keywords', 'class' => 'span5', 'rows' => 5, 'caption' => $this->view->_('META_KEYWORDS')),
                array('name' => 'h1', 'caption' => $this->view->_('H1'), 'placeholder' => $this->view->_('FROM_NAME'), 'class' => 'span5'),
                array('name' => 'menu_name', 'caption' => $this->view->_('MENU_NAME'), 'placeholder' => $this->view->_('FROM_NAME'), 'class' => 'span5'),
                array('name' => 'breadcrumbs_name', 'caption' => $this->view->_('BREADCRUMBS_NAME'), 'placeholder' => $this->view->_('FROM_NAME'), 'class' => 'span5'),
                array(
                    'type' => 'select',
                    'name' => 'changefreq', 
                    'caption' => $this->view->_('CHANGEFREQ'), 
                    'placeholder' => $this->view->_('AUTOMATICALLY'),
                    'children' => array(
                        array('value' => 'always', 'caption' => $this->view->_('CHANGEFREQ_ALWAYS')),
                        array('value' => 'hourly', 'caption' => $this->view->_('CHANGEFREQ_HOURLY')),
                        array('value' => 'daily', 'caption' => $this->view->_('CHANGEFREQ_DAILY')),
                        array('value' => 'weekly', 'caption' => $this->view->_('CHANGEFREQ_WEEKLY')),
                        array('value' => 'monthly', 'caption' => $this->view->_('CHANGEFREQ_MONTHLY')),
                        array('value' => 'yearly', 'caption' => $this->view->_('CHANGEFREQ_YEARLY')),
                        array('value' => 'never', 'caption' => $this->view->_('CHANGEFREQ_NEVER'))
                    )
                ), 
                array(
                    'type' => 'number', 
                    'class' => 'span5',
                    'min' => 0,
                    'step' => 0.1, 
                    'max' => 1,
                    'name' => 'sitemaps_priority', 
                    'caption' => $this->view->_('SITEMAPS_PRIORITY'), 
                    'default' => 0.5
                ), 
            )
        ));
        return $seoTab;
    }


    private function getServiceTab($Item, $Parent)
    {
        $serviceTab = new FormTab(array(
            'name' => 'service', 
            'caption' => $this->view->_('SERVICE'), 
            'children' => array(
                array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_($Parent->id ? 'VISIBLE' : 'IS_ACTIVE'), 'default' => 1),
                array('type' => 'datetime', 'name' => 'show_from', 'caption' => $this->view->_('SHOW_FROM')),
                array('type' => 'datetime', 'name' => 'show_to', 'caption' => $this->view->_('SHOW_TO')),
            )
        ));
        if ($Item->id) {
            $serviceTab->children[] = array('name' => 'post_date', 'caption' => $this->view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'modify_date', 'caption' => $this->view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'last_modified', 'caption' => $this->view->_('LAST_AFFECTED_MODIFICATION'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
        }
        return $serviceTab;
    }


    private function getPagesTab($Item, $Parent, $Type)
    {
        $temp = new Page();
        $pagesTab = new FormTab(array(
            'name' => 'pages',
            'caption' => $this->view->_('PAGES'),
            'children' => array(
                'page_id' => array(
                    'type' => 'select', 
                    'name' => 'page_id', 
                    'caption' => $this->view->_('MAIN_PARENT_PAGE'),
                    'children' => array(
                        'Set' => $temp->children, 
                        'additional' => function($row) use ($Item) { 
                            $arr = array(); 
                            $ids = array_map(function($x) { return $x->id; }, $Item->affectedPages);
                            if ($row->id && !in_array($row->id, $ids)) {
                                $arr['style'] = 'display: none'; 
                            } 
                            return $arr;
                        }
                    ), 
                    'placeholder' => $this->view->_('DEFAULT'),
                )
            )
        ));
        if (!$Type->global_type){
            $pagesTab->children['cats'] = array(
                'type' => 'checkbox', 
                'multiple' => true, 
                'name' => 'cats', 
                'caption' => $this->view->_('PAGES'),
                'required' => 'required', 
                'children' => array('Set' => $temp->children, 'additional' => function($row) { return array('data-group' => $row->template); }), 
                'default' => array((int)$Parent->id),
                'import' => function($Field) { return $Field->Form->Item->pages_ids; },
            );
        }
        return $pagesTab;
    }


    private function getMTab($Item, $mtype, $params)
    {
        $temp = new MaterialsRelatedTable(array(
            'Item' => $Item,
            'mtype' => $mtype,
            'hashTag' => $mtype->urn,
            'Set' => $params['MSet'][$mtype->urn],
            'Pages' => $params['MPages'][$mtype->urn], 
            'sortVar' => 'm' . $mtype->id . 'sort',
            'orderVar' => 'm' . $mtype->id . 'order',
            'pagesVar' => 'm' . $mtype->id . 'page',
            'sort' => $params['Msort'][$mtype->urn], 
            'order' => ((strtolower($params['Morder'][$mtype->urn]) == 'desc') ? Column::SORT_DESC : Column::SORT_ASC)
        ));
        $tab = new FormTab(array(
            'name' => '_' . $mtype->urn,
            'meta' => array('Table' => $temp, 'mtype' => $mtype),
            'caption' => $this->view->_($mtype->name),
            'template' => 'material_related.inc.php'
        ));
        return $tab;
    }
}