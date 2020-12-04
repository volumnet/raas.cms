<?php
/**
 * Виджет формы обратной связи (всплывающее окно)
 * @param Page $Page Текущая страница
 * @param Block_Form $Block Текущий блок
 * @param Feedback $Item Уведомление формы
 * @param Form $Form Форма
 */
namespace RAAS\CMS;

if ($_POST['AJAX'] && ($Item instanceof Feedback)) {
    $result = array();
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
    <div class="feedback-modal">
      <div id="<?php echo htmlspecialchars($Form->urn)?>_modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade">
        <div class="modal-dialog">
          <div class="modal-content">
            <div data-vue-role="ajax-form" data-vue-inline-template>
              <form action="#feedback" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                  <div class="h5 modal-title">
                    <?php echo htmlspecialchars($Block->name)?>
                  </div>
                  <button type="button" data-dismiss="modal" aria-hidden="true" class="close">&times;</button>
                </div>
                <div class="modal-body">
                  <?php include Package::i()->resourcesDir . '/form2.inc.php'?>
                  <div data-v-if="success || error">
                    <div class="alert alert-success" style="display: none" data-v-bind_style="{ display: 'block' }" data-v-if="success">
                      <?php echo FEEDBACK_SUCCESSFULLY_SENT?>
                    </div>
                    <div class="alert alert-danger" data-v-if="localError.length">
                      <ul>
                        <li data-v-for="error in localError" data-v-html="error"></li>
                      </ul>
                    </div>
                  </div>

                  <div <?php echo $success[(int)$Block->id] ? 'style="display: none"' : ''?>>
                    <p>
                      <small class="text-muted">
                        <?php echo ASTERISK_MARKED_FIELDS_ARE_REQUIRED?>
                      </small>
                    </p>
                    <?php if ($Form->signature) { ?>
                        <input type="hidden" name="form_signature" value="<?php echo htmlspecialchars($Form->getSignature($Block))?>" />
                    <?php }
                    if ($Form->antispam == 'hidden' && $Form->antispam_field_name) { ?>
                        <textarea autocomplete="off" name="<?php echo htmlspecialchars($Form->antispam_field_name)?>" style="position: absolute; left: -9999px"><?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?></textarea>
                    <?php }
                    foreach ($Form->fields as $fieldURN => $field) {
                        $fieldRenderer = FormFieldRenderer::spawn(
                            $field,
                            $Block,
                            $DATA,
                            $localError
                        );
                        $fieldHTML = $fieldRenderer->render();
                        $fieldCaption = htmlspecialchars($field->name);
                        if ($fieldURN == 'agree') {
                            $fieldCaption = '<a href="/privacy/" target="_blank">' .
                                               $fieldCaption .
                                            '</a>';
                        }
                        if ($field->required) {
                            $fieldCaption .= '*';
                        }
                        ?>
                        <div class="form-group">
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
                      <button class="feedback__submit btn btn-primary" type="submit" data-v-bind_disabled="loading">
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
    </div>
    <!--/noindex-->
<?php } ?>
