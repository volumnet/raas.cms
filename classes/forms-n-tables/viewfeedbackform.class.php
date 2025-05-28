<?php
/**
 * Форма просмотра сообщения обратной связи
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Field as RAASField;
use RAAS\FieldSet;
use RAAS\Form as RAASForm;
use RAAS\FormTab;
use RAAS\CMS\FieldGroup;

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
        $item = $params['Item'] ?? new Feedback();
        $defaultParams = $this->getParams($params);
        $defaultParams['children'] = $this->getChildren($item);
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает параметры для конструктора формы
     * @param array $params Входные параметры
     * @return array <pre><code>[
     *     'caption' => string Заголовок формы,
     *     'template' => string Шаблон формы
     * ]</code></pre>
     */
    protected function getParams(array $params = []): array
    {
        $arr = [];
        $arr['caption'] = sprintf($this->view->_('FEEDBACK_N'), (int)($params['Item']->id ?? 0));
        $arr['template'] = 'cms/feedback_view';
        return $arr;
    }


    /**
     * Получает список дочерних узлов
     * @param Feedback $item Заявка для получения
     * @return array <pre><code>array<FormTab|FieldSet|RAASField></code></pre>
     */
    protected function getChildren(Feedback $item): array
    {
        return $this->getDetails($item);
    }


    /**
     * Получает список дочерних узлов
     * @param Feedback $item Заявка для получения
     * @return array <pre><code>array<FormTab|FieldSet|RAASField></code></pre>
     */
    protected function getDetails(Feedback $item): array
    {
        $fieldGroups = $item->parent->fieldGroups;
        $result = [];
        if (count($fieldGroups) > 1) {
            foreach ($fieldGroups as $fieldGroupURN => $fieldGroup) {
                $fieldSetURN = ($fieldGroupURN ? ('fieldset.' . $fieldGroupURN) : 'common');
                $fieldSetData = [
                    'name' => $fieldSetURN,
                    'caption' => $fieldGroup->name ?: $this->view->_('GENERAL'),
                    'children' => [],
                ];
                if (!$fieldGroupURN) {
                    $fieldSetData['children'] = $this->getPreStat($item);
                }
                $fieldSetData['children'] = array_merge(
                    $fieldSetData['children'],
                    $this->getDetailsFields($item, $fieldGroup)
                );
                $result[$fieldSetURN] = new FieldSet($fieldSetData);
            }
            $result['stat'] = new FieldSet([
                'name' => 'stat',
                'caption' => $this->view->_('SERVICE'),
                'children' => $this->getStat($item),
            ]);
        } else {
            $result = array_merge($this->getPreStat($item), $this->getDetailsFields($item), $this->getStat($item));
        }
        return $result;
    }


    /**
     * Получает предварительную статистическую информацию по сообщению обратной связи
     * @param Feedback $item Заявка для получения
     * @return array <pre><code>array<string[] URN поля в форме просмотра => [
     *     'name' => string URN поля,
     *     'caption' => string Заголовок поля,
     *     'template' => string Шаблон поля,
     * ]></code></pre>
     */
    protected function getPreStat(Feedback $item): array
    {
        $result = [];
        $result['post_date'] = [
            'name' => 'post_date',
            'caption' => $this->view->_('POST_DATE'),
            'template' => 'cms/feedback_view.field.inc.php',
        ];
        return $result;
    }


    /**
     * Получает список дочерних полей по кастомным полям формы
     * @param Feedback $item Заявка для получения
     * @param ?FieldGroup $fieldGroup Группа полей (null, если общим списком)
     * @return array <pre><code>array<string[] ID# поля в форме просмотра => RAASField></code></pre>
     */
    protected function getDetailsFields(Feedback $item, ?FieldGroup $fieldGroup = null): array
    {
        $arr = [];
        if ($fieldGroup) {
            $fields = $fieldGroup->getFields($item);
        } else {
            $fields = $item->fields ?? [];
        }
        foreach ($fields as $field) {
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
     * @param Feedback $item Заявка для получения
     * @return array <pre><code>array<string[] URN поля в форме просмотра => [
     *     'name' => string URN поля,
     *     'caption' => string Заголовок поля,
     *     'template' => string Шаблон поля,
     * ]></code></pre>
     */
    protected function getStat(Feedback $item): array
    {
        $arr = [];
        $arr['pid'] = [
            'name' => 'pid',
            'caption' => $this->view->_('FORM'),
            'template' => 'cms/feedback_view.form_field.inc.php'
        ];
        if ($item && $item->uid) {
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
        if ($item && $item->viewer->id) {
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
    protected function getFeedbackField(array $params = []): RAASField
    {
        $defaultParams = ['template' => 'cms/feedback_view.field.inc.php'];
        $arr = array_merge($defaultParams, $params);
        return new RAASField($arr);
    }
}
