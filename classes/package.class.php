<?php
namespace RAAS\CMS;

class Package extends \RAAS\Package
{
    const templatesDir = 'templates';
    
    const version = '2013-12-01 18:23:01';
    
    protected static $instance;

    public function __get($var)
    {
        switch ($var) {
            case 'cacheDir':
                return $this->application->baseDir . '/cache';
                break;
            case 'cachePrefix':
                return 'raas_cache';
                break;
            case 'formTemplateFile':
                return $this->resourcesDir . '/form_fields.php';
                break;
            case 'stdFormTemplate':
                $text = file_get_contents($this->formTemplateFile);
                /*preg_match_all('/\\<\\?php echo @@@(CMS\\\\)?(\\w+) *\\?\\>/i', $text, $regs);
                foreach ($regs[2] as $key => $val) {
                    $text = str_replace($regs[0][$key], $this->view->_($val), $text);
                }
                preg_match_all('/@@(CMS\\\\)?(\\w+)/i', $text, $regs);
                foreach ($regs[2] as $key => $val) {
                    $text = str_replace($regs[0][$key], "'" . addslashes($this->view->_($val)) . "'", $text);
                }*/
                return $text;
            case 'stdMaterialInterfaceFile':
                return $this->resourcesDir . '/material_interface.php';
                break;
            case 'stdMaterialInterface':
                $text = file_get_contents($this->stdMaterialInterfaceFile);
                return $text;
                break;
            case 'stdMaterialViewFile':
                return $this->resourcesDir . '/material.tmp.php';
                break;
            case 'stdMaterialView':
                $text = file_get_contents($this->stdMaterialViewFile);
                return $text;
                break;
            case 'stdMenuInterfaceFile':
                return $this->resourcesDir . '/menu_interface.php';
                break;
            case 'stdMenuInterface':
                $text = file_get_contents($this->stdMenuInterfaceFile);
                return $text;
                break;
            case 'stdMenuViewFile':
                return $this->resourcesDir . '/menu.tmp.php';
                break;
            case 'stdMenuView':
                $text = file_get_contents($this->stdMenuViewFile);
                return $text;
                break;
            case 'stdFormInterfaceFile':
                return $this->resourcesDir . '/form_interface.php';
                break;
            case 'stdFormInterface':
                $text = file_get_contents($this->stdFormInterfaceFile);
                return $text;
                break;
            case 'stdFormViewFile':
                return $this->resourcesDir . '/form.tmp.php';
                break;
            case 'stdFormView':
                $text = file_get_contents($this->stdFormViewFile);
                return $text;
                break;
            case 'stdSearchInterfaceFile':
                return $this->resourcesDir . '/search_interface.php';
                break;
            case 'stdSearchInterface':
                $text = file_get_contents($this->stdSearchInterfaceFile);
                return $text;
                break;
            case 'stdSearchViewFile':
                return $this->resourcesDir . '/search.tmp.php';
                break;
            case 'stdSearchView':
                $text = file_get_contents($this->stdSearchViewFile);
                return $text;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }
    
    
    public function init()
    {
        $_SESSION['KCFINDER']['uploadURL'] = '/files/cms/common/';
        $_SESSION['KCFINDER']['disabled'] = false;
        parent::init();
        Block_Type::registerType('RAAS\\CMS\\Block_HTML', 'RAAS\\CMS\\ViewBlockHTML', 'RAAS\\CMS\\EditBlockHTMLForm');
        Block_Type::registerType('RAAS\\CMS\\Block_PHP', 'RAAS\\CMS\\ViewBlockPHP', 'RAAS\\CMS\\EditBlockPHPForm');
        Block_Type::registerType('RAAS\\CMS\\Block_Material', 'RAAS\\CMS\\ViewBlockMaterial', 'RAAS\\CMS\\EditBlockMaterialForm');
        Block_Type::registerType('RAAS\\CMS\\Block_Menu', 'RAAS\\CMS\\ViewBlockMenu', 'RAAS\\CMS\\EditBlockMenuForm');
        Block_Type::registerType('RAAS\\CMS\\Block_Form', 'RAAS\\CMS\\ViewBlockForm', 'RAAS\\CMS\\EditBlockFormForm');
        Block_Type::registerType('RAAS\\CMS\\Block_Search', 'RAAS\\CMS\\ViewBlockSearch', 'RAAS\\CMS\\EditBlockSearchForm');
        foreach ($this->modules as $module) {
            if (method_exists($module, 'registerBlockTypes')) {
                $module->registerBlockTypes();
            }
        }
    }
    
    
    public function show_page()
    {
        $Parent = new Page((isset($this->controller->nav['id']) ? (int)$this->controller->nav['id'] : 0));
        $columns = array_filter($Parent->fields, function($x) { return $x->show_in_table; });
        $Set = $Parent->children;
        if (isset($this->controller->nav['id'])) {
            $f = function($a, $b) { return $a->priority - $b->priority; };
            $sort = 'priority';
        } else {
            $f = function($a, $b) { return strcasecmp($a->urn, $b->urn); };
            $sort = 'urn';
            if (isset($this->controller->nav['sort'])) {
                if (isset($columns[$this->controller->nav['sort']]) && ($row = $columns[$this->controller->nav['sort']])) {
                    $sort = $row->urn;
                    $f = function($a, $b) use ($sort) { return strcasecmp($a->fields[$sort]->doRich(), $b->fields[$sort]->doRich()); };
                } else {
                    switch ($this->controller->nav['sort']) {
                        case 'name':
                            $sort = 'name';
                            $f = function($a, $b) { return strcasecmp($a->name, $b->name); };
                            break;
                    }
                }
            }
        }
        if (!isset($this->controller->nav['id']) && isset($this->controller->nav['order']) && ($this->controller->nav['order'] == 'desc')) {
            $order = 'desc';
            usort($Set, function($b, $a) use ($f) { return $f($a, $b); });
        } else {
            $order = 'asc';
            usort($Set, $f);
        }
        return array('Set' => $Set, 'sort' => $sort, 'order' => $order, 'columns' => $columns);
    }
    
    
    public function dev_dictionaries()
    {
        $Parent = new Dictionary(isset($this->controller->nav['id']) ? (int)$this->controller->nav['id'] : 0);
        $SQL_query = "SELECT SQL_CALC_FOUND_ROWS * FROM " . Dictionary::_tablename() . " WHERE pid = " . (int)$Parent->id;
        if ($Parent->orderby) {
            $sort = $Parent->orderby;
        } else {
            $sort = 'priority';
        }
        if ($Parent->orderby && isset($this->controller->nav['order']) && ($this->controller->nav['order'] == 'desc')) {
            $order = 'desc';
        } else {
            $order = 'asc';
        }
        $SQL_query .= " ORDER BY " . $sort . " " . strtoupper($order);
        $Pages = new \SOME\Pages(isset($this->controller->nav['page']) ? $this->controller->nav['page'] : 1, $this->registryGet('rowsPerPage'));
        $Set = Dictionary::getSQLSet($SQL_query, $Pages);
        return array('Set' => $Set, 'Pages' => $Pages, 'sort' => $sort, 'order' => $order);
    }
    
    
    public function dev_dictionaries_loadFile(Dictionary $Item, $file)
    {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $text = file_get_contents($file['tmp_name']);
        if (in_array($ext, array('csv', 'ini', 'sql')) && !mb_check_encoding($text)) {
            switch ($this->view->language) {
                default:
                    $text = iconv('Windows-1251', mb_internal_encoding(), $text);
                    break;
            }
        }
        switch ($ext) {
            case 'csv':
                $Item->parseCSV($text);
                break;
            case 'ini':
                $Item->parseINI($text);
                break;
            case 'xml':
                $Item->parseXML($text);
                break;
            case 'sql':
                $Item->parseSQL($text);
                break;
        }
    }
    
    
    public function dev_templates()
    {
        return Template::getSet();
    }
    
    
    public function material_types()
    {
        return Material_Type::getSet();
    }
    
    
    public function forms()
    {
        return Form::getSet();
    }
    
    
    public function dev_pages_fields()
    {
        return Page_Field::getSet(array('where' => "NOT pid"));
    }
    
    
    public function getDictionaries()
    {
        return Dictionary::getSet(array('where' => "NOT pid"));
    }
    
    
    public function getPageMaterials(Page $Page, Material_Type $MType, $search_string = null, $sort = 'post_date', $order = 'asc', $page = 1)
    {
        $columns = array_filter($MType->fields, function($x) { return $x->show_in_table; });

        $SQL_query = "SELECT SQL_CALC_FOUND_ROWS tM.* FROM " . Material::_tablename() . " AS tM ";
        if (!$MType->global_type) {
            $SQL_query .= " LEFT JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id ";
        }
        $SQL_query .= " WHERE tM.pid = " . (int)$MType->id;
        if (!$MType->global_type) {
            $SQL_query .= " AND (tMPA.pid = " . (int)$this->controller->id . " OR tMPA.pid IS NULL) ";
        }
        if ($search_string) {
            $SQL_query .= " AND (
                                    name LIKE '%" . $this->SQL->real_escape_string($search_string) . "%' 
                                 OR urn LIKE '%" . $this->SQL->real_escape_string($search_string) . "%' 
                            )";
        }
        $Pages = new \SOME\Pages($page, $this->registryGet('rowsPerPage'));
        if (isset($sort, $columns[$sort]) && ($row = $columns[$sort])) {
            $_sort = $row->urn;
            $f = function($a, $b) use ($_sort) { return strcasecmp($a->fields[$_sort]->doRich(), $b->fields[$_sort]->doRich()); };
            $Set = Material::getSQLSet($SQL_query);
            if (isset($order) && ($order == 'desc')) {
                $_order = 'desc';
                usort($Set, function($b, $a) use ($f) { return $f($a, $b); });
            } else {
                $_order = 'asc';
                usort($Set, $f);
            }
            $Set = \SOME\SOME::getArraySet($Set, $Pages);
        } else {
            switch ($sort) {
                case 'name': case 'urn': case 'modify_date':
                    $_sort = $sort;
                    break;
                default:
                    $_sort = 'post_date';
                    break;
            }
            if (isset($order) && ($order == 'desc')) {
                $_order = 'desc';
            } elseif (!isset($order) && in_array($sort, array('post_date', 'modify_date'))) {
                $_order = 'desc';
            } else {
                $_order = 'asc';
            }

            $SQL_query .= " ORDER BY " . $_sort . " " . strtoupper($_order);
            $Set = Material::getSQLSet($SQL_query, $Pages);
        }
        return array('Set' => $Set, 'Pages' => $Pages, 'sort' => $_sort, 'order' => $_order);
    }
    
    public function feedback()
    {
        $Parent = new Form(isset($this->controller->nav['id']) ? (int)$this->controller->nav['id'] : 0);
        $col_where = "classname = 'RAAS\\\\CMS\\\\Form' AND show_in_table";        
        $SQL_query = "SELECT SQL_CALC_FOUND_ROWS tF.* 
                        FROM " . Feedback::_tablename() .  " AS tF
                   LEFT JOIN " . Field::_tablename() .  " AS tFi ON tFi.pid = tF.pid AND tFi.classname = 'RAAS\\\\CMS\\\\Form'
                   LEFT JOIN " . Feedback::_dbprefix() . "cms_data AS tD ON tD.pid = tF.id AND tD.fid = tFi.id
                       WHERE 1 ";
        if ($Parent->id) {
            $SQL_query .= " AND tF.pid = " . (int)$Parent->id;
            $col_where .= " AND pid = " . (int)$Parent->id;
        }
        if (isset($this->controller->nav['search_string']) && $this->controller->nav['search_string']) {
            $SQL_query .= " AND tD.value LIKE '%" . $this->SQL->escape_like($this->controller->nav['search_string']) . "%' ";
        }
        
        $SQL_query .= " GROUP BY tF.id ORDER BY tF.post_date DESC ";
        $Pages = new \SOME\Pages(isset($this->controller->nav['page']) ? $this->controller->nav['page'] : 1, $this->registryGet('rowsPerPage'));
        $Set = Feedback::getSQLSet($SQL_query, $Pages);
        $columns = Form_Field::getSet(array('where' => $col_where));
        return array('Set' => $Set, 'Pages' => $Pages, 'Parent' => $Parent, 'columns' => $columns);
    }


    public function cleanCache()
    {
        if (is_dir($this->cacheDir)) {
            $dir = \SOME\File::scandir($this->cacheDir);
            foreach ($dir as $f) {
                if (is_file($this->cacheDir . '/' . $f) && preg_match('/^' . preg_quote($this->cachePrefix) . '(.*?)\\.(.*?)\\.php$/i', $f)) {
                    unlink($this->cacheDir . '/' . $f);
                }
            }
        }
    }


    public function copyItem(\SOME\SOME $Item)
    {
        $classname = get_class($Item);
        $Item2 = clone($Item);
        do {
            if (preg_match('/\\d+$/umi', trim($Item2->name), $regs)) {
                $i = (int)$regs[0] + 1;
                $Item2->name = preg_replace('/\\d+$/umi', $i, trim($Item2->name));
            } else {
                $i = 2;
                $Item2->name .= ' ' . $i;
            }
        } while ((int)$this->SQL->getvalue(array("SELECT COUNT(*) FROM " . $classname::_tablename() . " WHERE name = ?", $Item2->name)));
        if (preg_match('/\\d+$/umi', trim($Item2->urn), $regs)) {
            $Item2->urn = preg_replace('/\\d+$/umi', $i, trim($Item2->urn));
        } else {
            $Item2->urn .= '_' . $i;
        }
        while ((int)Package::i()->SQL->getvalue(array("SELECT COUNT(*) FROM " . $classname::_tablename() . " WHERE urn = ? AND id != ?", $Item2->urn, (int)$Item2->id))) {
            $Item2->urn = '_' . $Item2->urn . '_';
        }
        return $Item2;
    }
}