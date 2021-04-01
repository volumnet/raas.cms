<?php
/**
 * Виджет формы заказа звонка (всплывающее окно)
 * @param Page $Page Текущая страница
 * @param Block_Form $Block Текущий блок
 * @param Feedback $Item Уведомление формы
 * @param Form $Form Форма
 */
namespace RAAS\CMS;

if (($_POST['AJAX'] == (int)$Block->id) && ($Item instanceof Feedback)) {
    $result = [];
    if ($success[(int)$Block->id]) {
        $result['success'] = 1;
    }
    if ($localError) {
        $result['localError'] = $localError;
    }
    ob_clean();
    echo json_encode($result);
    exit;
} else { ?>
    <!--noindex-->
    <div id="<?php echo htmlspecialchars($Widget->urn)?>" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade feedback order-call-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="" method="post" enctype="multipart/form-data" data-vue-role="ajax-form" data-v-bind_block-id="<?php echo (int)$Block->id?>" data-v-slot="vm">
            <div class="modal-header">
              <div class="h5 modal-title">
                <?php echo htmlspecialchars($Block->name)?>
              </div>
              <button type="button" data-bs-dismiss="modal" aria-hidden="true" class="btn-close"></button>
            </div>
            <div class="modal-body">
              <div class="feedback__notifications" data-v-bind_class="{ 'feedback__notifications_active': true }" data-v-if="vm.success">
                <div class="alert alert-success">
                  <?php echo FEEDBACK_SUCCESSFULLY_SENT?>
                </div>
              </div>

              <div data-v-if="!vm.success">
                <div class="feedback__notifications" data-v-bind_class="{ 'feedback__notifications_active': true }" data-v-if="vm.hasErrors">
                  <div class="alert alert-danger">
                    <ul>
                      <li data-v-for="error in vm.errors" data-v-html="error"></li>
                    </ul>
                  </div>
                </div>
                <?php
                $formRenderer = new FormRenderer(
                    $Form,
                    $Block,
                    $DATA,
                    $localError
                );
                echo $formRenderer->renderSignatureField();
                echo $formRenderer->renderHiddenAntispamField();
                $fieldURN = 'phone';
                $field = $Form->fields[$fieldURN];
                $field->placeholder = $field->name . ($field->required ? '*' : '');
                $fieldRenderer = FormFieldRenderer::spawn(
                    $field,
                    $Block,
                    $DATA[$fieldURN],
                    $localError
                );
                $fieldHTML = $fieldRenderer->render([
                    'data-v-bind_class' => "{ 'is-invalid': !!vm.errors." . $fieldURN . " }",
                    'data-v-bind_title' => "vm.errors." . $fieldURN . " || ''"
                ]);
                ?>
                <div class="form-group input-group order-call-modal__phone" data-v-bind_class="{ 'text-danger': !!vm.errors.<?php echo htmlspecialchars($fieldURN)?> }">
                  <?php echo $fieldHTML; ?>
                  <button class="btn btn-primary order-call-modal__submit" type="submit" data-v-bind_disabled="vm.loading" data-v-bind_class="{ 'order-call-modal__submit_loading': vm.loading }"></button>
                </div>
                <?php
                $fieldURN = 'agree';
                $field = $Form->fields[$fieldURN];
                $fieldRenderer = FormFieldRenderer::spawn(
                    $field,
                    $Block,
                    $DATA[$fieldURN],
                    $localError
                );
                $fieldHTML = $fieldRenderer->render([
                      'data-v-bind_class' => "{ 'is-invalid': !!vm.errors." . $fieldURN . " }",
                      'data-v-bind_title' => "vm.errors." . $fieldURN . " || ''"
                  ]);
                $fieldCaption = '<a href="/privacy/" target="_blank">' .
                                   htmlspecialchars($field->name) .
                                '</a>';
                ?>
                <div class="form-group" data-v-bind_class="{ 'text-danger': !!vm.errors.<?php echo htmlspecialchars($fieldURN)?> }">
                  <label>
                    <?php echo $fieldHTML . ' ' . $fieldCaption; ?>
                  </label>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--/noindex-->
    <?php
    Package::i()->requestCSS('/css/feedback.css');
    Package::i()->requestJS('/js/feedback.js');
    Package::i()->requestCSS('/css/order-call-modal.css');
    Package::i()->requestJS('/js/order-call-modal.js');
} ?>
