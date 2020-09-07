<?php
/**
 * Таблица сущностей, использующих сниппет
 */
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Table;
use RAAS\Row;

/**
 * Класс таблицы сущностей, использующих сниппет
 * @property-read ViewSub_Dev $view Представление
 */
class SnippetUsersTable extends Table
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Dev::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $defaultParams = [
            'columns' => [
                'id' => [
                    'caption' => $this->view->_('ID'),
                    'callback' => function (SOME $item) use ($view) {
                        return '<a href="' . $this->getEditURL($item) . '">
                                  ' . (int)$item->id . '
                                </a>';
                    }
                ],
                'name' => [
                    'caption' => $this->view->_('NAME'),
                    'callback' => function (SOME $item) use ($view) {
                        $text = '<a href="' . $this->getEditURL($item) . '">';
                        if ($item instanceof Field) {
                            if (($item->classname == Material_Type::class) && $item->pid) {
                                $materialType = new Material_Type($item->pid);
                                $text .= htmlspecialchars($materialType->name)
                                      . ' / ';
                            } elseif (($item->classname == Material_Type::class) && !$item->pid) {
                                $text .= $this->view->_('PAGES') . ' / ';
                            } elseif ($item->classname == Form::class) {
                                $form = new Form($item->pid);
                                $text .= htmlspecialchars($form->name)
                                      . ' / ';
                            } elseif ($item->classname == User::class) {
                                $text .= $this->view->_('USERS') . ' / ';
                            }
                        }
                        $text .=   htmlspecialchars($item->name);
                        $text .= '</a>';
                        return $text;
                    }
                ],
            ],
            'emptyString' => $this->view->_('NO_ITEMS_FOUND'),
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает URL редактирования сущности
     * @param SOME $item Сущность для редактирования
     * @return string
     */
    public function getEditURL(SOME $item)
    {
        $priceloaderClassname = 'RAAS\\CMS\\Shop\\PriceLoader';
        $imageloaderClassname = 'RAAS\\CMS\\Shop\\ImageLoader';

        $isPriceLoader = $isImageLoader = false;
        if (class_exists($priceloaderClassname)) {
            if ((get_class($item) == $priceloaderClassname) ||
                is_subclass_of($item, $priceloaderClassname)
            ) {
                $isPriceLoader = true;
            }
        }
        if (class_exists($imageloaderClassname)) {
            if ((get_class($item) == $imageloaderClassname) ||
                is_subclass_of($item, $imageloaderClassname)
            ) {
                $isImageLoader = true;
            }
        }
        if ($item instanceof Block) {
            $url = '?p=cms&action=edit_block';
        } elseif ($item instanceof Form) {
            $url = $this->view->url . '&action=edit_form';
        } elseif ($item instanceof Snippet) {
            $url = $this->view->url . '&action=edit_snippet';
        } elseif ($item instanceof Field) {
            $url = $this->view->url;
            if (($item->classname == Material_Type::class) && $item->pid) {
                $url .= '&action=edit_material_field';
            } elseif (($item->classname == Material_Type::class) && !$item->pid) {
                $url .= '&action=edit_page_field';
            } elseif ($item->classname == Form::class) {
                $url .= '&action=edit_form_field';
            } elseif ($item->classname == User::class) {
                $url .= '&m=users&action=edit_field';
            }
        } elseif ($isPriceLoader) {
            $url = $this->view->url . '&m=shop&action=edit_priceloader';
        } elseif ($isImageLoader) {
            $url = $this->view->url . '&m=shop&action=edit_imageloader';
        }
        $url .= '&id=' . (int)$item->id;
        return $url;
    }
}
