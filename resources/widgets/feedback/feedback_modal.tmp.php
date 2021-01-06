<?php
/**
 * Виджет формы обратной связи (всплывающее окно)
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
    <div id="<?php echo htmlspecialchars($Form->urn)?>_modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade feedback feedback-modal" data-v-bind_block-id="<?php echo (int)$Block->id?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div data-vue-role="ajax-form" data-vue-inline-template>
            <form action="#feedback" method="post" enctype="multipart/form-data" data-vue-ref="form">
              <div class="modal-header">
                <div class="h5 modal-title">
                  <?php echo htmlspecialchars($Block->name)?>
                </div>
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close">&times;</button>
              </div>
              <div class="modal-body">
                <div class="feedback__notifications" data-v-bind_class="{ 'feedback__notifications_active': true }" data-v-if="success">
                  <div class="alert alert-success">
                    <?php echo FEEDBACK_SUCCESSFULLY_SENT?>
                  </div>
                </div>

                <div data-v-if="!success">
                  <div class="feedback__required-fields">
                    <?php echo str_replace(
                        '*',
                        '<span class="feedback__asterisk">*</span>',
                        ASTERISK_MARKED_FIELDS_ARE_REQUIRED
                    )?>
                  </div>
                  <div class="feedback__notifications" data-v-bind_class="{ 'feedback__notifications_active': true }" data-v-if="hasErrors">
                    <div class="alert alert-danger">
                      <ul>
                        <li data-v-for="error in errors" data-v-html="error"></li>
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
                  $DATA['full_name'] = 'Test User';
                  echo $formRenderer->renderSignatureField();
                  echo $formRenderer->renderHiddenAntispamField();
                  foreach ($Form->fields as $fieldURN => $field) {
                      $fieldRenderer = FormFieldRenderer::spawn(
                          $field,
                          $Block,
                          $DATA[$fieldURN],
                          $localError
                      );
                      $fieldHTML = $fieldRenderer->render([
                          'data-v-bind_class' => "{ 'is-invalid': !!errors." . $fieldURN . " }"
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
                      <div class="form-group" data-v-bind_class="{ 'text-danger': !!errors.<?php echo htmlspecialchars($fieldURN)?> }">
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
                    <button type="button" class="feedback__cancel btn btn-secondary" data-dismiss="modal">
                      <?php echo CANCEL?>
                    </button>
                    <button class="feedback__submit btn btn-primary" type="submit" data-v-bind_disabled="loading" data-v-bind_class="{ 'feedback__submit_loading': loading }">
                      <?php echo SEND?>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!--/noindex-->
<?php } ?>
