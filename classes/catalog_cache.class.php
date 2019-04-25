<?php
namespace RAAS\CMS;

use RAAS\Attachment;

class Catalog_Cache
{
    use CacheTrait;

    protected $_mtype = [];

    protected $_textKeys = [
        'urn',
        'name',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'h1',
        'menu_name',
        'breadcrumbs_name',
        'article'
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'data':
                return $this->$var;
                break;
            case 'mtype':
                return $this->{'_' . $var};
                break;
        }
    }


    public function __construct(Material_Type $materialType)
    {
        $this->_mtype = $materialType;
    }


    public function getCache()
    {
        $t = $this;
        $st = microtime(true);
        $this->clear();

        $sqlWhat = [];

        // Категория непосредственно с товарами
        $sqlFrom = $sqlWhere = [];
        $sort = $order = "";
        if (!$this->_mtype->global_type) {
            $sqlFrom['tMPA'] = " LEFT JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id ";
        }
        $sqlWhere[] = " tM.vis ";
        $sqlWhere[] = " tM.pid IN (" . implode(", ", $this->_mtype->selfAndChildrenIds) . ") ";

        /*** FILTERING ***/
        $sqlArray = [];

        // Набор полей
        if (!$this->_mtype->global_type) {
            $sqlWhat['pages_ids'] = "GROUP_CONCAT(DISTINCT tMPA.pid SEPARATOR '@@@') AS pages_ids";
        }
        foreach ($this->_mtype->selfAndChildren as $mtype) {
            foreach ($mtype->selfFields as $row) {
                if (in_array($row->urn, ['price', 'price_old'])) {
                    $temp = "CAST(value AS UNSIGNED)";
                } else {
                    $temp = "value";
                }
                $sqlWhat[$row->urn] = "(
                    SELECT GROUP_CONCAT(DISTINCT " . $temp . " ORDER BY fii ASC SEPARATOR '@@@')
                      FROM " . Field::data_table . "
                     WHERE pid = tM.id
                       AND fid = " . (int)$row->id . "
                ) AS `" . Field::_SQL()->real_escape_string($row->urn) . "`";
            }
        }

        /*** QUERY ***/
        Material::_SQL()->query("SET SESSION group_concat_max_len=1000000");
        Material::_SQL()->query("SET SQL_BIG_SELECTS=1");
        $sqlQuery = "SELECT SQL_CALC_FOUND_ROWS tM.* " . ($sqlWhat ? ", " . implode(", ", $sqlWhat) : "")
                   . "  FROM " . Material::_tablename() . " AS tM " . implode(" ", $sqlFrom)
                   . ($sqlWhere ? " WHERE " . implode(" AND ", $sqlWhere) : "")
                   . " GROUP BY tM.id ORDER BY NOT tM.priority, tM.priority ASC ";
        // echo $sqlQuery; exit;
        $sqlResult = Material::_SQL()->get($sqlQuery);
        $sqlResult = array_map(
            function ($x) use ($t) {
                $y = $x;
                foreach ($y as $key => $val) {
                    if (stristr($val, '@@@')) {
                        $y[$key] = explode('@@@', $val);
                        $y[$key] = array_unique($y[$key]);
                        $y[$key] = array_values($y[$key]);
                        if (count($y[$key]) == 1) {
                            $y[$key] = array_shift($y[$key]);
                        }
                    }
                    $y[$key] = $t->checkDeepNumeric($y[$key], $key);
                }
                return $y;
            },
            $sqlResult
        );

        // print_r ($sqlResult); exit;

        $this->data = $sqlResult;
        $this->save();
    }


    public function getFilename()
    {
        return Package::i()->cacheDir . '/system/raas_cache_materials' . $this->_mtype->id . '.php';
    }


    public function checkDeepNumeric($data, $key = null)
    {
        if (is_array($data)) {
            $temp = [];
            foreach ($data as $key => $val) {
                $temp[$key] = $this->checkDeepNumeric($val, $key);
            }
            return $temp;
        } else {
            $data = trim($data);
            if (is_numeric($data) && (!$key || !in_array($key, $this->_textKeys))) {
                return (float)$data;
            }
            return $data;
        }
    }
}
