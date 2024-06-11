<?php
/**
 * Форма редактирования текстового блока
 */
declare(strict_types=1);

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
     * Получает вкладку "Общие"
     * @return FormTab
     */
    protected function getCommonTab(Page $parent = null): FormTab
    {
        $tab = parent::getCommonTab();
        $mime = $parent->mime ?: 'text/html';
        if ($mime == 'text/html') {
            $tab->children['description'] = [
                'type' => 'htmlcodearea',
                'name' => 'description',
                'data-mime' => $mime,
            ];
        } else {
            $tab->children['description'] = [
                'type' => 'codearea',
                'name' => 'description',
                'data-mime' => $mime,
            ];
        }
        if ($mime == 'text/html') {
            $tab->children['wysiwyg'] = [
                'type' => 'checkbox',
                'name' => 'wysiwyg',
                'caption' => $this->view->_('USE_WYSIWYG_EDITOR'),
                'default' => 1
            ];
        }
        return $tab;
    }


    protected function getServiceTab(): FormTab
    {
        $tab = parent::getServiceTab();
        $tab->children['interface_id'] = $this->getInterfaceField();
        $tab->children['widget_id'] = $this->getWidgetField();
        return $tab;
    }
}
