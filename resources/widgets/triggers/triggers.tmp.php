<?php
/**
 * Виджет блока "Триггеры"
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

?>
<div data-vue-role="triggers" data-v-slot="vm">
  <div class="triggers">
    <div class="triggers__list">
      <div class="triggers-list">
        <div class="triggers-list__item triggers-list__item_totop" data-v-bind_class="{ 'triggers-list__item_active': (scrollTop > 500) }">
          <a class="triggers-item triggers-item_totop scrollTo" href="#top" title="Наверх"></a>
        </div>
        <div class="triggers-list__item triggers-list__item_filter" data-v-if="vm.filterActive" data-v-bind_class="{ 'triggers-list__item_active': vm.filterActive }">
          <a class="triggers-item triggers-item_filter" title="Фильтр каталога" data-v-on_click.stop="jqEmit('raas.shop.openfilter');"></a>
        </div>
        <div class="triggers-list__item triggers-list__item_menu">
          <a class="triggers-item triggers-item_menu" title="Меню" data-v-on_click.stop="jqEmit('raas.openmobilemenu');"></a>
        </div>
        <div class="triggers-list__item triggers-list__item_order-call">
          <a class="triggers-item triggers-item_order-call" data-bs-target="#order_call_modal" data-bs-toggle="modal" href="#" title="Заказать звонок"></a>
        </div>
        <div class="triggers-list__item triggers-list__item_feedback">
          <a class="triggers-item triggers-item_feedback" data-bs-target="#feedback_modal" data-bs-toggle="modal" href="#" title="Написать письмо"></a>
        </div>
      </div>
    </div>
  </div>
</div>
