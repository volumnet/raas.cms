<?php
namespace RAAS\CMS;
use \RAAS\IContext;

class Updater extends \RAAS\Updater
{
    public function preInstall()
    {
        $this->oldUpdates();
        $this->update20140202();
        $this->update20140202_2();
        $this->update20140619();
        $this->update20140706();
        $this->update20140717();
        $this->update20140910();
        $this->update20141104();
        $this->update20141222();
        $this->update20150125();
        $this->update20150301();
        $this->update20150504();
        $this->update20150610();
        $this->update20150617();
        $this->update20151129();
    }


    public function postInstall()
    {
        $this->update20141029();
        $this->update20141103();
        $this->update20141109();
        $w = new Webmaster();
        $w->checkStdSnippets();
        if (!$this->SQL->getvalue("SELECT COUNT(*) FROM " . \SOME\SOME::_dbprefix() . "cms_pages")) {
            $w->createSite();
        }
    }


    protected function oldUpdates()
    {
        $tables = $this->SQL->getcol("SHOW TABLES");

        // Меняем "виджеты" на "сниппеты"
        if (!in_array(\SOME\SOME::_dbprefix() . "cms_snippets", $tables) && in_array(\SOME\SOME::_dbprefix() . "cms_widgets", $tables)) {
            $SQL_query = "RENAME TABLE " . \SOME\SOME::_dbprefix() . "cms_widgets TO " . \SOME\SOME::_dbprefix() . "cms_snippets";
            $this->SQL->query($SQL_query);
            $SQL_query = "ALTER TABLE  " . \SOME\SOME::_dbprefix() . "cms_snippets COMMENT =  'Snippets'";
            $this->SQL->query($SQL_query);

            $SQL_query = "RENAME TABLE " . \SOME\SOME::_dbprefix() . "cms_widget_folders TO " . \SOME\SOME::_dbprefix() . "cms_snippet_folders";
            $this->SQL->query($SQL_query);
            $SQL_query = "ALTER TABLE  " . \SOME\SOME::_dbprefix() . "cms_snippet_folders COMMENT =  'Snippet folders'";
            $this->SQL->query($SQL_query);
        }

        // Добавляем возможность формам генерировать материалы
        if (in_array(\SOME\SOME::_dbprefix() . "cms_forms", $tables)) {
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_forms"));
            if (!in_array('material_type', $columns)) {
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_forms ADD material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type' AFTER name";
                $this->SQL->query($SQL_query);
            }
            if (!in_array('create_feedback', $columns)) {
                $this->SQL->query("ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_forms ADD create_feedback INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Create feedback' AFTER material_type");
            }
        }

        // Добавляем кэширование к страницам
        if (in_array(\SOME\SOME::_dbprefix() . "cms_pages", $tables)) {
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_pages"));
            if (!in_array('cache', $columns)) {
                $this->SQL->query("ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_pages ADD cache TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cache page'");
            }
            if (!in_array('inherit_cache', $columns)) {
                $this->SQL->query("ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_pages ADD inherit_cache TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit cache page'");
            }
        }

        // Добавляем возможность показывать настраиваемые поля в таблицах в админке
        if (in_array(\SOME\SOME::_dbprefix() . "cms_fields", $tables)) {
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_fields"));
            if (!in_array('show_in_table', $columns)) {
                $this->SQL->query("ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_fields ADD show_in_table TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Show as table column' AFTER placeholder");
            }
        }

        // Создаем пользовательские папки для CKEditor'а и .htaccess к ним
        foreach (array('file', 'image', 'flash', 'media') as $key) {
            if (!is_dir($this->filesDir . '/' . $key)) {
                @mkdir($this->filesDir . '/' . $key, 0777, true);
            }
        }
        if (!is_file($this->filesDir . '/.htaccess')) {
            $text = "Options -ExecCgi -Includes -Indexes\n"
                  . "RemoveHandler .phtml .php .php3 .php4 .php5 .php6 .phps .cgi .exe .pl .asp .aspx .shtml .shtm .fcgi .fpl .jsp .htm .html .wml\n"
                  . "AddType \"text/html\" .php .cgi .pl .fcgi .fpl .phtml .shtml .php2 .php3 .php4 .php5 .asp .jsp\n"
                  . "RemoveType php\n"
                  . "\n"
                  . "<IfModule mod_php4.c>\n"
                  . "php_flag engine 0\n"
                  . "</IfModule>\n"
                  . "\n"
                  . "<IfModule mod_php5.c>\n"
                  . "php_flag engine 0\n"
                  . "</IfModule>";
            file_put_contents($this->filesDir . '/.htaccess', $text);
        }

        // Разделяем блоки
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks", $tables)) {
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_blocks"));
            $tables = $this->SQL->getcol("SHOW TABLES");
            if (in_array('description', $columns) && !in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_php', $tables)) {
                $this->SQL->query("UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks SET description = REPLACE(description, '\\\\/files\\\\/common', '\\\\/')");
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_html', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_html (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        description MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Text',

                        PRIMARY KEY (id)
                    ) COMMENT 'HTML blocks';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_php', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_php (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        description MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Code',
                        widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Snippet ID#',

                        PRIMARY KEY (id),
                        KEY (widget)
                    ) COMMENT 'PHP blocks';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_material', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_material (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
                        std_interface TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Standard interface',
                        interface MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Interface code',
                        widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Snippet ID#',
                        description MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Snippet code',
                        pages_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
                        rows_per_page TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Rows per page',
                        sort_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Sorting var name',
                        order_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Order var name',
                        sort_field_default VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field for sorting by default',
                        sort_order_default VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Default order',
                        
                        PRIMARY KEY (id),
                        KEY (material_type),
                        KEY (widget)
                    ) COMMENT 'Material blocks';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_material_filter', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_material_filter (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        var VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Variable',
                        relation ENUM('=', 'LIKE', 'CONTAINED', 'FULLTEXT', '<=', '>=') NOT NULL DEFAULT '=' COMMENT 'Relation',
                        field VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field',
                        priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
                        
                        KEY (id),
                        INDEX (priority)
                    ) COMMENT 'Material blocks filtering';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_material_sort', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_material_sort (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        var VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Variable',
                        field VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field',
                        relation ENUM('asc!', 'desc!', 'asc', 'desc') NOT NULL DEFAULT 'asc!' COMMENT 'Relation',
                        priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
                        
                        KEY (id),
                        INDEX (priority)
                    ) COMMENT 'Material blocks sorting';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_form', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_form (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        form INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Form ID#',
                        std_interface TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Standard interface',
                        interface MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Interface code',
                        widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Snippet ID#',
                        description MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Snippet code',
                        
                        PRIMARY KEY (id),
                        KEY (form),
                        KEY (widget)
                    ) COMMENT 'Form blocks';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_menu', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_menu (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        menu INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Menu ID#',
                        full_menu TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Full menu',
                        std_interface TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Standard interface',
                        interface MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Interface code',
                        widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Snippet ID#',
                        description MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Snippet code',
                        
                        PRIMARY KEY (id),
                        KEY (menu),
                        KEY (widget)
                    ) COMMENT 'Menu blocks';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_search', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_search (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        search_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Search var name',
                        min_length TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Minimal query length',
                        pages_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
                        rows_per_page TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Rows per page',
                        std_interface TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Standard interface',
                        interface MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Interface code',
                        widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Snippet ID#',
                        description MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Snippet code',
                        
                        PRIMARY KEY (id),
                        KEY (widget)
                    ) COMMENT 'Search blocks';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_search_material_types_assoc', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_search_material_types_assoc (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',

                        PRIMARY KEY (id, material_type),
                        KEY (id),
                        KEY (material_type)
                    ) COMMENT 'Search blocks material types';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_search_languages_assoc', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_search_languages_assoc (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        language VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Language',

                        PRIMARY KEY (id, language),
                        KEY (id),
                        KEY (language)
                    ) COMMENT 'Search blocks languages';";
                    $this->SQL->query($SQL_query);
                }
                if (!in_array(\SOME\SOME::_dbprefix() . 'cms_blocks_search_pages_assoc', $tables)) {
                    $SQL_query = "CREATE TABLE IF NOT EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_search_pages_assoc (
                        id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
                        page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',

                        PRIMARY KEY (id, page_id),
                        KEY (id),
                        KEY (page_id)
                    ) COMMENT 'Search blocks pages';";
                    $this->SQL->query($SQL_query);
                }
                $Set = $this->SQL->get("SELECT * FROM " . \SOME\SOME::_dbprefix() . "cms_blocks");
                foreach ($Set as $arr) {
                    $temp = @json_decode($arr['description'], true);
                    switch ($arr['block_type']) {
                        case 'html':
                            $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_html WHERE id = " . (int)$arr['id']);
                            $this->SQL->add(\SOME\SOME::_dbprefix() . 'cms_blocks_html', array('id' => (int)$arr['id'], 'description' => (string)$arr['description']));
                            break;
                        case 'php':
                            $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_php WHERE id = " . (int)$arr['id']);
                            $this->SQL->add(
                                \SOME\SOME::_dbprefix() . 'cms_blocks_php', array('id' => (int)$arr['id'], 'description' => (string)$temp['description'], 'widget' => (int)$temp['widget'])
                            );
                            break;
                        case 'material':
                            $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_material WHERE id = " . (int)$arr['id']);
                            $this->SQL->add(
                                \SOME\SOME::_dbprefix() . 'cms_blocks_material', 
                                array(
                                    'id' => (int)$arr['id'], 
                                    'material_type' => (int)$temp['material_type'],
                                    'std_interface' => (int)$temp['std_interface'],
                                    'interface' => (string)$temp['interface'],
                                    'widget' => (int)$temp['widget'],
                                    'description' => (string)$temp['description'],
                                    'pages_var_name' => (string)$temp['pages_var_name'],
                                    'rows_per_page' => (int)$temp['rows_per_page'],
                                    'sort_var_name' => (string)$temp['sort_var_name'],
                                    'order_var_name' => (string)$temp['order_var_name'],
                                    'sort_field_default' => (string)$temp['sort_field_default'],
                                    'sort_order_default' => (string)$temp['sort_order_default'],
                                )
                            );
                            $arr2 = array();
                            for ($i = 0; $i < count($temp['filter']); $i++) {
                                if ($row = $temp['filter'][$i]) {
                                    $arr2[] = array(
                                        'id' => (int)$arr['id'], 
                                        'var' => (string)$row['var'],
                                        'relation' => (string)$row['relation'],
                                        'field' => (string)$row['field'],
                                        'priority' => ($i + 1),
                                    );
                                }
                            }
                            if ($arr2) {
                                $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_material_filter WHERE id = " . (int)$arr['id']);
                                $this->SQL->add(\SOME\SOME::_dbprefix() . 'cms_blocks_material_filter', $arr2);
                            }
                            $arr2 = array();
                            for ($i = 0; $i < count($temp['sort']); $i++) {
                                if ($row = $temp['sort'][$i]) {
                                    $arr2[] = array(
                                        'id' => (int)$arr['id'], 
                                        'var' => (string)$row['var'],
                                        'field' => (string)$row['field'],
                                        'relation' => (string)$row['relation'],
                                        'priority' => ($i + 1),
                                    );
                                }
                            }
                            if ($arr2) {
                                $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_material_sort WHERE id = " . (int)$arr['id']);
                                $this->SQL->add(\SOME\SOME::_dbprefix() . 'cms_blocks_material_sort', $arr2);
                            }
                            break;
                        case 'form':
                            $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_form WHERE id = " . (int)$arr['id']);
                            $this->SQL->add(
                                \SOME\SOME::_dbprefix() . 'cms_blocks_form', 
                                array(
                                    'id' => (int)$arr['id'], 
                                    'form' => (int)$temp['form'],
                                    'std_interface' => (int)$temp['std_interface'],
                                    'interface' => (string)$temp['interface'],
                                    'widget' => (int)$temp['widget'],
                                    'description' => (string)$temp['description'],
                                )
                            );
                            break;
                        case 'menu':
                            $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_menu WHERE id = " . (int)$arr['id']);
                            $this->SQL->add(
                                \SOME\SOME::_dbprefix() . 'cms_blocks_menu', 
                                array(
                                    'id' => (int)$arr['id'], 
                                    'menu' => (int)$temp['menu'],
                                    'full_menu' => (int)$temp['full_menu'],
                                    'std_interface' => (int)$temp['std_interface'],
                                    'interface' => (string)$temp['interface'],
                                    'widget' => (int)$temp['widget'],
                                    'description' => (string)$temp['description'],
                                )
                            );
                            break;
                        case 'search':
                            $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_search WHERE id = " . (int)$arr['id']);
                            $this->SQL->add(
                                \SOME\SOME::_dbprefix() . 'cms_blocks_search', 
                                array(
                                    'id' => (int)$arr['id'], 
                                    'search_var_name' => (string)$temp['search_var_name'],
                                    'min_length' => (int)$temp['min_length'],
                                    'pages_var_name' => (string)$temp['pages_var_name'],
                                    'rows_per_page' => (int)$temp['rows_per_page'],
                                    'std_interface' => (int)$temp['std_interface'],
                                    'interface' => (string)$temp['interface'],
                                    'widget' => (int)$temp['widget'],
                                    'description' => (string)$temp['description'],
                                )
                            );
                            $arr2 = array();
                            for ($i = 0; $i < count($temp['material_types']); $i++) {
                                if ($val = $temp['material_types'][$i]) {
                                    $arr2[] = array('id' => (int)$arr['id'], 'material_type' => (int)$val, );
                                }
                            }
                            if ($arr2) {
                                $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_search_material_types_assoc WHERE id = " . (int)$arr['id']);
                                $this->SQL->add(\SOME\SOME::_dbprefix() . 'cms_blocks_search_material_types_assoc', $arr2);
                            }
                            $arr2 = array();
                            for ($i = 0; $i < count($temp['languages']); $i++) {
                                if ($val = $temp['languages'][$i]) {
                                    $arr2[] = array('id' => (int)$arr['id'], 'language' => (string)$val, );
                                }
                            }
                            if ($arr2) {
                                $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_search_languages_assoc WHERE id = " . (int)$arr['id']);
                                $this->SQL->add(\SOME\SOME::_dbprefix() . 'cms_blocks_search_languages_assoc', $arr2);
                            }
                            $arr2 = array();
                            for ($i = 0; $i < count($temp['pages']); $i++) {
                                if ($val = $temp['pages'][$i]) {
                                    $arr2[] = array('id' => (int)$arr['id'], 'page_id' => (int)$val, );
                                }
                            }
                            if ($arr2) {
                                $this->SQL->query("DELETE FROM " . \SOME\SOME::_dbprefix() . "cms_blocks_search_pages_assoc WHERE id = " . (int)$arr['id']);
                                $this->SQL->add(\SOME\SOME::_dbprefix() . 'cms_blocks_search_pages_assoc', $arr2);
                            }
                            break;
                    }
                }
                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks
                                 SET block_type = CASE block_type 
                                WHEN 'html' THEN 'RAAS\\\\CMS\\\\Block_HTML' 
                                WHEN 'php' THEN 'RAAS\\\\CMS\\\\Block_PHP'
                                WHEN 'material' THEN 'RAAS\\\\CMS\\\\Block_Material'
                                WHEN 'form' THEN 'RAAS\\\\CMS\\\\Block_Form'
                                WHEN 'menu' THEN 'RAAS\\\\CMS\\\\Block_Menu'
                                WHEN 'search' THEN 'RAAS\\\\CMS\\\\Block_Search'
                                 END 
                               WHERE 1;";
                $this->SQL->query($SQL_query);
                $this->SQL->query("ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks DROP description");
            }
        }
    }


    protected function update20140202()
    {
        // Создаем блокированные сниппеты
        if (in_array(\SOME\SOME::_dbprefix() . "cms_snippets", $this->tables)) {
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_snippets"));
            if (!in_array('locked', $columns)) {
                $this->SQL->query("ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_snippets ADD locked TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Locked'");
            }
        }

        // Создаем блокированные папки сниппетов и URN у папок сниппетов
        if (in_array(\SOME\SOME::_dbprefix() . "cms_snippet_folders", $this->tables)) {
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_snippet_folders"));
            if (!in_array('locked', $columns)) {
                $this->SQL->query("ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_snippet_folders ADD locked TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Locked'");
            }
        
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_snippet_folders"));
            if (!in_array('urn', $columns)) {
                $this->SQL->query("ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_snippet_folders ADD urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN' AFTER id");
            }
        }
        \SOME\SOME::init();
    }


    protected function update20140202_2()
    {
        // Обновляем привязку к сниппетам у блоков
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks", $this->tables)) {
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_blocks"));
            if (!in_array('widget_id', $columns)) {
                $SQL_query .= "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks 
                                       ADD interface_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Interface ID#',
                                       ADD interface MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Interface code',
                                       ADD widget_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Widget ID#',
                                       ADD widget MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Widget code' ";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_html AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = 0,
                                     tB.interface =  '',
                                     tB.widget_id = 0,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "TRUNCATE TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_html";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_php AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = 0,
                                     tB.interface =  '',
                                     tB.widget_id = tB2.widget,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "DROP TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_php";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_material AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = IF(tB2.std_interface, (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__raas_material_interface'), 0),
                                     tB.interface = tB2.interface,
                                     tB.widget_id = tB2.widget,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_material DROP std_interface, DROP interface, DROP widget, DROP description";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_menu AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = IF(tB2.std_interface, (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__raas_menu_interface'), 0),
                                     tB.interface = tB2.interface,
                                     tB.widget_id = tB2.widget,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_menu DROP std_interface, DROP interface, DROP widget, DROP description";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_form AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = IF(tB2.std_interface, (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__raas_form_interface'), 0),
                                     tB.interface = tB2.interface,
                                     tB.widget_id = tB2.widget,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_form DROP std_interface, DROP interface, DROP widget, DROP description";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_search AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = IF(tB2.std_interface, (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__raas_search_interface'), 0),
                                     tB.interface = tB2.interface,
                                     tB.widget_id = tB2.widget,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_search DROP std_interface, DROP interface, DROP widget, DROP description";
                $this->SQL->query($SQL_query);
            }
        }

        if (in_array(\SOME\SOME::_dbprefix() . "cms_forms", $this->tables)) {
            $columns = array_map(function($x) { return $x['Field']; }, $this->SQL->get("SHOW FIELDS FROM " . \SOME\SOME::_dbprefix() . "cms_forms"));
            if (!in_array('interface_id', $columns)) {
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_forms ADD interface_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Interface ID#' AFTER std_template";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_forms SET interface_id = (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__raas_form_notify') 
                               WHERE std_template";
                $this->SQL->query($SQL_query);

                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_forms DROP std_template";
                $this->SQL->query($SQL_query);
            }
        }
    }


    protected function update20140619()
    {
        // Избавляемся от внутренних сниппетов
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks", $this->tables) && in_array(\SOME\SOME::_dbprefix() . "cms_snippets", $this->tables) && in_array(\SOME\SOME::_dbprefix() . "cms_blocks_html", $this->tables)) {
            if (in_array('interface', $this->columns(\SOME\SOME::_dbprefix() . "cms_blocks"))) {
                $SQL_query = "SELECT * FROM " . \SOME\SOME::_dbprefix() . "cms_blocks WHERE interface_id = 0 AND interface != ''";
                $SQL_result = $this->SQL->get($SQL_query);
                foreach ($SQL_result as $row) {
                    $urn = \SOME\Text::beautify($row['name']);
                    while ((int)$this->SQL->getvalue(array("SELECT COUNT(*) FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = ?", $urn))) {
                        $urn = '_' . $urn . '_';
                    }
                    $id = (int)$this->SQL->add(
                        \SOME\SOME::_dbprefix() . "cms_snippets",
                        array('pid' => (int)Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => $urn, 'name' => $row['name'], 'description' => $row['interface'], 'locked' => 0)
                    );
                    $this->SQL->update(\SOME\SOME::_dbprefix() . "cms_blocks", "id = " . (int)$row['id'], array('interface_id' => $id, 'interface' => ''));
                }

                $SQL_query = "SELECT * FROM " . \SOME\SOME::_dbprefix() . "cms_blocks WHERE widget_id = 0 AND widget != '' AND block_type != 'RAAS\\\\CMS\\\\Block_HTML'";
                $SQL_result = $this->SQL->get($SQL_query);
                foreach ($SQL_result as $row) {
                    $urn = \SOME\Text::beautify($row['name']);
                    while ((int)$this->SQL->getvalue(array("SELECT COUNT(*) FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = ?", $urn))) {
                        $urn = '_' . $urn . '_';
                    }
                    $id = (int)$this->SQL->add(
                        \SOME\SOME::_dbprefix() . "cms_snippets",
                        array('pid' => (int)Snippet_Folder::importByURN('__raas_views')->id, 'urn' => $urn, 'name' => $row['name'], 'description' => $row['widget'], 'locked' => 0)
                    );
                    $this->SQL->update(\SOME\SOME::_dbprefix() . "cms_blocks", "id = " . (int)$row['id'], array('widget_id' => $id, 'widget' => ''));
                }

                $SQL_query = "INSERT INTO " . \SOME\SOME::_dbprefix() . "cms_blocks_html (id, description) 
                              SELECT id, widget FROM " . \SOME\SOME::_dbprefix() . "cms_blocks WHERE block_type = 'RAAS\\\\CMS\\\\Block_HTML'";
                $this->SQL->query($SQL_query);

                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks DROP interface, DROP widget";
                $this->SQL->query($SQL_query);
            }
        }

        // Также в формах
        if (in_array(\SOME\SOME::_dbprefix() . "cms_forms", $this->tables) && in_array(\SOME\SOME::_dbprefix() . "cms_snippets", $this->tables)) {
            if (in_array('description', $this->columns(\SOME\SOME::_dbprefix() . "cms_forms"))) {
                $SQL_query = "SELECT * FROM " . \SOME\SOME::_dbprefix() . "cms_forms WHERE interface_id = 0 AND description != ''";
                $SQL_result = $this->SQL->get($SQL_query);
                foreach ($SQL_result as $row) {
                    $urn = \SOME\Text::beautify($row['name']);
                    while ((int)$this->SQL->getvalue(array("SELECT COUNT(*) FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = ?", $urn))) {
                        $urn = '_' . $urn . '_';
                    }
                    $id = (int)$this->SQL->add(
                        \SOME\SOME::_dbprefix() . "cms_snippets",
                        array('pid' => (int)Snippet_Folder::importByURN('__raas_interfaces')->id, 'urn' => $urn, 'name' => $row['name'], 'description' => $row['description'], 'locked' => 0)
                    );
                    $this->SQL->update(\SOME\SOME::_dbprefix() . "cms_forms", "id = " . (int)$row['id'], array('interface_id' => $id, 'description' => ''));
                }

                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_forms DROP description";
                $this->SQL->query($SQL_query);
            }
        }
    }


    protected function update20140706()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks_html", $this->tables) && !in_array('wysiwyg', $this->columns(\SOME\SOME::_dbprefix() . "cms_blocks_html"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_html ADD wysiwyg TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'WYSIWYG editor on'";
            $this->SQL->query($SQL_query);
        }
    }


    protected function update20140717()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_users", $this->tables) && !in_array('email', $this->columns(\SOME\SOME::_dbprefix() . "cms_users"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_users ADD email VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'E-mail', 
                          ADD INDEX (email), 
                          ADD post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Registration date',
                          ADD INDEX (post_date),
                          ADD vis TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Active',
                          ADD INDEX (vis),
                          ADD lang varchar(255) NOT NULL DEFAULT 'ru' COMMENT 'Language'";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_users", $this->tables) && !in_array('lang', $this->columns(\SOME\SOME::_dbprefix() . "cms_users"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_users ADD lang varchar(255) NOT NULL DEFAULT 'ru' COMMENT 'Language'";
            $this->SQL->query($SQL_query);
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "registry CHANGE `value` `value` TEXT NULL DEFAULT NULL COMMENT 'Value';";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_users", $this->tables) && !in_array('new', $this->columns(\SOME\SOME::_dbprefix() . "cms_users"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_users 
                            ADD new TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'New',
                            ADD activated TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Activated',
                            ADD INDEX(new),
                            ADD INDEX(activated)";
            $this->SQL->query($SQL_query);
        }
    }


    protected function update20140910()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_fields", $this->tables) && !in_array('defval', $this->columns(\SOME\SOME::_dbprefix() . "cms_fields"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_fields
                            ADD defval TEXT NULL DEFAULT NULL COMMENT 'Default value' AFTER source";
            $this->SQL->query($SQL_query);

            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_materials
                            ADD priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority'";
            $this->SQL->query($SQL_query);
        }
    }


    protected function update20141029()
    {
        $SQL_query = "SELECT COUNT(*)
                        FROM " . \SOME\SOME::_dbprefix() . "cms_snippets AS tS
                   LEFT JOIN " . \SOME\SOME::_dbprefix() . "cms_snippet_folders AS tSF ON tSF.id = tS.pid
                       WHERE NOT tS.locked AND tSF.urn != '__raas_interfaces' AND (tS.description LIKE '%href=\"%?id=%->id%\"%' OR tS.description LIKE '%<loc>%?id=%</loc>')";
        if ((int)$this->SQL->getvalue($SQL_query)) {
            $rep = array();
            $rep['href="<' . '?php echo $Page->url?' . '>?id=<' . '?php echo (int)$row->id?' . '>"'] = 'href="<' . '?php echo $Page->url . $row->urn?' . '>/"';
            $rep['?id=<' . '?php echo (int)$row->id?' . '>"'] = '<' . '?php echo $row->urn?' . '>/"';
            $rep['<loc>http://\' . htmlspecialchars($_SERVER[\'HTTP_HOST\'] . $row->url) . \'?id=\' . (int)$row2->id . \'</loc>'] = '<loc>http://\' . htmlspecialchars($_SERVER[\'HTTP_HOST\'] . $row->url . $row2->urn) . \'/</loc>';
            foreach ($rep as $key => $val) {
                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_snippets AS tS
                           LEFT JOIN " . \SOME\SOME::_dbprefix() . "cms_snippet_folders AS tSF ON tSF.id = tS.pid
                                 SET tS.description = REPLACE(tS.description, ?, ?) 
                               WHERE NOT tS.locked AND tSF.urn != '__raas_interfaces'";
                $this->SQL->query(array($SQL_query, $key, $val));
            }
        }
    }


    protected function update20141103()
    {
        if (!in_array('page_id', $this->columns(\SOME\SOME::_dbprefix() . "cms_materials"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_materials ADD page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Default page ID#' AFTER pid";
            $this->SQL->query($SQL_query);
            $rep = array();
            $rep['href="<' . '?php echo $Page->url . $row->urn?' . '>/"'] = 'href="<' . '?php echo $row->url?' . '>"';
            $rep['$text .= \'<url><loc>http://\' . htmlspecialchars($_SERVER[\'HTTP_HOST\'] . $row->url . $row2->urn) . \'/</loc></url>\';'] = 'if ($row2->parent->id == $row->id) {' . "\n"
                                                                                                                                             . '                    $text .= \'<url><loc>http://\' . htmlspecialchars($_SERVER[\'HTTP_HOST\'] . $row2->url) . \'</loc></url>\';' . "\n"
                                                                                                                                             . '                }';
            foreach ($rep as $key => $val) {
                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_snippets AS tS
                           LEFT JOIN " . \SOME\SOME::_dbprefix() . "cms_snippet_folders AS tSF ON tSF.id = tS.pid
                                 SET tS.description = REPLACE(tS.description, ?, ?) 
                               WHERE NOT tS.locked AND tSF.urn != '__raas_interfaces'";
                $this->SQL->query(array($SQL_query, $key, $val));
            }
        }
        if (!in_array('legacy', $this->columns(\SOME\SOME::_dbprefix() . "cms_blocks_material"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_material ADD legacy TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Redirect legacy addresses'";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks_material SET legacy = 1 WHERE 1";
            $this->SQL->query($SQL_query);
        }
    }


    protected function update20141104()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_material_types", $this->tables) && !in_array('pid', $this->columns(\SOME\SOME::_dbprefix() . "cms_material_types"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_material_types ADD pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent type ID#' AFTER id";
            $this->SQL->query($SQL_query);
        }
    }


    protected function update20141109()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_pages", $this->tables) && !in_array('visit_counter', $this->columns(\SOME\SOME::_dbprefix() . "cms_pages"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_pages
                            ADD visit_counter INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Visit counter',
                            ADD modify_counter INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modify counter',
                            ADD changefreq ENUM('', 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never') NOT NULL DEFAULT '' COMMENT 'Change frequency', 
                            ADD inherit_changefreq TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit change frequency',
                            ADD last_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last modified',
                            ADD sitemaps_priority DECIMAL(8,2) UNSIGNED NOT NULL DEFAULT 0.5 COMMENT 'Sitemaps priority',
                            ADD inherit_sitemaps_priority TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit sitemaps priority'";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_pages SET last_modified = modify_date, modify_counter = (post_date != modify_date) WHERE 1";
            $this->SQL->query($SQL_query);
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_materials
                            ADD visit_counter INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Visit counter',
                            ADD modify_counter INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modify counter',
                            ADD changefreq ENUM('', 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never') NOT NULL DEFAULT '' COMMENT 'Change frequency', 
                            ADD last_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last modified',
                            ADD sitemaps_priority DECIMAL(8,2) UNSIGNED NOT NULL DEFAULT 0.5 COMMENT 'Sitemaps priority'";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_materials SET last_modified = modify_date, modify_counter = (post_date != modify_date) WHERE 1";
            $this->SQL->query($SQL_query);
        }
    }


    protected function update20141222()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks", $this->tables) && !in_array('cache_type', $this->columns(\SOME\SOME::_dbprefix() . "cms_blocks"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks
                            ADD cache_type TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cache type',
                            ADD cache_single_page TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cache by single pages',
                            ADD cache_interface_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cache interface_id',
                            ADD KEY (cache_interface_id)";
            $this->SQL->query($SQL_query);
        }
    }


    protected function update20150125()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_fields", $this->tables)) {
            if (!in_array('step', $this->columns(\SOME\SOME::_dbprefix() . "cms_fields"))) {
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_fields ADD step FLOAT NOT NULL DEFAULT 0 COMMENT 'Step' AFTER max_val";
                $this->SQL->query($SQL_query);
            }
            if (!in_array('preprocessor_id', $this->columns(\SOME\SOME::_dbprefix() . "cms_fields"))) {
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_fields 
                                ADD preprocessor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Preprocessor interface ID#' AFTER step,
                                ADD KEY (preprocessor_id)";
                $this->SQL->query($SQL_query);
            }
            if (!in_array('postprocessor_id', $this->columns(\SOME\SOME::_dbprefix() . "cms_fields"))) {
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_fields 
                                ADD postprocessor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Postprocessor interface ID#' AFTER preprocessor_id,
                                ADD KEY (postprocessor_id)";
                $this->SQL->query($SQL_query);
            }
        }
    }


    protected function update20150301()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks_groups_blacklist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_groups_blacklist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks_users_blacklist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_users_blacklist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks_groups_whitelist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_groups_whitelist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks_users_whitelist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_blocks_users_whitelist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_materials_groups_blacklist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_materials_groups_blacklist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_materials_users_blacklist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_materials_users_blacklist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_materials_groups_whitelist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_materials_groups_whitelist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_materials_users_whitelist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_materials_users_whitelist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_pages_groups_blacklist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_pages_groups_blacklist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_pages_users_blacklist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_pages_users_blacklist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_pages_groups_whitelist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_pages_groups_whitelist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_pages_users_whitelist", $this->tables)) {
            $SQL_query = "DROP TABLE IF EXISTS " . \SOME\SOME::_dbprefix() . "cms_pages_users_whitelist";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_pages", $this->tables) && in_array('access', $this->columns(\SOME\SOME::_dbprefix() . "cms_pages"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_pages DROP access, DROP inherit_access";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_materials", $this->tables) && in_array('access', $this->columns(\SOME\SOME::_dbprefix() . "cms_materials"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_materials DROP access";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks", $this->tables) && in_array('access', $this->columns(\SOME\SOME::_dbprefix() . "cms_blocks"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks DROP access";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_blocks", $this->tables) && !in_array('vis_material', $this->columns(\SOME\SOME::_dbprefix() . "cms_blocks"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks ADD vis_material TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Visibility by material'";
            $this->SQL->query($SQL_query);
        }
    }


    public function update20150504()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_pages", $this->tables) && !in_array('h1', $this->columns(\SOME\SOME::_dbprefix() . "cms_pages"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_pages 
                            ADD h1 varchar(255) NOT NULL DEFAULT '' COMMENT 'H1 title' AFTER inherit_meta_keywords, 
                            ADD menu_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu name' AFTER h1,
                            ADD breadcrumbs_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Breadcrumbs name' AFTER menu_name";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_materials", $this->tables) && !in_array('h1', $this->columns(\SOME\SOME::_dbprefix() . "cms_materials"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_materials 
                            ADD h1 varchar(255) NOT NULL DEFAULT '' COMMENT 'H1 title' AFTER meta_keywords, 
                            ADD menu_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu name' AFTER h1,
                            ADD breadcrumbs_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Breadcrumbs name' AFTER menu_name";
            $this->SQL->query($SQL_query);
        }
  
    }


    public function update20150610()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_feedback", $this->tables) && !in_array('material_id', $this->columns(\SOME\SOME::_dbprefix() . "cms_feedback"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_feedback ADD material_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material ID#' AFTER page_id";
            $this->SQL->query($SQL_query);
        }
    }


    public function update20150617()
    {
        if (in_array(\SOME\SOME::_dbprefix() . "cms_materials", $this->tables) && !in_array('show_from', $this->columns(\SOME\SOME::_dbprefix() . "cms_materials"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_materials 
                            ADD show_from DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish from date/time',
                            ADD INDEX (show_from)";
            $this->SQL->query($SQL_query);
        }
        if (in_array(\SOME\SOME::_dbprefix() . "cms_materials", $this->tables) && !in_array('show_to', $this->columns(\SOME\SOME::_dbprefix() . "cms_materials"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_materials 
                            ADD show_to DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish to date/time',
                            ADD INDEX (show_to)";
            $this->SQL->query($SQL_query);
        }
    }


    public function update20151129()
    {
        if (!in_array('urn', $this->columns(\SOME\SOME::_dbprefix() . "cms_forms"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_forms 
                            ADD urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN' AFTER name,
                            ADD INDEX (urn)";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_forms SET urn = 'feedback' WHERE (urn = '') AND (name = 'Обратная связь' OR name = 'Feedback')";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_forms SET urn = 'order_call' WHERE (urn = '') AND (name = 'Заказать звонок' OR name = 'Order call')";
            $this->SQL->query($SQL_query);
        }
        if (!in_array('urn', $this->columns(\SOME\SOME::_dbprefix() . "cms_groups"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_groups 
                            ADD urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN' AFTER name,
                            ADD INDEX (urn)";
            $this->SQL->query($SQL_query);
        }
        if (!in_array('urn', $this->columns(\SOME\SOME::_dbprefix() . "cms_menus"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_menus 
                            ADD urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN' AFTER name,
                            ADD INDEX (urn)";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_menus SET urn = 'top' WHERE (NOT pid) AND (urn = '') AND (name = 'Верхнее меню' OR name = 'Top menu')";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_menus SET urn = 'bottom' WHERE (NOT pid) AND (urn = '') AND (name = 'Нижнее меню' OR name = 'Bottom menu')";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_menus SET urn = 'left' WHERE (NOT pid) AND (urn = '') AND (name = 'Левое меню' OR name = 'Left menu')";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_menus SET urn = 'right' WHERE (NOT pid) AND (urn = '') AND (name = 'Правое меню' OR name = 'Right menu')";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_menus SET urn = 'main' WHERE (NOT pid) AND (urn = '') AND (name = 'Главное меню' OR name = 'Main menu')";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_menus SET urn = 'sitemap' WHERE (NOT pid) AND (urn = '') AND (name = 'Карта сайта' OR name = 'Sitemap')";
            $this->SQL->query($SQL_query);
        }
        if (!in_array('urn', $this->columns(\SOME\SOME::_dbprefix() . "cms_templates"))) {
            $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_templates 
                            ADD urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN' AFTER name,
                            ADD INDEX (urn)";
            $this->SQL->query($SQL_query);
            $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_templates SET urn = 'main' WHERE (urn = '') AND (name = 'Главная' OR name = 'Main')";
            $this->SQL->query($SQL_query);
        }
    }
}