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
    }


    public function postInstall()
    {
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
                                 SET tB.interface_id = IF(tB2.std_interface, (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__RAAS_material_interface'), 0),
                                     tB.interface = tB2.interface,
                                     tB.widget_id = tB2.widget,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_material DROP std_interface, DROP interface, DROP widget, DROP description";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_menu AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = IF(tB2.std_interface, (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__RAAS_menu_interface'), 0),
                                     tB.interface = tB2.interface,
                                     tB.widget_id = tB2.widget,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_menu DROP std_interface, DROP interface, DROP widget, DROP description";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_form AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = IF(tB2.std_interface, (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__RAAS_form_interface'), 0),
                                     tB.interface = tB2.interface,
                                     tB.widget_id = tB2.widget,
                                     tB.widget = tB2.description";
                $this->SQL->query($SQL_query);
                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_blocks_form DROP std_interface, DROP interface, DROP widget, DROP description";
                $this->SQL->query($SQL_query);

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_blocks AS tB
                                JOIN " . \SOME\SOME::_dbprefix() . "cms_blocks_search AS tB2 ON tB.id = tB2.id
                                 SET tB.interface_id = IF(tB2.std_interface, (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__RAAS_search_interface'), 0),
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

                $SQL_query = "UPDATE " . \SOME\SOME::_dbprefix() . "cms_forms SET interface_id = (SELECT id FROM " . \SOME\SOME::_dbprefix() . "cms_snippets WHERE urn = '__RAAS_form_notify') 
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
                        array('pid' => Snippet_Folder::importByURN('__RAAS_interfaces')->id, 'urn' => $urn, 'name' => $row['name'], 'description' => $row['interface'], 'locked' => 0)
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
                        array('pid' => Snippet_Folder::importByURN('__RAAS_views')->id, 'urn' => $urn, 'name' => $row['name'], 'description' => $row['widget'], 'locked' => 0)
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
                        array('pid' => Snippet_Folder::importByURN('__RAAS_interfaces')->id, 'urn' => $urn, 'name' => $row['name'], 'description' => $row['description'], 'locked' => 0)
                    );
                    $this->SQL->update(\SOME\SOME::_dbprefix() . "cms_forms", "id = " . (int)$row['id'], array('interface_id' => $id, 'description' => ''));
                }

                $SQL_query = "ALTER TABLE " . \SOME\SOME::_dbprefix() . "cms_forms DROP description";
                $this->SQL->query($SQL_query);
            }
        }
    }
}