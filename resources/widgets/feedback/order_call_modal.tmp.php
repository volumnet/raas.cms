<?php
/**
 * Виджет формы заказа звонка (всплывающее окно)
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
} else {
    $formArrayFormatter = new FormArrayFormatter($Form);
    $formArr = $formArrayFormatter->format(
        ['signature' => function ($form) use ($Block) {
            return $form->getSignature($Block);
        }],
        [
            'htmlId' => function ($field) use ($Block) {
                return $field->getHTMLId($Block);
            },
            'placeholder' => function ($field) use ($Block) {
                if ($field->urn == 'phone') {
                    return $field->name . ($field->required ? '*' : '');
                }
                return null;
            },
        ],
    );
    $formData = (object)$DATA;
    ?>
    <!--noindex-->
    <div
      data-vue-role="order-call-modal"
      data-v-bind_block-id="<?php echo (int)$Block->id?>"
      data-v-bind_form="<?php echo htmlspecialchars(json_encode($formArr))?>"
      data-v-bind_initial-form-data="<?php echo htmlspecialchars(json_encode($formData))?>"
      data-v-bind_scroll-to-errors="true"
      data-v-bind_title="<?php echo htmlspecialchars(json_encode($Block->name))?>"
    ></div>
    <!--/noindex-->
<?php } ?>
