<?php
/**
 * Представление блока
 */
namespace RAAS\CMS;

use SOME\HTTP;
use RAAS\Abstract_Package_View as RAASAbstractPackageView;
use RAAS\Abstract_Module_View as RAASAbstractModuleView;

/**
 * Класс представления блока
 * @property-read RAASAbstractPackageView|RAASAbstractModuleView $view Представление
 */
abstract class ViewBlock
{
    /**
     * CSS-класс блока в списке
     */
    const blockListItemClass = 'cms-block';

    public function __get($var)
    {
        switch ($var) {
            case 'view':
                $NS = \SOME\Namespaces::getNS($this);
                if ($NS == __NAMESPACE__) {
                    return Package::i()->view;
                } else {
                    $classname = $NS . '\\Module';
                    return $classname::i()->view;
                }
                break;
        }
    }


    /**
     * Получает HTML-представление блока в списке
     * @param Block $block Блок для рендеринга
     * @param Page $page Страница, на которой располагается блок
     * @param Location $location Размещение, где находится блок
     * @param int $i Порядковый номер блока в списке
     * @return string
     */
    public function renderBlock(
        Block $block,
        Page $page,
        Location $location,
        $i = 0
    ) {
        // Заменил $this->view на Package::i()->view, т.к. блоки создаются
        // из основного пакета
        $text .= '<div class="well well-small cms-block ' . static::blockListItemClass . '" id="block-' . (int)$block->id . '" title="' . ($block->title) . '">
                    <a class="cms-block-name" href="' . Package::i()->view->url . '&action=edit_block&id=' . (int)$block->id . '&pid=' . (int)$page->id . '">
                      <span' . (!$block->vis ? ' class="muted"' : '') . '>'
              .         $block->title
              .      '</span>
                    </a>';
        if ($temp = ViewSub_Main::i()->getBlockContextMenu(
            $block,
            $page,
            $i,
            count($page->blocksByLocations[$location->urn])
        )) {
            $f = function ($x) {
                return [
                    'text' => '<i class="icon-' . $x['icon'] . '"></i>&nbsp;'
                           .  $x['name'],
                    'href' => $x['href'],
                    'onclick' => $x['onclick']
                ];
            };
            $temp = array_map($f, $temp);
            $temp = json_encode($temp);
            $text .= '<script type="text/javascript">
            jQuery(document).ready(function($) {
                var temp = ' . $temp . ';
                for (var i = 0; i < temp.length; i++) {
                    if (temp[i].onclick) {
                        temp[i].action = new Function("e", temp[i].onclick);
                    }
                }
                console.log(temp);
                context.attach("#block-' . (int)$block->id . '", temp)
            })
            </script>';
        }
        $text .= '<input type="hidden" value="' . (int)$block->id . '" />
                </div>';
        return $text;
    }


    /**
     * Получает название типа блока
     * @return string
     */
    public function renderBlockTypeName()
    {
        return $this->view->_('BLOCK');
    }


    /**
     * Получает HTML-представление легенды блока
     * @param Block_Type|string|null $name Наименование типа блока
     * @return string
     */
    public function renderLegend()
    {
        $arg = func_get_arg(0);
        if ($arg instanceof Block_Type) {
            $name = $arg->viewer->renderBlockTypeName();
        } elseif ($arg instanceof self) {
            $name = $this->renderBlockTypeName();
        } elseif (!is_object($arg)) {
            $name = trim($arg);
        }
        return '<div class="well well-small cms-block ' . static::blockListItemClass . '">
                  <span class="cms-block-name">' .
                    $name .
               '  </span>
                </div>';
    }


    /**
     * Получает контекстное меню размещения
     * @param Page $page Страница, где находится блок
     * @param Location $location Размещение, где находится блок
     * @param string $name|null Наименование пункта на создание блока
     * @param string $type|null Тип блока
     * @return array<[
     *             'name' => string Наименование пункта меню,
     *             'href' => string Ссылка с пункта меню
     *         ]>
     */
    public function locationContextMenu(Page $page, Location $location)
    {
        $name = func_get_arg(2);
        $type = func_get_arg(3);
        return [
            [
                'name' => $name,
                // Заменил $this->view на Package::i()->view, т.к. блоки
                // создаются из основного пакета
                'href' => HTTP::queryString(
                    'type=' . $type,
                    false,
                    (
                        Package::i()->view->url .
                        '&action=edit_block&pid=' . (int)$page->id .
                        '&loc=' . urlencode($location->urn)
                    )
                )
            ]
        ];
    }
}
