<?php
/**
 * Виджет блока "{{WIDGET_NAME}}"
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

?>
<div class="{{WIDGET_CSS_CLASSNAME}}">
  <div class="{{WIDGET_CSS_CLASSNAME}}__list">
    <div class="{{WIDGET_CSS_CLASSNAME}}-list">
      <div class="{{WIDGET_CSS_CLASSNAME}}-list__item">
        <a class="{{WIDGET_CSS_CLASSNAME}}-item {{WIDGET_CSS_CLASSNAME}}-item_totop scrollTo" href="#top" title="Наверх"></a>
      </div>
      <div class="{{WIDGET_CSS_CLASSNAME}}-list__item">
        <a class="{{WIDGET_CSS_CLASSNAME}}-item {{WIDGET_CSS_CLASSNAME}}-item_menu" href="#" title="Меню"></a>
      </div>
      <div class="{{WIDGET_CSS_CLASSNAME}}-list__item">
        <a class="{{WIDGET_CSS_CLASSNAME}}-item {{WIDGET_CSS_CLASSNAME}}-item_order-call" data-target="#order_call_modal" data-toggle="modal" href="#" title="Заказать звонок"></a>
      </div>
      <div class="{{WIDGET_CSS_CLASSNAME}}-list__item">
        <a class="{{WIDGET_CSS_CLASSNAME}}-item {{WIDGET_CSS_CLASSNAME}}-item_feedback" data-target="#feedback_modal" data-toggle="modal" href="#" title="Написать письмо"></a>
      </div>
    </div>
  </div>
</div>
<?php echo Package::i()->asset('/js/{{WIDGET_CSS_CLASSNAME}}.js')?>
