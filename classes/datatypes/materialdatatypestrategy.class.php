<?php
/**
 * Стратегия типа данных "Материал"
 */
namespace RAAS\CMS;

use RAAS\DatatypeStrategy;

class MaterialDatatypeStrategy extends DatatypeStrategy
{
    protected static $instance;

    public function export($value)
    {
        if ($value instanceof Material) {
            $value = $value->id;
        }
        return (int)$value ?: null;
    }


    public function import($value)
    {
        $material = new Material($value);
        if ($material->id) {
            return $material;
        }
        return null;
    }


    public function batchImport(array $values): array
    {
        $materialsIds = array_map('intval', $values);
        $materialsIds = array_values(array_unique(array_filter($materialsIds)));

        $materials = [];
        if ($materialsIds) {
            $sqlResult = Material::getSet([
                'where' => "id IN (" . implode(", ", $materialsIds) . ")",
                'orderBy' => "id",
            ]);
            foreach ($sqlResult as $material) {
                $materials[trim($material->id)] = $material;
            }
        }

        $result = [];
        foreach ($values as $value) {
            if (isset($materials[(int)$value])) {
                $result[] = $materials[(int)$value];
            }
        }
        return $result;
    }
}
