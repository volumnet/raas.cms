<?php
/**
 * Трейт для стратегий медиа-типов данных
 */
namespace RAAS\CMS;

use InvalidArgumentException;
use RAAS\Attachment;

trait MediaDatatypeStrategyTrait
{
    protected function getRawFilesMetaData(string $fieldName, array $postData = [])
    {
        $result = [];
        foreach (['vis', 'name', 'description', 'attachment'] as $key) {
            $result[$key] = $postData[$fieldName . '@' .$key] ?? null;
        }
        return $result;
    }


    /**
     * Обработка значения для сохранения в базу данных
     * @param Attachment $value Значение для сохранения
     * @return mixed
     * @throws InvalidArgumentException В случае, если переданное значение $value не является вложением
     */
    public function export($value)
    {
        if (!($value instanceof Attachment)) {
            throw new InvalidArgumentException('Value must be an attachment');
        }
        $json = [
            'vis' => (bool)(int)$value->vis,
            'name' => (string)$value->name,
            'description' => (string)$value->description,
            'attachment' => (int)$value->id,
        ];
        $result = json_encode($json);
        return $result;
    }


    public function import($value)
    {
        $fileData = (array)json_decode($value, true);
        if ($fileData) {
            $fileData['vis'] = (bool)($fileData['vis'] ?? false);
            $fileData['name'] = trim($fileData['name'] ?? '');
            $fileData['description'] = trim($fileData['description'] ?? '');
            $fileData['attachment'] = (int)($fileData['attachment'] ?? 0);
            if ($attachmentId = $fileData['attachment']) {
                $attachment = new Attachment($attachmentId);
                foreach ($fileData as $key => $val) {
                    $attachment->$key = $val;
                }
                return $attachment;
            }
        }
        return null;
    }


    public function batchImportAttachmentsIds(array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            $value = (array)json_decode($value, true);
            if (isset($value['attachment'])) {
                $result[trim((int)$value['attachment'])] = (int)$value['attachment'];
            }
        }
        $result = array_values($result);
        return $result;
    }


    public function batchImport(array $values): array
    {
        $isIndexedArray = !array_filter(array_keys($values), function ($key) {
            return !is_numeric($key);
        });
        $ids = $this->batchImportAttachmentsIds($values);

        $result = [];

        if ($ids) {
            $sqlQuery = "SELECT * FROM " . Attachment::_tablename() . " WHERE id IN (" . implode(", ", $ids) . ")";
            $sqlResult = Attachment::_SQL()->get($sqlQuery);
            $attachmentsData = [];
            foreach ($sqlResult as $sqlRow) {
                $attachmentsData[trim($sqlRow['id'])] = $sqlRow;
            }
            foreach ($values as $key => $value) {
                $value = (array)json_decode($value, true);
                $value['vis'] = (bool)($value['vis'] ?? false);
                $value['name'] = trim($value['name'] ?? '');
                $value['description'] = trim($value['description'] ?? '');
                $value['attachment'] = (int)($value['attachment'] ?? 0);
                if (isset($attachmentsData[trim($value['attachment'])])) {
                    $result[$key] = new Attachment(array_merge($attachmentsData[trim($value['attachment'])], $value));
                }
            }
        }
        if ($isIndexedArray) {
            $result = array_values($result);
        }
        return $result;
    }
}
