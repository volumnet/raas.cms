<?php
namespace RAAS\CMS;

class EditBlockHTMLForm extends EditBlockForm
{
    public function __construct(array $params = array())
    {
        parent::__construct($params);
        unset(
            $this->children['serviceTab']->children['cache_type'], 
            $this->children['serviceTab']->children['cache_single_page'], 
            $this->children['serviceTab']->children['cache_interface_id']
        );
    }


    protected function getCommonTab()
    {
        $tab = parent::getCommonTab();
        if (!$this->Item->id || $this->Item->wysiwyg) {
            $tab->children[] = array('type' => 'htmlarea', 'name' => 'description');
        } else {
            $tab->children[] = array('type' => 'codearea', 'name' => 'description', 'data-language' => 'html');
        }
        $tab->children[] = array('type' => 'checkbox', 'name' => 'wysiwyg', 'caption' => $this->view->_('USE_WYSIWYG_EDITOR'), 'default' => 1);
        return $tab;
    }
}