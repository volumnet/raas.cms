<?php
/**
 * Поле
 */
declare(strict_types=1);

namespace RAAS\CMS;

use Exception;
use SOME\Namespaces;
use SOME\SOME;
use RAAS\Attachment;
use RAAS\Application;
use RAAS\CustomField;
use RAAS\DatatypeStrategy;
use RAAS\Field as RAASField;

/**
 * Класс поля
 * @property-read RAASField $Field Поле для формы редактирования
 * @property-read Snippet $Preprocessor Препроцессор поля
 * @property-read Snippet $Postprocessor Постпроцессор поля
 */
class Field extends CustomField
{
    /**
     * Таблица данных
     */
    const DATA_TABLE = 'cms_data';

    /**
     * Класс справочника
     */
    const DICTIONARY_CLASS = Dictionary::class;

    protected static $objectCascadeDelete = true;

    protected static $references = [
        'Preprocessor' => [
            'FK' => 'preprocessor_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
        'Postprocessor' => [
            'FK' => 'postprocessor_id',
            'classname' => Snippet::class,
            'cascade' => false
        ],
    ];

    protected static $tablename = 'cms_fields';

    public function __get($var)
    {
        switch ($var) {
            case 'datatypeStrategyURN':
                $prefixedStrategyURN = 'cms.' . $this->datatype;
                $registeredStrategies = DatatypeStrategy::get();
                if (isset($registeredStrategies[$prefixedStrategyURN])) {
                    return $prefixedStrategyURN;
                }
                return $this->datatype;
                break;
            case 'datatypeStrategy':
                return DatatypeStrategy::spawn($this->datatypeStrategyURN);
                break;
            case 'Field':
                $field = parent::__get($var);
                $field->datatypeStrategyURN = $this->datatypeStrategyURN;
                if (Namespaces::getNS($this->datatypeStrategy) == __NAMESPACE__) {
                    $field->template = 'cms/field.inc.php';
                }
                return $field;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function oncommit(RAASField $field)
    {
        if ($this->datatypeStrategy->isMedia()) {
            $this->deleteValues();
            $filesData = $this->datatypeStrategy->getFilesData($this, true, true);
            $filesToProcess = [];
            if ($this->Preprocessor->id) {
                $this->Preprocessor->process(['files' => (array)($_FILES[$field->name]['tmp_name'] ?? [])]);
            }
            foreach ($filesData as $key => $fileData) {
                // 2017-09-05, AVS: убрал создание attachment'а по ID#, чтобы не было конфликтов
                // в случае дублирования материалов с одним attachment'ом
                // с текущего момента каждый новый загруженный файл - это новый attachment
                $attachment = $this->processAttachment($fileData);
                $oldAttachmentId = (int)($fileData['meta']['attachment'] ?? null);
                if ($attachment) {
                    $filesToProcess[] = $attachment->file;
                }
                if (!$attachment && $oldAttachmentId) {
                    $attachment = new Attachment($oldAttachmentId);
                }
                if ($attachment && $attachment->id) {
                    $attachment->vis = (bool)($fileData['meta']['vis'] ?? true);
                    $attachment->name = trim($fileData['meta']['name'] ?? '');
                    $attachment->description = trim($fileData['meta']['description'] ?? '');
                    $value = $this->datatypeStrategy->export($attachment);
                    if ($value !== null) {
                        $this->addValue($value);
                    }
                }
            }
            if ($this->Postprocessor->id) {
                $this->Postprocessor->process(['files' => $filesToProcess]);
            }
            $this->clearLostAttachments();
        } else {
            return parent::oncommit($field);
        }
    }


    /**
     * Меняет значение свойства "отображать в таблице"
     */
    public function show_in_table()
    {
        $this->show_in_table = (int)!(bool)$this->show_in_table;
        $this->commit();
    }


    /**
     * Возвращает максимальный размер изображения в пикселях
     * @return int
     */
    public function getMaxSize(): int
    {
        $result = (int)Package::i()->registryGet('maxsize');
        return $result;
    }


    /**
     * Возвращает размер эскиза в пикселях
     * @return int
     */
    public function getTnSize(): int
    {
        $result = (int)Package::i()->registryGet('tnsize');
        return $result;
    }
}
