<?php
/**
 * Триггеры
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

?>
<div class="triggers triggers__list triggers-list" data-vue-role="triggers" data-v-slot="vm">
  <a href="#top" class="triggers-list__item triggers-item triggers-item_totop scrollTo" data-v-if="scrollTop > 500" data-v-bind_class="{ 'triggers-item_active': (scrollTop > 500) }" title="Наверх"></a>
  <!--nodesktop-->
  <a href="#" class="triggers-list__item triggers-item triggers-item_filter" data-v-if="vm.filterActive" title="Фильтр каталога" style="display: none" data-v-bind_style="{ display: 'flex' }" data-v-on_click.stop.prevent="jqEmit('raas.shop.openfilter');"></a>
  <a href="#" class="triggers-list__item triggers-item triggers-item_menu" title="Меню" data-v-on_click.stop.prevent="jqEmit('raas.openmobilemenu');"></a>
  <!--/nodesktop-->
  <a href="#" class="triggers-list__item triggers-item triggers-item_order-call" data-bs-target="#order_call_modal" data-bs-toggle="modal" title="Заказать звонок"></a>
  <a href="#" class="triggers-list__item triggers-item triggers-item_feedback" data-bs-target="#feedback_modal" data-bs-toggle="modal" title="Написать письмо"></a>
</div>
