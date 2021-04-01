<?php
/**
 * Виджет формы оставления вопроса
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
    <div class="feedback feedback_standalone faq-form">
      <?php if ($Block->name[0] != '.') { ?>
          <div class="faq-form__title">
            <?php echo htmlspecialchars($Block->name)?>
          </div>
      <?php } ?>
      <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" data-vue-role="ajax-form" data-v-bind_block-id="<?php echo (int)$Block->id?>" data-v-slot="vm">
        <div class="feedback__notifications" data-v-bind_class="{ 'feedback__notifications_active': true }" data-v-if="vm.success">
          <div class="alert alert-success">
            <?php echo FEEDBACK_SUCCESSFULLY_SENT?>
          </div>
        </div>

        <div data-v-if="!vm.success">
          <div class="feedback__required-fields">
            <?php echo str_replace(
                '*',
                '<span class="feedback__asterisk">*</span>',
                ASTERISK_MARKED_FIELDS_ARE_REQUIRED
            )?>
          </div>
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
          foreach ($Form->fields as $fieldURN => $field) {
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
                    <div class="feedback__control-label"></div>
                    <label class="feedback__input-container">
                      <?php echo $fieldHTML . ' ' . $fieldCaption; ?>
                    </label>
                <?php } else { ?>
                    <label class="feedback__control-label" <?php echo !$field->multiple ? ' for="' . htmlspecialchars($field->getHTMLId($Block)) . '"' : ''?>>
                      <?php echo $fieldCaption; ?>:
                    </label>
                    <div class="feedback__input-container">
                      <?php echo $fieldHTML; ?>
                    </div>
                <?php } ?>
              </div>
          <?php } ?>
          <div class="feedback__controls">
            <button class="feedback__submit btn btn-primary" type="submit" data-v-bind_disabled="vm.loading" data-v-bind_class="{ 'feedback__submit_loading': vm.loading }">
              <?php echo SEND?>
            </button>
          </div>
        </div>
      </form>
    </div>
<?php }
Package::i()->requestCSS('/css/feedback.css');
Package::i()->requestJS('/js/feedback.js');
Package::i()->requestCSS('/css/faq-form.css');
Package::i()->requestJS('/js/faq-form.js');
