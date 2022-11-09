<?php
/**
 * Виджет формы обратной связи (всплывающее окно)
 * @param Page $Page Текущая страница
 * @param Block_Form $Block Текущий блок
 * @param Feedback $Item Уведомление формы
 * @param Form $Form Форма
 */
namespace RAAS\CMS;

use RAAS\AssetManager;

if (($_POST['AJAX'] == (int)$Block->id) && ($Item instanceof Feedback)) {
    $result = [];
    if ($success[(int)$Block->id]) {
        $result['success'] = true;
    }
    if ($localError) {
        $result['localError'] = $localError;
    }
    while (ob_get_level()) {
        ob_end_clean();
    }
    echo json_encode($result);
    exit;
} else { ?>
    <!--noindex-->
    <div id="<?php echo htmlspecialchars($Form->urn)?>_modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade feedback feedback-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="" method="post" enctype="multipart/form-data" data-vue-role="ajax-form" data-v-bind_block-id="<?php echo (int)$Block->id?>" data-v-slot="vm">
            <div class="modal-header h5 modal-title">
              <?php echo htmlspecialchars($Block->name)?>
              <button type="button" data-bs-dismiss="modal" aria-hidden="true" class="btn-close"></button>
            </div>
            <div class="modal-body">
              <div class="feedback__notifications alert alert-success" data-v-bind_class="{ 'feedback__notifications_active': true }" data-v-if="vm.success">
                <?php echo FEEDBACK_SUCCESSFULLY_SENT?>
              </div>

              <div data-v-if="!vm.success">
                <div class="feedback__required-fields">
                  <?php echo str_replace(
                      '*',
                      '<span class="feedback__asterisk">*</span>',
                      ASTERISK_MARKED_FIELDS_ARE_REQUIRED
                  )?>
                </div>
                <div class="feedback__notifications alert alert-danger" data-v-bind_class="{ 'feedback__notifications_active': true }" data-v-if="vm.hasErrors">
                  <ul>
                    <li data-v-for="error in vm.errors" data-v-html="error"></li>
                  </ul>
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
                foreach ($Form->visFields as $fieldURN => $field) {
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
                    $fieldCaption = htmlspecialchars($field->name);
                    if ($fieldURN == 'agree') {
                        $fieldCaption = '<a href="/privacy/" target="_blank">' .
                                           $fieldCaption .
                                        '</a>';
                    }
                    if ($field->required) {
                        $fieldCaption .= '<span class="feedback__asterisk">*</span>';
                    }
                    ?>
                    <div class="form-group" data-v-bind_class="{ 'text-danger': !!vm.errors.<?php echo htmlspecialchars($fieldURN)?> }">
                      <?php
                      if (($field->datatype == 'checkbox') &&
                          !$field->multiple
                      ) { ?>
                          <label>
                            <?php echo $fieldHTML . ' ' . $fieldCaption; ?>
                          </label>
                      <?php } else { ?>
                          <label <?php echo !$field->multiple ? ' for="' . htmlspecialchars($field->getHTMLId($Block)) . '"' : ''?>>
                            <?php echo $fieldCaption; ?>:
                          </label>
                          <?php echo $fieldHTML;
                      } ?>
                    </div>
                <?php } ?>
                <div class="feedback-modal__controls">
                  <button type="button" class="feedback__cancel btn btn-secondary" data-bs-dismiss="modal">
                    <?php echo CANCEL?>
                  </button>
                  <button class="feedback__submit btn btn-primary" type="submit" data-v-bind_disabled="vm.loading" data-v-bind_class="{ 'feedback__submit_loading': vm.loading }">
                    <?php echo SEND?>
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--/noindex-->
    <?php
    AssetManager::requestCSS('/css/feedback.css');
    AssetManager::requestJS('/js/feedback.js');
    AssetManager::requestCSS('/css/feedback-modal.css');
    AssetManager::requestJS('/js/feedback-modal.js');
} ?>
