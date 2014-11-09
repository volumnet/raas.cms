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

        $temp = new Page();
        if ($Parent->id) {
            $title = $Item->id ? $this->view->_('EDITING_PAGE') : $this->view->_('CREATING_PAGE');
        } else {
            $title = $Item->id ? $this->view->_('EDITING_SITE') : $this->view->_('CREATING_SITE');
        }
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
        $commonTab->children['name'] = new RAASField(array('name' => 'name', 'caption' => $this->view->_('NAME'), 'required' => 'required'));
        $commonTab->children['description'] = new RAASField(array('type' => 'htmlarea', 'name' => 'description', 'caption' => $this->view->_('DESCRIPTION')));
        $seoTab = new FormTab(array(
            'name' => 'seo', 
            'caption' => $this->view->_('SEO'), 
            'children' => array(
                array('name' => 'urn', 'caption' => $this->view->_('URN')),
                array('name' => 'meta_title', 'caption' => $this->view->_('META_TITLE')),
                array('name' => 'meta_description', 'caption' => $this->view->_('META_DESCRIPTION')),
                array('name' => 'meta_keywords', 'caption' => $this->view->_('META_KEYWORDS')),
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
                    'min' => 0,
                    'step' => 0.1, 
                    'max' => 1,
                    'name' => 'sitemaps_priority', 
                    'caption' => $this->view->_('SITEMAPS_PRIORITY'), 
                    'default' => 0.5
                ), 
            )
        ));
        $serviceTab = new FormTab(array(
            'name' => 'service', 
            'caption' => $this->view->_('SERVICE'), 
            'children' => array(array('type' => 'checkbox', 'name' => 'vis', 'caption' => $this->view->_($Parent->id ? 'VISIBLE' : 'IS_ACTIVE'), 'default' => 1))
        ));
        if ($Item->id) {
            $serviceTab->children[] = array('name' => 'post_date', 'caption' => $this->view->_('CREATED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'modify_date', 'caption' => $this->view->_('EDITED_BY'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
            $serviceTab->children[] = array('name' => 'last_modified', 'caption' => $this->view->_('LAST_AFFECTED_MODIFICATION'), 'export' => 'is_null', 'import' => 'is_null', 'template' => 'stat.inc.php');
        }
        foreach ($Item->fields as $row) {
            $commonTab->children[] = $row->Field;
        }
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
                            if ($row->id && !in_array($row->id, $Item->parents_ids)) {
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
        if ($Item->id) {
            $mTabs = array();
            foreach ($Item->relatedMaterialTypes as $mtype) {
                if ($params['MSet'][$mtype->urn]) {
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
                    $mTabs['_' . $mtype->urn] = new FormTab(array(
                        'name' => '_' . $mtype->urn,
                        'meta' => array('Table' => $temp, 'mtype' => $mtype),
                        'caption' => $this->view->_($mtype->name),
                        'template' => 'material_related.inc.php'
                    ));
                }
            }
        }

        $defaultParams = array(
            'Item' => $Item, 
            'action' => '#',
            'parentUrl' => $this->view->url . '&id=' . $Parent->id . '#_' . $Type->urn, 
            'caption' => $Item->id ? $Item->name : $this->view->_('CREATING_MATERIAL'),
            'children' => array_merge(array($commonTab, $seoTab, $serviceTab, $pagesTab), $mTabs),
            'export' => function($Form) use ($Parent) {
                $Form->exportDefault();
                $Form->Item->editor_id = Application::i()->user->id;
                if (!$Form->Item->id) {
                    $Form->Item->author_id = $Form->Item->editor_id;
                }
            }
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}