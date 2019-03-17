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


    /**
     * Получает поле "Интерфейс"
     * @return RAASField
     */
    protected function getInterfaceField()
    {
        $field = parent::getInterfaceField();
        $field->required = false;
        return $field;
    }


    /**
     * Получает поле "Виджет"
     * @return RAASField
     */
    protected function getWidgetField()
    {
        $field = parent::getWidgetField();
        $field->required = false;
        return $field;
    }


    /**
     * Получает вкладку "Общие"
     * @return FormTab
     */
    protected function getCommonTab(Page $parent = null)
    {
        $tab = parent::getCommonTab();
        $mime = 'text/html';
        if ($parent->mime) {
            $mime = $parent->mime;
        }
        if (($mime == 'text/html') && (!$this->Item->id || $this->Item->wysiwyg)) {
            $tab->children[] = [
                'type' => 'htmlarea',
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


    /**
     * Получает вкладку "Служебные"
     * @return FormTab
     */
    protected function getServiceTab()
    {
        $tab = parent::getServiceTab();
        $tab->children[] = $this->getInterfaceField();
        $tab->children[] = $this->getWidgetField();
        return $tab;
    }
}
