<?php
/**
 * Триггеры
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

$company = $Page->company;
$socials = $company->fields['socials']->getValues(true);
$waMatches = array_values(array_filter($socials, function ($x) {
    return stristr($x, 'wa') || stristr($x, 'whatsapp');
}));
$whatsapp = '';
if ($waMatches) {
    $whatsapp = $waMatches[0];
}

?>
<div class="triggers triggers__list triggers-list" data-vue-role="triggers" data-v-slot="vm">
  <a
    href="#top"
    class="triggers-list__item triggers-item triggers-item_totop scrollTo"
    data-v-if="scrollTop > 500"
    data-v-bind_class="{ 'triggers-item_active': (scrollTop > 500) }"
    title="<?php echo TO_TOP?>"
  ></a>
  <!--nodesktop-->
  <button
    type="button"
    class="triggers-list__item triggers-item triggers-item_filter"
    data-v-if="vm.filterActive"
    title="<?php echo CATALOG_FILTER?>"
    style="display: none"
    data-v-bind_style="{ display: 'flex' }"
    data-v-on_click.stop.prevent="jqEmit('raas.shop.openfilter');"
  ></button>
  <button
    type="button"
    class="triggers-list__item triggers-item triggers-item_menu"
    title="<?php echo MENU?>"
    data-v-on_click.stop.prevent="jqEmit('raas.openmobilemenu');"
  ></button>
  <!--/nodesktop-->
  <button
    type="button"
    class="triggers-list__item triggers-item triggers-item_order-call"
    data-bs-target="#order_call_modal"
    data-bs-toggle="modal"
    title="<?php echo ORDER_CALL?>"
  ></button>
  <?php /*
  <button
    type="button"
    class="triggers-list__item triggers-item triggers-item_feedback"
    data-bs-target="#feedback_modal"
    data-bs-toggle="modal"
    title="<?php echo FEEDBACK_HEADER?>"
  ></button>
  */ ?>
  <?php if ($whatsapp) { ?>
      <a
        href="<?php echo htmlspecialchars($whatsapp)?>"
        target="_blank"
        rel="nofollow"
        class="triggers-list__item triggers-item triggers-item_whatsapp"
        title="<?php echo WE_ARE_IN_WHATSAPP?>"
      ></a>
  <?php } ?>
</div>
