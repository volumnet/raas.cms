<?php
/**
 * Представление подмодуля "Обратная связь"
 */
namespace RAAS\CMS;

use SOME\HTTP;
use RAAS\Abstract_Sub_View as RAASAbstractSubView;

/**
 * Класс представления подмодуля "Обратная связь"
 */
class ViewSub_Feedback extends RAASAbstractSubView
{
    protected static $instance;

    /**
     * Просмотр сообщения обратной связи
     * @param [
     *            'Item' => Feedback Сообщение обратной связи
     *            'Form' => ViewFeedbackForm Форма просмотра сообщения,
     *            'Forms' => array<Form> Список форм
     *        ] $in Входные данные
     */
    public function view(array $in = [])
    {
        $this->assignVars($in);
        $this->title = $in['Form']->caption;
        $this->path[] = [
            'name' => $this->_('FEEDBACK'),
            'href' => $this->url
        ];
        $this->path[] = [
            'name' => $in['Item']->parent->name,
            'href' => $this->url . '&id=' . $in['Item']->pid
        ];
        foreach ((array)$in['Forms'] as $row) {
            $this->submenu[] = [
                'name' => $row->name
                       . ($row->unreadFeedbacks ? ' (' . (int)$row->unreadFeedbacks . ')' : ''),
                'href' => $this->url . '&id=' . (int)$row->id,
                'active' => ($row->id == $in['Item']->pid)
            ];
        }
        $this->contextmenu = $this->getFeedbackContextMenu($in['Item']);
        $this->template = $in['Form']->template;
    }


    /**
     * Возвращает контекстное меню для сообщения обратной связи
     * @param Feedback $feedback Сообщение обратной связи
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getFeedbackContextMenu(Feedback $feedback)
    {
        $arr = [];
        if ($feedback->id) {
            $edit = ($this->action == 'view');
            if (!$edit) {
                $arr[] = [
                    'href' => $this->url . '&action=view&id='
                           .  (int)$feedback->id,
                    'name' => $this->_('VIEW'),
                    'icon' => 'edit'
                ];
                if ($feedback->vis) {
                    $arr[] = [
                        'href' => $this->url . '&action=chvis&id='
                               .  (int)$feedback->id,
                        'name' => $this->_('MARK_AS_UNREAD'),
                        'icon' => 'eye-close'
                    ];
                }
            }
            $arr[] = [
                'href' => $this->url . '&action=delete&id=' . (int)$feedback->id
                       .  ($edit ? '' : '&back=1'),
                'name' => $this->_('DELETE'),
                'icon' => 'remove',
                'onclick' => 'return confirm(\''
                          .  $this->_('DELETE_TEXT')
                          .  '\')'
            ];
        }
        return $arr;
    }


    /**
     * Возвращает контекстное меню для списка сообщений обратной связи
     * @return array<[
     *             'href' ?=> string Ссылка,
     *             'name' => string Заголовок пункта
     *             'icon' ?=> string Наименование иконки,
     *             'title' ?=> string Всплывающая подсказка
     *             'onclick' ?=> string JavaScript-команда при клике,
     *         ]>
     */
    public function getAllFeedbacksContextMenu()
    {
        $arr = [];
        $arr[] = [
            'name' => $this->_('MARK_AS_UNREAD'),
            'href' => $this->url . '&action=chvis&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('MARK_AS_UNREAD')
        ];
        $arr[] = [
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\''
                      .  $this->_('DELETE_MULTIPLE_TEXT')
                      .  '\')'
        ];
        return $arr;
    }


    /**
     * Список сообщений обратной связи
     * @param [
     *            'Set' => array<Feedback> набор заявок,
     *            'Pages' => Pages Постраничная разбивка,
     *            'Item' => Form Родительская форма,
     *            'columns' => array<Form_Field> Поля для отображения,
     *            'Forms' => array<Form> Список форм,
     *            'search_string' => string Поисковая строка
     *        ] $in Входные данные
     */
    public function feedback(array $in = [])
    {
        $view = $this;
        $in['Table'] = new FeedbackTable($in);
        $this->assignVars($in);
        if ($in['Item']->id) {
            $this->path[] = [
                'name' => $this->_('FEEDBACK'),
                'href' => $this->url
            ];
        }
        foreach ((array)$in['Forms'] as $row) {
            $this->submenu[] = [
                'name' => $row->name
                       .  ($row->unreadFeedbacks ? ' (' . (int)$row->unreadFeedbacks . ')' : ''),
                'href' => $this->url . '&id=' . (int)$row->id,
                'active' => ($row->id == $in['Item']->id)
            ];
        }
        $this->title = $in['Table']->caption;
        $this->template = $in['Table']->template;
        $this->contextmenu = [
            [
                'name' => $this->_('EXPORT_CSV_UTF8'),
                'href' => HTTP::queryString('action=export&format=csv'),
            ],
            [
                'name' => $this->_('EXPORT_CSV_WIN1251'),
                'href' => HTTP::queryString('action=export&format=csv1251'),
            ],
            [
                'name' => $this->_('EXPORT_EXCEL'),
                'href' => HTTP::queryString('action=export&format=xls'),
            ],
            [
                'name' => $this->_('EXPORT_EXCEL2007'),
                'href' => HTTP::queryString('action=export&format=xlsx'),
            ],
        ];
    }
}
