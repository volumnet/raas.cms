<?php
namespace RAAS\CMS;

use RAAS\Attachment;
use RAAS\Application;
use \Mustache_Engine;

$notify = function (Feedback $Item, Material $Material = null) {
    $temp = array_values(array_filter(array_map('trim', preg_split('/( |;|,)/', $Item->parent->email))));
    $emails = $sms_emails = $sms_phones = array();
    foreach ($temp as $row) {
        if (($row[0] == '[') && ($row[strlen($row) - 1] == ']')) {
            if (filter_var(substr($row, 1, -1), FILTER_VALIDATE_EMAIL)) {
                $sms_emails[] = substr($row, 1, -1);
            } elseif (preg_match('/(\\+)?\\d+/umi', substr($row, 1, -1))) {
                $sms_phones[] = substr($row, 1, -1);
            }
        } else {
            $emails[] = $row;
        }
    }
    if ($Item->parent->Interface->id) {
        $template = $Item->parent->Interface->description;
    }

    ob_start();
    eval('?' . '>' . $template);
    $message = ob_get_contents();
    ob_end_clean();

    ob_start();
    $SMS = true;
    eval('?' . '>' . $template);
    $message_sms = ob_get_contents();
    ob_end_clean();

    $subject = date(DATETIMEFORMAT) . ' ' . sprintf(FEEDBACK_STANDARD_HEADER, $Item->parent->name, $Item->page->name);
    if ($emails) {
        Application::i()->sendmail($emails, $subject, $message, ADMINISTRATION_OF_SITE . ' ' . $_SERVER['HTTP_HOST'], 'info@' . $_SERVER['HTTP_HOST']);
    }
    if ($sms_emails) {
        Application::i()->sendmail($sms_emails, $subject, $message_sms, ADMINISTRATION_OF_SITE . ' ' . $_SERVER['HTTP_HOST'], 'info@' . $_SERVER['HTTP_HOST'], false);
    }
    if ($sms_phones) {
        $urlTemplate = Package::i()->registryGet('sms_gate');
        $m = new Mustache_Engine();
        foreach ($sms_phones as $phone) {
            $url = $m->render($urlTemplate, array('PHONE' => urlencode($phone), 'TEXT' => urlencode($message_sms)));
            $result = file_get_contents($url);
        }
    }
};

$OUT = array();
$Form = new Form(isset($config['form']) ? (int)$config['form'] : 0);
if ($Form->id) {
    $localError = array();
    if (($Form->signature && isset($_POST['form_signature']) && $_POST['form_signature'] == md5('form' . (int)$Form->id . (int)$Block->id)) || (!$Form->signature && ($_SERVER['REQUEST_METHOD'] == 'POST'))) {
        $Item = new Feedback();
        $Item->pid = (int)$Form->id;
        if ($Form->Material_Type->id) {
            $Material = new Material();
            $Material->pid = (int)$Form->Material_Type->id;
            $Material->vis = 0;
        }

        // Проверка полей на корректность
        foreach ($Form->fields as $row) {
            switch ($row->datatype) {
                case 'file':
                case 'image':
                    $val = isset($_FILES[$row->urn]['tmp_name']) ? $_FILES[$row->urn]['tmp_name'] : null;
                    if ($val && $row->multiple) {
                        $val = (array)$val;
                        $val = array_shift($val);
                    }
                    if (!isset($val) || !$row->isFilled($val)) {
                        if ($row->required && !$row->countValues()) {
                            $localError[$row->urn] = sprintf(ERR_CUSTOM_FIELD_REQUIRED, $row->name);
                        }
                    } elseif (!$row->multiple) {
                        if (!$row->validate($val)) {
                            $localError[$row->urn] = sprintf(ERR_CUSTOM_FIELD_INVALID, $row->name);
                        }
                    }
                    $allowedExtensions = preg_split('/\\W+/umis', $row->source);
                    $allowedExtensions = array_map('mb_strtolower', array_filter($allowedExtensions, 'trim'));
                    if ($allowedExtensions) {
                        if ($row->multiple) {
                            foreach ((array)$_FILES[$row->urn]['tmp_name'] as $i => $val) {
                                if (is_uploaded_file($_FILES[$row->urn]['tmp_name'][$i])) {
                                    $ext = pathinfo(
                                        $_FILES[$row->urn]['name'][$i],
                                        PATHINFO_EXTENSION
                                    );
                                    $ext = mb_strtolower($ext);
                                    if (!in_array($ext, $allowedExtensions)) {
                                        $localError[$row->urn] = sprintf(
                                            INVALID_FILE_EXTENSION,
                                            implode(', ', $allowedExtensions)
                                        );
                                        break;
                                    }
                                }
                            }
                        } else {
                            if (is_uploaded_file($_FILES[$row->urn]['tmp_name'])) {
                                $ext = pathinfo(
                                    $_FILES[$row->urn]['name'],
                                    PATHINFO_EXTENSION
                                );
                                $ext = mb_strtolower($ext);
                                if (!in_array($ext, $allowedExtensions)) {
                                    $localError[$row->urn] = sprintf(
                                        INVALID_FILE_EXTENSION,
                                        implode(', ', $allowedExtensions)
                                    );
                                    break;
                                }
                            }
                        }
                    }
                    break;
                default:
                    $val = isset($_POST[$row->urn]) ? $_POST[$row->urn] : null;
                    if ($val && $row->multiple) {
                        $val = (array)$val;
                        $val = array_shift($val);
                    }
                    if (!isset($val) || !$row->isFilled($val)) {
                        if ($row->required) {
                            $localError[$row->urn] = sprintf(ERR_CUSTOM_FIELD_REQUIRED, $row->name);
                        }
                    } elseif (!$row->multiple) {
                        if (($row->datatype == 'password') && ($_POST[$row->urn] != $_POST[$row->urn . '@confirm'])) {
                            $localError[$row->urn] = sprintf(ERR_CUSTOM_PASSWORD_DOESNT_MATCH_CONFIRM, $row->name);
                        } elseif (!$row->validate($val)) {
                            $localError[$row->urn] = sprintf(ERR_CUSTOM_FIELD_INVALID, $row->name);
                        }
                    }
                    break;
            }
        }

        // Проверка на антиспам
        if ($Form->antispam && $Form->antispam_field_name) {
            switch ($Form->antispam) {
                case 'captcha':
                    if (!isset($_POST[$Form->antispam_field_name], $_SESSION['captcha_keystring']) || ($_POST[$Form->antispam_field_name] != $_SESSION['captcha_keystring'])) {
                        $localError[$Form->antispam_field_name] = ERR_CAPTCHA_FIELD_INVALID;
                    }
                    break;
                case 'hidden':
                    if (isset($_POST[$Form->antispam_field_name]) && $_POST[$Form->antispam_field_name]) {
                        $localError[$Form->antispam_field_name] = ERR_CAPTCHA_FIELD_INVALID;
                    }
                    break;
            }
        }

        if (!$localError) {
            if ((\RAAS\Controller_Frontend::i()->user instanceof User) && \RAAS\Controller_Frontend::i()->user->id) {
                $Item->uid = (int)Controller_Frontend::i()->user->id;
            } else {
                $Item->uid = 0;
            }
            // Для AJAX'а
            $Referer = Page::importByURL($_SERVER['HTTP_REFERER']);
            $RefererMaterialUrl = explode('/', trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '/'));
            $RefererMaterial = Material::importByURN($RefererMaterialUrl[count($RefererMaterialUrl) - 1]);
            $Item->page_id = (int)$Referer->id ?: (int)$Page->id;
            if ($RefererMaterial) {
                $Item->material_id = (int)$RefererMaterial->id;
            } elseif ($Page->Material->id) {
                $Item->material_id = (int)$Page->Material->id;
            }
            $Item->ip = (string)$_SERVER['REMOTE_ADDR'];
            $Item->user_agent = (string)$_SERVER['HTTP_USER_AGENT'];
            $Objects = array($Item);
            if ($Form->Material_Type->id) {
                if (!$Form->Material_Type->global_type) {
                    $Material->cats = array((int)$Referer->id ?: (int)$Page->id);
                }
                $Objects[] = $Material;
            }

            foreach ($Objects as $Object) {
                // Заполняем основные данные создаваемого материала
                if ($Object instanceof Material) {
                    if (isset($Item->fields['_name_'])) {
                        $Object->name = $Item->fields['_name_']->getValue();
                    } else {
                        $Object->name = $Form->Material_Type->name . ': ' . date(DATETIMEFORMAT);
                    }
                    if (isset($Item->fields['_description_'])) {
                        $Object->description = $Item->fields['_description_']->getValue();
                    }
                }
                $Object->commit();

                // Автоматически подставляем недостающие поля даты/времени у материала
                if ($Object instanceof Material) {
                    foreach ($Object->fields as $fname => $temp) {
                        if (!isset($Item->fields[$fname])) {
                            switch ($temp->datatype) {
                                case 'datetime':
                                case 'datetime-local':
                                    $temp->addValue(date('Y-m-d H:i:s'));
                                    break;
                                case 'date':
                                    $temp->addValue(date('Y-m-d'));
                                    break;
                                case 'time':
                                    $temp->addValue(date('H:i:s'));
                                    break;
                            }
                        }
                    }
                }

                foreach ($Item->fields as $fname => $temp) {
                    if (isset($Object->fields[$fname])) {
                        $row = $Object->fields[$fname];
                        switch ($row->datatype) {
                            case 'file':
                            case 'image':
                                $row->deleteValues();
                                if ($row->multiple) {
                                    foreach ($_FILES[$row->urn]['tmp_name'] as $key => $val) {
                                        $row2 = array(
                                            'vis' => isset($_POST[$row->urn . '@vis'][$key]) ? (int)$_POST[$row->urn . '@vis'][$key] : 1,
                                            'name' => (string)$_POST[$row->urn . '@name'][$key],
                                            'description' => (string)$_POST[$row->urn . '@description'][$key],
                                            'attachment' => (int)$_POST[$row->urn . '@attachment'][$key]
                                        );
                                        if (is_uploaded_file($_FILES[$row->urn]['tmp_name'][$key]) && $row->validate($_FILES[$row->urn]['tmp_name'][$key])) {
                                            $att = new Attachment((int)$row2['attachment']);
                                            $att->upload = $_FILES[$row->urn]['tmp_name'][$key];
                                            $att->filename = $_FILES[$row->urn]['name'][$key];
                                            $att->mime = $_FILES[$row->urn]['type'][$key];
                                            $att->parent = $Object;
                                            if ($row->datatype == 'image') {
                                                $att->image = 1;
                                                if ($temp = (int)Package::i()->registryGet('maxsize')) {
                                                    $att->maxWidth = $att->maxHeight = $temp;
                                                }
                                                if ($temp = (int)Package::i()->registryGet('tnsize')) {
                                                    $att->tnsize = $temp;
                                                }
                                            }
                                            $att->copy = true;
                                            $att->commit();
                                            $row2['attachment'] = (int)$att->id;
                                            $row->addValue(json_encode($row2));
                                        } elseif ($row2['attachment']) {
                                            $row->addValue(json_encode($row2));
                                        }
                                        unset($att, $row2);
                                    }
                                } else {
                                    $row2 = array(
                                        'vis' => isset($_POST[$row->urn . '@vis']) ? (int)$_POST[$row->urn . '@vis'] : 1,
                                        'name' => (string)$_POST[$row->urn . '@name'],
                                        'description' => (string)$_POST[$row->urn . '@description'],
                                        'attachment' => (int)$_POST[$row->urn . '@attachment']
                                    );

                                    if (is_uploaded_file($_FILES[$row->urn]['tmp_name']) && $row->validate($_FILES[$row->urn]['tmp_name'])) {
                                        $att = new Attachment((int)$row2['attachment']);
                                        $att->upload = $_FILES[$row->urn]['tmp_name'];
                                        $att->filename = $_FILES[$row->urn]['name'];
                                        $att->mime = $_FILES[$row->urn]['type'];
                                        $att->parent = $Object;
                                        if ($row->datatype == 'image') {
                                            $att->image = 1;
                                            if ($temp = (int)Package::i()->registryGet('maxsize')) {
                                                $att->maxWidth = $att->maxHeight = $temp;
                                            }
                                            if ($temp = (int)Package::i()->registryGet('tnsize')) {
                                                $att->tnsize = $temp;
                                            }
                                        }
                                        $att->copy = true;
                                        $att->commit();
                                        $row2['attachment'] = (int)$att->id;
                                        $row->addValue(json_encode($row2));
                                    } elseif ($_POST[$row->urn . '@attachment']) {
                                        $row2['attachment'] = (int)$_POST[$row->urn . '@attachment'];
                                        $row->addValue(json_encode($row2));
                                    }
                                    unset($att, $row2);
                                }
                                break;
                            default:
                                $row->deleteValues();
                                if (isset($_POST[$row->urn])) {
                                    foreach ((array)$_POST[$row->urn] as $val) {
                                        // 2019-01-22, AVS: закрываем XSS-уязвимость
                                        $row->addValue(strip_tags($val));
                                    }
                                }
                                break;
                        }
                        if (in_array($row->datatype, array('file', 'image'))) {
                            $row->clearLostAttachments();
                        }
                    }
                }

                // Заполняем данные пользователя в полях материала
                if ($Object instanceof Material) {
                    if (isset($Object->fields['ip'])) {
                        $Object->fields['ip']->deleteValues();
                        $Object->fields['ip']->addValue((string)$_SERVER['REMOTE_ADDR']);
                    }
                    if (isset($Object->fields['user_agent'])) {
                        $Object->fields['user_agent']->deleteValues();
                        $Object->fields['user_agent']->addValue((string)$_SERVER['HTTP_USER_AGENT']);
                    }
                }
            }
            if ($Form->email) {
                $notify($Item, $Form->Material_Type->id ? $Material : null);
            }
            if (!$Form->create_feedback) {
                Feedback::delete($Item);
            }
            $OUT['success'][(int)$Block->id] = true;
        }
        $OUT['DATA'] = $_POST;
    } else {
        $OUT['DATA'] = array();
        foreach ($Form->fields as $key => $row) {
            if ($row->defval) {
                $OUT['DATA'][$key] = $row->defval;
            }
        }
    }
    $OUT['localError'] = $localError;
    $OUT['Item'] = $Item;
    if ($Form->Material_Type->id) {
        $OUT['Material'] = $Material;
    }
}
$OUT['Form'] = $Form;

return $OUT;