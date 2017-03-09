<?php
$_RAASForm_Control = function (\RAAS\Field $Field) use (&$_RAASForm_Attrs, &$_RAASForm_Options, &$_RAASForm_Checkbox, &$_RAASForm_Control) {
    $Item = $Field->Form->Item;
    if (isset($Field->meta['Field'])) {
        $f = $Field->meta['Field'];
        $values = $f->getValues(true);
        $arr = array();
        foreach ($values as $key => $val) {
            $val = $f->doRich($val);
            switch ($f->datatype) {
                case 'date':
                    $arr[$key] = date(DATEFORMAT, strtotime($val));
                    break;
                case 'datetime-local':
                    $arr[$key] = date(DATETIMEFORMAT, strtotime($val));
                    break;
                case 'color':
                    $arr[$key] = '<span style="display: inline-block; height: 16px; width: 16px; background-color: ' . htmlspecialchars($val) . '"></span>';
                    break;
                case 'email':
                    $arr[$key] .= '<a href="mailto:' . htmlspecialchars($val) . '">' . htmlspecialchars($val) . '</a>';
                    break;
                case 'url':
                    $arr[$key] .= '<a href="' . (!preg_match('/^http(s)?:\\/\\//umi', trim($val)) ? 'http://' : '') . htmlspecialchars($val) . '">' . htmlspecialchars($val) . '</a>';
                    break;
                case 'file':
                    $arr[$key] .= '<a href="/' . $val->fileURL . '">' . htmlspecialchars($val->filename) . '</a>';
                    break;
                case 'image':
                    $arr[$key] .= '<a href="/' . $val->fileURL . '"><img src="/' . $val->tnURL. '" alt="' . htmlspecialchars($val->filename) . '" title="' . htmlspecialchars($val->filename) . '" /></a>';
                    break;
                case 'htmlarea':
                    $arr[$key] = '<div>' . $val . '</div>';
                    break;
                case 'material':
                    $arr[$key] .= '<a href="?p=cms&action=edit_material&id=' . $val->id . '" target="_blank">' . htmlspecialchars($val->name) . '</a>';
                    break;
                default:
                    if (!$f->multiple && ($f->datatype == 'checkbox')) {
                        $arr[$key] = $val ? _YES : _NO;
                    } else {
                        $arr[$key] = nl2br(htmlspecialchars($val));
                    }
                    break;
            }
        }
        echo implode(', ', $arr);
    } else {
        switch ($Field->name) {
            case 'post_date':
                echo date(DATETIMEFORMAT, strtotime($Item->post_date));
                break;
            case 'uid':
                echo '<a href="?p=' . \RAAS\CMS\Package::i()->alias . '&m=users&action=edit&id=' . (int)$Item->uid . '">' .
                        htmlspecialchars($Item->user->full_name ?: ($Item->user->login ?: $Item->user->email)) .
                     '</a>';
                break;
            case 'page_id':
                if ($Item->page->parents) {
                    foreach ($Item->page->parents as $row) {
                        echo '<a href="' . \RAAS\CMS\Sub_Main::i()->url . '&id=' . (int)$row->id . '">' . htmlspecialchars($row->name) . '</a> / ';
                    }
                }
                echo '<a href="' . \RAAS\CMS\Sub_Main::i()->url . '&id=' . (int)$Item->page_id . '">' . htmlspecialchars($Item->page->name) . '</a>';
                if ($Item->material->id) {
                    echo ' / <a href="' . \RAAS\CMS\Sub_Main::i()->url . '&action=edit_material&id=' . (int)$Item->material_id . '&pid=' . (int)$Item->page_id . '">' . htmlspecialchars($Item->material->name) . '</a>';
                }
                break;
            case 'vis':
                if ($Item->viewer->id) {
                    if ($Item->viewer->email) {
                        echo '<a href="mailto:' . htmlspecialchars($Item->viewer->email) . '">' .
                                htmlspecialchars($Item->viewer->full_name ? $Item->viewer->full_name : $Item->viewer->login) .
                             '</a>';
                    } else {
                        echo htmlspecialchars($Item->viewer->full_name ? $Item->viewer->full_name : $Item->viewer->login);
                    }
                }
                break;
            case 'ip':
                echo '<a href="https://www.nic.ru/whois/?query=' . htmlspecialchars(urlencode($Item->ip)) . '" target="_blank">' . htmlspecialchars($Item->ip) . '</a>';
                break;
            default:
                echo htmlspecialchars($Item->{$Field->name});
                break;
        }
    }
};
