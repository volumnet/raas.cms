<?php
/**
 * Сообщение с формы обратной связи
 */
namespace RAAS\CMS;

use SOME\SOME;
use SOME\Text;
use RAAS\Attachment;
use RAAS\User as RAASUser;

/**
 * Класс сообщения с формы обратной связи
 * @property-read array<Form_Field> $fields Поля формы с установленным свойством
 *                                          $Owner
 * @property-read User $user Пользователь, отправивший сообщение
 * @property-read Form $parent Родительская форма
 * @property-read Page $page Страница, с которой отправлено сообщения
 * @property-read Material $material Материал, со страницы которого отправлено
 *                                   сообщение
 * @property-read RAASUser $viewer Пользователь админки, который просмотрел
 *                                 сообщение
 * @property-read string $domain Адрес домена, с которого отправлено сообщение
 * @property-read string $description Текстовое описание сообщения
 */
class Feedback extends SOME
{
    protected static $tablename = 'cms_feedback';

    protected static $defaultOrderBy = "post_date DESC";

    protected static $objectCascadeDelete = true;

    protected static $cognizableVars = ['fields', 'visFields'];

    protected static $references = [
        'user' => [
            'FK' => 'uid',
            'classname' => User::class,
            'cascade' => true
        ],
        'parent' => [
            'FK' => 'pid',
            'classname' => Form::class,
            'cascade' => true
        ],
        'page' => [
            'FK' => 'page_id',
            'classname' => Page::class,
            'cascade' => false
        ],
        'material' => [
            'FK' => 'material_id',
            'classname' => Material::class,
            'cascade' => false
        ],
        'viewer' => [
            'FK' => 'vis',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'domain':
                return $this->page->domain;
                break;
            case 'description':
                $text = '';
                foreach ($this->fields as $field) {
                    $values = $field->getValues(true);
                    $arr = [];
                    foreach ($values as $key => $val) {
                        $val = $field->doRich($val);
                        switch ($field->datatype) {
                            case 'date':
                                $t = strtotime($val);
                                $arr[$key] = date(Package::i()->view->_('DATEFORMAT'), $t);
                                break;
                            case 'datetime-local':
                                $t = strtotime($val);
                                $arr[$key] = date(Package::i()->view->_('DATETIMEFORMAT'), $t);
                                break;
                            case 'email':
                            case 'url':
                                $arr[$key] = $val;
                                break;
                            case 'htmlarea':
                                $arr[$key] = strip_tags($val);
                                break;
                            case 'file':
                            case 'image':
                            case 'material':
                                break;
                            default:
                                if (!$field->multiple &&
                                    ($field->datatype == 'checkbox')
                                ) {
                                    $arr[$key] = Package::i()->view->_($val ? '_YES' : '_NO');
                                } else {
                                    $arr[$key] = $val;
                                }
                                break;
                        }
                        $arr[$key] = Text::cuttext(
                            preg_replace('/\\s/i', ' ', $arr[$key] ?? ''),
                            32
                        );
                    }
                    $arr = array_filter($arr, 'trim');
                    if ($arr) {
                        $text .= implode(', ', $arr) . '; ';
                    }
                    if (mb_strlen($text) > 256) {
                        break;
                    }
                }
                $text = Text::cuttext($text, 256);
                return $text;
                break;
            default:
                $val = parent::__get($var);
                if ($val !== null) {
                    return $val;
                } else {
                    $vis = false;
                    if (substr($var, 0, 3) == 'vis') {
                        $var = strtolower(substr($var, 3));
                        $vis = true;
                    }
                    if (($this->fields[$var] ?? null) &&
                        ($this->fields[$var] instanceof Form_Field)
                    ) {
                        $temp = $this->fields[$var]->getValues();
                        if ($vis) {
                            $temp = array_values(array_filter(
                                (array)$temp,
                                function ($x) {
                                    return isset($x->vis) && $x->vis;
                                }
                            ));
                        }
                        return $temp;
                    }
                }
                break;
        }
    }

    public function commit()
    {
        if (!$this->id) {
            $this->post_date = date('Y-m-d H:i:s');
        }
        parent::commit();
    }


    public static function delete(SOME $object)
    {
        foreach ($object->fields as $row) {
            if (in_array($row->datatype, ['image', 'file'])) {
                foreach ($row->getValues(true) as $att) {
                    Attachment::delete($att);
                }
            }
            $row->deleteValues();
        }
        parent::delete($object);
    }


    /**
     * Получает поля сообщения (поля формы с установленным свойством $Owner)
     * @return Form_Field[]
     */
    protected function _fields()
    {
        $temp = $this->parent->fields;
        $arr = [];
        foreach ($temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    /**
     * Список видимых полей (включая родительские)
     * @return Form_Field[]
     */
    protected function _visFields()
    {
        return array_filter($this->fields, function ($x) {
            return $x->vis;
        });
    }

    /**
     * Получает общее количество непросмотренных сообщений
     * @return int
     */
    public static function unreadFeedbacks()
    {
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . static::_tablename()
                  . " WHERE NOT vis";
        return static::$SQL->getvalue($sqlQuery);
    }
}
