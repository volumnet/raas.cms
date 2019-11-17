<?php
/**
 * Форма просмотра сообщения обратной связи
 */
namespace RAAS\CMS;

use \RAAS\Field as RAASField;
use \RAAS\FieldSet;
use \RAAS\Form as RAASForm;
use \RAAS\FormTab;

/**
 * Класс формы просмотра сообщения обратной связи
 * @property-read ViewSub_Feedback $view Представление
 */
class ViewFeedbackForm extends RAASForm
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Feedback::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $defaultParams = $this->getParams();
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
        $this->__set('children', $this->getChildren());
    }


    /**
     * Получает параметры для конструктора формы
     * @return [
     *             'caption' => string Заголовок формы,
     *             'template' => string Шаблон формы
     *         ]
     */
    protected function getParams()
    {
        $arr = [];
        $arr['caption'] = $this->view->_('FEEDBACK');
        $arr['template'] = 'cms/feedback_view';
        return $arr;
    }


    /**
     * Получает список дочерних узлов
     * @return array<FormTab|FieldSet|RAASField>
     */
    protected function getChildren()
    {
        return $this->getDetails();
    }


    /**
     * Получает список дочерних узлов
     * @return array<FormTab|FieldSet|RAASField>
     */
    protected function getDetails()
    {
        $arr = [];
        $arr['post_date'] = $this->getFeedbackField([
            'name' => 'post_date',
            'caption' => $this->view->_('POST_DATE')
        ]);
        $arr = array_merge($arr, $this->getDetailsFields());
        $arr['pid'] = [
            'name' => 'pid',
            'caption' => $this->view->_('FORM'),
            'template' => 'cms/feedback_view.form_field.inc.php'
        ];
        $arr = array_merge($arr, $this->getStat());
        return $arr;
    }


    /**
     * Получает список дочерних полей по кастомным полям формы
     * @return array<string[] ID# поля в форме просмотра => RAASField>
     */
    protected function getDetailsFields()
    {
        $arr = [];
        foreach ($this->Item->fields as $field) {
            $arr['field.' . $field->urn] = $this->getFeedbackField([
                'name' => 'field.' . $field->urn,
                'caption' => $field->name,
                'meta' => ['Field' => $field],
            ]);
        }
        return $arr;
    }


    /**
     * Получает статистическую информацию по сообщению обратной связи
     * @return array<string[] URN поля в форме просмотра => [
     *             'name' => string URN поля,
     *             'caption' => string Заголовок поля,
     *             'template' => string Шаблон поля,
     *         ]>
     */
    protected function getStat()
    {
        $arr = [];
        if ($this->Item->uid) {
            $arr['uid'] = [
                'name' => 'uid',
                'caption' => $this->view->_('USER'),
                'template' => 'cms/feedback_view.field.inc.php',
            ];
        }
        $arr['page_id'] = [
            'name' => 'page_id',
            'caption' => $this->view->_('PAGE'),
            'template' => 'cms/feedback_view.field.inc.php',
        ];
        if ($this->Item->viewer->id) {
            $arr['vis'] = [
                'name' => 'vis',
                'caption' => $this->view->_('VIEWED_BY'),
                'template' => 'cms/feedback_view.field.inc.php',
            ];
        }
        $arr['ip'] = [
            'name' => 'ip',
            'caption' => $this->view->_('IP_ADDRESS'),
            'template' => 'cms/feedback_view.field.inc.php',
        ];
        $arr['user_agent'] = [
            'name' => 'user_agent',
            'caption' => $this->view->_('USER_AGENT'),
            'template' => 'cms/feedback_view.field.inc.php',
        ];
        return $arr;
    }


    /**
     * Получает поле для формы просмотра
     * @param [
     *            'name' => string URN поля,
     *            'caption' => string Заголовок поля,
     *            'meta' =>? array<string[] => mixed> Мета-данные
     *        ] Параметры создания поля
     * @return RAASField
     */
    protected function getFeedbackField(array $params = [])
    {
        $defaultParams = ['template' => 'cms/feedback_view.field.inc.php'];
        $arr = array_merge($defaultParams, $params);
        return new RAASField($arr);
    }
}
