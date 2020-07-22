<?php
/**
 * Виджет блока "Триггеры"
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

?>
<div data-vue-role="triggers" data-inline-template>
  <div class="triggers">
    <div class="triggers__list">
      <div class="triggers-list">
        <div class="triggers-list__item triggers-list__item_totop" data-v-bind_class="toTopClass">
          <a class="triggers-item triggers-item_totop scrollTo" href="#top" title="Наверх"></a>
        </div>
        <div class="triggers-list__item triggers-list__item_filter" data-v-if="filterActive" data-v-bind_class="filterClass">
          <a class="triggers-item triggers-item_filter" href="#" title="Фильтр каталога" data-v-on_click="openFilter($event);"></a>
        </div>
        <div class="triggers-list__item triggers-list__item_menu">
          <a class="triggers-item triggers-item_menu" href="#" title="Меню"></a>
        </div>
        <div class="triggers-list__item triggers-list__item_order-call">
          <a class="triggers-item triggers-item_order-call" data-target="#order_call_modal" data-toggle="modal" href="#" title="Заказать звонок"></a>
        </div>
        <div class="triggers-list__item triggers-list__item_feedback">
          <a class="triggers-item triggers-item_feedback" data-target="#feedback_modal" data-toggle="modal" href="#" title="Написать письмо"></a>
        </div>
      </div>
    </div>
  </div>
</div>
