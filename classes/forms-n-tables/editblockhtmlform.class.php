<?php
/**
 * Форма редактирования текстового блока
 */
namespace RAAS\CMS;

use RAAS\Field as RAASField;
use RAAS\FormTab;

/**
 * Класс формы редактирования текстового блока
 */
class EditBlockHTMLForm extends EditBlockForm
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        unset(
            $this->children['serviceTab']->children['cache_type'],
            $this->children['serviceTab']->children['cache_single_page'],
            $this->children['serviceTab']->children['cache_interface_id']
        );
    }


    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        return $field;
    }


    protected function getWidgetField()
    {
        $field = parent::getWidgetField();
        return $field;
    }


    /**
     * Получает вкладку "Общие"
     * @return FormTab
     */
    protected function getCommonTab(Page $parent = null)
    {
        $tab = parent::getCommonTab();
        $mime = $parent->mime ?: 'text/html';
        if ($mime == 'text/html') {
            $tab->children[] = [
                'type' => 'htmlcodearea',
                'name' => 'description',
                'data-mime' => $mime,
            ];
        } else {
            $tab->children[] = [
                'type' => 'codearea',
                'name' => 'description',
                'data-mime' => $mime,
            ];
        }
        if ($mime == 'text/html') {
            $tab->children[] = [
                'type' => 'checkbox',
                'name' => 'wysiwyg',
                'caption' => $this->view->_('USE_WYSIWYG_EDITOR'),
                'default' => 1
            ];
        }
        return $tab;
    }


    protected function getServiceTab()
    {
        $tab = parent::getServiceTab();
        $tab->children[] = $this->getInterfaceField();
        $tab->children[] = $this->getWidgetField();
        return $tab;
    }
}
