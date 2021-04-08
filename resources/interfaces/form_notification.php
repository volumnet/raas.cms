<?php
/**
 * Стандартное уведомление о форме обратной связи
 * @param bool $SMS Уведомление отправляется по SMS
 * @param Feedback $Item Уведомление формы обратной связи
 * @param Material $Material Созданный материал
 * @param bool $forUser Отправка сообщения для пользователя
 *     (если false то для администратора)
 */
namespace RAAS\CMS;

use RAAS\Controller_Frontend as ControllerFrontend;

$cf = ControllerFrontend::i();
$adminUrl = $cf->schemeHost . '/admin/?p=cms';

$form = $Item->parent;
$page = $Item->page;
$material = $Item->material;

if ($SMS) {
    echo date(DATETIMEFORMAT) . ' ' .
        sprintf(
            FEEDBACK_STANDARD_HEADER,
            $form->name,
            $page->name,
            $cf->idnHost
        ) . "\n" .
        FEEDBACK_ID . ': ' . (int)$Item->id . "\n";
    foreach ($Item->fields as $field) {
        $renderer = NotificationFieldRenderer::spawn($field);
        echo $renderer->render(['admin' => !$forUser, 'sms' => true]);
    }
} else { ?>
    <div>
      <?php echo FEEDBACK_ID . ': ' . (int)$Item->id?>
    </div>
    <div>
      <?php
      if ($forUser) {
          $fields = $Item->visFields;
      } else {
          $fields = $Item->fields;
      }
      foreach ($fields as $field) {
          $renderer = NotificationFieldRenderer::spawn($field);
          echo $renderer->render(['admin' => !$forUser, 'sms' => false]);
      } ?>
    </div>
    <?php if (!$forUser) {
        $url = '';
        if ($Material && $Material->id) {
            $url = $cf->schemeHost .
                '/admin/?p=cms&sub=main&action=edit_material&id=' .
                (int)$Material->id . '&pid=';
            $affectedPagesIds = array_map(function ($x) {
                return $x->id;
            }, (array)$form->Material_Type->affectedPages);
            if (in_array($page->id, $affectedPagesIds)) {
                $url .= $page->id;
            } else {
                $url .= $affectedPagesIds[0];
            }
        } elseif ($form->create_feedback) {
            $url = $cf->schemeHost
                . '/admin/?p=cms&sub=feedback&action=view&id=' . $Item->id;
        }
        if ($url) { ?>
            <p>
              <a href="<?php echo htmlspecialchars($url)?>">
                <?php echo VIEW?>
              </a>
            </p>
        <?php } ?>
        <p>
          <small>
            <?php
            echo IP_ADDRESS . ': ' .
                htmlspecialchars($Item->ip) . '<br />' .
                USER_AGENT . ': ' .
                htmlspecialchars($Item->user_agent) . '<br />' .
                PAGE . ': ';
            if ($page->parents) {
                foreach ($page->parents as $row) { ?>
                    <a href="<?php echo htmlspecialchars($adminUrl . '&id=' . (int)$row->id)?>">
                      <?php echo htmlspecialchars($row->name)?>
                    </a> /
                <?php }
            } ?>
            <a href="<?php echo htmlspecialchars($adminUrl . '&id=' . (int)$page->id)?>">
              <?php echo htmlspecialchars($page->name)?>
            </a>
            <?php if ($material->id) { ?>
                /
                <a href="<?php echo htmlspecialchars($adminUrl . '&action=edit_material&id=' . (int)$material->id)?>">
                  <?php echo htmlspecialchars($material->name)?>
                </a>
            <?php } ?>
            <br />
            <?php echo FORM . ': ';
            if ($form->create_feedback) { ?>
                <a href="<?php echo htmlspecialchars($adminUrl . '&sub=feedback&id=' . (int)$form->id)?>">
                  <?php echo htmlspecialchars($form->name)?>
                </a>
            <?php } else {
                echo htmlspecialchars($form->name);
            } ?>
          </small>
        </p>
    <?php }
}
