<?php
/**
 * Команда проверки на битые файлы
 */
namespace RAAS\CMS;

use RAAS\Attachment;
use RAAS\Command;

/**
 * Команда проверки на битые файлы
 */
class CheckLostFilesCommand extends Command
{
    /**
     * Набор данных медиаполей
     * @var array <pre><code>array<
     *     string[] ID# поля => array Данные поля
     * ></code></pre>
     */
    public $fields = [];

    /**
     * Набор данных вложений
     * @var array <pre><code>array<
     *     string[] ID# вложения => array Данные вложения
     * ></code></pre>
     */
    public $attachments = [];

    /**
     * Набор данных о вложениях без файлов
     * @var array <pre><code>array<
     *     string[] ID# вложения => array<'file'|'tn'|'small'[] => string имя файла> категории потерянных файлов
     * ></code></pre>
     */
    public $lostAttachments = [];

    /**
     * Набор данных о потерянных ссылках
     * @var array <pre><code>array<
     *     string[] класс элемента => array<
     *         string[] ID# элемента => array<
     *             string[] ID# поля => array<
     *                 string индекс поля => array<
     *                     'lost'|'file'|'tn'|'small' => true|string имя файла
     *                 > категории потерянных файлов
     *             >
     *         >
     *     >
     * ></code></pre>
     */
    public $lostData = [];

    public function process()
    {
        $baseUrl = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];

        // Получим список медиа-полей
        $sqlQuery = "SELECT *
                       FROM " . Field::_tablename() . "
                      WHERE datatype IN ('image', 'file')
                        AND classname = ?";
        $sqlResult = Field::_SQL()->get([$sqlQuery, Material_Type::class]);
        foreach ($sqlResult as $sqlRow) {
            $this->fields[trim($sqlRow['id'])] = $sqlRow;
        }

        // Получим список вложений
        $sqlQuery = "SELECT * FROM " . Attachment::_tablename();
        $sqlResult = Attachment::_SQL()->get($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            $this->attachments[trim($sqlRow['id'])] = $sqlRow;
        }

        // Проверим вложения на присутствие
        $c = count($this->attachments);
        $i = 0;
        foreach ($this->attachments as $attachmentId => $attachmentData) {
            $attachment = new Attachment($attachmentData);
            if (!is_file($attachment->file)) {
                $this->lostAttachments[trim($attachmentId)]['file'] = $baseUrl . '/' . $attachment->fileURL;
            }
            if ($attachment->image) {
                if (!is_file($attachment->tn)) {
                    $this->lostAttachments[trim($attachmentId)]['tn'] = $baseUrl . '/' . $attachment->tnURL;
                }
                if (!is_file($attachment->small)) {
                    $this->lostAttachments[trim($attachmentId)]['small'] = $baseUrl . '/' . $attachment->smallURL;
                }
            }
            $i++;
            // if (!($i % 1000)) {
            //     $this->controller->doLog('Проверено вложений ' . $i . ' / ' . $c);
            // }
        }

        if ($this->fields) {
            $sqlQuery = "SELECT *
                           FROM cms_data WHERE fid IN (" . implode(", ", array_keys($this->fields)) . ")";
            $sqlResult = Field::_SQL()->get($sqlQuery);
            foreach ($sqlResult as $sqlRow) {
                $value = (array)json_decode($sqlRow['value'], true);
                if ($value['attachment']) {
                    $lostData = [];
                    if (!($this->attachments[$value['attachment']] ?? null)) {
                        $lostData['lost'] = true;
                    } elseif ($this->lostAttachments[$value['attachment']] ?? null) {
                        $lostData = array_merge($lostData, $this->lostAttachments[$value['attachment']]);
                    }
                    if ($lostData) {
                        $fieldData = $this->fields[$sqlRow['fid']];
                        if ($fieldData['pid']) {
                            $classname = Material::class;
                        } else {
                            $classname = Page::class;
                        }
                        $this->lostData[$classname][$sqlRow['pid']][$sqlRow['fid']][$sqlRow['fii']] = $lostData;
                    }
                }
            }
        }

        if ($this->lostData) {
            $text = 'Найдены следующие потерянные файлы:';
            foreach ($this->lostData as $classname => $classData) {
                $url = $baseUrl . '/admin/?p=cms&action=edit';
                if ($classname == Material::class) {
                    $url .= '_material';
                }
                foreach ($classData as $id => $idData) {
                    $text .= "\n\n" . $url . '&id=' . $id;
                    foreach ($idData as $fid => $fidData) {
                        $text .= "\n    " . 'Поле "' . $this->fields[$fid]['name'] . '"';
                        foreach ($fidData as $fii => $fiiData) {
                            $text .= "\n        Индекс " . $fii;
                            if ($fiiData['lost']) {
                                $text .= ' — потеряно вложение';
                            } else {
                                foreach ($fiiData as $key => $filename) {
                                    $text .= "\n            Потерян " . $key . ' файл: ' . $filename;
                                }
                            }
                        }
                    }
                }
            }
            $this->controller->doLog($text);
        } else {
            $this->controller->doLog('Потерянных файлов не обнаружено');
        }
    }
}
